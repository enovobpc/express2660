<?php

namespace App\Http\Controllers\Admin\Invoices;

use App\Models\Agency;
use App\Models\Bank;
use App\Models\Trip\Trip;
use App\Models\Trip\TripExpense;
use App\Models\Billing\ItemStockHistory;
use App\Models\FileRepository;
use App\Models\FleetGest\Cost;
use App\Models\FleetGest\Expense;
use App\Models\FleetGest\FuelLog;
use App\Models\FleetGest\Maintenance;
use App\Models\FleetGest\Vehicle;
use App\Models\PaymentCondition;
use App\Models\PaymentMethod;
use App\Models\Provider;
use App\Models\Invoice;
use App\Models\ProviderCategory;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceLine;
use App\Models\PurchaseInvoiceType;
use App\Models\PurchasePaymentNote;
use App\Models\PurchasePaymentNoteInvoice;
use App\Models\PurchasePaymentNoteMethod;
use App\Models\Shipment;
use App\Models\User;
use App\Models\UserExpense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Jenssegers\Date\Date;
use Yajra\Datatables\Facades\Datatables;
use Auth, Response, Setting, DB;

class PurchasesController extends \App\Http\Controllers\Admin\Controller
{

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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $purchasesTypes = PurchaseInvoiceType::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $paymentConditions = PaymentCondition::filterSource()
            ->isSalesVisible()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->whereSource(config('app.source'))
            ->filterAgencies()
            ->orderBy('code', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'purchasesTypes',
            'paymentConditions',
            'agencies'
        );

