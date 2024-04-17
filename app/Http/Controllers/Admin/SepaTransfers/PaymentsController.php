<?php

namespace App\Http\Controllers\Admin\SepaTransfers;

use App\Models\Bank;
use App\Models\Customer;
use App\Models\GatewayPayment\Base;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Models\Provider;
use App\Models\PurchaseInvoice;
use App\Models\SepaTransfer\Payment;
use App\Models\SepaTransfer\PaymentGroup;
use App\Models\SepaTransfer\PaymentTransaction;
use Illuminate\Http\Request;
use Yajra\Datatables\Facades\Datatables;
use Html, Auth, Response, File, Setting, Redirect, Artisan;

class PaymentsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'sepa_transfers';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',sepa_transfers']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        return $this->setContent('admin.sepa_transfers.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $action = 'Criar transferência SEPA';

        $payment = new Payment();
        $payment->code = $payment->setCode();

        $formOptions = array('route' => array('admin.sepa-transfers.store'), 'method' => 'POST');

        $banks = Bank::filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'payment',
            'banks',
            'action',
            'formOptions'
        );

        return view('admin.sepa_transfers.create', $data)->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        return $this->update($request, null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        $payment = Payment::filterSource()->findOrfail($id);

        $formOptions = array('route' => array('admin.sepa-transfers.update', $payment->id), 'method' => 'PUT');

        $banks = Bank::filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $action = 'Consultar Transferência SEPA';
        if($payment->edit_mode) {
            $action = 'Editar Transferência SEPA';
        }

        $data = compact(
            'payment',
            'banks',
            'action',
            'formOptions'
        );

        return view('admin.sepa_transfers.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        $input = $request->all();

        if(@$input['bank_id']) {
            $bank = Bank::filterSource()->findOrFail($input['bank_id']);
            $input['company_id']   = $bank->company_id;
            $input['company_name'] = $bank->titular_name;
            $input['company_vat']  = $bank->titular_vat;
            $input['bank_name']    = $bank->bank_name;
            $input['bank_iban']    = $bank->bank_iban;
            $input['bank_swift']   = $bank->bank_swift;
            $input['credor_code']  = $bank->credor_code;
        }

        $payment = Payment::findOrNew($id);
        $exists  = $payment->exists;

        if($request->get('conclude')) {
            if($payment->type == 'dd') {
                $input['status'] = Payment::STATUS_PENDING;
            } else {
                $input['status'] = Payment::STATUS_CONCLUDED;

                PaymentGroup::where('payment_id', $payment->id)->update(['status' => PaymentGroup::STATUS_ACCEPTED]);
                PaymentTransaction::where('payment_id', $payment->id)->update(['status' => PaymentTransaction::STATUS_ACCEPTED]);
            }
        }

        if ($payment->validate($input)) {
            $payment->fill($input);
            $payment->source = config('app.source');
            $payment->save();

            if($exists) {
                return Redirect::route('admin.sepa-transfers.index')->with('success', 'Transferência gravada com sucesso.');
            }

            return Redirect::route('admin.sepa-transfers.index', ['action' => 'edit', 'id' => $payment->id])->with('success', 'Transferência gravada com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $payment->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        $result = Payment::filterSource()
            ->whereId($id)
            ->delete();

        if ($result) {

            Invoice::where('sepa_payment_id', $id)->update(['sepa_payment_id' => null]);

            return Redirect::route('admin.sepa-transfers.index')->with('success', 'Registo removido com sucesso.');
        }

        return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o registo.');

    }

    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        $ids = explode(',', $request->ids);

        $result = Payment::filterSource()
            ->whereIn('id', $ids)
            ->whereStatus(Payment::STATUS_EDITING)
            ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Não foi possível remover os registos selecionados');
        }

        return Redirect::back()->with('success', 'Registos selecionados removidos com sucesso.');
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatable(Request $request) {

        $data = Payment::with('groups')
                    ->select();

        //filter status
        $value = $request->get('status');
        if($request->has('status')) {
            $data = $data->where('status', $value);
        }

        //filter type
        $value = $request->get('type');
        if($request->has('type')) {
            $data = $data->where('type', $value);
        }

        return Datatables::of($data)
            ->editColumn('code', function($row) {
                return view('admin.sepa_transfers.datatables.code', compact('row'))->render();
            })
            ->editColumn('name', function($row) {
                return view('admin.sepa_transfers.datatables.name', compact('row'))->render();
            })
            ->editColumn('bank_iban', function($row) {
                return view('admin.sepa_transfers.datatables.bank_iban', compact('row'))->render();
            })
            ->editColumn('transactions_total', function($row) {
                return view('admin.sepa_transfers.datatables.transactions_total', compact('row'))->render();
            })
            ->editColumn('status', function($row) {
                return view('admin.sepa_transfers.datatables.status', compact('row'))->render();
            })
            ->editColumn('created_at', function($row) {
                return view('admin.partials.datatables.created_at', compact('row'))->render();
            })
            ->addColumn('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->addColumn('actions', function($row) {
                return view('admin.sepa_transfers.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }
    
    /**
     * Show the form for merge two helpdesks.
     *
     * @return \Illuminate\Http\Response
     */
    public function createXML(Request $request, $paymentId) {
        $sepaPayment = Payment::filterSource()->find($paymentId);
        $sepaPayment->createXml('file');
        exit;
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editReturnFile(Request $request, $paymentId) {

        $payment = Payment::filterSource()->find($paymentId);

        $formOptions = array('route' => array('admin.sepa-transfers.return.store', $payment->id), 'method' => 'POST', 'files' => true);

        $data = compact(
            'payment',
            'formOptions'
        );

        return view('admin.sepa_transfers.import_return', $data)->render();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeReturnFile(Request $request, $paymentId)
    {
        $payment = Payment::filterSource()->find($paymentId);

        try {
            $xml = simplexml_load_file($request->file('file')->getRealPath());

            /* 
            //Antigo método onde o identificador era o nome
            $paymentDesc = (string) $xml->CstmrPmtStsRpt->OrgnlGrpInfAndSts->OrgnlMsgId;
            
            $paymentName = removeAccents($payment->name);
            
            if($paymentCode != $payment->code) {
                return Redirect::route('admin.sepa-transfers.index')->with('error', 'O ficheiro de retorno não corresponde à transferência SEPA selecionada.');
            } 
            */

            $paymentCode = (string) $xml->CstmrPmtStsRpt->OrgnlGrpInfAndSts->OrgnlMsgId;

            if($paymentCode != $payment->code) {
                return Redirect::route('admin.sepa-transfers.index')->with('error', 'O ficheiro de retorno não corresponde à transferência SEPA selecionada.');
            }

            //processa os grupos
            foreach ($xml->CstmrPmtStsRpt->OrgnlPmtInfAndSts as $group) {

                $groupCode = (string) $group->OrgnlPmtInfId;
                $errorCode = (string) $group->StsRsnInf->Rsn->Prtry;
                $errorMsg  = $payment->getReturnStatusMsg($errorCode);
                $status    = PaymentGroup::STATUS_REJECTED;

                if($errorCode == 'L000') {
                    $errorCode = null;
                    $errorMsg  = null;
                    $status    = PaymentGroup::STATUS_ACCEPTED;
                } elseif($errorCode == 'L001') {
                    $status    = PaymentGroup::STATUS_ACCEPTED_PARTIAL;
                }

                PaymentGroup::where('payment_id', $paymentId)
                    ->where('code', $groupCode)
                    ->update([
                        'status'     => $status,
                        'error_code' => $errorCode,
                        'error_msg'  => $errorMsg
                    ]);


                //processa as transações do grupo
                foreach ($xml->CstmrPmtStsRpt->OrgnlPmtInfAndSts->TxInfAndSts as $transaction) {

                    $reference = (string) $transaction->OrgnlEndToEndId;
                    $errorCode = (string) $transaction->StsRsnInf->Rsn->Cd;
                    $errorMsg  = $payment->getReturnStatusMsg($errorCode);
                    $status    = PaymentTransaction::STATUS_REJECTED;

                    if($errorCode == '0000') {
                        $errorCode = null;
                        $errorMsg  = null;
                        $status    = PaymentTransaction::STATUS_ACCEPTED;
                    }

                    $updateArr = [
                        'status'     => $status,
                        'error_code' => $errorCode,
                        'error_msg'  => $errorMsg
                    ];

                    if($status == PaymentTransaction::STATUS_REJECTED) {
                        $updateArr['invoice_id'] = null;

                        //desassocia as faturas não pagas e liberta-as para poder gerar novo pagamento
                        $internalCode = str_replace('PRF', '', $reference);
                        $internalCode = str_replace('FT', '', $internalCode);
                        $internalCode = trim($internalCode);

                        Invoice::where('doc_id', $internalCode)
                            ->where('sepa_payment_id', $paymentId)
                            ->update(['sepa_payment_id' => null]);
                    }


                    PaymentTransaction::where('payment_id', $paymentId)
                        ->where('reference', $reference)
                        ->update($updateArr);

                }
            }

            //processa estado geral do pagamento
            $bankOperationCode = (string) $xml->CstmrPmtStsRpt->GrpHdr->MsgId;
            $paymentStatusCode = (string) $xml->CstmrPmtStsRpt->OrgnlGrpInfAndSts->StsRsnInf->Rsn->Prtry;
            $paymentStatusMsg  = $payment->getReturnStatusMsg($paymentStatusCode);
            $paymentStatus     = Payment::STATUS_REJECTED;
            $hasErrors         = 1;

            if($paymentStatusCode == 'M000') {
                $paymentStatusCode = null;
                $paymentStatusMsg  = null;
                $paymentStatus     = Payment::STATUS_CONCLUDED;
                $hasErrors         = 0;
            } elseif($paymentStatusCode == 'M001') {
                $paymentStatus     = Payment::STATUS_CONCLUDED_PARTIAL;
            }

            $payment->update([
                'status'     => $paymentStatus,
                'error_code' => $paymentStatusCode,
                'error_msg'  => $paymentStatusMsg,
                'has_errors' => $hasErrors,
                'bank_operation_code' => $bankOperationCode,
            ]);

            return Redirect::route('admin.sepa-transfers.index', ['action' => 'edit', 'id' => $payment->id])->with('success', 'Ficheiro lido com sucesso.');

        } catch (\Exception $e) {
            return Redirect::route('admin.sepa-transfers.index')->with('error', 'Falha na leitura do ficheiro de retorno: '. $e->getMessage());
        }


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editImportInvoices(Request $request) {

        $ids = $request->id;

        $invoices = Invoice::with('customer')
            ->filterSource()
            ->where('is_settle', 0)
            ->where('is_draft', 0)
            ->where('is_deleted', 0)
            ->whereNull('sepa_payment_id')
            ->whereIn('doc_type', ['invoice', 'proforma-invoice'])
            ->where('payment_condition', 'dbt')
            ->whereIn('id', $ids)
            ->get();

        $banks = Bank::filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $payment = new Payment();
        $payment->code = $payment->setCode();

        $formOptions = array('route' => array('admin.sepa-transfers.import.invoices.store'), 'method' => 'POST', 'class' => 'form-billing');

        $data = compact(
            'payment',
            'invoices',
            'banks',
            'formOptions'
        );

        return view('admin.sepa_transfers.import_invoices', $data)->render();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeImportInvoices(Request $request) {

        $input = $request->toArray();

        $bank = Bank::filterSource()->findOrFail($input['bank_id']);

        $invoices = Invoice::filterSource()
            ->whereIn('id', $input['invoice'])
            ->whereNull('sepa_payment_id')
            ->get();

        try {
            //create payment
            $payment = new Payment();
            $payment->source        = config('app.source');
            $payment->code          = $input['code'];
            $payment->name          = $input['name'];
            $payment->company_id    = $bank->company_id;
            $payment->company_name  = $bank->titular_name;
            $payment->company_vat   = $bank->titular_vat;
            $payment->bank_id       = $bank->id;
            $payment->bank_name     = $bank->bank_name;
            $payment->bank_iban     = $bank->bank_iban;
            $payment->bank_swift    = $bank->bank_swift;
            $payment->credor_code   = $bank->credor_code;
            $payment->transactions_count = $invoices->count();
            $payment->transactions_total = $invoices->sum('doc_total');
            $payment->status        = Payment::STATUS_EDITING;
            $payment->save();


            //create payment group
            $paymentGroup = new PaymentGroup();
            $paymentGroup->payment_id         = $payment->id;
            $paymentGroup->code               = $payment->code.'-01';
            $paymentGroup->processing_date    = $input['processing_date'];
            $paymentGroup->service_type       = $input['service_type'];
            $paymentGroup->sequence_type      = $input['sequence_type'];
            $paymentGroup->company            = $payment->company;
            $paymentGroup->bank_name          = $payment->bank_name;
            $paymentGroup->bank_iban          = $payment->bank_iban;
            $paymentGroup->bank_swift         = $payment->bank_swift;
            $paymentGroup->credor_code        = $payment->credor_code;
            $paymentGroup->transactions_count = $payment->transactions_count;
            $paymentGroup->transactions_total = $payment->transactions_total;
            $paymentGroup->save();

            foreach ($invoices as $invoice) {
                $transaction = new PaymentTransaction();
                $transaction->payment_id    = $payment->id;
                $transaction->group_id      = $paymentGroup->id;
                $transaction->invoice_id    = $invoice->id;
                $transaction->customer_id   = $invoice->customer_id;
                $transaction->amount        = $invoice->doc_total;
                $transaction->mandate_code  = @$invoice->customer->bank_mandate;
                $transaction->mandate_date  = @$invoice->customer->bank_mandate_date ? @$invoice->customer->bank_mandate_date : @$invoice->customer->created_at;
                $transaction->reference     = @$invoice->name;
                $transaction->company_code  = @$invoice->customer->code;
                $transaction->company_name  = @$invoice->customer->billing_name;
                $transaction->company_vat   = @$invoice->customer->vat;
                $transaction->bank_name     = @$invoice->customer->bank_name;
                $transaction->bank_iban     = @$invoice->customer->bank_iban;
                $transaction->bank_swift    = @$invoice->customer->bank_swift;
                $transaction->obs           = 'Documento '.@$invoice->name;
                $transaction->save();
            }

            Invoice::whereIn('id', $input['invoice'])->update(['sepa_payment_id' => $payment->id]);
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }


        return Redirect::route('admin.sepa-transfers.index', ['action' => 'edit', 'id' => $payment->id])->with('success', 'Transferência SEPA criada com sucesso.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printSummary(Request $request, $paymentId)
    {
        return Payment::printSummary([$paymentId]);
    }

    /**
     * Notify transactions errors
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function notifyTransactionsErrors(Request $request, $paymentId)
    {
        $payment = Payment::filterSource()->findOrFail($paymentId);
        Artisan::call('sepa:notify-errors');

        return Redirect::route('admin.sepa-transfers.index')->with('success', 'Notificação executada com sucesso.');
    }

    /**
     * Search on selectbox
     * @return type
     */
    public function searchSelectBox(Request $request, $searchType) {
        if($searchType == 'customer') {
            return $this->searchCustomer($request);
        } elseif($searchType == 'provider') {
            return $this->searchProvider($request);
        } elseif($searchType == 'invoice') {
            return $this->searchInvoice($request);
        } elseif($searchType == 'purchase-invoice') {
            return $this->searchPurchaseInvoice($request);
        }
    }

    /**
     * Search customers on DB
     *
     * @return type
     */
    public function searchCustomer(Request $request) {

        $search = trim($request->get('q'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

        $fields = [
            'id',
            'code',
            'name',
            'billing_name',
            'bank_name',
            'bank_iban',
            'bank_swift',
            'bank_mandate',
            'bank_mandate_date',
            'created_at'
        ];

        try {
            $results = [];

            $customers = Customer::filterSource()
                ->where(function($q) use($search){
                    $q->where('code', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search)
                        ->orWhere('billing_name', 'LIKE', $search)
                        ->orWhere('vat', 'LIKE', $search);
                })
                ->get($fields);

            if($customers) {
                $results = array();
                foreach($customers as $customer) {
                    $results[] = [
                        'id'           => $customer->id,
                        'text'         => '['.$customer->code.'] '.str_limit($customer->name, 35),
                        'code'         => $customer->code,
                        'name'         => $customer->billing_name,
                        'bank_name'    => $customer->bank_name,
                        'bank_iban'    => $customer->bank_iban,
                        'bank_swift'   => $customer->bank_swift,
                        'mandate_code' => $customer->bank_mandate,
                        'mandate_date' => $customer->bank_mandate_date ? $customer->bank_mandate_date : $customer->created_at
                    ];
                }

            } else {
                $results[] = [
                    'id'  => '',
                    'text' => 'Nenhum cliente encontrado.'
                ];
            }

        } catch(\Exception $e) {
            $results[] = [
                'id' => '',
                'text' => 'Erro interno. ' . $e->getMessage()
            ];
        }

        return Response::json($results);
    }


    /**
     * Search customers on DB
     *
     * @return type
     */
    public function searchProvider(Request $request) {

        $search = trim($request->get('q'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

        $fields = [
            'id',
            'code',
            'name',
            'company',
            'bank_name',
            'bank_iban',
            'bank_swift'
        ];

        try {
            $results = [];

            $providers = Provider::filterSource()
                ->where(function($q) use($search){
                    $q->where('code', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search)
                        ->orWhere('company', 'LIKE', $search)
                        ->orWhere('vat', 'LIKE', $search);
                })
                ->get($fields);

            if($providers) {
                $results = array();
                foreach($providers as $provider) {
                    $results[] = [
                        'id'           => $provider->id,
                        'text'         => '['.$provider->code.'] '.str_limit($provider->name, 35),
                        'code'         => $provider->code,
                        'name'         => $provider->company,
                        'bank_name'    => $provider->bank_name,
                        'bank_iban'    => $provider->bank_iban,
                        'bank_swift'   => $provider->bank_swift,
                        'bank_mandate' => $provider->bank_mandate,
                        'mandate_code' => null,
                        'mandate_date' => null
                    ];
                }

            } else {
                $results[] = [
                    'id'  => '',
                    'text' => 'Nenhum fornecedor encontrado.'
                ];
            }

        } catch(\Exception $e) {
            $results[] = [
                'id' => '',
                'text' => 'Erro interno. ' . $e->getMessage()
            ];
        }

        return response()->json($results);
    }


    /**
     * Search customers on DB
     *
     * @return type
     */
    public function searchInvoice(Request $request) {

        $search = trim($request->get('q'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

        $fields = [
            'id',
            'doc_id',
            'doc_type',
            'doc_series',
            'internal_code',
            'billing_name',
            'doc_total',
            'vat'
        ];

        try {
            $results = [];

            $invoices = Invoice::filterSource()
                ->where('is_deleted', 0)
                ->where('is_draft', 0)
                ->where('is_settle', 0)
                ->whereNull('sepa_payment_id')
                ->whereIn('doc_type', ['proforma-invoice', 'invoice'])
                ->where(function($q) use($search){
                    $q->where('internal_code', 'LIKE', $search)
                        ->orWhere('doc_total', 'LIKE', $search)
                        ->orWhere('billing_name', 'LIKE', $search)
                        ->orWhere('vat', 'LIKE', $search);
                })
                ->get($fields);

            if($invoices) {
                $results = array();
                foreach($invoices as $invoice) {

                    $docType = trans('admin/billing.types_code.'.$invoice->doc_type);

                    $results[] = [
                        'id'           => $invoice->id,
                        'text'         => '['. $docType . $invoice->internal_code. '] '.str_limit($invoice->billing_name, 35),
                        'code'         => $invoice->internal_code,
                        'total'        => $invoice->doc_total
                    ];
                }

            } else {
                $results[] = [
                    'id'  => '',
                    'text' => 'Nenhuma fatura encontrada.'
                ];
            }

        } catch(\Exception $e) {
            $results[] = [
                'id' => '',
                'text' => 'Erro interno. ' . $e->getMessage()
            ];
        }

        return Response::json($results);
    }


    /**
     * Search customers on DB
     *
     * @return type
     */
    public function searchPurchaseInvoice(Request $request) {

        $search = trim($request->get('q'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

        $fields = [
            'id',
            'doc_id',
            'doc_type',
            'doc_series',
            'billing_name',
            'total',
            'vat'
        ];

        try {
            $results = [];

            $invoices = PurchaseInvoice::filterSource()
                ->where('is_deleted', 0)
                ->where('is_draft', 0)
                ->where('is_settle', 0)
                ->whereNull('sepa_payment_id')
                ->whereNotIn('doc_type', ['payment-note'])
                ->where(function($q) use($search){
                    $q->orWhere('total', 'LIKE', $search)
                        ->orWhere('billing_name', 'LIKE', $search)
                        ->orWhere('vat', 'LIKE', $search);
                })
                ->get($fields);

            if($invoices) {
                $results = array();
                foreach($invoices as $invoice) {

                    $docType = trans('admin/billing.types_code.'.$invoice->doc_type);

                    $results[] = [
                        'id'           => $invoice->id,
                        'text'         => '['. $docType . $invoice->doc_series. $invoice->doc_id. '] '.str_limit($invoice->billing_name, 35),
                        'code'         => $invoice->doc_series. $invoice->doc_id,
                        'total'        => $invoice->total
                    ];
                }

            } else {
                $results[] = [
                    'id'  => '',
                    'text' => 'Nenhuma fatura encontrada.'
                ];
            }

        } catch(\Exception $e) {
            $results[] = [
                'id' => '',
                'text' => 'Erro interno. ' . $e->getMessage()
            ];
        }

        return Response::json($results);
    }

    /**
     * Show modal to stettle no doc invoice
     *
     * @param Request $request
     * @param $invoiceId
     * @return string
     * @throws \Throwable
     */
    public function editInvoices(Request $request, $paymentId = null) {

        $payment = Payment::with(['transactions' => function($q){
                $q->with('invoice');
                $q->whereNotNull('invoice_id');
            }])
            ->whereHas('transactions', function($q){
                $q->whereNotNull('invoice_id');
                $q->whereHas('invoice', function($q){
                    $q->whereNull('assigned_invoice_id');
                });
            })
            ->filterSource()
            ->findOrFail($paymentId);

        $transactions = $payment->transactions;
        
        $series  = Invoice::getApiKeys();

        $paymentMethods = PaymentMethod::ordered()
            ->pluck('name', 'code')
            ->toArray();

        $data = compact(
            'payment',
            'transactions',
            'paymentMethods',
            'series'
        );

        return view('admin.sepa_transfers.modals.autocreate_invoice', $data)->render();
    }

    /**
     * Show modal to stettle no doc invoice
     *
     * @param Request $request
     * @param $invoiceId
     * @return string
     * @throws \Throwable
     */
    public function storeInvoices(Request $request, $paymentId = null) {

        $input = $request->all();
        $input['apiKey']         = $request->get('apiKey');
        $input['doc_date']       = $request->get('doc_date');
        $input['doc_type']       = $request->get('doc_type');
        $input['payment_method'] = $request->get('payment_method');
        $input['payment_date']   = $request->get('payment_date');
        $input['send_email']     = $request->get('send_email');

        $payment = Payment::with(['transactions' => function($q){
                $q->with('invoice');
                $q->whereNotNull('invoice_id');
                $q->whereHas('invoice', function($q){
                    $q->whereNull('assigned_invoice_id');
                });
            }])
            ->whereHas('transactions', function($q){
                $q->whereNotNull('invoice_id');
                $q->whereHas('invoice', function($q){
                    $q->whereNull('assigned_invoice_id');
                });
            })
            ->filterSource()
            ->findOrFail($paymentId);

        $transactions = $payment->transactions;

        foreach ($transactions as $transaction) {

            try {
                $invoice = Invoice::filterSource()->find($transaction->invoice_id);

                if(!$invoice->is_settle && !$invoice->is_deleted && !$invoice->is_reversed) {
                    if($invoice->doc_type == Invoice::DOC_TYPE_FT) {
                        $invoice->autocreateReceiptFromInvoice($input);

                        //credita saldo na conta do cliente se o cliente tiver metodo de pagamento conta corrente
                        if(@$invoice->customer->payment_method->code == 'wallet' || @$invoice->customer->payment_method == 'wallet') {

                            $customer = $invoice->customer;
                            $customer->addWallet($invoice->total);

                            //regista transação
                            $paymentLog = Base::where('customer_id', $customer->id)
                                ->where('target', 'Invoice')
                                ->where('target_id', $invoice->id)
                                ->first();
                                
                            if(!$paymentLog) {
                                $paymentLog = new Base();
                            }

                            $paymentLog->source = config('app.source');
                            $paymentLog->customer_id    = $customer->id;
                            $paymentLog->gateway        = '';
                            $paymentLog->target         = 'Invoice';
                            $paymentLog->target_id      = $invoice->id;
                            $paymentLog->method         = 'wallet';
                            $paymentLog->value          = $invoice->total;
                            $paymentLog->sense          = 'credit';
                            $paymentLog->currency       = 'EUR';
                            $paymentLog->reference      = 'FT'.$invoice->doc_id;
                            $paymentLog->description    = 'Carregamento Conta Débito Direto. FT '.$invoice->doc_id;
                            $paymentLog->status         = 'success';
                            $paymentLog->setCode();
                        }
                    } else {
                        $invoice->autocreatePaymentDocument($input);
                    }
                }
               
            } catch (\Exception $e) {
                return Redirect::back()->with('error', $e->getMessage());
            }
        }

        return Redirect::route('admin.invoices.index')->with('success', 'Faturas emitidas com sucesso.');
    }
}
