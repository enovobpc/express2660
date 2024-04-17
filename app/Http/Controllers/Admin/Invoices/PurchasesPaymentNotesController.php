<?php

namespace App\Http\Controllers\Admin\Invoices;

use App\Models\Bank;
use App\Models\FileRepository;
use App\Models\InvoiceGateway\KeyInvoice\Document;
use App\Models\PaymentMethod;
use App\Models\Provider;
use App\Models\Invoice;
use App\Models\PurchaseInvoice;
use App\Models\PurchasePaymentNote;
use App\Models\PurchasePaymentNoteInvoice;
use App\Models\PurchasePaymentNoteMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Auth, Response,File;

class PurchasesPaymentNotesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'purchase-invoices';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',purchase_invoices']);
        validateModule('purchase_invoices');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {

        $paymentNote = PurchasePaymentNote::with('invoices.invoice', 'payment_methods')
            ->filterSource()
            ->where('id', $id)
            ->firstOrFail();

        $invoiceAttachments = FileRepository::with('created_user')
            ->where('source_class', 'PurchasePaymentNote')
            ->where('source_id', $paymentNote->id)
            ->orderBy('name', 'asc')
            ->get();

        $invoices = $paymentNote->invoices;
        $payments = $paymentNote->payment_methods;
        $banks    = Bank::listBanks();

        $compact = compact(
            'invoices',
            'payments',
            'paymentNote',
            'banks',
            'invoiceAttachments'
        );

        return view('admin.invoices.purchases.payment_show', $compact)->render();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {

        $ids = $request->id;
        $providerId = $request->provider;

        $paymentNote = new PurchasePaymentNote();

        $payments = null;
        $provider = null;
        $invoices = null;

        if($providerId) {
            $provider = Provider::filterSource()->whereId($providerId)->first();
        }

        if($ids) {
            $invoices = PurchaseInvoice::filterSource()
                ->whereIn('id', $ids)
                ->where('is_settle', 0)
                ->where('is_deleted', 0)
                ->get();

            $provider = $invoices->first()->provider;
        } else if($provider) {

            $provider = Provider::filterSource()->where('id', $provider->id)->first();

            $invoices = PurchaseInvoice::filterSource()
                //->where('provider_id', $provider->id) //original. Verifica pelo mesmo fornecedor
                ->where('vat', $provider->vat) //verifica todas as faturas do mesmo NIF
                ->where('is_settle', 0)
                ->where('doc_type', '!=' , 'order')
                ->where('is_deleted', 0)
                ->get();
        }

        $paymentMethods = PaymentMethod::filterSource()
            ->isActive()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $banks = Bank::listBanks();

        $formOptions = ['route' => ['admin.invoices.purchase.payment-notes.store'], 'class' => 'settle-invoice', 'files' => true];

        $compact = compact(
            'invoices',
            'payments',
            'provider',
            'paymentNote',
            'paymentMethods',
            'banks',
            'formOptions'
        );

        return view('admin.invoices.purchases.payment', $compact)->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $id    = null;
        $input = $request->all();
        $input['date'] = @$input['date'] ? $input['date'] : date('Y-m-d');

        $provider = null;
        if(empty($input['provider_id']) && (empty($input['vat']) || $input['vat'] == '999999990') && empty($input['code'])) {
            return response()->json([
                'result'   => false,
                'feedback' => 'Não é possível gerar o pagamento: O fornecedor indicado é inválido.'
            ]);
        } else {
            $provider = Provider::where('id', $input['provider_id'])->first();
        }

        $invoices = [];
        if(@$input['invoices']) {

            foreach ($input['invoices'] as $invoiceId => $value) {
                $value = (float)@$value['total'];

                $invoice = PurchaseInvoice::find($invoiceId);
                $invoicePending = $invoice->total_unpaid ? $invoice->total_unpaid : $invoice->total;
                $valuePending   = $invoicePending - $value;


                if (!empty($value)) {
                    $invoices[] = [
                        'invoice_id'    => $invoiceId,
                        'total'         => $value,
                        'total_pending' => $valuePending,
                        'invoice_total' => $invoice->total,
                        'invoice_unpaid'=> $invoicePending,
                    ];
                }
            }
        }

        $payments = [];
        if(@$input['payment']) {
            foreach ($input['payment'] as $payment) {
                if (!empty(@$payment['value'])) {
                    $payments[] = [
                        'payment_method_id' => $payment['payment_method_id'],
                        'bank_id'           => $payment['bank_id'],
                        'date'              => $payment['date'],
                        'total'             => $payment['value'],
                        'obs'               => $payment['obs'],
                    ];
                }
            }
        }

        if(empty($invoices)) {
            return response()->json([
                'result'   => false,
                'feedback' => 'Não é possível gerar o pagamento: Não existem faturas a liquidar.',
                'printPdf' => false,
                'html'     => false
            ]);
        }



        //obtem a nota de pagamento ou cria uma nova caso ainda não exista
        $paymentNote = PurchasePaymentNote::findOrNew($id);

        if(!$paymentNote->exists) {
            $paymentNote->source           = config('app.source');
            $paymentNote->provider_id      = $input['provider_id'];
            $paymentNote->doc_date         = $input['date'];
            $paymentNote->reference        = $input['reference'];
            $paymentNote->vat              = @$provider->vat;
            $paymentNote->billing_code     = @$provider->code;
            $paymentNote->billing_name     = @$provider->company;
            $paymentNote->billing_address  = @$provider->address;
            $paymentNote->billing_zip_code = @$provider->zip_code;
            $paymentNote->billing_city     = @$provider->city;
            $paymentNote->billing_country  = @$provider->country;
            $paymentNote->user_id          = Auth::user()->id;
            $paymentNote->setCode(true);
        }

        $paymentNoteId = $paymentNote->id;

        if($paymentNoteId) {

            try {

                $paymentMethodCode = @$payments[0]['method'];

                //grava os valores liquidados de cada fatura
                foreach ($invoices as $invoice) {
                    $paymentInvoice = PurchasePaymentNoteInvoice::findOrNew($id);
                    $paymentInvoice->payment_note_id = $paymentNoteId;
                    $paymentInvoice->fill($invoice);
                    $paymentInvoice->save();
                }

                //grava os valores e formas de pagamento
                foreach ($payments as $payment) {
                    $paymentMethod = PurchasePaymentNoteMethod::findOrNew($id);
                    $paymentMethod->payment_note_id = $paymentNoteId;
                    $paymentMethod->fill($payment);
                    $paymentMethod->save();
                }

                //update invoice total payments
                $paymentNoteTotal = 0;
                $invoicesIds = [];
                foreach ($invoices as $invoice) {
                    $invoicesIds[] = $invoice['invoice_id'];
                    $purchaseInvoice = PurchaseInvoice::find($invoice['invoice_id']);
                    $purchaseInvoice->total_unpaid-= $invoice['total'];
                    $purchaseInvoice->is_settle      = $purchaseInvoice->total_unpaid <= 0.00 ? true : false;
                    $purchaseInvoice->payment_date   = $purchaseInvoice->total_unpaid <= 0.00 ? $input['date'] : null;
                    $purchaseInvoice->payment_method = $purchaseInvoice->total_unpaid <= 0.00 ? $paymentMethodCode : null;
                    $purchaseInvoice->save();

                    $paymentNoteTotal+= $invoice['total'];

                    //update provider counters
                    PurchaseInvoice::updateProviderCounters($purchaseInvoice->provider_id);
                }


                if(@$input['discount']) {
                    $paymentNote->discount = @$input['discount'];
                    $paymentNote->discount_unity = @$input['discount_unity'];

                    if($paymentNote->discount_unity == '%') {
                        $paymentNote->discount = ($paymentNoteTotal * ($input['discount'] / 100));
                    }

                    $paymentNoteTotal = $paymentNoteTotal - $paymentNote->discount;

                } else {
                    $paymentNote->discount_unity = null;
                }



                //REGIME IVA CAIXA - corridexcelente
                //a fatura e sempre paga na totalidade
                if(config('app.source') == 'corridexcelente') {
                    $purchasesInvoices = PurchaseInvoice::whereIn('id', $invoicesIds)->get();
                    $paymentNote->subtotal  = $purchasesInvoices->sum('subtotal');
                    $paymentNote->vat_total = $purchasesInvoices->sum('vat_total');
                }

                $paymentNote->total = $paymentNoteTotal;
                $paymentNote->save();

                $paymentNote->storeOrUpdatePurchaseInvoice();


                //ASSIGN ATTACHMENT
                if ($request->hasFile('attachment')) {

                    $files = $request->file('attachment');
                    foreach ($files as $file) {
                        /*$attachment = FileRepository::firstOrNew([
                            'source_id'    => $paymentNote->id,
                            'source_class' => 'PurchasePaymentNote'
                        ]);*/

                        $attachment = new FileRepository();
                        $attachment->parent_id = FileRepository::FOLDER_PURCHASE_INVOICES;
                        $attachment->source_class = 'PurchasePaymentNote';
                        $attachment->source_id = $paymentNote->id;
                        $attachment->user_id = Auth::user()->id;

                        if (!$attachment->upload($file, true, 40)) {
                            //return Redirect::back()->withInput()->with('error', 'Não foi possível carregar o documento.');
                        }
                    }
                }

            } catch (\Exception $e) {
                dd($e->getMessage());
                if(@$paymentNote->exists) {
                    $paymentNote->delete();
                }
            }

            if(@$input['send_email'] && @$paymentNote->exists) {

                $emailResult = $paymentNote->sendEmail([
                    'email'       => trim(@$input['billing_email']),
                    'attachments' => @$input['attachments'],
                ]);

                if(!$emailResult) {
                    return response()->json([
                        'result'   => true,
                        'feedback' => 'Não foi possível enviar o e-mail ao cliente.',
                        'printPdf' => route('admin.invoices.purchase.payment-notes.download', $paymentNote->id),
                        'html'     => view('admin.shipments.shipments.modals.popup_denied')->render()
                    ]);
                }
            }

            return response()->json([
                'result'   => true,
                'feedback' => 'Pagamento realizado com sucesso.',
                'printPdf' => route('admin.invoices.purchase.payment-notes.download', @$paymentNote->id),
                'html'     => view('admin.shipments.shipments.modals.popup_denied')->render()
            ]);
        }


        return response()->json([
            'result'   => false,
            'feedback' => 'Não é possível gerar o pagamento.',
            'printPdf' => false,
            'html'     => false
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {

        $paymentNote = PurchasePaymentNote::with('invoices.invoice', 'payment_methods')
            ->filterSource()
            ->where('id', $id)
            ->firstOrFail();

        $paymentMethods = PaymentMethod::filterSource()
            ->isActive()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $invoices = $paymentNote->invoices;
        $payments = $paymentNote->payment_methods;
        $provider = @$invoices->first()->invoice->provider;

        $banks = Bank::listBanks();

        $formOptions = ['route' => ['admin.invoices.purchase.payment-notes.update', $paymentNote->id], 'method' => 'PUT', 'class' => 'settle-invoice', 'files' => true];

        $compact = compact(
            'invoices',
            'payments',
            'provider',
            'paymentNote',
            'paymentMethods',
            'banks',
            'formOptions'
        );

        return view('admin.invoices.purchases.payment_edit', $compact)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id = null) {

        $input = $request->all();

        //obtem a nota de pagamento ou cria uma nova caso ainda não exista
        $paymentNote = PurchasePaymentNote::findOrNew($id);

        if($paymentNote->exists) {
            $paymentNote->fill($input);
            $paymentNote->save();

            if ($request->hasFile('attachment')) {

                $files = $request->file('attachment');
                foreach ($files as $file) {
                    
                    $attachment = new FileRepository();
                    $attachment->name         = 'Recibo';
                    $attachment->parent_id    = FileRepository::FOLDER_PURCHASE_INVOICES;
                    $attachment->source_class = 'PurchasePaymentNote';
                    $attachment->source_id    = $paymentNote->id;
                    $attachment->user_id      = Auth::user()->id;

                    if (!$attachment->upload($file, true, 40)) {
                        //return Redirect::back()->withInput()->with('error', 'Não foi possível carregar o documento.');
                    }
                }
            }

            return response()->json([
                'result'   => true,
                'feedback' => 'Pagamento editado com sucesso.'
            ]);


        }


        return response()->json([
            'result'   => false,
            'feedback' => 'Não é possível editar o pagamento.',
            'printPdf' => false,
            'html'     => false
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id = null) {

        try {

            if($request->has('reference')) {
                $paymentNote = PurchasePaymentNote::filterSource()
                    ->where('code', $request->get('reference'));

                if($request->has('provider')) {
                    $paymentNote = $paymentNote->where('provider_id', $request->get('provider'));
                }

                $paymentNote = $paymentNote->first();
            } else {
                $paymentNote = PurchasePaymentNote::whereId($id)->first();
            }

            $providerId  = $paymentNote->provider_id;

            if ($paymentNote->id) {

                //apaga anexos
                $attachments = FileRepository::where('source_id', $paymentNote->id)
                    ->where('source_class', 'PurchasePaymentNote')
                    ->get();

                foreach ($attachments as $attachment) {
                    if(File::exists(public_path($attachment->filepath))) {
                        File::delete(public_path($attachment->filepath));
                    }
                    $attachment->delete();
                }


                PurchasePaymentNoteMethod::where('payment_note_id', $paymentNote->id)->delete();

                $purchaseInvoices = PurchasePaymentNoteInvoice::with('invoice')
                    ->where('payment_note_id', $paymentNote->id)
                    ->get();

                foreach ($purchaseInvoices as $invoice) {

                    $purchaseInvoice = $invoice->invoice;
                    if($purchaseInvoice) {
                        $purchaseInvoice->total_unpaid = @$purchaseInvoice->total_unpaid + @$invoice->total;
                        $purchaseInvoice->is_settle = 0;
                        $purchaseInvoice->payment_date = null;
                        $purchaseInvoice->payment_method = null;
                        $purchaseInvoice->save();
                    }

                    $invoice->delete();
                }

                $paymentNote->deleted_by = Auth::user()->id; //atualiza quem eliminou
                $paymentNote->save();

                $paymentNote->delete();


                //atualiza na conta corrente geral
                PurchaseInvoice::where('doc_type', 'payment-note')
                    ->where('reference', $paymentNote->code)
                    ->where('provider_id', $paymentNote->provider_id)
                    ->delete();

                PurchaseInvoice::updateProviderCounters($providerId);

            }

            return Redirect::back()->with('success', 'Nota pagamento anulada com sucesso.');

        } catch (\Exception $e) {
            dd($e->getMessage(). ' '.$e->getFile() . ' linha '. $e->getLine());
            return Redirect::back()->with('error', 'Erro ao anular a Nota de Pagamento.');
        }

    }


    /**
     * Search customers on DB
     *
     * @return type
     */
    public function searchProvider(Request $request) {

        $search = $request->get('q');
        $search = '%' . str_replace(' ', '%', $search) . '%';

        try {

            $providers = Provider::filterSource()
                ->where(function($q) use($search){
                    $q->where('code', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search)
                        ->orWhere('vat', 'LIKE', $search)
                        ->orWhere('phone', 'LIKE', $search);
                })
                ->get(['name', 'code', 'id']);

            if($providers) {

                $results = array();
                foreach($providers as $provider) {
                    $results[]=array('id'=> $provider->id, 'text' => $provider->code. ' - '.str_limit($provider->company, 40));
                }

            } else {
                $results = [['id' => '', 'text' => 'Nenhum fornecedor encontrado.']];
            }

        } catch(\Exception $e) {
            $results = [['id' => '', 'text' => 'Erro interno ao processar o pedido.']];
        }

        return Response::json($results);
    }

    /**
     * Download billing invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request, $id, $type = null) {

        if($request->has('reference')) {
            $paymentNote = PurchasePaymentNote::filterSource()
                ->withTrashed()
                ->where('code', $request->get('reference'));

            if($request->has('provider')) {
                $paymentNote = $paymentNote->where('provider_id', $request->get('provider'));
            }

            $paymentNote = $paymentNote->first();
            $id = $paymentNote->id;
        } else {
            $paymentNote = PurchasePaymentNote::filterSource()->withTrashed()->find($id);
        }

        if(empty($paymentNote)) {
            return Redirect::back()->with('error', 'Não foi encontrada nenhuma nota de pagamento.');
        }

        return PurchasePaymentNote::printPaymentNote([$id]);
    }

    /**
     * Show modal to edit billing emaill
     * @param Request $request
     * @param $id
     */
    public function editEmail(Request $request, $paymentNoteId) {

        if($request->has('reference')) {
            $paymentNote = PurchasePaymentNote::with('provider')
                ->filterSource()
                ->where('code', $request->get('reference'));

            if($request->has('provider')) {
                $paymentNote = $paymentNote->where('provider_id', $request->get('provider'));
            }

            $paymentNote = $paymentNote->first();
        } else {
            $paymentNote = PurchasePaymentNote::with('provider')
                ->filterSource()
                ->findOrFail($paymentNoteId);
        }

        $data = compact('paymentNote');

        return view('admin.invoices.purchases.modals.email', $data)->render();
    }

    /**
     * submit billing info by e-mail
     * @param Request $request
     * @param $id
     */
    public function submitEmail(Request $request, $paymentNoteId) {

        $paymentNote = PurchasePaymentNote::with('provider')
            ->filterSource()
            ->findOrFail($paymentNoteId);

        $data = [
            'email'         => $request->get('email'),
            'attachments'   => $request->get('attachments', []),
        ];

        $result = $paymentNote->sendEmail($data);

        if(!$result) {
            return Response::json([
                'result'   => false,
                'feedback' => 'Não foi possível enviar o e-mail. Não selecionou nenhum documento para enviar em anexo.'
            ]);
        }

        return Response::json([
            'result'   => true,
            'feedback' => 'E-mail enviado com sucesso.'
        ]);
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatable(Request $request) {

        $data = PurchasePaymentNote::filterSource()
            ->with('provider', 'invoices')
            ->select();

        //filter date min
        $dtMin = $request->get('date_min');
        if ($request->has('date_min')) {
            $dtMax = $dtMin;
            if ($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }
            $data = $data->whereBetween('doc_date', [$dtMin, $dtMax]);
        }

        //filter provider
        $value = $request->provider;
        if($request->has('provider')) {
            $data = $data->where('provider_id', $value);
        }

        //filter deleted
        $value = $request->deleted;
        if($request->has('deleted')) {
            if($value) {
                $data = $data->withTrashed();
            }
        }

        return Datatables::of($data)
            ->edit_column('doc_date', function($row) {
                return view('admin.invoices.purchases.datatables.payment_notes.doc_date', compact('row'))->render();
            })
            ->edit_column('code', function($row) {
                return view('admin.invoices.purchases.datatables.payment_notes.code', compact('row'))->render();
            })
            ->edit_column('reference', function($row) {
                return view('admin.invoices.purchases.datatables.payment_notes.reference', compact('row'))->render();
            })
            ->edit_column('provider_id', function($row) {
                return view('admin.invoices.purchases.datatables.payment_notes.provider', compact('row'))->render();
            })
            ->edit_column('total', function($row) {
                return view('admin.invoices.purchases.datatables.payment_notes.total', compact('row'))->render();
            })
            ->edit_column('count_invoices', function($row) {
                return view('admin.invoices.purchases.datatables.payment_notes.count_invoices', compact('row'))->render();
            })
            ->edit_column('created_at', function($row) {
                return view('admin.invoices.purchases.datatables.payment_notes.created_at', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.invoices.purchases.datatables.payment_notes.actions', compact('row'))->render();
            })
            ->make(true);
    }
}