        return $this->setContent('admin.invoices.purchases.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $invoice = new PurchaseInvoice();
        $invoice->is_scheduled = $request->get('scheduled');

        if ($request->has('type')) {
            $invoice->type_id = $request->get('type');
        }

        $providerId = $request->get('provider');
        $provider   = Provider::findOrNew($providerId);
        $newProviderCode = $provider->setCode(false);

        $docDate = date('Y-m-d');
        $docLimitDate = new Carbon();
        $docLimitDate = $docLimitDate->addDays(30)->format('Y-m-d');
        $paymentUntil = $docLimitDate;


        $apiKeys  = Invoice::getApiKeys();
        $vatTaxes = Invoice::getVatTaxes(false);

        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->whereSource(config('app.source'))
            ->filterAgencies()
            ->orderBy('code', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $purchasesTypes = $this->listTypes(PurchaseInvoiceType::filterSource()
            ->ordered()
            ->get());

        $providerCategories = ProviderCategory::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $paymentConditions = PaymentCondition::filterSource()
            ->isSalesVisible()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $paymentMethods = PaymentMethod::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $banks = Bank::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $invoiceAttachments = null;

        $route = route('admin.invoices.purchase.store', [
            'provider'  => @$provider->id,
            'target'    => PurchaseInvoice::TARGET_INVOICE,
        ]);


        $formOptions = ['url' => $route, 'method' => 'POST', 'files' => true, 'class' => 'form-billing'];

        $action = 'Registar fatura de compra';
        if ($invoice->is_scheduled) {
            $action = 'Agendar despesa fixa ou períodica';
        }

        $data = compact(
            'invoice',
            'provider',
            'docDate',
            'docLimitDate',
            'paymentUntil',
            'apiKeys',
            'vatTaxes',
            'action',
            'formOptions',
            'newProviderCode',
            'agencies',
            'purchasesTypes',
            'providerCategories',
            'invoiceAttachments',
            'paymentConditions',
            'paymentMethods',
            'banks'
        );

        return view('admin.invoices.purchases.edit', $data)->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->update($request, null);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {

        $invoice = PurchaseInvoice::filterSource()
            ->whereId($id)
            ->firstOrFail();

        $provider     = $invoice->provider;
        $docDate      = $invoice->doc_date;
        $docLimitDate = $invoice->due_date;
        $paymentUntil = $invoice->payment_until;

        $apiKeys  = Invoice::getApiKeys();
        $vatTaxes = Invoice::getVatTaxes(false);

        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->whereSource(config('app.source'))
            ->filterAgencies()
            ->orderBy('code', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $purchasesTypes = $this->listTypes(PurchaseInvoiceType::filterSource()
            ->ordered()
            ->get());

        $providerCategories = ProviderCategory::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $invoiceAttachments = FileRepository::with('created_user')
            ->where('source_class', 'PurchaseInvoice')
            ->where('source_id', $invoice->id)
            ->orderBy('name', 'asc')
            ->get();

        $paymentConditions = PaymentCondition::filterSource()
            ->isSalesVisible()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $paymentMethods = PaymentMethod::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $banks = Bank::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();


        $route = route('admin.invoices.purchase.update', [
            $id,
            'provider'  => @$invoice->provider_id,
            'target'    => $invoice->target,
        ]);

        $formOptions = ['url' => $route, 'method' => 'PUT', 'files' => true, 'class' => 'form-billing'];

        $action = 'Editar fatura de compra';
        if ($invoice->is_scheduled) {
            $action = 'Editar despesa fixa ou períodica';
        }

        if ($invoice->target == 'Vehicle') {
            $targetSources = Vehicle::filterSource()->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        } elseif ($invoice->target == 'User') {
            $targetSources = User::filterSource()->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        } elseif ($invoice->target == 'Shipment') {
            $targetSources = Shipment::filterAgencies()
                ->where('is_collection', 0)
                ->where('provider_id', $invoice->provider_id)
                ->orderBy('date', 'desc')
                ->take(200)
                ->get(['tracking_code', 'date', 'sender_name', 'id'])
                ->toArray();
        } else {
            $targetSources = [];
        }

        $targetType = $invoice->target;

        $data = compact(
            'invoice',
            'agencies',
            'provider',
            'docDate',
            'docLimitDate',
            'paymentUntil',
            'apiKeys',
            'vatTaxes',
            'action',
            'formOptions',
            'purchasesTypes',
            'targetSources',
            'providerCategories',
            'targetType',
            'invoiceAttachments',
            'paymentConditions',
            'paymentMethods',
            'banks',
            'paymentConditions',
            'banks'
        );

        return view('admin.invoices.purchases.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id = null)
    {

        $input = $request->all();
        $input['ignore_stats'] = $request->get('ignore_stats', false);
        $input['assigned_targets'] = $request->get('assigned_targets', []);
        $input['assigned_targets'] = array_filter($input['assigned_targets']);

        if (!empty($input['payment_method_id'])) {
            $paymentMethod = PaymentMethod::find($input['payment_method_id']);
            $input['payment_method'] = $paymentMethod->code;
        }

        if (empty($input['target'])) {
            return response()->json([
                'result'   => false,
                'feedback' => 'Não é possível gerar a fatura: Target em falta.'
            ]);
        }

        if (empty($input['provider_id']) && (empty($input['vat']) || $input['vat'] == '999999990') && empty($input['code'])) {
            return response()->json([
                'result'   => false,
                'feedback' => 'Não é possível gerar a fatura: O fornecedor o contribuínte é inválido ou o fornecedor não tem código.'
            ]);
        }


        $invoiceExists = PurchaseInvoice::where('vat', trim($input['vat']))
            ->where('reference', $input['docref'])
            ->where('is_deleted', 0)
            ->where('id', '<>', $id)
            ->first();

        if ($invoiceExists) {
            return response()->json([
                'result'   => false,
                'feedback' => 'A fatura indicada já existe em sistema para este fornecedor.'
            ]);
        }

        $type = PurchaseInvoiceType::filterSource()->find($input['type_id']);
        $targetType = $type->target_type ? $type->target_type : 'Invoice';

        if (empty(@$input['provider_id'])) {
            $provider = Provider::firstOrNew([
                'vat'  => $input['vat'],
                'code' => $input['billing_code'],
            ]);
        } else {
            $provider = Provider::firstOrNew([
                'id' => $input['provider_id']
            ]);
        }

        $providerExists = $provider->exists;

        if ($providerExists) {

            //grava a categoria
            if (empty($provider->category_id)) {
                $provider->category_id = @$input['category_id'];
                $provider->save();
            }

            //atualiza temporariamente os dados de faturação por defeito
            $provider->vat       = $input['vat'];
            $provider->code      = $input['billing_code'];
            $provider->name      = str_limit($input['billing_name'], 15, '');
            $provider->company   = $input['billing_name'];
            $provider->address   = $input['billing_address'];
            $provider->zip_code  = $input['billing_zip_code'];
            $provider->city      = $input['billing_city'];
            $provider->country   = @$input['billing_country'] ? $input['billing_country'] : Setting::get('app_country');
            $provider->billing_country = @$input['billing_country'];
        } else {

            $category = ProviderCategory::filterSource()->find($input['category_id']);

            $provider->agencies      = Auth::user()->agencies;
            $provider->vat           = $input['vat'];
            $provider->code          = $input['billing_code'];
            $provider->name          = str_limit($input['billing_name'], 15, '');
            $provider->company       = $input['billing_name'];
            $provider->address       = $input['billing_address'];
            $provider->zip_code      = $input['billing_zip_code'];
            $provider->city          = $input['billing_city'];
            $provider->country       = $input['billing_country'];
            $provider->type          = 'others';
            $provider->category_id   = @$category->id;
            $provider->category_slug = @$category->slug;
            $provider->color         = @$category->color;
            $provider->save();
        }

        try {
            $invoice = PurchaseInvoice::findOrNew($id);

            $oldTargets = $invoice->assigned_targets;

            $invoice->fill($input);
            $invoice->source            = config('app.source');
            $invoice->provider_id       = $provider->id;
            $invoice->target            = $targetType;
            //$invoice->description     = $input['description'];
            $invoice->doc_date          = $input['docdate'];
            $invoice->due_date          = @$input['duedate'];
            $invoice->reference         = @$input['docref'];
            $invoice->subtotal          = $input['subtotal'];
            $invoice->vat               = $input['vat'];
            $invoice->total             = $input['total'];
            $invoice->total_unpaid      = ($input['subtotal'] + $input['vat_total']);
            $invoice->setCode();


            if ($invoice->doc_type == 'provider-credit-note' || $invoice->doc_type == 'credit-note') {
                $invoice->sense        = 'debit';
                $invoice->subtotal     = $invoice->subtotal > 0.00 ? $invoice->subtotal * -1 : $invoice->subtotal;
                $invoice->vat_total    = $invoice->vat_total > 0.00 ? $invoice->vat_total * -1 : $invoice->vat_total;
                $invoice->total        = $invoice->total > 0.00 ? $invoice->total * -1 : $invoice->total;
                $invoice->total_unpaid = $invoice->total_unpaid > 0.00 ? $invoice->total_unpaid * -1 : $invoice->total_unpaid;
                $invoice->save();
            } else {
                $invoice->sense        = 'credit';
                $invoice->subtotal     = $invoice->subtotal < 0.00 ? $invoice->subtotal * -1 : $invoice->subtotal;
                $invoice->vat_total    = $invoice->vat_total < 0.00 ? $invoice->vat_total * -1 : $invoice->vat_total;
                $invoice->total        = $invoice->total < 0.00 ? $invoice->total * -1 : $invoice->total;
                $invoice->total_unpaid = $invoice->total_unpaid < 0.00 ? $invoice->total_unpaid * -1 : $invoice->total_unpaid;
                $invoice->save();
            }


            ItemStockHistory::deleteByPurchaseInvoiceId($invoice->id);
            PurchaseInvoiceLine::where('invoice_id', $invoice->id)->forceDelete();
            PurchaseInvoiceLine::storeLines($invoice->id, $input['line'], $provider);

            //update total unpaid
            if ($invoice->exists) {
                $totalUnpaid = PurchasePaymentNoteInvoice::where('invoice_id', $invoice->id)->sum('total');
                $invoice->total_unpaid = $invoice->total - $totalUnpaid;
            }

            if (in_array($invoice->doc_type, ['provider-simplified-invoice', 'provider-invoice-receipt'])) {
                $invoice->payment_date  = $invoice->doc_date;
                $invoice->is_settle     = 1;
                $invoice->total_unpaid  = 0;
            }

            $invoice->save();

            PurchaseInvoice::updateProviderCounters($invoice->provider_id);


            //store assigned expense
            if (!$invoice->ignore_stats) {
                $this->syncAssignedTargets($invoice, $input['assigned_targets'], @$oldTargets);
            }

            //ASSIGN ATTACHMENT
            if ($request->hasFile('file')) {

                $attachment = FileRepository::firstOrNew([
                    'source_id'    => $invoice->id,
                    'source_class' => 'PurchaseInvoice'
                ]);

                $attachment->fill($input);
                $attachment->parent_id    = FileRepository::FOLDER_PURCHASE_INVOICES;
                $attachment->source_class = 'PurchaseInvoice';
                $attachment->source_id    = $invoice->id;
                $attachment->user_id      = Auth::user()->id;

                if (!$attachment->upload($request->file('file'), true, 40)) {
                    return Redirect::back()->withInput()->with('error', 'Não foi possível carregar o documento.');
                }
            }


            //adiciona uma nota de pagamento para o tipo de documento fatura-recibo
            if ($invoice->doc_type == 'provider-invoice-receipt') {

                $paymentNote = PurchasePaymentNote::firstOrNew([
                    'provider_id' => $invoice->provider_id,
                    'reference'   => $invoice->reference
                ]);

                $paymentNote->source            = config('app.source');
                $paymentNote->provider_id       = $invoice->provider_id;
                $paymentNote->reference         = $invoice->reference;
                $paymentNote->vat               = $invoice->vat;
                $paymentNote->billing_code      = $invoice->billing_code;
                $paymentNote->billing_name      = $invoice->billing_name;
                $paymentNote->billing_address   = $invoice->billing_address;
                $paymentNote->billing_zip_code  = $invoice->billing_zip_code;
                $paymentNote->billing_city      = $invoice->billing_city;
                $paymentNote->billing_country   = $invoice->billing_country;
                $paymentNote->doc_date          = $invoice->doc_date;
                $paymentNote->subtotal          = $invoice->subtotal;
                $paymentNote->vat_total         = $invoice->vat_total;
                $paymentNote->discount          = $invoice->discount;
                $paymentNote->discount_unity    = $invoice->discount_unity;
                $paymentNote->total             = $invoice->total;
                $paymentNote->user_id           = $invoice->created_by;
                $paymentNote->setCode(true);

                if ($paymentNote->id) {
                    $paymentNoteInvoice = PurchasePaymentNoteInvoice::firstOrNew([
                        'payment_note_id' => $paymentNote->id,
                        'invoice_id'      => $invoice->id
                    ]);

                    $paymentNoteInvoice->payment_note_id = $paymentNote->id;
                    $paymentNoteInvoice->invoice_id      = $invoice->id;
                    $paymentNoteInvoice->total           = $invoice->total;
                    $paymentNoteInvoice->total_pending   = 0;
                    $paymentNoteInvoice->invoice_total   = $invoice->total;
                    $paymentNoteInvoice->invoice_unpaid  = 0;
                    $paymentNoteInvoice->save();


                    if (!empty($invoice->payment_method) || !empty($invoice->bank_id)) {
                        $paymentNoteMethod = PurchasePaymentNoteMethod::firstOrNew([
                            'payment_note_id' => $paymentNote->id
                        ]);

                        $paymentNoteMethod->payment_note_id     = $paymentNote->id;
                        $paymentNoteMethod->payment_method_id   = $invoice->payment_method_id;
                        $paymentNoteMethod->bank_id             = $invoice->bank_id;
                        /* $paymentNoteMethod->bank                = $invoice->bank->code;
                        $paymentNoteMethod->method              = $invoice->payment_method;  */
                        $paymentNoteMethod->date                = $invoice->doc_date;
                        $paymentNoteMethod->total               = $invoice->total;
                        $paymentNoteMethod->save();
                    }
                }
            }


            $result = [
                'result'   => true,
                'feedback' => 'Fatura de compra gravada com sucesso.'
            ];
        } catch (\Exception $e) {
            ItemStockHistory::deleteByPurchaseInvoiceId($invoice->id);
            PurchaseInvoiceLine::where('invoice_id', $invoice->id)->forceDelete();
            PurchaseInvoice::whereId($invoice->id)->forceDelete();

            $result = [
                'result'   => false,
                'feedback' => $e->getMessage() . (Auth::user()->isAdmin() ? ' na linha ' . $e->getLine() . ' ficheiro ' . $e->getFile() : '')
            ];
        }

        return response()->json($result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id = null)
    {

        $purchaseInvoice = PurchaseInvoice::whereId($id)->first();

        $providerId = $purchaseInvoice->provider_id;

        $purchaseInvoice->deleted_by = Auth::user()->id;
        $purchaseInvoice->save();

        /*//elimina uma nota de pagamento para o tipo de documento fatura-recibo
        if(config('app.source') == 'corridexcelente') {
            if($purchaseInvoice->doc_type == 'provider-invoice-receipt') {
                PurchasePaymentNote::where('provider_id', $purchaseInvoice->provider_id)
                    ->where('reference', $purchaseInvoice->reference)
                    ->update(['deleted_by' =>  $purchaseInvoice->deleted_by]);
            }
        }*/

        $result = $purchaseInvoice->delete();

        if ($result) {

            ItemStockHistory::deleteByPurchaseInvoiceId($id);
            PurchaseInvoice::updateProviderCounters($providerId);

            return Redirect::back()->with('success', 'Fatura eliminada com sucesso.');
        }

        return Redirect::back()->with('error', 'Erro ao anular a fatura.');
    }

    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request)
    {

        $ids = explode(',', $request->ids);

        $invoices = PurchaseInvoice::filterSource()
            ->whereIn('id', $ids)
            ->where('is_settle', false)
            ->get();

        $errors = false;
        foreach ($invoices as $invoice) {

            ItemStockHistory::deleteByPurchaseInvoiceId($invoice->id);

            $invoice->is_deleted = true;
            $result = $invoice->save();

            if (!$result) {
                $errors = true;
            }
        }

        if ($errors) {
            return Redirect::back()->with('error', 'Não foi possível anular um ou mais registos selecionados');
        }

        return Redirect::back()->with('success', 'Registos selecionados anulados com sucesso.');
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatable(Request $request)
    {

        $scheduled = $request->get('scheduled', false);

        $data = PurchaseInvoice::filterSource()
            ->with('payment_notes')
            ->with('provider', 'user')
            //->where('doc_type', '<>', 'payment-note')
            ->select();

        //filter agency
        $value = $request->agency;
        if ($request->has('agency')) {
            $data = $data->whereHas('provider', function ($q) use ($value) {
                $q->where('agencies', 'like', '%"' . $value . '"%');
            });
        }

        //filter date min
        $dtMin = $request->get('date_min');
        if ($request->has('date_min')) {
            $dtMax = $dtMin;
            if ($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }

            $dateUnity = 'doc_date';
            if ($request->has('date_unity')) {
                if ($request->date_unity == 'due') {
                    $dateUnity = 'due_date';
                } elseif ($request->date_unity == 'pay') {
                    $dateUnity = 'payment_date';
                }
            }

            $data = $data->whereBetween($dateUnity, [$dtMin, $dtMax]);
        }

        //filter sense
        $value = $request->sense;
        if ($request->has('sense')) {
            $data = $data->where('sense', $value);
        }

        //filter paid
        $value = $request->paid;
        if ($request->has('paid')) {
            if ($value == 1) {
                $data = $data->where('is_settle', 1);
            } elseif ($value == 2) {
                $data = $data->where('is_settle', 0)
                    ->where('total_unpaid', '>', 0.00)
                    ->whereRaw('total_unpaid < total');
            } else {
                $data = $data->where('is_settle', 0);
            }
        }

        //filter expired
        $value = $request->expired;
        if ($request->has('expired')) {
            if ($value) {
                $data = $data->where('due_date', '<', date('Y-m-d'));
            } else {
                $data = $data->where('due_date', '>=', date('Y-m-d'));
            }
        }

        //filter ignore invoice
        $value = $request->ignore_stats;
        if ($request->has('ignore_stats')) {
            $data = $data->where('ignore_stats', $value);
        }

        //filter assigned targets
        $value = $request->assigned_targets;
        if ($request->has('assigned_targets')) {
            if ($value > 0) {
                $data = $data->whereNotNull('ignore_stats');
            } else {
                $data = $data->whereNull('ignore_stats');
            }
        }

        //filter target
        $value = $request->target;
        if ($request->has('target')) {
            $data = $data->where('target', $value);
        }

        //filter target id
        $value = $request->target_id;
        if ($request->has('target_id')) {
            $data = $data->where('target_id', $value);
        }

        //filter type
        $value = $request->type;
        if ($request->has('type')) {
            $data = $data->whereIn('type_id', $value);
        }

        //filter doc id
        $value = $request->doc_id;
        if ($request->has('doc_id')) {
            $data = $data->where('reference', $value);
        }

        //filter doc type
        $value = $request->doc_type;
        if ($request->has('doc_type')) {
            $data = $data->whereIn('doc_type', $value);
        }

        //filter provider
        $value = $request->provider;
        if ($request->has('provider')) {
            $data = $data->where('provider_id', $value);
        }

        //filter payment condition
        $value = $request->payment_condition;
        if ($request->has('payment_condition')) {
            $data = $data->whereIn('payment_method', $value);
        }

        if ($scheduled) {
            $data = $data->whereNotNull('is_scheduled');
        } else {
            $data = $data->whereNull('is_scheduled');
        }

        //filter deleted
        $value = $request->deleted;
        if ($request->has('deleted') && empty($value)) {
            $data = $data->where('is_deleted', $value);
        }

        return Datatables::of($data)
            ->edit_column('code', function ($row) {
                return view('admin.invoices.purchases.datatables.code', compact('row'))->render();
            })
            ->edit_column('doc_date', function ($row) {
                return view('admin.invoices.purchases.datatables.doc_date', compact('row'))->render();
            })
            ->edit_column('doc_type', function ($row) {
                return view('admin.invoices.purchases.datatables.doc_type', compact('row'))->render();
            })
            ->edit_column('due_date', function ($row) {
                return view('admin.invoices.purchases.datatables.due_date', compact('row'))->render();
            })
            ->edit_column('payment_date', function ($row) {
                return view('admin.invoices.purchases.datatables.status', compact('row'))->render();
            })
            ->edit_column('payment_method', function ($row) {
                return view('admin.invoices.purchases.datatables.payment_date', compact('row'))->render();
            })
            ->edit_column('provider_id', function ($row) {
                return view('admin.invoices.purchases.datatables.provider', compact('row'))->render();
            })
            ->edit_column('description', function ($row) {
                return view('admin.invoices.purchases.datatables.description', compact('row'))->render();
            })
            ->edit_column('total', function ($row) {
                return view('admin.invoices.purchases.datatables.total', compact('row'))->render();
            })
            ->add_column('credit', function ($row) {
                return view('admin.invoices.purchases.datatables.credit', compact('row'))->render();
            })
            ->add_column('debit', function ($row) {
                return view('admin.invoices.purchases.datatables.debit', compact('row'))->render();
            })
            ->edit_column('vat_total', function ($row) {
                return view('admin.invoices.purchases.datatables.vat_total', compact('row'))->render();
            })
            ->edit_column('total_unpaid', function ($row) {
                return view('admin.invoices.purchases.datatables.unpaid', compact('row'))->render();
            })
            ->edit_column('assigned_targets', function ($row) {
                return view('admin.invoices.purchases.datatables.assigned_targets', compact('row'))->render();
            })
            ->edit_column('ignore_stats', function ($row) {
                return view('admin.invoices.purchases.datatables.ignore_stats', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->edit_column('is_scheduled', function ($row) {
                return view('admin.invoices.purchases.datatables.scheduled', compact('row'))->render();
            })
            ->edit_column('created_at', function ($row) {
                return view('admin.invoices.purchases.datatables.created_at', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.invoices.purchases.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Store target expenses
     *
     * @param $method
     * @param $parameters
     */
    public function storeTargetExpense($invoice)
    {

        if (!$invoice->target_id) {
            return false;
        }

        /*if($invoice->type_id == 1) { //abastecimentos
            $log = new FuelLog();
            $log->assigned_invoice_id = $invoice->id;
            $log->vehicle_id          = $invoice->target_id;
            $log->provider_id         = $invoice->provider_id;
            $log->total               = $invoice->subtotal;
            $log->date                = $invoice->doc_date;
            $log->km                  = @$invoice->kms ? @$invoice->kms : 0;
            //$log->operator_id         = @$invoice->operator_id;
            $log->created_by          = @$invoice->created_by;
            $log->save();

            FuelLog::updateVehicleCounters($log->vehicle_id);

        } elseif($invoice->type_id == 3) { //manutenções
            $log = new FuelLog();
            $log->assigned_invoice_id = $invoice->id;
            $log->vehicle_id          = $invoice->target_id;
            $log->provider_id         = $invoice->provider_id;
            $log->total               = $invoice->subtotal;
            $log->date                = $invoice->doc_date;
            $log->km                  = @$invoice->kms ? @$invoice->kms : 0;
            //$log->operator_id         = @$invoice->operator_id;
            $log->created_by          = @$invoice->created_by;
            $log->save();

            FuelLog::updateVehicleCounters($log->vehicle_id);
        } else { //custos gerais viatura*/

        $expense = new Expense();
        $expense->assigned_invoice_id = $invoice->id;
        $expense->vehicle_id          = $invoice->target_id;
        $expense->provider_id         = $invoice->provider_id;
        $expense->total               = $invoice->subtotal;
        $expense->date                = $invoice->doc_date;
        $expense->type_id             = @$invoice->type_id;
        $expense->km                  = @$invoice->kms ? @$invoice->kms : 0;
        $expense->title               = @$invoice->lines->first()->description;
        //$expense->operator_id         = @$invoice->operator_id;
        $expense->created_by          = @$invoice->created_by;

        $expense->save();

        Expense::updateVehicleCounters($expense->vehicle_id);
        /*}*/
    }

    /**
     * Download billing invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request, $id, $type = null)
    {

        $invoice = PurchaseInvoice::filterSource()->find($id);

        if (empty($invoice)) {
            return Redirect::back()->with('error', 'Não foi encontrada nenhuma fatura.');
        }

        /*if(0) {
            $webservice = new Invoice($invoice->api_key);
            $doc = $webservice->getDocumentPdf($invoice->doc_id, $invoice->doc_type);

            $data = base64_decode($doc);
            header('Content-Type: application/pdf');
            echo $data;
        }*/

        return PurchaseInvoice::printInvoices([$id]);
    }

    /**
     * Show modal to edit billing emaill
     * @param Request $request
     * @param $id
     */
    public function editEmail(Request $request, $providerId, $invoiceId)
    {

        $apiKey = $request->get('key');

        if ($apiKey) {
            $invoice = PurchaseInvoice::with('provider')
                ->filterSource()
                ->where('api_key', $apiKey)
                ->where('doc_id', $invoiceId)
                ->firstOrFail();
        } else {
            $invoice = PurchaseInvoice::with('provider')
                ->filterSource()
                ->where('provider_id', $providerId)
                ->where('doc_id', $invoiceId)
                ->firstOrFail();
        }

        $data = compact(
            'invoice',
            'month',
            'year',
            'period'
        );

        return view('admin.invoices.modals.email', $data)->render();
    }

    /**
     * submit billing info by e-mail
     * @param Request $request
     * @param $id
     */
    public function submitEmail(Request $request, $providerId, $invoiceId)
    {

        $apiKey = $request->get('key');

        if ($apiKey) {
            $invoice = PurchaseInvoice::with('provider')
                ->filterSource()
                ->where('api_key', $apiKey)
                ->where('doc_id', $invoiceId)
                ->firstOrFail();
        } else {
            $invoice = PurchaseInvoice::with('provider')
                ->filterSource()
                ->where('provider_id', $providerId)
                ->where('doc_id', $invoiceId)
                ->firstOrFail();
        }

        $data = [
            'email'         => $request->get('email'),
            'attachments'   => $request->get('attachments', []),
        ];

        $result = $invoice->sendEmail($data);

        if (!$result) {
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
     * Add fuel tax line
     *
     * @return \Illuminate\Http\Response
     */
    public function addFuelTaxLine($lines, $fuelTax, $exemptionReason = 'M05')
    {

        $fuelTax = $fuelTax / 100;

        $monthVat = $monthNoVat = 0;

        foreach ($lines as $line) {
            if (in_string('M', $line['tax_rate'])) {
                $monthNoVat += $line['subtotal'];
            } else {
                $monthVat += $line['subtotal'];
            }
        }

        $lines = [];

        if ($monthVat > 0.00) {
            $priceVat = $monthVat * $fuelTax;

            $lines['fuel-vat'] = [
                "reference"     => Setting::get('invoice_item_fuel_ref'),
                "description"   => Setting::get('invoice_item_fuel_desc'),
                "qty"           => 1,
                "total_price"   => $priceVat,
                "discount"      => 0,
                "subtotal"      => $priceVat,
                "tax_rate"      => Setting::get('vat_rate_normal'),
                'hidden'        => 1,
            ];
        }

        if ($monthNoVat > 0.00) {
            $priceVat = $monthNoVat * $fuelTax;

            $lines['fuel-nvat'] = [
                "reference"     => Setting::get('invoice_item_fuel_ref'),
                "description"   => Setting::get('invoice_item_fuel_desc'),
                "qty"           => 1,
                "total_price"   => $priceVat,
                "discount"      => 0,
                "subtotal"      => $priceVat,
                "tax_rate"      => Setting::get('exemption_reason_' . strtolower($exemptionReason)),
                'hidden'        => 1,
            ];
        }

        return $lines;
    }

    /**
     * Search providers on db
     *
     * @return type
     */
    public function searchProvider(Request $request)
    {

        $search = trim($request->get('query'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

        $fields = [
            'id',
            'vat',
            'code',
            'name',
            'company',
            'address',
            'zip_code',
            'city',
            'country',
            'email',
            'type',
            'category_id',
            'payment_method'
        ];

        try {

            $providers = Provider::filterSource()
                ->filterAgencies()
                ->filterActive()
                ->where(function ($q) use ($search) {
                    $q->where('code', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search)
                        ->orWhere('vat', 'LIKE', $search)
                        ->orWhere('phone', 'LIKE', $search);
                })
                ->take(10)
                ->get($fields);

            if ($providers) {

                $results = array();
                foreach ($providers as $provider) {
                    $results[] = [
                        'data'     => $provider->id,
                        'value'    => $provider->company ? strtoupper(trim($provider->company)) : strtoupper(trim($provider->name)),
                        'vat'      => str_replace(' ', '', trim($provider->vat)),
                        'code'     => strtoupper(trim($provider->code)),
                        'name'     => $provider->company ? strtoupper(trim($provider->company)) : strtoupper(trim($provider->name)),
                        'address'  => strtoupper(trim($provider->address)),
                        'zip_code' => strtoupper(trim($provider->zip_code)),
                        'city'     => strtoupper(trim($provider->city)),
                        'country'  => $provider->country,
                        'email'    => strtolower(trim($provider->email)),
                        'category' => $provider->category_id,
                        'payment_condition' => $provider->payment_method
                    ];
                }
            } else {
                $results = ['Nenhum fornecedor encontrado.'];
            }
        } catch (\Exception $e) {
            $results = ['Erro interno ao processar o pedido.'];
        }

        $results = [
            'suggestions' => $results
        ];

        return Response::json($results);
    }

    /**
     * Search providers on db
     *
     * @return type
     */
    public function searchProviderSelect2(Request $request)
    {

        $search = trim($request->get('q'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

        $fields = [
            'id',
            'code',
            'company',
            'name',
            'vat',
            'bank_iban'
        ];

        try {
            $results = [];

            $data = Provider::filterSource()
                ->filterAgencies()
                ->where(function ($q) use ($search) {
                    $q->where('company', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search)
                        ->orWhere('vat', 'LIKE', $search)
                        ->orWhere('code', 'LIKE', $search);
                })
                ->take(30)
                ->get($fields);

            if ($data) {
                $results = array();
                foreach ($data as $item) {
                    $results[] = [
                        'id'   => $item->id,
                        'text' => $item->code . ' - ' . str_limit($item->company ? $item->company : $item->name, 80),
                        'data-iban' => $item->bank_iban
                    ];
                }
            } else {
                $results[] = [
                    'id'  => '',
                    'text' => 'Nenhum fornecedor encontrado.'
                ];
            }
        } catch (\Exception $e) {
            $results[] = [
                'id' => '',
                'text' => 'Erro interno. ' . $e->getMessage()
            ];
        }

        return Response::json($results);
    }

    /**
     * Search invoices on DB
     *
     * @return type
     */
    public function searchInvoice(Request $request)
    {

        $search = trim($request->get('q'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

        $fields = [
            'id',
            'reference',
            'billing_name',
            'total'
        ];

        try {
            $results = [];

            $data = PurchaseInvoice::filterSource()
                //->filterAgencies()
                ->where(function ($q) use ($search) {
                    $q->where('reference', 'LIKE', $search)
                        ->orWhere('billing_name', 'LIKE', $search)
                        ->orWhere('total', 'LIKE', $search);
                })
                ->take(30)
                ->get($fields);

            if ($data) {
                $results = array();
                foreach ($data as $item) {
                    $results[] = [
                        'id'   => $item->id,
                        'text' => '[' . $item->reference . '] ' . str_limit($item->billing_name, 40),
                    ];
                }
            } else {
                $results[] = [
                    'id'  => '',
                    'text' => 'Nenhuma fatura encontrada.'
                ];
            }
        } catch (\Exception $e) {
            $results[] = [
                'id' => '',
                'text' => 'Erro interno. ' . $e->getMessage()
            ];
        }

        return Response::json($results);
    }

    /**
     * Search providers on DB
     *
     * @return type
     */
    public function getAssignedSources(Request $request)
    {

        $targetType = $request->get('type');
        $invoiceId  = $request->get('invoiceId');
        $providerId = $request->get('providerId');

        if ($targetType == 'Vehicle') {
            $targetSources = Vehicle::filterSource()->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        } elseif ($targetType == 'User') {
            $targetSources = User::filterSource()->where('active', 1)->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        } elseif ($targetType == 'Shipment' && $providerId) {
            $targetSources = Shipment::filterAgencies()
                ->where('is_collection', 0)
                ->where('provider_id', $providerId)
                ->orderBy('date', 'desc')
                ->take(200)
                ->get(['tracking_code', 'date', 'sender_name', 'id'])
                ->toArray();
        } else {
            $targetSources = [];
        }

        $invoice = PurchaseInvoice::filterSource()
            ->findOrNew($invoiceId);

        $data = compact(
            'invoice',
            'targetSources',
            'targetType'
        );

        return view('admin.invoices.purchases.partials.tabs.linked', $data);
    }

    /**
     * Replicate invoices
     *
     * @return \Illuminate\Http\Response
     */
    public function replicate($invoiceId)
    {

        try {
            $invoice = PurchaseInvoice::with('lines')->find($invoiceId);
            $lines = $invoice->lines;

            /*if ($invoice->target != 'Invoice') {
                return Redirect::back()->with('error', 'Não pode duplicar a fatura porque diz respeito a uma fatura mensal.');
            }*/

            $date = new Date();

            $newInvoice = $invoice->replicate();
            $newInvoice->doc_id         = null;
            $newInvoice->doc_date       = $date->format('Y-m-d');
            $newInvoice->due_date       = null;
            $newInvoice->total_unpaid   = $newInvoice->total;
            $newInvoice->is_settle      = 0;
            $newInvoice->is_deleted     = 0;
            $newInvoice->is_draft       = 1;
            $newInvoice->api_key        = null;
            $newInvoice->created_by     = Auth::user()->id;
            $newInvoice->save();

            foreach ($lines as $line) {
                $newLine = new PurchaseInvoiceLine();
                $newLine->fill($line->toArray());
                $newLine->invoice_id = $newInvoice->id;
                $newLine->save();
            }

            return Redirect::route('admin.invoices.purchase.index', ['invoice' => $newInvoice->id])->with('success', 'Fatura duplicada com sucesso.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erro interno ao duplicar a fatura.');
        }
    }

    /**
     * Return list of types with data attributes
     *
     * @param type $allTypes
     * @return type
     */
    public function listTypes($allTypes)
    {

        $list[] = ['value' => '', 'display' => ''];
        foreach ($allTypes as $item) {
            $list[] = [
                'value'       => $item->id,
                'display'     => $item->name,
                'data-target-type' => $item->target_type
            ];
        }

        return $list;
    }

    /**
     * @param $invoice
     * @param $targets
     */
    public function syncAssignedTargets($invoice, $targets, $oldTargets = [])
    {

        $deleteTargets = is_array($oldTargets) ? array_diff($oldTargets, $targets) : [];
        $targetType = $invoice->type->target_type;

        if ($targetType == 'Vehicle') {
            $this->syncVehicleTarget($invoice, $targets, $deleteTargets);
        } elseif ($targetType == 'User') {
            $this->syncUserTarget($invoice, $targets, $deleteTargets);
        } elseif ($targetType == 'Shipment') {
            $this->syncShipmentTarget($invoice, $targets, $deleteTargets);
        }
    }

    /**
     * Sync vehicle targets
     *
     * @param $invoice
     * @param $targets
     * @param $deleteTargets
     */
    public function syncVehicleTarget($invoice, $targets, $deleteTargets)
    {

        $deleteTargetsIds = array_keys($deleteTargets);
        $targetIds        = array_keys($targets);
        $vehicles         = Vehicle::filterSource()->whereIn('id', $targetIds)->get();

        //apaga os registos apagados
        foreach ($deleteTargetsIds as $vehicleId) {
            $vehicleCost = Expense::firstOrNew([
                'vehicle_id'          => $vehicleId,
                'assigned_invoice_id' => $invoice->id
            ]);

            $vehicleCost->delete();
        }

        //Apaga todas as despesas associadas a esta fatura
        Cost::where('assigned_invoice_id', $invoice->id)->forceDelete();

        //apaga todas as despesas associadas a mapas de viagem
        TripExpense::where('purchase_invoice_id', $invoice->id)->forceDelete();

        //insere para cada viatura novas despesas
        foreach ($vehicles as $vehicle) {

            $vehicleCost = Expense::firstOrNew([
                'vehicle_id'          => $vehicle->id,
                'assigned_invoice_id' => $invoice->id
            ]);

            $vehicleCost->assigned_invoice_id = $invoice->id;
            $vehicleCost->type_id     = $invoice->type_id ? $invoice->type_id : $invoice->provider->category_id;
            $vehicleCost->vehicle_id  = $vehicle->id;
            $vehicleCost->provider_id = $invoice->provider_id;
            $vehicleCost->title       = @$invoice->lines->first()->description;
            $vehicleCost->date        = $invoice->doc_date;
            $vehicleCost->total       = @$targets[$vehicle->id];
            $vehicleCost->created_by  = Auth::user()->id;
            $vehicleCost->save();


            //imputa o custo a uma viagem
            $trip = Trip::where('vehicle', $vehicle->license_plate)
                ->where(function($q) use($invoice) {
                    $q->where(function($q) use($invoice) {
                        $q->where('start_date', '<=', $invoice->doc_date);
                        $q->whereNull('end_date');
                    })
                    ->orWhere(function($q) use($invoice) {
                        $q->where('start_date', '<=', $invoice->doc_date);
                        $q->where('end_date', '>=', $invoice->doc_date);
                    });
                })
                ->orderBy('start_date', 'desc')
                ->first();

            if($trip) {

                $tripExpense = TripExpense::firstOrNew([
                    'purchase_invoice_id' => $invoice->id
                ]); 
    
                $tripExpense->trip_id     = $trip->id;
                $tripExpense->purchase_invoice_id  = $invoice->id;
                $tripExpense->date        = $vehicleCost->date;
                $tripExpense->total       = $vehicleCost->total;
                $tripExpense->description = $vehicleCost->title;
                $tripExpense->type        = 'other';
                $tripExpense->save();
            }
        }

        
    }

    /**
     * Sync user targets
     *
     * @param $invoice
     * @param $targets
     * @param $deleteTargets
     */
    public function syncUserTarget($invoice, $targets, $deleteTargets)
    {

        $deleteTargetsIds = array_keys($deleteTargets);
        $targetIds        = array_keys($targets);
        $users            = User::filterSource()->whereIn('id', $targetIds)->get();

        //apaga os registos apagados
        foreach ($deleteTargetsIds as $userId) {
            $userCost = UserExpense::firstOrNew([
                'user_id'             => $userId,
                'assigned_invoice_id' => $invoice->id,
                'is_fixed'            => 0
            ]);

            $userCost->forceDelete();
        }

        //Apaga todas as despesas associadas a esta fatura
        UserExpense::where('assigned_invoice_id', $invoice->id)->delete();

        //apaga todas as despesas associadas a mapas de viagem
        TripExpense::where('purchase_invoice_id', $invoice->id)->forceDelete();
        
        foreach ($users as $user) {

            $userExpense = UserExpense::firstOrNew([
                'user_id'             => $user->id,
                'assigned_invoice_id' => $invoice->id,
                'is_fixed'            => 0
            ]);

            $userExpense->source      = config('app.source');
            $userExpense->assigned_invoice_id = $invoice->id;
            $userExpense->type_id     = $invoice->type_id;
            $userExpense->user_id     = $user->id;
            $userExpense->provider_id = $invoice->provider_id;
            $userExpense->description = @$invoice->lines->first()->description;
            $userExpense->date        = $invoice->doc_date;
            $userExpense->total       = @$targets[$user->id];
            $userExpense->created_by  = Auth::user()->id;
            $userExpense->is_fixed    = 0;
            $userExpense->start_date  = null;
            $userExpense->end_date    = null;
            $userExpense->save();

            //imputa o custo a uma viagem
            $trip = Trip::where('operator_id', $user->id)
                ->where(function($q) use($invoice) {
                    $q->where(function($q) use($invoice) {
                        $q->where('start_date', '<=', $invoice->doc_date);
                        $q->whereNull('end_date');
                    })
                    ->orWhere(function($q) use($invoice) {
                        $q->where('start_date', '<=', $invoice->doc_date);
                        $q->where('end_date', '>=', $invoice->doc_date);
                    });
                })
                ->orderBy('start_date', 'desc')
                ->first();

            if($trip) {

                $tripExpense = TripExpense::firstOrNew([
                    'purchase_invoice_id' => $invoice->id
                ]); 
    
                $tripExpense->trip_id     = $trip->id;
                $tripExpense->purchase_invoice_id  = $invoice->id;
                $tripExpense->date        = $userExpense->date;
                $tripExpense->total       = $userExpense->total;
                $tripExpense->description = $userExpense->description;
                $tripExpense->type        = 'other';
                $tripExpense->save();
            }
        }
    }

    /**
     * Sync user targets
     *
     * @param $invoice
     * @param $targets
     * @param $deleteTargets
     */
    public function syncShipmentTarget($invoice, $targets, $deleteTargets)
    {

        $deleteTargetsIds = array_keys($deleteTargets);
        $targetIds        = array_keys($targets);

        //desassocia a fatura de todos os envios onde esteja associada
        Shipment::whereIn('id', $deleteTargetsIds)
            ->where('purchase_invoice_id', $invoice->id)
            ->update(['purchase_invoice_id' => null]);


        //associa aos envios a fatura
        Shipment::whereIn('id', $targetIds)
            ->update(['purchase_invoice_id' => $invoice->id]);
    }

      /**
     * Edit initial balance modal
     *
     * @param Request $request
     * @param [type] $invoiceId
     * @return void
     */
    public function editInitialBalance(Request $request)
    {

        $docDate = date('Y').'-01-01';

        $entityType = 'providers';

        $entities = Provider::with('sind_invoice', 'sinc_invoice')
            ->orderBy('code', 'asc')
            ->get([
                'id',
                'code',
                'vat',
                DB::raw('company as name'),
                DB::raw('name as billing_name'),
                DB::raw('country as billing_country')
            ]);

        $data = compact(
            'entities',
            'entityType',
            'docDate'
        );

        return view('admin.invoices.sales.edit_initial_balance', $data)->render();
    }

    /**
     * Store initial balance
     *
     * @param Request $request
     * @param $invoiceId
     * @return mixed
     */
    public function storeInitialBalance(Request $request)
    {

        $input   = $request->all();
        $entity  = $request->get('entity');
        $docDate = $request->get('doc_date');
        $source  = config('app.source');

        $debits  = array_filter($input['sind']);
        $credits = array_filter($input['sinc']);
        $dates   = array_filter($input['doc_date']);

        try {
            if($debits) {
                foreach($debits as $providerId => $total) {

                    $provider = Provider::find($providerId);

                    $purchaseInvoice = PurchaseInvoice::firstOrNew([
                        'doc_type'    => PurchaseInvoice::DOC_TYPE_SIND,
                        'provider_id' => $providerId,
                        'is_deleted'  => 0,
                    ]);

                    if(empty($purchaseInvoice->total_unpaid) && !$purchaseInvoice->is_settle) {

                        $docDate = @$dates[$providerId] ? $dates[$providerId] : $docDate;

                        //cria ou atualiza o saldo inicial a débito
                        $purchaseInvoice->source            = $source;
                        $purchaseInvoice->sort              = '19000000_0'; //força a ficar sempre em primeiro lugar
                        $purchaseInvoice->provider_id       = $providerId;
                        $purchaseInvoice->doc_type          = PurchaseInvoice::DOC_TYPE_SIND;
                        $purchaseInvoice->doc_series        = 'SIND';
                        $purchaseInvoice->reference         = 'SIND';
                        $purchaseInvoice->code              = 'SIND';
                        $purchaseInvoice->doc_date          = $docDate;
                        $purchaseInvoice->due_date          = $docDate;
                        $purchaseInvoice->subtotal          = 0;
                        $purchaseInvoice->vat               = 0;
                        $purchaseInvoice->total             = $total;
                        $purchaseInvoice->total_unpaid      = $total;
                        $purchaseInvoice->sense             = 'debit';
                        $purchaseInvoice->vat               = $provider->vat;
                        $purchaseInvoice->billing_code      = $provider->code;
                        $purchaseInvoice->billing_name      = $provider->billing_name;
                        $purchaseInvoice->billing_address   = $provider->billing_address;
                        $purchaseInvoice->billing_zip_code  = $provider->billing_zip_code;
                        $purchaseInvoice->billing_city      = $provider->billing_city;
                        $purchaseInvoice->billing_country   = $provider->billing_country;
                        $purchaseInvoice->is_draft          = 0;
                        $purchaseInvoice->save();
                    }
                }
            }
            

            if($credits) {
                foreach($credits as $providerId => $total) {

                    if($total > 0.00) {
                        $total = $total * -1;
                    }
                   
                    $provider = Provider::find($providerId);

                    $purchaseInvoice = PurchaseInvoice::firstOrNew([
                        'doc_type'    => PurchaseInvoice::DOC_TYPE_SINC,
                        'provider_id' => $providerId,
                        'is_deleted'  => 0,
                    ]);

                    if(empty($purchaseInvoice->total_unpaid) && !$purchaseInvoice->is_settle) {

                        $docDate = @$dates[$providerId] ? $dates[$providerId] : $docDate;

                        //cria ou atualiza o saldo inicial a crédito
                        $purchaseInvoice->source            = $source;
                        $purchaseInvoice->sort              = '19000000_0'; //força a ficar sempre em primeiro lugar
                        $purchaseInvoice->provider_id       = $providerId;
                        $purchaseInvoice->doc_type          = PurchaseInvoice::DOC_TYPE_SINC;
                        $purchaseInvoice->doc_series        = 'SINC';
                        $purchaseInvoice->reference         = 'SINC';
                        $purchaseInvoice->code              = 'SINC';
                        $purchaseInvoice->doc_date          = $docDate;
                        $purchaseInvoice->due_date          = $docDate;
                        $purchaseInvoice->subtotal          = 0;
                        $purchaseInvoice->vat               = 0;
                        $purchaseInvoice->total             = $total;
                        $purchaseInvoice->total_unpaid      = $total;
                        $purchaseInvoice->sense             = 'credit';
                        $purchaseInvoice->vat               = $provider->vat;
                        $purchaseInvoice->billing_code      = $provider->code;
                        $purchaseInvoice->billing_name      = $provider->billing_name;
                        $purchaseInvoice->billing_address   = $provider->billing_address;
                        $purchaseInvoice->billing_zip_code  = $provider->billing_zip_code;
                        $purchaseInvoice->billing_city      = $provider->billing_city;
                        $purchaseInvoice->billing_country   = $provider->billing_country;
                        $purchaseInvoice->is_draft          = 0;
                        $purchaseInvoice->save();
                    }
                }
            }

        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }

        return Redirect::back()->with('success', 'Saldos gravados com sucesso.');
    }
}
