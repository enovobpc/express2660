<?php

namespace App\Http\Controllers\Admin\Invoices;

use App\Models\Agency;
use App\Models\Bank;
use App\Models\Billing\ApiKey;
use App\Models\Billing\Item;
use App\Models\Billing\ItemStockHistory;
use App\Models\Company;
use App\Models\Customer;
use App\Models\CustomerBalance;
use App\Models\CustomerType;
use App\Models\InvoiceDivergence;
use App\Models\InvoiceGateway\Base;
use App\Models\InvoiceGateway\KeyInvoice\Document;
use App\Models\InvoiceSchedule;
use App\Models\PaymentCondition;
use App\Models\PaymentMethod;
use App\Models\Route;
use App\Models\Saft;
use App\Models\User;
use App\Models\CustomerBilling;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\Provider;
use App\Models\Shipment;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Jenssegers\Date\Date;
use Yajra\Datatables\Facades\Datatables;
use Auth, Response, Setting, Mail, DB, File;

class SalesController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'invoices';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',invoices|billing|billing_providers|billing_agencies']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        validateModule('invoices');

        $years  = yearsArr(2016, date('Y'), true);
        $months = array_reverse(trans('datetime.list-month'), true);

        //força qualquer registo que esteja com valor pendente 0.00 a ficar como pago
        Invoice::where('doc_total_pending', '0.00')
            ->where('is_settle', 0)
            ->update(['is_settle' => 1]);

        $operators = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->isActive()
            ->isOperator(false)
            ->orderBy('source', 'asc')
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $series = Invoice::groupBy('doc_series')
            ->where('doc_series_id', '<>', '')
            ->pluck('doc_series', 'doc_series_id')
            ->toArray();

        $routes = Route::listsWithCode(Route::remember(config('cache.query_ttl'))
            ->cacheTags(Route::CACHE_TAG)
            ->filterSource()
            ->ordered()
            ->get());

        $sellers = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->isActive()
            ->isOperator(false)
            ->orderBy('source', 'asc')
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $paymentConditions = PaymentCondition::filterSource()
            ->isSalesVisible()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $paymentMethods = PaymentMethod::filterSource()
            ->isActive()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $customerTypes = CustomerType::remember(config('cache.query_ttl'))
            ->cacheTags(CustomerType::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();


        $agencies = Auth::user()->listsAgencies(false);

        $recipientCounties = [];
        $recipientDistrict = $request->get('fltr_recipient_district');
        if ($request->has('fltr_recipient_district')) {
            $recipientCounties = trans('districts_codes.counties.pt.' . $recipientDistrict);
        }

        $data = compact(
            'years',
            'months',
            'operators',
            'series',
            'agencies',
            'routes',
            'sellers',
            'customerTypes',
            'paymentConditions',
            'paymentMethods',
            'recipientDistrict',
            'recipientCounties'
        );

        return $this->setContent('admin.invoices.sales.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        validateModule('invoices');

        $action = 'Emitir fatura';

        $customerId = $request->get('customer');
        $customer   = Customer::findOrNew($customerId);
        $newCustomerCode = $customer->setCode(false);

        $schedule = null;
        if ($request->get('scheduled', false)) {
            $action = 'Criar Fatura Programada';
            $schedule = new InvoiceSchedule();
            $schedule->repeat_every = 1;
            $schedule->repeat       = 'day';
            $schedule->frequency    = 'month';
        }


        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->whereSource(config('app.source'))
            ->filterAgencies()
            ->orderBy('code', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $billing = new CustomerBilling();
        $billing->billing_type      = 'invoice';
        $billing->payment_method    = '';
        $billing->payment_condition = '30d';

        $docDate = date('Y-m-d');
        /*$mostRecentDate = Invoice::getLastDocDate();
        if($mostRecentDate > $docDate) {
            $docDate = $mostRecentDate;
        }*/

        $docLimitDate = new Carbon('last day of next month');
        $docLimitDate = $docLimitDate->addDays(30)->format('Y-m-d');

        $apiKeys  = Invoice::getApiKeys();
        $vatTaxes = Invoice::getVatTaxes();

        $paymentConditions = PaymentCondition::filterSource()
            ->isSalesVisible()
            ->where('code', '<>', 'sft')
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $paymentMethods = PaymentMethod::filterSource()
            ->isActive()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $route = route('admin.invoices.store', [
            'customer'  => @$customer->id,
            'target'    => Invoice::TARGET_INVOICE,
        ]);

        $formOptions = ['url' => $route, 'method' => 'POST', 'class' => 'form-billing'];

        $billingMonth = false;

        $appCountry = Setting::get('app_country');

        $customerCategories = CustomerType::remember(config('cache.query_ttl'))
            ->cacheTags(CustomerType::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'billing',
            'customer',
            'agencies',
            'docDate',
            'docLimitDate',
            'apiKeys',
            'vatTaxes',
            'action',
            'formOptions',
            'billingMonth',
            'newCustomerCode',
            'paymentConditions',
            'paymentMethods',
            'schedule',
            'appCountry',
            'customerCategories'
        );

        return view('admin.invoices.sales.edit', $data)->render();
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

        $customerId = $request->get('customer');
        $invoiceId  = $request->get('invoice-id');
        $customer   = Customer::findOrNew($customerId);
        $newCustomerCode = $customer->setCode(false);

        //edita todos dados fatura
        if ($request->action == 'admin') {
            $invoice = Invoice::filterSource()->findOrFail($id);
            return view('admin.invoices.sales.modals.admin_edit', compact('invoice'))->render();
        }

        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->whereSource(config('app.source'))
            ->filterAgencies()
            ->orderBy('code', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        if ($id != '0') {
            $invoice = Invoice::filterSource()
                ->where(function ($q) {
                    $q->where('is_draft', 1);
                    $q->orWhere('is_scheduled', 1);
                })
                ->findOrFail($id);
        } else {
            $invoice = Invoice::filterSource()
                ->where('is_draft', 1)
                ->where('customer_id', $customer->id)
                ->where('doc_id', $invoiceId)
                ->first();
        }

        if ($invoice->doc_type == 'receipt') {
            return $this->editReceipt($request, $id);
        } elseif ($invoice->doc_type == 'regularization') {
            return $this->editRegularization($request, $id);
        }

        $customer->id               = $invoice->customer_id;
        $customer->vat              = $invoice->vat;
        $customer->code             = $invoice->billing_code;
        $customer->billing_name     = $invoice->billing_name;
        $customer->billing_address  = $invoice->billing_address;
        $customer->billing_zip_code = $invoice->billing_zip_code;
        $customer->billing_city     = $invoice->billing_city;
        $customer->billing_country  = $invoice->billing_country;
        $customer->billing_email    = $invoice->billing_email;
        $customer->agency_id        = @$invoice->customer->agency_id;

        if ($invoice->target == Invoice::TARGET_CUSTOMER_BILLING && !empty($invoice->target_id)) {
            $billing = CustomerBilling::findOrNew($invoice->target_id);
        } else {
            $billing = new CustomerBilling();
            $billing->billing_type = 'invoice';
        }

        $billing->total_month        = $invoice->total;
        $billing->total_month_vat    = $invoice->total_vat;
        $billing->total_month_no_vat = $invoice->total_no_vat;
        $billing->invoice_type       = $invoice->doc_type;
        $billing->is_draft           = $invoice->is_draft;
        $billing->fuel_tax           = $invoice->fuel_tax;
        $billing->irs_tax            = $invoice->irs_tax;
        $billing->total_discount     = $invoice->total_discount;
        $billing->reference          = $invoice->reference;
        $billing->payment_method     = $invoice->payment_method;
        $billing->payment_condition  = $invoice->payment_condition;
        $billing->api_key            = $invoice->api_key;
        $billing->lines              = $invoice->lines;

        $docVat = 0;
        foreach ($billing->lines as $line) {
            $docVat += $line->subtotal * ($line->tax_rate / 100);
        }
        $billing->document_subtotal  = $invoice->doc_subtotal;
        $billing->document_vat       = $invoice->doc_vat;
        $billing->document_total     = $invoice->doc_total;

        if ($invoice->exists) {
            $billing->obs = $invoice->obs;
        }

        $docDate      = $invoice->doc_date;
        $docLimitDate = $invoice->due_date;

        $apiKeys  = Invoice::getApiKeys();
        $vatTaxes = Invoice::getVatTaxes();

        $paymentConditions = PaymentCondition::filterSource()
            ->isSalesVisible()
            ->where('code', '<>', 'sft')
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $paymentMethods = PaymentMethod::filterSource()
            ->isActive()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $route = route('admin.invoices.update', [
            $id,
            'customer'  => @$customer->id,
            'target'    => $invoice->target,
        ]);

        $formOptions = ['url' => $route, 'method' => 'PUT', 'class' => 'form-billing'];

        $action = 'Emitir fatura';

        $billingMonth = false;

        $appCountry = Setting::get('app_country');

        $customerCategories = CustomerType::remember(config('cache.query_ttl'))
            ->cacheTags(CustomerType::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $schedule = null;
        if ($request->get('schedule', false)) {
            $action = 'Criar Fatura Programada';
            $schedule = InvoiceSchedule::firstOrNew([
                'invoice_id' => $invoice->id
            ]);

            if (!$schedule->exists) {
                $schedule->repeat_every = 1;
                $schedule->repeat       = 1;
                $schedule->frequency    = 'week';
            }
        }


        $data = compact(
            'billing',
            'agencies',
            'customer',
            'docDate',
            'docLimitDate',
            'apiKeys',
            'vatTaxes',
            'action',
            'formOptions',
            'billingMonth',
            'newCustomerCode',
            'paymentConditions',
            'paymentMethods',
            'schedule',
            'appCountry',
            'customerCategories'
        );


        return view('admin.invoices.sales.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id = null)
    {

        if ($request->action == 'admin') {
            return $this->updateAdmin($request, $id);
        }

        $input = $request->all();
        $input['month']          = empty($input['month']) ? date('n') : $input['month'];
        $input['year']           = empty($input['year']) ? date('Y') : $input['year'];
        $input['period']         = empty($input['period']) ? '30d' : $input['period'];
        $input['billed']         = $request->get('billed', false);
        $input['draft']          = $request->get('draft', false);
        $input['final_consumer'] = $request->get('final_consumer', false);
        $input['send_email']     = $request->get('send_email', false);
        $input['ref_mb']         = $request->get('ref_mb', false);
        $input['doc_type']       = hasModule('invoices') ? $input['doc_type'] : Invoice::DOC_TYPE_NODOC;
        $isSchedule              = $request->get('schedule_frequency') && $request->get('schedule_repeat_every');
        $submitInvoice           = Invoice::canSubmitWebservice($request->get('doc_type'));
        $mbRef                   = null;


        //VALIDA MODULOS FATURAÇÃO AVANÇADOS
        if ((!hasModule('invoices-advanced') && in_array($request->get('doc_type'), [Invoice::DOC_TYPE_INTERNAL_DOC, Invoice::DOC_TYPE_FP])) && config('app.source') != 'transcapital') {
            return response()->json([
                'result'   => false,
                'feedback' => 'O tipo de documento escolhido não está disponível na sua licença.'
            ]);
        }

        //VALIDA TARGET DO DOCUMENTO
        if (empty($input['target'])) {
            return response()->json([
                'result'   => false,
                'feedback' => 'Não é possível gerar a fatura: Target em falta.'
            ]);
        }

        //VALIDA SÉRIE DA FATURA
        if (0) {
            return response()->json([
                'result'   => false,
                'feedback' => 'Série inválida ou inativa.'
            ]);
        }

        //VALIDA DATA DA FATURA
        if (0) {
            return response()->json([
                'result'   => false,
                'feedback' => 'Data inválida. Existe um documento emitido com data superior à deste documento.'
            ]);
        }

        //VALIDA TIPOS DE DOCUMENTO
        if ($input['doc_type'] == Invoice::DOC_TYPE_GT) {
            return $this->storeTransportGuide($request, $id);
        } elseif (in_array($input['doc_type'], [Invoice::DOC_TYPE_FR, Invoice::DOC_TYPE_FS])) {
            $input['payment_date']      = $input['docdate']; //força data de pagamento = data documento
            $input['duedate']           = $input['docdate']; //força data de vencimento = data documento
            $input['payment_condition'] = 'prt'; //força a pagamento
        } elseif (!in_array($input['doc_type'], [Invoice::DOC_TYPE_FR, Invoice::DOC_TYPE_FS])) {
            $input['payment_date']  = null; //força todos os outros documentos a terem data pagamento vazia
        }

        //VALIDA DATA VENCIMENTO
        if (empty(@$input['duedate'])) {
            $paymentCondition = $input['payment_condition'];
            $dueDays = PaymentCondition::getDays($paymentCondition); //obtem dias de pagamento pela base de dados
            $dt = new Date($input['docdate']);
            $input['duedate'] = $dt->addDays($dueDays)->format('Y-m-d');
        }

        //VALIDA CLIENTE
        if (empty($input['customer_id']) && (empty($input['vat']) || $input['vat'] == '999999990') && empty($input['code'])) {
            //faturação como consumidor final (nao grava ficha nem dados do cliente em sistema)
            $customer = \App\Models\Customer::filterSource()->where('code', 'CFINAL')->first();
        } else {
            if (!empty($input['customer_id'])) { //novo cliente
                $customer = \App\Models\Customer::findOrNew($input['customer_id']);
            } else { //obtem cliente através do NIF
                $customer = \App\Models\Customer::firstOrNew(['vat' => $input['vat']]);
            }

            $customerExists = $customer->exists;
            if ($customerExists) {
                //atualiza temporariamente os dados de faturação por defeito para depois os submeter
                $customer->vat       = $input['vat'];
                $customer->code      = $input['billing_code'];
                $customer->name      = $input['billing_name'];
                $customer->address   = $input['billing_address'];
                $customer->zip_code  = $input['billing_zip_code'];
                $customer->city      = $input['billing_city'];
                $customer->country   = $input['billing_country'];
                $customer->billing_country = $input['billing_country'];
                $customer->contact_email   = @$input['billing_email'];
            } else {
                //cria novo cliente
                $customer->agency_id = @$input['agency_id'];
                $customer->vat       = $input['vat'];
                $customer->code      = $input['billing_code'];
                $customer->name      = $input['billing_name'];
                $customer->address   = $input['billing_address'];
                $customer->zip_code  = $input['billing_zip_code'];
                $customer->city      = $input['billing_city'];
                $customer->country   = $input['billing_country'];
                $customer->contact_email = @$input['billing_email'];
                $customer->setCode();
            }
        }

        $customer->final_consumer = false;
        if ($input['final_consumer'] || empty($input['vat']) || $input['vat'] == '999999990') {
            $customer->final_consumer = true;
        }

        try {
            $invoice = Invoice::findOrNew($id);
            $invoice->fill($input);
            $invoice->source       = config('app.source');
            $invoice->customer_id  = $customer->id;
            $invoice->target       = $input['target'];
            $invoice->doc_date     = $input['docdate'];
            $invoice->due_date     = $input['duedate'];
            $invoice->reference    = $input['docref'];
            //$invoice->total        = $input['total_month'];
            //$invoice->total_vat    = $input['total_month_vat'];
            //$invoice->total_no_vat = $input['total_month_no_vat'];
            $invoice->save();

            //apaga rascuho anterior no sistema de faturação caso seja um rascunho
            if ($submitInvoice && (!$isSchedule && $invoice->is_draft && $invoice->exists && $invoice->doc_id)) {
                $class = Base::getNamespaceTo('Document');
                $doc   = new $class($invoice->api_key);
                $doc->deleteDraft($invoice->doc_id, $invoice->doc_type);
            }

            ItemStockHistory::deleteBySaleInvoiceId($invoice->id);
            //apaga linhas da fatura anteriormente gravadas e grava as novas linhas
            InvoiceLine::where('invoice_id', $invoice->id)->forceDelete();
            $docTotals = InvoiceLine::storeLines($invoice->id, $input['line'], $customer);

            //preenche observacoes da fatura dinamicamente
            if (!empty($invoice->obs)) {
                $invoice->doc_subtotal = $docTotals['subtotal'];
                $invoice->obs = Invoice::prefillObs($invoice->obs, $invoice);
                $input['obs'] = $invoice->obs;
                unset($invoice->doc_subtotal);
            }

            if (!$isSchedule) {
                if (hasModule('invoices') && $submitInvoice) {

                    //SUBMIT KEYINVOICE
                    if (Invoice::getInvoiceSoftware() == Invoice::SOFTWARE_KEYINVOICE) {
                        $header = $invoice->prepareDocumentHeader($input, $customer);
                        $lines  = $invoice->prepareDocumentLines();

                        $invoice->insertOrUpdateCustomer($customer);

                        $documentId = $invoice->createDraft($input['doc_type'], $header, $lines);

                        if (!$input['draft']) {
                            $invoiceDocId = $invoice->convertDraftToDoc($documentId, $input['doc_type']);

                            if ($input['ref_mb']) {
                                $mbRef = $invoice->addPaymentMb($invoiceDocId, $input['doc_type']);
                            }
                        }
                    }

                    //SUBMIT SAGE X3
                    elseif (Invoice::getInvoiceSoftware() == Invoice::SOFTWARE_SAGEX3) {
                        $invoiceDocId = $invoice->storeSageX3($input, $customer);
                        $invoice->doc_id        = $invoiceDocId;
                        $invoice->is_draft      = 0;
                    }
                } else {
                    //GRAVA FATURA ENOVO TMS
                    $documentId             = $invoice->setDocumentNo();
                    $invoice->doc_id        = @$documentId['doc_id'];
                    $invoice->doc_series    = @$documentId['doc_serie'];
                    $invoice->doc_series_id = @$documentId['doc_serie_id'];
                    $invoice->internal_code = @$documentId['internal_code'];
                    $invoice->is_draft      = $input['draft'];
                    $invoice->api_key       = null;

                    if ($input['ref_mb']) {
                        $mbRef = []; //criar método proprio nosso para gerar uma ref. multibanco
                    }
                }
            }

            $invoice->is_draft      = $isSchedule ? false : $invoice->is_draft;
            $invoice->doc_subtotal  = number($docTotals['subtotal']);
            $invoice->doc_vat       = number($docTotals['vat']);
            $invoice->doc_total     = number($docTotals['total']);
            $invoice->is_settle     = in_array($invoice->doc_type, [Invoice::DOC_TYPE_FR, Invoice::DOC_TYPE_FS]) ? 1 : 0;

            if (isset($mbRef['mb_reference'])) {
                $invoice->mb_reference = str_replace(' ', '', $mbRef['mb_reference']);
                $invoice->mb_entity    = $mbRef['mb_entity'];
            }

            //APLICA DESCONTO TOTAL DO DOCUMENTO
            if (!empty($invoice->total_discount) && $invoice->total_discount > 0.00) {
                $invoice->doc_subtotal = number(($invoice->doc_subtotal - number($invoice->doc_subtotal * ($invoice->total_discount / 100))));
                $invoice->doc_vat      = number(($invoice->doc_vat - number($invoice->doc_vat * ($invoice->total_discount / 100))));
                $invoice->doc_total    = $invoice->doc_subtotal + $invoice->doc_vat;
            }

            //APLICA TAXA DE IRS
            if (!empty($invoice->irs_tax) && $invoice->irs_tax > 0.00) {
                $invoice->doc_total = number(($invoice->doc_subtotal - number($invoice->doc_subtotal * ($invoice->irs_tax / 100))) + $invoice->doc_vat);
            }

            $invoice->save();


            //verifica se existe algum proforma associado à fatura
            $proformaInvoice = Invoice::where('doc_type', Invoice::DOC_TYPE_FP)
                ->where('assigned_invoice_id', $invoice->id)
                ->first();

            $alreadyExistsMonthlyBilling = false;
            if ($proformaInvoice) {

                //atualiza o pagamento do proforma para pago
                $proformaInvoice->update([
                    'is_settle'      => true,
                    'payment_method' => $invoice->payment_method,
                    'payment_date'   => $invoice->payment_date
                ]);

                //verifica se existem faturas mensais associadas ao proforma.
                //Se existirem, troca o proforma pela fatura associada.
                $monthlyBilling = CustomerBilling::where('invoice_id', $proformaInvoice->id)->first();

                if ($monthlyBilling) {
                    $alreadyExistsMonthlyBilling = true;

                    $monthlyBilling->invoice_id     = $invoice->id;
                    $monthlyBilling->invoice_doc_id = $invoice->doc_id;
                    $monthlyBilling->invoice_type   = $invoice->doc_type;
                    $monthlyBilling->invoice_draft  = $invoice->is_draft;
                    $monthlyBilling->api_key        = $invoice->api_key;
                    $monthlyBilling->save();

                    $updateArr = [
                        'invoice_id'     => $invoice->id,
                        'invoice_doc_id' => $invoice->doc_id,
                        'invoice_type'   => $invoice->doc_type,
                        'invoice_draft'  => $invoice->is_draft,
                        'invoice_key'    => $invoice->api_key,
                    ];

                    if (Setting::get('shipments_status_after_billing')) {
                        $updateArr['status_id'] = Setting::get('shipments_status_after_billing');
                    }

                    Shipment::whereIn('id', $monthlyBilling->shipments)
                        ->update($updateArr);
                }
            }

            //Assign records to target
            $html = null;
            if (!$isSchedule && $invoice->target == Invoice::TARGET_CUSTOMER_BILLING && !$alreadyExistsMonthlyBilling) {
                $html = $this->storeCustomerBilling($invoice, $customer, $input);
            }

            //SCHEDULE SHIPMENT
            if ($isSchedule) {
                $schedule = InvoiceSchedule::firstOrNew(['invoice_id' => $invoice->id]);
                $schedule->source           = config('app.source');
                $schedule->invoice_id       = $invoice->id;
                $schedule->repeat_every     = $request->get('schedule_repeat_every');
                $schedule->frequency        = $request->get('schedule_frequency');
                $schedule->repeat           = $request->get('schedule_repeat');
                $schedule->month_days       = $request->get('schedule_month_days', ["1"]);
                $schedule->year_days        = $request->get('schedule_year_days');
                $schedule->weekdays         = $request->get('schedule_weekdays');
                $schedule->start_date       = $request->get('docdate');
                $schedule->end_date         = $request->get('schedule_end_date');
                $schedule->end_repetitions  = $request->get('schedule_end_repetitions');
                $schedule->send_email       = $request->get('schedule_email', false);
                $schedule->is_draft         = $request->get('schedule_draft', false);
                $schedule->mb_active        = $request->get('mb_active', false);
                $schedule->mbw_active       = $request->get('mbw_active', false);
                $schedule->paypal_active    = $request->get('paypal_active', false);
                $schedule->last_schedule    = null; //previne que caso mudem a frequencia, deixe de ser emitida a fatura.
                $schedule->save();

                $invoice->is_scheduled = 1;
                $invoice->save();
            }

            $result = [
                'result'     => true,
                'feedback'   => 'Fatura emitida com sucesso.',
                'printPdf'   => (!$invoice->is_draft && $invoice->doc_type != 'nodoc' && Setting::get('invoices_autoprint')) ? route('admin.invoices.download.pdf', @$invoice->id) : '',
                'invoice_id' => $invoice->id,
                'invoice_doc_id' => $invoice->doc_id,

            ];

            //comentado 22/09/2023 para desativar a atualização automática de conta corrente keyinvoice
            /* if (hasModule('customers_balance') && !$invoice->is_draft) {
                $result['balanceUpdate'] = route('admin.billing.balance.sync.all', $invoice->customer_id);
                $result['feedback']      = 'Fatura emitida com sucesso. Atualização de conta corrente em execução.';
            } */

            if ($html) {
                $result['html_header']  = $html['header'];
                $result['html_sidebar'] = $html['sidebar'];
            }
        } catch (\Exception $e) {
            if (!$isSchedule) {
                ItemStockHistory::deleteBySaleInvoiceId($invoice->id);
                InvoiceLine::where('invoice_id', $invoice->id)->forceDelete();
                Invoice::whereId($invoice->id)->forceDelete();
            }

            $result = [
                'result'   => false,
                'feedback' => $e->getMessage() //. (Auth::user()->isAdmin() ? ' na linha ' . $e->getLine() . ' ficheiro ' . $e->getFile() : '')
            ];
        }

        try {

            //envia documento(s) por e-mail
            if (!$isSchedule && !$invoice->is_draft && $input['send_email'] && !empty($input['billing_email']) && !empty($input['attachments']) && $result['result']) {

                $emailResult = $invoice->sendEmail([
                    'email'       => trim(@$input['billing_email']),
                    'attachments' => @$input['attachments'],
                ]);

                if (!$emailResult) {
                    $result['feedback'] = 'Fatura emitida. Não foi possível enviar o e-mail ao cliente.';
                }
            }
        } catch (\Exception $e) {

            if (isset($result)) {
                $result['result']   = true;
                $result['feedback'] = $e->getMessage() . (Auth::user()->isAdmin() ? ' na linha ' . $e->getLine() . ' ficheiro ' . $e->getFile() : '');
            } else {
                $result = [
                    'result'   => true,
                    'feedback' => $e->getMessage() . (Auth::user()->isAdmin() ? ' na linha ' . $e->getLine() . ' ficheiro ' . $e->getFile() : '')
                ];
            }
        }

        return response()->json($result);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAdmin(Request $request, $id = null)
    {
        $input = $request->all();

        $invoice = Invoice::findOrFail($id);
        $invoice->fill($input);
        $invoice->save();

        return redirect()->back()->with('success', 'Atualização realizada.');
    }

    /**
     * Crete receipt
     *
     * @param Request $request
     * @param $id
     * @return string
     * @throws \Throwable
     */
    public function createReceipt(Request $request)
    {

        $action = 'Emitir recibo';

        $formOptions = ['url' => route('admin.invoices.receipt.store'), 'method' => 'POST', 'class' => 'form-billing'];

        $customerId       = null;
        $customersIds     = [];
        $customersSameVat = new Customer();
        $serie = $request->get('serie');
        $docId = $request->get('doc');
        $ids   = $request->get('id');

        if ($request->get('customer')) {
            $customerId   = $request->get('customer');
            $customersIds = [$customerId];
        }

        if (!empty($ids) && empty($customerId)) {
            $invoices = Invoice::filterSource()
                ->whereIn('id', $ids)
                ->get(['customer_id', 'vat']);

            $uniqueVats      = count($invoices->groupBy('vat')->toArray());
            $uniqueCustomers = array_keys($invoices->groupBy('customer_id')->toArray());

            if ($uniqueVats > 1) {
                $blocked = true;
                return view('admin.invoices.sales.edit_receipt', compact('blocked', 'formOptions', 'action'))->render();
            } else {
                $customerId   = @$invoices->first()->customer_id;
                $customersIds = [$customerId];
            }

            $customersSameVat = null;
            if ($uniqueCustomers > 2) {
                $customersIds     = $uniqueCustomers;
                $customersSameVat = Customer::filterSource()->whereIn('id', $uniqueCustomers)->get(['id', 'vat', 'name', 'code', 'city']);
            }
        }


        $customer = Customer::filterSource()
            ->whereIn('id', @$customersIds)
            ->first();

        $customersList = [@$customer->id => @$customer->code . ' - ' . @$customer->billing_name];
        if (count($customersSameVat->toArray()) > 1) {
            $customersList = ['' => ''];
            foreach ($customersSameVat as $row) {
                $customersList[@$row->id] = @$row->code . ' - ' . @$row->billing_name . ' (' . @$row->city . ')';
            }
        }

        if (!empty($serie) && !empty($docId)) { //chamadas a partir do menu de conta corrente

            $invoices = Invoice::filterSource()
                ->whereIn('customer_id', $customersIds)
                ->whereIn('doc_type', ['invoice', 'credit-note', 'debit-note'])
                ->where('doc_id', $docId)
                ->where('doc_series_id', $serie)
                ->where('is_deleted', 0)
                ->get();
        } else { //chamadas a partir do menu de faturas

            $invoices = Invoice::filterSource()
                ->whereIn('customer_id', $customersIds)
                ->whereIn('doc_type', ['invoice', 'credit-note', 'debit-note'])
                ->where(function ($q) {
                    $q->where(function ($q) {
                        $q->whereIn('doc_type', ['invoice', 'debit-note'])
                            ->where('is_settle', 0);
                    });
                    $q->orWhere(function ($q) {
                        $q->where('doc_type', 'credit-note');
                        $q->where('doc_date', '>', '2020-09-01'); //só começa a funcionar apos dia 1 janeiro 2021
                        $q->where('is_settle', 0);
                        $q->where(function ($q) {
                            $q->whereNull('doc_total_pending');
                            $q->orWhereRaw('doc_total_pending < doc_total');
                        });
                    });
                })
                ->where('is_deleted', 0)
                ->where('is_draft', 0)
                ->orderBy('due_date', 'asc');

            if (!empty($ids)) {
                $invoices = $invoices->whereIn('id', $ids);
            }

            $invoices = $invoices->orderBy('due_date', 'desc')
                ->get();
        }

        $paymentMethods = PaymentMethod::filterSource()
            ->isActive()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $banks    = Bank::listBanks();

        $apiKeys  = Invoice::getApiKeys();

        $currency = Setting::get('app_currency');

        $customersSameVat = null;

        $data = compact(
            'apiKeys',
            'action',
            'formOptions',
            'customer',
            'invoices',
            'customersList',
            'customersSameVat',
            'paymentMethods',
            'banks',
            'currency'
        );

        return view('admin.invoices.sales.edit_receipt', $data)->render();
    }


    /**
     * Edit receipt
     *
     * @param Request $request
     * @param $id
     * @return string
     * @throws \Throwable
     */
    public function editReceipt(Request $request, $receiptId)
    {

        $receipt = Invoice::where('doc_type', 'receipt')
            ->whereId($receiptId)
            ->firstOrFail();

        $customer = $receipt->customer;

        $invoicesValues = $receipt->lines->pluck('total_price', 'assigned_invoice_id')->toArray();

        $invoices = Invoice::filterSource()
            ->whereIn('id', array_keys($invoicesValues))
            ->whereIn('doc_type', ['invoice', 'credit-note', 'debit-note'])
            ->where('is_deleted', 0)
            ->orderBy('due_date', 'asc')
            ->get();

        $paymentMethods = PaymentMethod::filterSource()
            ->isActive()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $banks    = Bank::listBanks();

        $apiKeys  = Invoice::getApiKeys();

        $currency = Setting::get('app_currency');

        $action = 'Editar recibo';

        $formOptions = ['url' => route('admin.invoices.receipt.update', [$receiptId]), 'method' => 'PUT', 'class' => 'form-billing'];

        $data = compact(
            'apiKeys',
            'action',
            'formOptions',
            'customer',
            'invoices',
            'receipt',
            'invoicesValues',
            'paymentMethods',
            'banks',
            'currency'
        );

        return view('admin.invoices.sales.edit_receipt', $data)->render();
    }

    /**
     * Store receipt
     *
     * @param Request $request
     * @param $id
     * @return string
     * @throws \Throwable
     */
    public function storeReceipt(Request $request, $id = null)
    {
        return $this->updateReceipt($request, null);
    }

    /**
     * Update receipt
     *
     * @param Request $request
     * @param $id
     * @return string
     * @throws \Throwable
     */
    public function updateReceipt(Request $request, $id = null, $returnResult = false)
    {

        $input               = $request->all();
        $input['docdate']    = $request->get('docdate', date('Y-m-d'));
        $input['draft']      = $request->get('draft', false);
        $input['send_email'] = $request->get('send_email', false);
        $customerId          = $request->get('customer_id');
        $invoicesTotals      = array_filter($input['invoices']);
        $email               = $request->get('billing_email');
        $sendEmail           = $request->get('send_email', false);
        $submitInvoice       = Setting::get('invoice_software') == 'EnovoTms' ? false : true;

        try {
            $customer = Customer::filterSource()
                ->whereId($customerId)
                ->first();

            if ($customer->code == 'CFINAL' || $customer->vat == '999999999' || $customer->vat == '999999990' || $customer->vat == '') {
                $invoices = Invoice::filterSource()
                    ->where(function ($q) {
                        $q->whereNull('vat');
                        $q->orWhere('vat', '');
                        $q->orWhere('vat', '999999999');
                        $q->orWhere('vat', '999999990');
                    })
                    ->whereIn('id', array_keys($invoicesTotals))
                    ->get();
            } else {
                $invoices = Invoice::filterSource()
                    ->whereIn('id', array_keys($invoicesTotals));

                if (empty($customer->vat)) { //se o cliente tem o NIF vazio temos forçadamente de pesquisar as faturas pelo ID de cliente
                    $invoices = $invoices->where('customer_id', $customer->id);
                } else {
                    $invoices = $invoices->where('vat', $customer->vat); //para que possa contabilizar qualquer fatura do NIF e não apenas do cliente.
                }

                $invoices = $invoices->get();
            }

            $invoicesCustumersIds = $invoices->pluck('customer_id')->toArray();
            $invoicesCustumersIds = array_unique($invoicesCustumersIds);

            $input['vat']              = $customer->code == 'CFINAL' ? '' : $customer->vat;
            $input['billing_code']     = $customer->code;
            $input['billing_name']     = $customer->billing_name;
            $input['billing_address']  = $customer->billing_address;
            $input['billing_zip_code'] = $customer->billing_zip_code;
            $input['billing_city']     = $customer->billing_city;
            $input['billing_phone']    = $customer->billing_phone;
            $input['billing_email']    = $customer->billing_email;
            $input['billing_country']  = $customer->billing_country;

            $receipt = Invoice::findOrNew($id);
            $receipt->fill($input);
            $receipt->source       = config('app.source');
            $receipt->customer_id  = $customer->id;
            $receipt->doc_type     = 'receipt';
            $receipt->doc_date     = @$input['docdate'];
            $receipt->due_date     = @$input['docdate'];
            $receipt->reference    = @$input['docref'];
            $receipt->obs          = @$input['obs'];
            $receipt->api_key      = @$input['api_key'];
            $receipt->save();

            if ($receipt->is_draft && $receipt->exists && $submitInvoice) { //apaga o rascunho anterior
                $class = Base::getNamespaceTo('Document');
                $doc   = new $class($receipt->api_key);
                //$doc->deleteDraft($receipt->doc_id, $receipt->doc_type);
            }
            
            InvoiceLine::where('invoice_id', $receipt->id)->forceDelete();
            $receiptTotal = InvoiceLine::storeReceiptLines($receipt->id, $invoices, $invoicesTotals, $customer);

            if ($submitInvoice) {
                $header = $receipt->prepareReceiptHeader($input, $customer);
                $lines  = $receipt->prepareReceiptLines();

                $documentId = $receipt->createReceiptDraft($header, $lines);

                if (!$input['draft']) {
                    $receipt->convertDraftToDoc($documentId, 'receipt');
                }
            } else {
                $documentId             = $receipt->setDocumentNo();
                $receipt->doc_id        = @$documentId['doc_id'];
                $receipt->doc_series    = @$documentId['doc_serie'];
                $receipt->doc_series_id = @$documentId['doc_serie_id'];
                $receipt->internal_code = @$documentId['internal_code'];
                $receipt->is_draft      = $input['draft'];
            }

            $receipt->doc_subtotal  = $receiptTotal['subtotal'];
            $receipt->doc_vat       = $receiptTotal['vat'];
            $receipt->doc_total     = $receiptTotal['total'];
            $receipt->is_settle     = 1;
            $receipt->save();

            //update total unpaid
            if (!$input['draft']) {

                //atualiza valores totais pagos de cada docomento incluido na fatura
                foreach ($invoices as $invoice) {
                    $totalPaid = InvoiceLine::whereHas('invoice', function ($q) {
                        $q->whereNull('deleted_at')
                            ->where('is_draft', 0)
                            ->where('is_deleted', 0);
                    })
                        ->where('assigned_invoice_id', $invoice->id)
                        ->sum('total_price');



                    if ($invoice->doc_type == 'credit-note') {

                        $invoice->doc_total = $invoice->doc_total > 0.00 ? $invoice->doc_total * -1 : $invoice->doc_total; //garante que NC tem sempre sinal negativo. 
                        $invoice->doc_total_pending = $totalPaid - $invoice->doc_total;

                        //coloca nota de crédito como paga se for o caso
                        if ($invoice->doc_total_pending == 0.00 || empty($invoice->doc_total_pending)) {
                            $invoice->is_settle = 1;

                            //atualiza antiga conta corrente keyinvoice
                            //apagar de futuro
                            CustomerBalance::where('doc_type', $invoice->doc_type)
                                ->where('doc_serie_id', $invoice->doc_series_id)
                                ->where('doc_id', $invoice->doc_id)
                                //->where('customer_id', $invoice->customer_id)
                                ->update(['is_paid' => 1]);
                        }

                        $invoice->save();
                    } else {
                        $invoice->doc_total_pending = $invoice->doc_total - $totalPaid;

                        //if (Setting::get('invoice_software') == 'EnovoTms' && (empty($invoice->doc_total_pending) || $invoice->doc_total_pending == 0.00)) {
                        if ((empty($invoice->doc_total_pending) || $invoice->doc_total_pending == 0.00)) {
                            $invoice->is_settle = 1; //marca a fatura como paga
                        }

                        $invoice->save();
                    }
                }
            }

            //ATUALIZA CONTA CORRENTE COM RECIBO REPARTIDO
            //se o numero de customer_id é superior a 1, então estamos a liquidar a conta de vários clientes
            if (count($invoicesCustumersIds) > 1 && !$input['draft']) {

                $partNo = 1;
                foreach ($invoices as $invoice) {
                    $balanceRow = [
                        'assigned_receipt' => $receipt->id,
                        'receipt_part'  => $partNo,
                        'customer_id'   => $invoice->customer_id,
                        'total'         => (float) @$invoicesTotals[$invoice->id],
                        'sense'         => 'credit',
                        'doc_type'      => 'receipt',
                        'doc_id'        => $receipt->doc_id,
                        'doc_serie_id'  => $receipt->doc_series_id,
                        'doc_serie'     => $receipt->doc_series,
                        'reference'     => $receipt->reference,
                        'date'          => $receipt->doc_date,
                        'due_date'      => $receipt->due_date,
                        'is_paid'       => 1,
                        'created_at'    => date('Y-m-d H:i:s'),
                    ];

                    CustomerBalance::insert($balanceRow);
                    $partNo++;
                }
            }

            if (Setting::get('invoice_software') != 'EnovoTms') {
                //atualiza conta corrente do cliente (documentos, saldo e faturas pagas)
                /* $result = CustomerBalance::syncBalanceAll($customerId);

                $updateArr = [
                    'balance_total_unpaid'  => @$result['valueUnpaid'],
                    'balance_count_unpaid'  => @$result['countDocsUnpaid'],
                    'balance_count_expired' => @$result['countDocsExpired'],
                    'balance_last_update'   => date('Y-m-d H:i:s'),
                ];

                Customer::where('id', $customerId)->update($updateArr); */
            }

            //envia e-mail
            if ($sendEmail && !$receipt->is_draft) {

                $data = [
                    'email' => $email,
                    'attachments' => ['receipt']
                ];
                $receipt->sendEmail($data);
            }


            $result = [
                'result'   => true,
                'feedback' => 'Recibo emitido com sucesso.',
                'printPdf' => Setting::get('invoices_autoprint') && !$receipt->is_draft ? route('admin.invoices.download.pdf', $receipt->id) : '',
                'doc_id'   => @$receipt->id
            ];
        } catch (\Exception $e) {
            InvoiceLine::where('invoice_id', $receipt->id)->forceDelete();
            Invoice::whereId($receipt->id)->forceDelete();

            $result = [
                'result'   => false,
                'feedback' => $e->getMessage() . (Auth::user()->isAdmin() ? ' na linha ' . $e->getLine() . ' ficheiro ' . $e->getFile() : '')
            ];
        }

        if ($returnResult) {
            return $result;
        }

        return response()->json($result);
    }


    /**
     * Crete regularization
     *
     * @param Request $request
     * @param $id
     * @return string
     * @throws \Throwable
     */
    public function createRegularization(Request $request)
    {

        $action = 'Emitir regularização';

        $formOptions = ['url' => route('admin.invoices.regularization.store'), 'method' => 'POST', 'class' => 'form-billing'];

        $customerId       = null;
        $customersIds     = [];
        $customersSameVat = new Customer();
        $serie = $request->get('serie');
        $docId = $request->get('doc');
        $ids   = $request->get('id');

        if ($request->get('customer')) {
            $customerId   = $request->get('customer');
            $customersIds = [$customerId];
        }

        if (!empty($ids) && empty($customerId)) {
            $invoices = Invoice::filterSource()
                ->notFiscalDocument()
                ->whereIn('id', $ids)
                ->get(['customer_id', 'vat']);

            $uniqueVats      = count($invoices->groupBy('vat')->toArray());
            $uniqueCustomers = array_keys($invoices->groupBy('customer_id')->toArray());

            if ($uniqueVats > 1) {
                $blocked = true;
                return view('admin.invoices.sales.edit_regularization', compact('blocked', 'formOptions', 'action'))->render();
            } else {
                $customerId   = @$invoices->first()->customer_id;
                $customersIds = [$customerId];
            }

            $customersSameVat = null;
            if ($uniqueCustomers > 2) {
                $customersIds     = $uniqueCustomers;
                $customersSameVat = Customer::filterSource()->whereIn('id', $uniqueCustomers)->get(['id', 'vat', 'name', 'code', 'city']);
            }
        }


        $customer = Customer::filterSource()
            ->whereIn('id', @$customersIds)
            ->first();

        $customersList = [@$customer->id => @$customer->code . ' - ' . @$customer->billing_name];
        if (count($customersSameVat->toArray()) > 1) {
            $customersList = ['' => ''];
            foreach ($customersSameVat as $row) {
                $customersList[@$row->id] = @$row->code . ' - ' . @$row->billing_name . ' (' . @$row->city . ')';
            }
        }

    
        $invoices = Invoice::filterSource()
            ->whereIn('customer_id', $customersIds)
            ->notFiscalDocument()
            ->where('is_settle', 0)
            ->where('is_deleted', 0)
            ->where('is_draft', 0)
            ->orderBy('due_date', 'asc');

        if (!empty($ids)) {
            $invoices = $invoices->whereIn('id', $ids);
        }

        $invoices = $invoices->orderBy('due_date', 'desc')
            ->get();
        

        $paymentMethods = PaymentMethod::filterSource()
            ->isActive()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $banks    = Bank::listBanks();

        $apiKeys  = Invoice::getApiKeys();

        $currency = Setting::get('app_currency');

        $customersSameVat = null;

        $data = compact(
            'apiKeys',
            'action',
            'formOptions',
            'customer',
            'invoices',
            'customersList',
            'customersSameVat',
            'paymentMethods',
            'banks',
            'currency'
        );

        return view('admin.invoices.sales.edit_regularization', $data)->render();
    }


    /**
     * Edit regularization
     *
     * @param Request $request
     * @param $id
     * @return string
     * @throws \Throwable
     */
    public function editRegularization(Request $request, $regularizationId)
    {

        $regularization = Invoice::where('doc_type', 'regularization')
            ->whereId($regularizationId)
            ->firstOrFail();

        $customer = $regularization->customer;

        $invoicesValues = $regularization->lines->pluck('total_price', 'assigned_invoice_id')->toArray();

        $invoices = Invoice::filterSource()
            ->whereIn('id', array_keys($invoicesValues))
            ->notFiscalDocument()
            ->where('is_deleted', 0)
            ->orderBy('due_date', 'asc')
            ->get();

        $paymentMethods = PaymentMethod::filterSource()
            ->isActive()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $banks    = Bank::listBanks();

        $apiKeys  = Invoice::getApiKeys();

        $currency = Setting::get('app_currency');

        $action = 'Editar regularização';

        $formOptions = ['url' => route('admin.invoices.regularization.update', [$regularizationId]), 'method' => 'PUT', 'class' => 'form-billing'];

        $data = compact(
            'apiKeys',
            'action',
            'formOptions',
            'customer',
            'invoices',
            'regularization',
            'invoicesValues',
            'paymentMethods',
            'banks',
            'currency'
        );

        return view('admin.invoices.sales.edit_regularization', $data)->render();
    }

    /**
     * Store receipt
     *
     * @param Request $request
     * @param $id
     * @return string
     * @throws \Throwable
     */
    public function storeRegularization(Request $request, $id = null)
    {
        return $this->updateRegularization($request, null);
    }

    /**
     * Update receipt
     *
     * @param Request $request
     * @param $id
     * @return string
     * @throws \Throwable
     */
    public function updateRegularization(Request $request, $id = null, $returnResult = false)
    {

        $input               = $request->all();
        $input['docdate']    = $request->get('docdate', date('Y-m-d'));
        $input['draft']      = $request->get('draft', false);
        //$input['send_email'] = $request->get('send_email', false);
        $customerId          = $request->get('customer_id');
        $invoicesTotals      = array_filter($input['invoices']);
        $email               = $request->get('billing_email');
        $sendEmail           = $request->get('send_email', false);
        
        try {
            $customer = Customer::filterSource()
                ->whereId($customerId)
                ->first();


            $invoices = Invoice::filterSource()
                ->whereIn('id', array_keys($invoicesTotals))
                ->where('customer_id', $customer->id)
                ->get();

            $invoicesCustumersIds = $invoices->pluck('customer_id')->toArray();
            $invoicesCustumersIds = array_unique($invoicesCustumersIds);

            $input['vat']              = $customer->final_consumer ? '' : $customer->vat;
            $input['billing_code']     = $customer->code;
            $input['billing_name']     = $customer->billing_name;
            $input['billing_address']  = $customer->billing_address;
            $input['billing_zip_code'] = $customer->billing_zip_code;
            $input['billing_city']     = $customer->billing_city;
            $input['billing_phone']    = $customer->billing_phone;
            $input['billing_email']    = $customer->billing_email;
            $input['billing_country']  = $customer->billing_country;

            $regularization = Invoice::findOrNew($id);
            $regularization->fill($input);
            $regularization->source       = config('app.source');
            $regularization->customer_id  = $customer->id;
            $regularization->doc_type     = Invoice::DOC_TYPE_RG;
            $regularization->doc_date     = @$input['docdate'];
            $regularization->due_date     = @$input['docdate'];
            $regularization->reference    = @$input['docref'];
            $regularization->obs          = @$input['obs'];
            //$regularization->api_key      = @$input['api_key']; //serie
            $regularization->save();

         
            InvoiceLine::where('invoice_id', $regularization->id)->forceDelete();
            $regularizationTotal = InvoiceLine::storeReceiptLines($regularization->id, $invoices, $invoicesTotals, $customer);

            $documentId = $regularization->setDocumentNo();
            $regularization->doc_id        = @$documentId['doc_id'];
            $regularization->doc_series    = @$documentId['doc_serie'];
            $regularization->doc_series_id = @$documentId['doc_serie_id'];
            $regularization->internal_code = @$documentId['internal_code'];
            $regularization->is_draft      = $input['draft'];
            

            $regularization->doc_subtotal  = $regularizationTotal['subtotal'];
            $regularization->doc_vat       = $regularizationTotal['vat'];
            $regularization->doc_total     = $regularizationTotal['total'];
            $regularization->is_settle     = 1;
            $regularization->save();

            //update total unpaid
            if (!$input['draft']) {

                //atualiza valores totais pagos de cada docomento incluido na fatura
                foreach ($invoices as $invoice) {
                    $totalPaid = InvoiceLine::whereHas('invoice', function ($q) {
                            $q->whereNull('deleted_at')
                                ->where('is_draft', 0)
                                ->where('is_deleted', 0);
                        })
                        ->where('assigned_invoice_id', $invoice->id)
                        ->sum('total_price');



                    if ($invoice->doc_type == 'credit-note') {

                        $invoice->doc_total = $invoice->doc_total > 0.00 ? $invoice->doc_total * -1 : $invoice->doc_total; //garante que NC tem sempre sinal negativo. 
                        $invoice->doc_total_pending = $totalPaid - $invoice->doc_total;

                        //coloca nota de crédito como paga se for o caso
                        if ($invoice->doc_total_pending == 0.00 || empty($invoice->doc_total_pending)) {
                            $invoice->is_settle = 1;

                            //atualiza antiga conta corrente keyinvoice
                            //apagar de futuro
                            CustomerBalance::where('doc_type', $invoice->doc_type)
                                ->where('doc_serie_id', $invoice->doc_series_id)
                                ->where('doc_id', $invoice->doc_id)
                                //->where('customer_id', $invoice->customer_id)
                                ->update(['is_paid' => 1]);
                        }

                        $invoice->save();
                    } else {
                        $invoice->doc_total_pending = $invoice->doc_total - $totalPaid;

                        //if (Setting::get('invoice_software') == 'EnovoTms' && (empty($invoice->doc_total_pending) || $invoice->doc_total_pending == 0.00)) {
                        if ((empty($invoice->doc_total_pending) || $invoice->doc_total_pending == 0.00)) {
                            $invoice->is_settle = 1; //marca a fatura como paga
                        }

                        $invoice->save();
                    }
                }
            }

            //ATUALIZA CONTA CORRENTE COM RECIBO REPARTIDO
            //se o numero de customer_id é superior a 1, então estamos a liquidar a conta de vários clientes
            if (count($invoicesCustumersIds) > 1 && !$input['draft']) {

                $partNo = 1;
                foreach ($invoices as $invoice) {
                    $balanceRow = [
                        'assigned_receipt' => $regularization->id,
                        'receipt_part'  => $partNo,
                        'customer_id'   => $invoice->customer_id,
                        'total'         => (float) @$invoicesTotals[$invoice->id],
                        'sense'         => 'credit',
                        'doc_type'      => 'receipt',
                        'doc_id'        => $regularization->doc_id,
                        'doc_serie_id'  => $regularization->doc_series_id,
                        'doc_serie'     => $regularization->doc_series,
                        'reference'     => $regularization->reference,
                        'date'          => $regularization->doc_date,
                        'due_date'      => $regularization->due_date,
                        'is_paid'       => 1,
                        'created_at'    => date('Y-m-d H:i:s'),
                    ];

                    CustomerBalance::insert($balanceRow);
                    $partNo++;
                }
            }


            //envia e-mail
            if ($sendEmail && !$regularization->is_draft) {
                $data = [
                    'email' => $email,
                    'attachments' => ['receipt']
                ];
                $regularization->sendEmail($data);
            }


            $result = [
                'result'   => true,
                'feedback' => 'Regularização emitida com sucesso.',
                'printPdf' => Setting::get('invoices_autoprint') && !$regularization->is_draft ? route('admin.invoices.download.pdf', $regularization->id) : '',
                'doc_id'   => @$regularization->id
            ];
        } catch (\Exception $e) {
            InvoiceLine::where('invoice_id', $regularization->id)->forceDelete();
            Invoice::whereId($regularization->id)->forceDelete();

            $result = [
                'result'   => false,
                'feedback' => $e->getMessage() . (Auth::user()->isAdmin() ? ' na linha ' . $e->getLine() . ' ficheiro ' . $e->getFile() : '')
            ];
        }

        if ($returnResult) {
            return $result;
        }

        return response()->json($result);
    }


    /**
     * Create a transport guide
     *
     * @param $docType
     * @param $header
     * @param $lines
     * @return mixed
     */
    public static function storeTransportGuide(Request $request, $id)
    {

        try {
            $input = $request->all();
            $shipment = Shipment::filterAgencies()->find($request->get('shipment'));
            $customer = $shipment->customer;

            if (!$shipment->at_guide_doc_id) {
                $guideId  = Invoice::createTransportGuideFromShipment($shipment, $input);

                $shipment->at_guide_doc_id = $guideId;
                $shipment->at_guide_serie  = null;
                $shipment->at_guide_key    = @$input['api_key'];
                $shipment->save();
            } else {
                $guideId = $shipment->at_guide_doc_id;
            }

            if ($guideId && !$shipment->at_guide_codeat) {
                $invoice = new Invoice();
                $atCode = $invoice->communicateAT($guideId, 'transport-guide');

                if ($atCode) {
                    $shipment->at_guide_codeat = $atCode;
                    $shipment->save();
                }
            }

            $result = [
                'result'   => true,
                'feedback' => 'Guia de Transporte emitida com sucesso.'
            ];
        } catch (\Exception $e) {
            $result = [
                'result'   => false,
                'feedback' => $e->getMessage() // . ' file '. $e->getFile() . ' line '. $e->getLine()
            ];
        }

        return response()->json($result);
    }

    /**
     * Get list of invoices available to have receipt
     *
     * @param Request $request
     * @return string
     * @throws \Throwable
     */
    public function getCustomerInvoices(Request $request)
    {

        $customerId = $request->get('customerId');

        $customer = Customer::filterSource()->find($customerId);

        if($request->target == 'regularization') {

            $invoices = Invoice::filterSource()
                ->where('customer_id', $customerId)
                ->allowRegularizationDocs()
                ->where('is_deleted', 0)
                ->where('is_settle', 0)
                ->where('is_draft', 0)
                ->orderBy('due_date', 'asc')
                ->get();
        } elseif (@$customer->code == 'CFINAL') {
            $invoices = Invoice::filterSource()
                ->where(function ($q) {
                    $q->whereNull('vat');
                    $q->orWhere('vat', '');
                    $q->orWhere('vat', '999999999');
                    $q->orWhere('vat', '999999990');
                })
                ->whereIn('doc_type', ['invoice', 'credit-note'])
                ->where('is_deleted', 0)
                ->where('is_settle', 0)
                ->where('is_draft', 0)
                ->orderBy('due_date', 'asc')
                ->get();
        } else {
            $invoices = Invoice::filterSource()
                ->where('customer_id', $customerId)
                ->where(function ($q) {
                    $q->where(function ($q) {
                        $q->whereIn('doc_type', ['invoice', 'debit-note'])
                            ->where('is_settle', 0);
                    });
                    $q->orWhere(function ($q) {
                        $q->where('doc_type', 'credit-note');
                        $q->where('doc_date', '>', '2020-09-01'); //só começa a funcionar apos dia 09 set 2020
                        $q->where('is_settle', 0);
                        $q->where(function ($q) {
                            $q->whereNull('doc_total_pending');
                            $q->orWhereRaw('doc_total_pending <= doc_total');
                        });
                    });
                })
                /*->whereIn('doc_type', ['invoice', 'credit-note'])
                ->where('is_settle', 0)*/
                ->where('is_deleted', 0)
                ->where('is_draft', 0)
                ->orderBy('due_date', 'asc')
                ->get();
        }

        $currency = Setting::get('app_currency');


        if($request->target == 'regularization') {
            $html = view('admin.invoices.sales.partials.regularization_invoices_list', compact('invoices', 'customer', 'currency'))->render();
        } else {
            $html = view('admin.invoices.sales.partials.receipt_invoices_list', compact('invoices', 'customer', 'currency'))->render();
        }

        $response = [
            'html'  => $html,
            'email' => @$customer->billing_email,
            'name'  => @$customer->billing_name
        ];

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {

        $invoice = Invoice::filterSource()
            ->where('id', $id)
            ->firstOrFail();

        if (empty($invoice)) {
            return Redirect::back()->with('error', __('Não foi encontrado nenhum documento.'));
        }

        $savedInvoiceId = $invoice->id;
        $deleteType     = $request->get('delete_type', 'reverse');
        $deleteType     = $invoice->can_delete ? $deleteType : 'reverse'; //se ja nao pode apagar, força sempre a ser estorno.
        $customer       = $invoice->customer;
        $customerId     = $customer->id;
        $creditNote     = null;
        
        $submitWebservice = Setting::get('invoice_software') == 'EnovoTms' ? false : true;
        $submitWebservice = in_array($invoice->doc_type, [Invoice::DOC_TYPE_FP, Invoice::DOC_TYPE_RG, Invoice::DOC_TYPE_INTERNAL_DOC, Invoice::DOC_TYPE_NODOC]) ? false : $submitWebservice;

        try {

            if ($invoice->doc_type == Invoice::DOC_TYPE_NODOC) {
                $result  = true;
                $isDraft = false;
            } else {

                $isDraft     = $invoice->is_draft;
                $isScheduled = $invoice->is_scheduled;

                if ($isDraft || $isScheduled) { //rascunho
                    if (Invoice::getInvoiceSoftware() == Invoice::SOFTWARE_ENOVO || empty($invoice->doc_id)) {
                        $result = $invoice->delete();
                    } else {

                        if($submitWebservice && !$isScheduled) {
                            $webservice = new Invoice(@$request->apiKey);
                            $result = $webservice->destroyDraft($invoice->doc_id, $invoice->doc_type);
                        } else {
                            $result = $invoice->delete();
                        }
                    }
                } elseif (in_array($invoice->doc_type, [Invoice::DOC_TYPE_FP, Invoice::DOC_TYPE_INTERNAL_DOC, Invoice::DOC_TYPE_NODOC, Invoice::DOC_TYPE_RG])) {  //documentos sem validade fiscal
                    $result = $invoice->update([
                        'is_deleted'    => true,
                        'delete_reason' => $request->credit_reason,
                        'delete_user'   => Auth::user()->id,
                        'delete_date'   => date('Y-m-d H:i:s')
                    ]);
                } else {

                    //todos os restantes documentos
                    if ($invoice->can_delete && $deleteType == 'delete') {

                        //ANULA REALMENTE A FATURA
                        if (Invoice::getInvoiceSoftware() == Invoice::SOFTWARE_ENOVO) {
                            $result = true;
                        } else {
                            $data = [
                                'doc_serie'     => $invoice->doc_series_id,
                                'credit_serie'  => null,
                                'credit_date'   => $request->credit_date ? $request->credit_date : date('Y-m-d'),
                                'credit_reason' => $request->credit_reason
                            ];

                            $webservice = new Invoice($request->apiKey ?? $invoice->api_key ?? null);
                            $result = $webservice->destroyDocument($invoice->doc_id, $invoice->doc_type, $data); //anula o documento

                            $creditNote = null;
                            $creditId   = null;

                            if (is_numeric($result) && $result > 0) {
                                $creditId = $result; //ID da fatura
                                $deleteType = 'reverse'; //como foi gerada uma nota de crédito em vez de ser mesmo apagado, subscreve a definicao usada ao gravar, para estorno em vez de anulação

                                //grava na tabela de invoices o registo da fatura
                                $creditNote = $invoice->replicate();
                                $creditNote->doc_type       = 'credit-note';
                                $creditNote->doc_id         = $result;
                                $creditNote->doc_series     = null;
                                $creditNote->doc_series_id  = null;
                                $creditNote->doc_date       = date('Y-m-d');
                                $creditNote->due_date       = date('Y-m-d');
                                $creditNote->reference      = config('webservices_mapping.keyinvoice.doc_type.' . $invoice->doc_type) . ' ' . $invoice->doc_series_id . '/' . $invoice->doc_id;
                                $creditNote->obs            = null;
                                $creditNote->created_by     = Auth::user()->id;
                                $creditNote->is_settle      = 0;
                                $creditNote->save();

                                //envia email
                                try {
                                    if ($request->send_email && $result) {
                                        $email = $request->billing_email ? $request->billing_email : $customer->billing_email;
                                        $data = [
                                            'email'         => $email,
                                            'attachments'   => ['invoice']
                                        ];

                                        $creditNote->sendEmail($data);
                                    }
                                } catch (\Exception $e) {
                                }
                            } else {
                                //envia email
                                if ($request->send_email && $result) {
                                    $email = $request->billing_email ? $request->billing_email : $customer->billing_email;
                                    $data = [
                                        'email'         => $email,
                                        'attachments'   => ['invoice']
                                    ];

                                    $invoice->sendEmail($data);
                                }
                            }
                        }
                    } else {

                        //ESTORNA O DOCUMENTO
                        $data = [
                            'doc_serie'     => $invoice->doc_series_id,
                            'credit_serie'  => $request->apiKey,
                            'credit_date'   => $request->credit_date ? $request->credit_date : date('Y-m-d'),
                            'credit_reason' => $request->credit_reason
                        ];

                        if($invoice->doc_type == Invoice::DOC_TYPE_NC) {
                            $creditNote = $invoice->autocreateDebitNote($data);
                        } else {
                            $creditNote = $invoice->autocreateCreditNote($data);
                        }

                        $result = false;
                        if (@$creditNote->id) {
                            $result = true;
                            $creditId   = $creditNote->doc_id;

                            //envia email
                            if ($request->send_email && $result) {
                                $email = $request->billing_email ? $request->billing_email : $customer->billing_email;
                                $data = [
                                    'email'         => $email,
                                    'attachments'   => ['invoice']
                                ];

                                $creditNote->sendEmail($data);
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {

            if ($e->getMessage() == 'Este documento já foi estornado') {
                $invoice->update([
                    'is_reversed' => true
                ]);
            }

            return response()->json([
                'result'  => false,
                'feedback' => $e->getMessage()
            ]);
        }


        if ($result) {

            //desvincula qualquer fatura-proforma que esteja associada a esta fatura
            Invoice::filterSource()
                ->where('doc_type', 'proforma-invoice')
                ->where('assigned_invoice_id', $savedInvoiceId)
                ->update([
                    'is_settle'           => 0,
                    'assigned_invoice_id' => null,
                    'payment_method'      => null,
                    'payment_date'        => null
                ]);

            //delete month invoice
            if ($invoice->target == Invoice::TARGET_CUSTOMER_BILLING) {
                $billing = CustomerBilling::where('customer_id', $customerId)
                    ->where('id', $invoice->target_id)
                    ->first();

                if (!$billing) {
                    //se não encontrou antes, tenta agora encontrar através do numero da fatura
                    $billing = CustomerBilling::where('customer_id', $customerId)
                        ->where('invoice_doc_id', $invoice->doc_id)
                        ->first();
                }

                if ($billing) {
                    $month  = $billing->month;
                    $year   = $billing->year;
                    $period = $billing->period;

                    $this->detachShipmentsInvoice($billing->shipments);
                    $billing->forceDelete();

                    $billedItems = CustomerBilling::getBilledShipments($invoice->customer_id, $year, $month, $period);
                    $customer = CustomerBilling::getBilling($customerId, $month, $year, $period, null, @$billedItems['ids']);

                    $htmlHeader  = view('admin.billing.customers.partials.header', compact('customer', 'billedItems', 'month', 'year', 'period'))->render();
                    $htmlSidebar = view('admin.billing.customers.partials.sidebar', compact('customer', 'billedItems', 'month', 'year', 'period'))->render();
                }
            }

            ItemStockHistory::deleteBySaleInvoiceId($invoice->id);

            if ($isDraft) {
                InvoiceLine::where('invoice_id', $invoice->id)->forceDelete();
                $invoice->forceDelete();
            } else {

                $isSettle = 0;
                if (in_array($invoice->doc_type, [ Invoice::DOC_TYPE_FR,  Invoice::DOC_TYPE_FS])) {
                    $isSettle = 1; //se é fatura-recibo ou fat. simplificada tem de ser forçado a que esteja marcada como paga (uma FR/FS está sempre paga). 
                }

                //delete invoice
                $invoice->update([
                    'is_deleted'     => $deleteType == 'delete' ? 1 : 0,
                    'is_reversed'    => $deleteType == 'reverse' ? 1 : 0,
                    'is_settle'      => $isSettle,
                    'delete_reason'  => $request->credit_reason,
                    'credit_note_id' => @$creditNote->id,
                    'delete_date'    => date('Y-m-d H:i:s'),
                    'delete_user'    => Auth::user()->id,
                ]);
            }

            //repõe receipts e regularizations como não pagos
            if ($invoice->doc_type == Invoice::DOC_TYPE_RC || $invoice->doc_type == Invoice::DOC_TYPE_RG) { 

                $receiptLines = InvoiceLine::where('invoice_id', $invoice->id)
                    ->whereNotNull('assigned_invoice_id')
                    ->get();

                foreach ($receiptLines as $receiptLine) {

                    $invoices = Invoice::where('id', $receiptLine->assigned_invoice_id)->get();

                    foreach ($invoices as $invoice) {

                        $totalPaid = InvoiceLine::whereHas('invoice', function ($q) {
                                $q->whereNull('deleted_at')
                                    ->where('is_draft', 0)
                                    ->where('is_deleted', 0);
                            })
                            ->where('assigned_invoice_id', $invoice->id)
                            ->sum('total_price');

                        $invoice->assigned_receipt  = null;
                        $invoice->is_settle         = 0;
                        $invoice->doc_total_pending = $invoice->doc_total - $totalPaid;
                        $invoice->save();

                        /* CustomerBalance::where('doc_id', $invoice->doc_id)
                            ->where('doc_serie_id', $invoice->doc_series_id)
                            ->update(['is_paid' => 0]); */
                    }
                }
            }

            $result =  [
                'result'        => true,
                'html_header'   => @$htmlHeader,
                'html_sidebar'  => @$htmlSidebar,
                'feedback'      => 'Documento de venda anulado com sucesso.' . (@$creditId ? ' Foi criada a Nota Crédito ' . $creditId : ''),
                'printPdf'      => Setting::get('invoices_autoprint') && $invoice->is_reversed && $invoice->credit_note_id ? route('admin.invoices.download.pdf', $invoice->credit_note_id) : ''
            ];
        } else {
            $result =  [
                'result'   => false,
                'feedback' => 'Ocorreu um erro ao anular o documento de venda.',
                'printPdf' => ''
            ];
        }

        /*if($isDraft) {
            return Redirect::back()->with($result['result'] ? 'success' : 'error', $result['feedback']);
        }*/

        return response()->json($result);
    }

    /**
     * Replicate invoices
     *
     * @return \Illuminate\Http\Response
     */
    public function replicate(Request $request, $invoiceId)
    {

        try {

            $replicateType = $request->get('type');
            $replicateType = $replicateType ? $replicateType : null; //'proforma-invoice';

            $invoice = Invoice::with('lines')->find($invoiceId);
            $lines = $invoice->lines;

            /*if ($invoice->target != 'Invoice') {
                return Redirect::back()->with('error', 'Não pode duplicar a fatura porque diz respeito a uma fatura mensal.');
            }*/

            $date = new Date();

            $newInvoice = $invoice->replicate();
            $newInvoice->created_by         = Auth::user()->id;
            $newInvoice->doc_series         = null;
            $newInvoice->doc_series_id      = null;
            $newInvoice->doc_id             = null;
            $newInvoice->doc_type           = 'invoice';
            $newInvoice->doc_date           = $date->format('Y-m-d');
            $newInvoice->due_date           = null;
            $newInvoice->is_settle          = 0;
            $newInvoice->is_deleted         = 0;
            $newInvoice->is_reversed        = 0;
            $newInvoice->is_scheduled       = 0;
            $newInvoice->is_draft           = 1;
            $newInvoice->api_key            = null;
            $newInvoice->doc_total_pending  = null;
            $newInvoice->mb_entity          = null;
            $newInvoice->mb_reference       = null;
            $newInvoice->mbw_phone          = null;
            $newInvoice->paypal_account     = null;
            $newInvoice->delete_date        = null;
            $newInvoice->delete_reason      = null;
            $newInvoice->delete_user        = null;
            $newInvoice->credit_note_id     = null;
            $newInvoice->settle_date        = null;
            $newInvoice->settle_method      = null;
            $newInvoice->settle_obs         = null;
            $newInvoice->sepa_payment_id    = null;
            $newInvoice->assigned_invoice_id = null;


            if ($replicateType) {
                $newInvoice->doc_type = $replicateType;
            }

            $newInvoice->save();

            //se é fatura-proforma, associa à fatura-proforma o ID da fatura gerada
            if ($invoice->doc_type == 'proforma-invoice') {
                $invoice->update(['assigned_invoice_id' => $newInvoice->id]);
            }

            foreach ($lines as $line) {
                $newLine = new InvoiceLine();
                $newLine->fill($line->toArray());
                $newLine->invoice_id = $newInvoice->id;
                $newLine->save();
            }

            return Redirect::route('admin.invoices.index', ['invoice' => $newInvoice->id])->with('success', 'Fatura duplicada com sucesso.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erro interno ao duplicar a fatura.');
        }
    }


    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatable(Request $request)
    {

        if ($request->has('scheduled')) {
            return $this->datatableScheduled($request);
        }

        $data = Invoice::filterSource()
            ->filterAgencies()
            ->with('customer', 'user', 'receipts.invoice')
            ->whereNull('deleted_at')
            ->where('is_scheduled', 0)
            ->where(function ($q) {
                $q->where('is_hidden', 0);
                $q->orWhereNull('is_hidden');
            })
            ->select(['*', DB::raw("(`doc_id` * 1) AS `doc_id`")]);

        //filter date min
        $dtMin = $request->get('date_min');
        if ($request->has('date_min')) {
            $dtMax = $dtMin;
            if ($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }
            $data = $data->whereBetween('doc_date', [$dtMin, $dtMax]);
        }


        //filter agencies
        if ($request->has('agency') || $request->has('route') || $request->has('seller')) {
            $data = $data->whereHas('customer', function ($q) use ($request) {
                if ($request->has('agency')) {
                    $q->where('agency_id', $request->get('agency'));
                }
                if ($request->has('route')) {
                    $q->where('route_id', $request->get('route'));
                }
                if ($request->has('seller')) {
                    $q->where('seller_id', $request->get('seller'));
                }
            });
        }

        //filter hide_receipts

        $value = $request->hide_receipts;
        if ($request->has('hide_receipts') && !empty($value)) {
            $data = $data->whereNotIn('doc_type', [Invoice::DOC_TYPE_RC, Invoice::DOC_TYPE_RG]);
        }

        //filter expired
        $value = $request->expired;
        if ($request->has('expired')) {
            $data = $data->where('due_date', '<', $value);
        }

        //filter serie
        $value = $request->serie;
        if ($request->has('serie')) {
            $data = $data->whereIn('doc_series_id', $value);
        }

        //filter year
        $value = $request->year;
        if ($request->has('year')) {
            $data = $data->whereRaw('YEAR(doc_date) = ' . $value);
        }

        //filter month
        $value = $request->month;
        if ($request->has('month')) {
            $data = $data->whereRaw('MONTH(doc_date) = ' . $value);
        }

        //filter doc id
        $value = $request->doc_id;
        if ($request->has('doc_id')) {
            $data = $data->where('doc_id', $value);
        }

        //filter doc type
        $value = $request->doc_type;
        if ($request->has('doc_type')) {
            $data = $data->whereIn('doc_type', $value);
        } else {
            $data = $data->where('doc_type', '<>', 'nodoc');
        }

        //filter customer
        $value = $request->customer;
        if ($request->has('customer')) {
            if ($value == '1') { //CFINAL
                $data = $data->where(function ($q) {
                    $q->where('customer_id', 1);
                    $q->orWhereNull('vat');
                    $q->orWhere('vat', '');
                });
            } else {
                $data = $data->where('customer_id', $value);
            }
        }

        //filter operator
        $value = $request->operator;
        if ($request->has('operator')) {
            $data = $data->whereIn('created_by', $value);
        }

        //filter payment condition
        $value = $request->payment_condition;
        if ($request->has('payment_condition')) {
            $data = $data->whereIn('payment_condition', $value);
        }

        //filter payment method
        $value = $request->payment_method;
        if ($request->has('payment_method')) {
            $data = $data->whereIn('payment_method', $value);
        }

        //filter draft
        $value = $request->draft;
        if ($request->has('draft')) {
            $data = $data->where('is_draft', $value);
        }

        //filter settle
        $value = $request->settle;
        if ($request->has('settle')) {
            if (!empty($request->doc_type) && in_array('nodoc', $request->doc_type)) {
                if ($value == 1) {
                    $data = $data->where(function ($q) use ($value) {
                        $q->where('is_settle', 1);
                        $q->whereNotNull('settle_method');
                    });
                } elseif ($value == 3) { //não pago - vencido
                    $data = $data->where(function ($q) use ($value) {
                        $q->where('is_settle', 0);
                        $q->orWhere(function ($q) {
                            $q->where('is_settle', 1);
                            $q->whereNull('settle_method');
                        });
                        $q->where('due_date', '<', date('Y-m-d'));
                    });
                } elseif ($value == 4) { //não pago - pendente
                    $data = $data->where(function ($q) use ($value) {
                        $q->where('is_settle', 0);
                        $q->where('due_date', '>=', date('Y-m-d'));
                    });
                } else {
                    $data = $data->where(function ($q) use ($value) {
                        $q->where('is_settle', 0);
                        $q->orWhere(function ($q) {
                            $q->where('is_settle', 1);
                            $q->whereNull('settle_method');
                        });
                    });
                }
            } else {
                if ($value == 2) {
                    $data = $data->where(function ($q) use ($value) {
                        $q->where('is_settle', 0);
                        $q->where('doc_total_pending', '>', 0.00);
                    });
                } elseif ($value == 3) { //não pago - vencido
                    $data = $data->where(function ($q) use ($value) {
                        $q->where('is_settle', 0);
                        $q->where('due_date', '<', date('Y-m-d'));
                    });
                } elseif ($value == 4) { //não pago - pendente
                    $data = $data->where(function ($q) use ($value) {
                        $q->where('is_settle', 0);
                        $q->where('due_date', '>=', date('Y-m-d'));
                    });
                } else {
                    $data = $data->where('is_settle', $value);
                }
            }
        }

        //filter target
        $value = $request->target;
        if ($request->has('target')) {
            $data = $data->whereIn('target', $value);
        }

        //filter deleted
        $value = $request->deleted;
        if ($request->has('deleted') && empty($value)) {
            $data = $data->where('is_deleted', $value);
        }


        return Datatables::of($data)
            ->edit_column('doc_date', function ($row) {
                return view('admin.invoices.sales.datatables.doc_date', compact('row'))->render();
            })
            ->edit_column('sort', function ($row) {
                return $row->doc_date;
            })
            ->edit_column('doc_id', function ($row) {
                return view('admin.invoices.sales.datatables.doc_id', compact('row'))->render();
            })
            ->add_column('doc_name', function ($row) {
                return view('admin.invoices.sales.datatables.doc_name', compact('row'))->render();
            })
            ->edit_column('doc_type', function ($row) {
                return view('admin.invoices.sales.datatables.doc_type', compact('row'))->render();
            })
            ->edit_column('due_date', function ($row) {
                return view('admin.invoices.sales.datatables.due_date', compact('row'))->render();
            })
            ->edit_column('payment_date', function ($row) {
                return view('admin.invoices.sales.datatables.payment_date', compact('row'))->render();
            })
            ->edit_column('is_settle', function ($row) {
                return view('admin.invoices.sales.datatables.is_settle', compact('row'))->render();
            })
            ->edit_column('customer_id', function ($row) {
                return view('admin.invoices.sales.datatables.customer', compact('row'))->render();
            })
            ->edit_column('created_at', function ($row) {
                return view('admin.invoices.sales.datatables.created_at', compact('row'))->render();
            })
            ->edit_column('doc_subtotal', function ($row) {
                return $row->doc_type == 'receipt' ? '' : money($row->doc_subtotal, Setting::get('app_currency'));
            })
            ->edit_column('doc_total', function ($row) {
                return view('admin.invoices.sales.datatables.doc_total', compact('row'))->render();
            })
            ->edit_column('total', function ($row) {
                return view('admin.invoices.sales.datatables.total', compact('row'))->render();
            })
            ->edit_column('doc_total_pending', function ($row) {
                return view('admin.invoices.sales.datatables.total_pending', compact('row'))->render();
            })
            ->edit_column('customer_balance', function ($row) {
                return view('admin.invoices.sales.datatables.customer_balance', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.invoices.sales.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatableScheduled(Request $request)
    {

        $bindings = [
            'invoices.*',
            'invoices_scheduled.frequency',
            'invoices_scheduled.repeat_every',
            'invoices_scheduled.repeat',
            'invoices_scheduled.weekdays',
            'invoices_scheduled.month_days',
            'invoices_scheduled.year_days',
            'invoices_scheduled.end_repetitions',
            'invoices_scheduled.start_date',
            'invoices_scheduled.end_date',
            'invoices_scheduled.count_repetitions',
            'invoices_scheduled.last_schedule',
            'invoices_scheduled.finished',
            'invoices_scheduled.send_email',
            'invoices_scheduled.is_draft',
        ];

        $data = Invoice::join('invoices_scheduled', 'invoices.id', '=', 'invoices_scheduled.invoice_id')
            ->where('invoices_scheduled.source', config('app.source'))
            ->with('customer', 'user')
            ->where('doc_type', '<>', 'nodoc')
            ->whereNull('invoices.deleted_at')
            ->where('is_scheduled', 1)
            ->where(function ($q) {
                $q->where('is_hidden', 0);
                $q->orWhereNull('is_hidden');
            })
            ->select($bindings);

        //filter serie
        $value = $request->serie;
        if ($request->has('serie')) {
            $data = $data->whereIn('doc_series_id', $value);
        }

        //filter doc type
        $value = $request->type;
        if ($request->has('type')) {
            $data = $data->whereIn('doc_type', $value);
        }

        //filter customer
        $value = $request->customer;
        if ($request->has('customer')) {
            $data = $data->where('customer_id', $value);
        }

        //filter payment method
        $value = $request->payment_method;
        if ($request->has('payment_method')) {
            $data = $data->whereIn('payment_method', $value);
        }

        //filter payment condition
        $value = $request->payment_condition;
        if ($request->has('payment_condition')) {
            $data = $data->whereIn('payment_condition', $value);
        }

        //filter deleted
        $value = $request->deleted;

        if ($request->has('deleted') && empty($value)) {
            $data = $data->where('is_deleted', $value);
        }


        return Datatables::of($data)
            ->editColumn('id', function ($row) {
                return view('admin.invoices.sales.datatables.customer', compact('row'))->render();
            })
            ->edit_column('doc_date', function ($row) {
                return view('admin.invoices.sales.datatables.doc_date', compact('row'))->render();
            })
            ->edit_column('doc_id', function ($row) {
                return view('admin.invoices.sales.datatables.doc_id', compact('row'))->render();
            })
            ->edit_column('doc_type', function ($row) {
                return view('admin.invoices.sales.datatables.doc_type', compact('row'))->render();
            })
            ->edit_column('payment_condition', function ($row) {
                return view('admin.invoices.sales.datatables.schedule.payment_condition', compact('row'))->render();
            })
            ->add_column('schedule_time', function ($row) {
                return view('admin.invoices.sales.datatables.schedule.schedule_time', compact('row'))->render();
            })
            ->add_column('schedule_config', function ($row) {
                return view('admin.invoices.sales.datatables.schedule.schedule_config', compact('row'))->render();
            })
            ->add_column('schedule_end', function ($row) {
                return view('admin.invoices.sales.datatables.schedule.schedule_end', compact('row'))->render();
            })
            ->add_column('finished', function ($row) {
                return view('admin.invoices.sales.datatables.schedule.finished', compact('row'))->render();
            })
            ->edit_column('customer_id', function ($row) {
                return view('admin.invoices.sales.datatables.customer', compact('row'))->render();
            })
            ->edit_column('created_at', function ($row) {
                return view('admin.invoices.sales.datatables.created_at', compact('row'))->render();
            })
            ->edit_column('doc_subtotal', function ($row) {
                return $row->doc_type == 'receipt' ? '' : money($row->doc_subtotal, Setting::get('app_currency'));
            })
            ->edit_column('total', function ($row) {
                return view('admin.invoices.sales.datatables.total', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.invoices.sales.datatables.schedule.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Convert a Draft into Invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function convertFromDraft(Request $request, $invoiceId)
    {

        $invoice = Invoice::filterSource()
            ->where('id', $invoiceId)
            ->first();

        if (empty($invoice)) {
            return Redirect::back()->with('error', 'Não foi encontrada nenhuma fatura.');
        }

        try {

            if (Setting::get('invoice_software') == 'EnovoTms') {
                $invoiceId = $invoice->setDocumentNo();
                $invoiceId = $invoiceId['doc_id'];
            } else {
                $webservice = new Invoice($invoice->api_key);
                $invoiceId = $webservice->convertDraftToDoc($invoice->doc_id, $invoice->doc_type, $invoice->doc_series_id);
            }

            if ($invoiceId) {
                $invoice->doc_id = $invoiceId;
                $invoice->is_draft = 0;
                $invoice->save();

                if ($invoice->doc_type != 'receipt') {
                    $shipmentsIds = [];
                    //atualiza registos de faturação mensal dos clientes
                    if ($invoice->target == Invoice::TARGET_CUSTOMER_BILLING && !empty($invoice->target_id)) {
                        $billing = CustomerBilling::findOrNew($invoice->target_id);
                        $billing->invoice_id     = $invoice->id;
                        $billing->invoice_doc_id = $invoice->doc_id;
                        $billing->invoice_draft = 0;
                        $billing->save();
                        $shipmentsIds = $billing->shipments;
                    }

                    //marca todos os envios como faturados
                    $this->assignShipmentsInvoice($invoice, $shipmentsIds);
                } else {

                    $invoicesIds = $invoice->lines->pluck('assigned_invoice_id')->toArray();
                    $invoices = Invoice::whereIn('id', $invoicesIds)->get();

                    foreach ($invoices as $invoice) {

                        $totalPaid = InvoiceLine::whereHas('invoice', function ($q) {
                            $q->whereNull('deleted_at')
                                ->where('is_draft', 0)
                                ->where('is_deleted', 0);
                        })
                            ->where('assigned_invoice_id', $invoice->id)
                            ->sum('total_price');

                        $invoice->assigned_receipt = null;
                        $invoice->is_settle = 0;
                        $invoice->doc_total_pending = $invoice->doc_total - $totalPaid;
                        $invoice->save();

                        /* CustomerBalance::where('doc_id', $invoice->doc_id)
                            ->where('doc_serie_id', $invoice->doc_series_id)
                            ->update(['is_paid' => 0]); */
                    }
                }

                return Redirect::back()->with('success', 'Rascunho convertido com sucesso.');
            }
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * Download billing invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function documentPdf(Request $request, $invoiceId)
    {
        $data = [
            'id' => $invoiceId,
            'refresh_cache' => $request->get('cache', false)
        ];

        $doc = Invoice::downloadPdf($data, 'string');
        $data = base64_decode($doc);
        header('Content-Type: application/pdf');
        echo $data;
        exit;
    }

    /**
     * Download billing invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request, $customerId, $invoiceId)
    {

        $id       = $request->get('id');
        $apiKey   = $request->key;
        $docSerie = $request->serie;
        $docType  = $request->type;

        $data = [
            'id'          => $id,
            'customer_id' => $customerId,
            'api_key'     => $apiKey,
            'serie'       => $docSerie,
            'doc_type'    => $docType,
            'doc_id'      => $invoiceId
        ];

        $doc = Invoice::downloadPdf($data, 'string');
        $data = base64_decode($doc);
        header('Content-Type: application/pdf');
        echo $data;
        exit;
    }

    /**
     * Show destroy invoice modal.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyEdit(Request $request, $customerId, $invoiceId)
    {

        $id       = $request->get('id');
        $apiKey   = $request->get('key');
        $docSerie = $request->get('serie');
        $docType  = $request->get('type');
        $series   = Invoice::getApiKeys();

        if (in_array($docType, ['proforma-invoice', 'internal-doc'])) {
            $apiKey = null;
        }

        if (!empty($id)) {
            $invoice = Invoice::filterSource()
                ->whereId($id)
                ->first();
        } else if ($apiKey) {
            $invoice = Invoice::filterSource()
                ->where('api_key', $apiKey)
                ->where('doc_id', $invoiceId)
                ->where('customer_id', $customerId)
                ->orderBy('id', 'desc')
                ->firstOrFail();
        } else {
            $invoice = Invoice::filterSource()
                ->where('customer_id', $customerId)
                ->where('doc_id', $invoiceId);
            if ($docSerie) {
                $invoice = $invoice->where('doc_series_id', $docSerie);
            }
            if ($docType) {
                $invoice = $invoice->where('doc_type', $docType);
            }
            $invoice = $invoice->first();
        }

        return view('admin.invoices.sales.modals.destroy_invoice', compact('invoice', 'series'))->render();
    }

    /**
     * Show modal to edit billing emaill
     * @param Request $request
     * @param $id
     */
    public function editEmail(Request $request, $customerId, $invoiceId)
    {

        $id       = $request->get('id');
        $apiKey   = $request->get('key');
        $docSerie = $request->get('serie');
        $docType  = $request->get('type');

        if ($id) {
            $invoice = Invoice::filterSource()
                ->whereId($id)
                ->first();
        } else if ($apiKey) {
            $invoice = Invoice::with('customer')
                ->filterSource()
                ->where('api_key', $apiKey)
                ->where('doc_id', $invoiceId)
                ->orderBy('id', 'desc')
                ->firstOrFail();
        } else {
            $invoice = Invoice::filterSource()
                ->where('customer_id', $customerId)
                ->where('doc_id', $invoiceId);
            if ($docSerie) {
                $invoice = $invoice->where('doc_series_id', $docSerie);
            }
            if ($docType) {
                $invoice = $invoice->where('doc_type', $docType);
            }
            $invoice = $invoice->first();
        }

        $data = compact(
            'invoice'
        );

        return view('admin.invoices.sales.modals.email', $data)->render();
    }

    /**
     * submit billing info by e-mail
     * @param Request $request
     * @param $id
     */
    /*public function submitEmail(Request $request, $customerId, $invoiceId) {

        $apiKey   = $request->get('key');
        $docSerie = $request->get('serie');
        $docType  = $request->get('type');

        if($apiKey) {
            $invoice = Invoice::with('customer')
                ->filterSource()
                ->where('api_key', $apiKey)
                ->where('doc_id', $invoiceId)
                ->orderBy('id', 'desc')
                ->firstOrFail();
        } else {
            $invoice = Invoice::filterSource()
                ->where('customer_id', $customerId)
                ->where('doc_id', $invoiceId);
            if($docSerie) {
                $invoice = $invoice->where('doc_series_id', $docSerie);
            }
            if($docType) {
                $invoice = $invoice->where('doc_type', $docType);
            }
            $invoice = $invoice->first();
        }

        $data = [
            'email'         => $request->get('email'),
            'attachments'   => $request->get('attachments', []),
        ];

        $result = $invoice->sendEmail($data);

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
    }*/

    public function submitEmail(Request $request, $id)
    {

        $invoice = Invoice::with('customer')
            ->filterSource()
            ->whereId($id)
            ->firstOrFail();

        $data = [
            'email'       => $request->get('email'),
            'attachments' => $request->get('attachments', []),
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

        $fuelTaxPercent = $fuelTax;
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
                "description"   => Setting::get('invoice_item_fuel_desc') . ($fuelTaxPercent ? ' - ' . money($fuelTaxPercent, '%') : ''),
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
                "description"   => Setting::get('invoice_item_fuel_desc') . ($fuelTaxPercent ? ' - ' . money($fuelTaxPercent, '%') : ''),
                "qty"           => 1,
                "total_price"   => $priceVat,
                "discount"      => 0,
                "subtotal"      => $priceVat,
                "tax_rate"      => $exemptionReason,
                'hidden'        => 1
            ];
        }

        return $lines;
    }

    /**
     * Assign invoice to selected shipments
     *
     * @param $invoice
     * @param $shipmentsIds
     */
    public function assignShipmentsInvoice($invoice, $shipmentsIds)
    {

        if (!empty(Setting::get('block_shipments_after_billing'))) {
            Shipment::where('customer_id', $invoice->customer_id)
                ->whereIn('id', $shipmentsIds)
                ->update(['is_blocked' => true]);
        }


        $updateArr = [
            'invoice_id'      => $invoice->id,
            'invoice_doc_id'  => $invoice->doc_id,
            'invoice_type'    => $invoice->doc_type,
            'invoice_draft'   => $invoice->is_draft,
            'invoice_key'     => $invoice->api_key
        ];

        if (Setting::get('shipments_status_after_billing')) {
            $updateArr['status_id'] = Setting::get('shipments_status_after_billing');
        }

        Shipment::whereIn('id', $shipmentsIds)->update($updateArr);
    }

    /**
     * Detach invoice to selected shipments
     *
     * @param $invoice
     * @param $shipmentsIds
     */
    public function detachShipmentsInvoice($shipmentsIds)
    {

        if ($shipmentsIds && is_array($shipmentsIds)) {
            if (!empty(Setting::get('block_shipments_after_billing'))) {
                Shipment::whereIn('id', $shipmentsIds)
                    ->update(['is_blocked' => false]);
            }


            Shipment::whereIn('id', $shipmentsIds)->update([
                'invoice_id'     => null,
                'invoice_doc_id' => null,
                'invoice_type'   => null,
                'invoice_draft'  => null,
                'invoice_key'    => null
            ]);
        }
    }

    /**
     * Store customer billing
     *
     * @param $invoice
     * @param $input
     * @return array
     */
    public function storeCustomerBilling($invoice, $customer, $input)
    {

        $shipmentIds  = explode(',', @$input['shipments']);
        $covenantsIds = explode(',', @$input['covenants']);
        $productsIds  = explode(',', @$input['products']);

        if (count($shipmentIds) == 1) {
            $input['billing_type'] = 'single';
        }

        $billing = CustomerBilling::findOrNew($invoice->target_id);
        $billing->fill($input);
        $billing->shipments      = $shipmentIds;
        $billing->covenants      = $covenantsIds;
        $billing->products       = $productsIds;
        $billing->customer_id    = $invoice->customer_id;
        $billing->invoice_id     = $invoice->id;
        $billing->invoice_draft  = $invoice->is_draft;
        $billing->invoice_type   = $invoice->doc_type;
        $billing->invoice_doc_id = $invoice->doc_id;
        $billing->save();

        $invoice->target_id = $billing->id;
        $invoice->save();

        $month  = $input['month'];
        $year   = $input['year'];
        $period = $input['period'];

        $billedItems = CustomerBilling::getBilledShipments($invoice->customer_id, $year, $month, $period);
        $customer    = CustomerBilling::getBilling($invoice->customer_id, $month, $year, $period, null, @$billedItems['ids']);

        $htmlHeader  = view('admin.billing.customers.partials.header', compact('customer', 'billedItems', 'month', 'year', 'period'))->render();
        $htmlSidebar = view('admin.billing.customers.partials.sidebar', compact('customer', 'billedItems', 'month', 'year', 'period'))->render();

        //Assign invoice to all selected shipments
        if (!empty($shipmentIds)) {
            $this->assignShipmentsInvoice($invoice, $shipmentIds);
        }

        return [
            'header'  => $htmlHeader,
            'sidebar' => $htmlSidebar
        ];
    }

    /**
     * Search customers on DB
     *
     * @return type
     */
    public function searchCustomerSelect2(Request $request)
    {

        $search = trim($request->get('q'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

        $fields = [
            'id',
            'vat',
            'code',
            'name',
            'address',
            'zip_code',
            'city',
            'country',
            'contact_email',
            'billing_name',
            'billing_address',
            'billing_zip_code',
            'billing_city',
            'billing_country',
            'billing_reference',
            'email',
            'other_name',
            'billing_email',
            'payment_method',
            'default_invoice_type',
            'billing_discount_value',
            'agency_id',
            'company_id'
        ];

        try {
            $results = [];

            $customers = Customer::filterSource()
                ->filterAgencies()
                ->isActive()
                ->where(function ($q) use ($search) {
                    $q->where('code', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search)
                        ->orWhere('billing_name', 'LIKE', $search)
                        ->orWhere('other_name', 'LIKE', $search)
                        ->orWhere('vat', 'LIKE', $search)
                        ->orWhere('phone', 'LIKE', $search);
                })
                ->isDepartment(false)
                ->isProspect(false)
                ->get($fields);

            if ($customers) {
                $results = array();
                foreach ($customers as $customer) {
                    $results[] = [
                        'id'    => $customer->id,
                        'text'  => $customer->code . ' - ' . str_limit($customer->billing_name, 40),

                        'vat'       => str_replace(' ', '', trim($customer->vat)),
                        'code'      => strtoupper(trim($customer->code)),
                        'name'      => strtoupper(trim($customer->billing_name)),
                        'address'   => strtoupper(trim($customer->billing_address)),
                        'zip_code'  => strtoupper(trim($customer->billing_zip_code)),
                        'city'      => strtoupper(trim($customer->billing_city)),
                        'country'   => $customer->billing_country,
                        'agency_id' => $customer->agency_id,
                        'email'     => strtolower(trim($customer->billing_email)),
                        'reference' => $customer->billing_reference,
                        'payment_condition' => @$customer->paymentCondition->code ? @$customer->paymentCondition->code : '30d',
                        'doc_type'  => $customer->default_invoice_type,
                        'billing_discount_value' => $customer->billing_discount_value
                    ];
                }
            } else {
                $results[] = [
                    'id'  => '',
                    'text' => 'Nenhum cliente encontrado.'
                ];
            }
        } catch (\Exception $e) {
            $results[] = [
                'id' => '',
                'text' => 'Erro interno. ' . $e->getMessage() . ' Line ' . $e->getLine()
            ];
        }

        return Response::json($results);
    }

    /**
     * Search customers on db - autocomplete plugin
     *
     * @return type
     */
    public function searchCustomer(Request $request)
    {

        $search = trim($request->get('query'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

        $fields = [
            'id',
            'vat',
            'code',
            'name',
            'address',
            'zip_code',
            'city',
            'country',
            'contact_email',
            'billing_name',
            'billing_address',
            'billing_zip_code',
            'billing_city',
            'billing_country',
            'billing_reference',
            'email',
            'other_name',
            'billing_email',
            'payment_method',
            'default_invoice_type',
            'billing_discount_value',
            'agency_id',
            'company_id'
        ];

        try {

            $customers = Customer::filterAgencies()
                ->isActive()
                ->where(function ($q) use ($search) {
                    $q->where('code', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search)
                        ->orWhere('billing_name', 'LIKE', $search)
                        ->orWhere('other_name', 'LIKE', $search)
                        ->orWhere('vat', 'LIKE', $search)
                        ->orWhere('phone', 'LIKE', $search);
                })
                ->isDepartment(false)
                ->isProspect(false)
                ->take(10)
                ->get($fields);

            if ($customers) {

                $results = array();
                foreach ($customers as $customer) {
                    $results[] = [
                        'data'     => $customer->id,
                        'value'    => strtoupper(trim($customer->billing_name)),
                        'vat'      => str_replace(' ', '', trim($customer->vat)),
                        'code'     => strtoupper(trim($customer->code)),
                        'name'     => strtoupper(trim($customer->billing_name)),
                        'address'  => strtoupper(trim($customer->billing_address)),
                        'zip_code' => strtoupper(trim($customer->billing_zip_code)),
                        'city'     => strtoupper(trim($customer->billing_city)),
                        'country'  => $customer->billing_country,
                        'agency_id' => $customer->agency_id,
                        'email'    => strtolower(trim($customer->billing_email)),
                        'reference' => $customer->billing_reference,
                        'payment_condition' => @$customer->paymentCondition->code ? @$customer->paymentCondition->code : '30d',
                        'doc_type' => $customer->default_invoice_type,
                        'billing_discount_value' => $customer->billing_discount_value
                    ];
                }
            } else {
                $results = ['Nenhum cliente encontrado.'];
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
     * Search billing item
     *
     * @return type
     */
    public function searchItems(Request $request)
    {
        $isPurchase = $request->get('is_purchase', false);
        $customerId = $request->get('customer_id');

        $customer = null;
        if ($customerId && !$isPurchase) {
            $customer = Customer::filterSource()
                ->where('id', $customerId)
                ->first();
        }

        $search = trim($request->get('query'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

        try {

            $products = Item::remember(config('cache.query_ttl'))
                ->cacheTags(Item::CACHE_TAG)
                ->filterSource()
                ->isActive()
                ->where(function ($q) use ($search) {
                    $q->where('reference', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search);
                })
                ->ordered()
                ->get();

            if ($products) {

                $results = array();
                foreach ($products as $product) {
                    if ($isPurchase) {
                        $price = $product->price;
                    } else {
                        $price = @$customer->custom_billing_items[$product->id] ?? $product->sell_price;
                    }

                    $price = floatval($price);

                    $results[] = [
                        'data'             => $product->id,
                        'value'            => trim($product->name),
                        'reference'        => $product->reference,
                        'name'             => trim($product->name),
                        'price'            => number_format($price, 2, '.', ''),
                        'tax_rate'         => $product->tax_rate,
                        'service'          => $product->is_service,
                        'has_stock'        => (bool) $product->has_stock,
                        'stock_total'      => $product->stock_total,
                        'stock_total_html' => view('admin.billing.items.datatables.stock_total', ['row' => $product])->render()
                    ];
                }
            } else {
                $results = ['Nenhum artigo encontrado.'];
            }

            // $results[] = [
            //     'value'  => 'Criar Artigo Faturação',
            //     'create' => true,
            //     'url'    => route('admin.billing.items.create', ['name' => $request->get('query')])
            // ];
        } catch (\Exception $e) {
            $results = ['Erro: ' . $e->getMessage()];
        }

        $results = [
            'suggestions' => $results
        ];

        return Response::json($results);
    }


    /**
     * Print invoice summary
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function invoiceSummary(Request $request, $customerId, $invoiceId)
    {

        ini_set("memory_limit", "-1");
        ini_set('max_execution_time', 0);

        $id     = $request->get('id');
        $apiKey = $request->get('key');

        if ($id) {
            $invoice = Invoice::filterSource()
                ->whereId($id)
                ->firstOrFail();
        } else if ($apiKey) {
            $invoice = Invoice::filterSource()
                ->where('api_key', $apiKey)
                ->where('doc_id', $invoiceId)
                ->orderBy('id', 'desc')
                ->firstOrFail();
        } else {
            $invoice = Invoice::filterSource()
                ->where('customer_id', $customerId)
                ->where('doc_id', $invoiceId)
                ->firstOrFail();
        }

        try {

            $class = '\App\Models\\' . $invoice->target;
            $target = $class::where('customer_id', $customerId)
                ->where('id', $invoice->target_id)

                ->firstOrFail();

            $year   = $target->year;
            $month  = $target->month;
            $period = $target->period;

            $shipmentsIds = [
                'shipments' => $target->shipments,
                'covenants' => $target->covenants,
                'products'  => $target->products,
                'apiKey'    => $target->api_key,
                'invoice'   => @$invoice
            ];

            CustomerBilling::printShipments($customerId, $month, $year, 'pdf', $shipmentsIds, $period, null, $invoice);
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * Search customer by vat
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function searchCustomerByVat(Request $request)
    {

        try {
            $customer = Customer::filterSource()
                ->isActive()
                ->firstOrNew([
                    'vat' => $request->vat
                ]);

            $result = [
                'exists'    => $customer->exists,
                'vat'       => @$customer->vat,
                'code'      => @$customer->code,
                'name'      => @$customer->billing_name,
                'address'   => @$customer->billing_address,
                'zip_code'  => @$customer->billing_zip_code,
                'city'      => @$customer->billing_city,
                'country'   => @$customer->billing_country,
                'address'   => @$customer->billing_address,
                'agency_id' => @$customer->agency_id,
                'email'     => @$customer->billing_email,
                'condition' => @$customer->paymentCondition->code,
                'is_particular' => @$customer->is_particular,
                'billing_discount_value' => @$customer->billing_discount_value
            ];

            return response()->json($result);
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * Download Saft File
     * @param Request $request
     * @return mixed
     * @throws \Webit\GlsTracking\Api\Exception\Exception
     */
    public function showSaft(Request $request)
    {
        $companyId = $request->get('company');

        $allCompanies = Company::filterSource()
            ->get();

        $companies = [];
        foreach ($allCompanies as $company) {
            $companies[$company->id] = $company->vat . ' - ' . $company->name;
        }

        $companyId = empty($companyId) ? $allCompanies->first()->id : $companyId;

        $saftDB = Saft::where('source', config('app.source'))
            ->where('company_id', $companyId)
            ->get();

        //obtem o ano e mes inicial a partir da 1ª vez que se importaram os artigos para o sistema
        $item = Item::first();

        $firstDate = date('Y-m') . '-01';
        if ($item) {
            $firstDate = new Date($item->created_at);
            $firstDate->firstOfMonth()->format('Y-m-d');
        }

        $period = CarbonPeriod::create($firstDate, '1 month', date('Y-m') . '-01');


        $months = [];
        foreach ($period as $dt) {
            $months[] = $dt->format("Y-m");
        }

        $curYear  = date('Y');
        $curMonth = date('m');

        $safts = [];
        $months = array_reverse($months);
        foreach ($months as $key => $month) {

            $parts = explode('-', $month);
            $year  = $parts[0];
            $month = $parts[1];

            $current = false;
            $issued  = true;
            if ($year == $curYear && $month == $curMonth) {
                $current = true;
                $issued  = false;
            }

            if ($key == 1 && date('d') <= '5') {
                $issued = false;
            }

            $saft = $saftDB->filter(function ($item) use ($year, $month) {
                return $item->year == $year && $item->month == $month;
            })->first();

            if (empty($saft)) {
                $saft = new Saft();
                $saft->year   = $year;
                $saft->month  = $month;
                $saft->source = config('app.source');
                //$saft->issued = $issued;
                $saft->company_id = $companyId;
                $saft->save();
            }

            /*elseif ($key >= 1 && date('d') > 5) {
                $saft->issued = 1;
                $saft->save(); //se ja tiver passado o dia 5 de cada mes, força o saft a ficar marcado como submetido.
            }*/

            $safts[] = [
                'id'         => view('admin.invoices.sales.datatables.saft.month', compact('saft', 'current'))->render(),
                'created_at' => view('admin.invoices.sales.datatables.saft.created_at', compact('saft', 'current'))->render(),
                'created_by' => view('admin.invoices.sales.datatables.saft.created_by', compact('saft', 'current'))->render(),
                'download'   => view('admin.invoices.sales.datatables.saft.download', compact('saft', 'current'))->render()
            ];
        }

        return view('admin.invoices.sales.modals.saft', compact('safts', 'companies', 'companyId'))->render();
    }

    /**
     * Show modal to send saft email
     * @param Request $request
     * @return mixed
     * @throws \Webit\GlsTracking\Api\Exception\Exception
     */
    public function editSaftEmail(Request $request, $year, $month)
    {
        $companyId = $request->get('company');

        $saft = Saft::whereSource(config('app.source'))
            ->where('company_id', $companyId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        return view('admin.invoices.sales.modals.saft_email', compact('year', 'month', 'saft'))->render();
    }

    /**
     * Download Saft File
     * @param Request $request
     * @return mixed
     * @throws \Webit\GlsTracking\Api\Exception\Exception
     */
    public function sendSaftEmail(Request $request, $year, $month)
    {

        $input = $request->all();
        $input['email_cc'] = $request->get('email_cc', false);
        $companyId = $request->get('company');

        $email = validateNotificationEmails($input['email']);

        $str = null;
        if ($email['valid']) {
            $str = implode(';', $email['valid']);
        }
        Setting::set('accountant_email', $str);
        Setting::save();

        try {
            $response = Saft::sendMail($year, $month, $companyId, $input);
        } catch (\Exception $e) {
            $response = [
                'result' => false,
                'feedback' => $e->getMessage()
            ];
        }
        return Response::json($response);
    }

    /**
     * Download Saft File
     * @param Request $request
     * @return mixed
     * @throws \Webit\GlsTracking\Api\Exception\Exception
     */
    public function downloadSaft(Request $request, $year, $month)
    {
        try {
            $companyId = $request->get('company');
            Saft::download($year, $month, $companyId);
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * Download billing invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadZipDoc(Request $request)
    {

        $docType    = $request->get('doctype');
        $month      = $request->get('month', date('m'));
        $year       = $request->get('year', date('Y'));
        $startDate  = $request->get('start_date');
        $endDate    = $request->get('end_date');
        $mode       = 'pdf';

        try {
            $webservice = new Invoice();
            $docUrl = $webservice->getZipDocuments($docType, $month, $year, $mode, $startDate, $endDate);

            return response()->json([
                'result'    => true,
                'feedback'  => 'Ficheiro gerado com sucesso',
                'url'       => $docUrl,
                'title'     => 'Download (' . trans('admin/billing.types-list.' . $docType) . ' - ' . trans('datetime.list-month.' . $month) . ' ' . $year . ')'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result'    => false,
                'feedback'  => $e->getMessage(),
            ]);
        }
    }

    /**
     * Show modal to stettle no doc invoice
     *
     * @param Request $request
     * @param $invoiceId
     * @return string
     * @throws \Throwable
     */
    public function nodocSettleEdit(Request $request, $invoiceId = null)
    {

        $ids = $request->id;

        $invoice = null;
        if (empty($ids) && $invoiceId) {
            $invoice = Invoice::filterSource()
                ->where('doc_type', 'nodoc')
                ->findOrFail($invoiceId);
        }

        $paymentMethods = PaymentMethod::filterSource()
            ->isActive()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        return view('admin.invoices.sales.modals.settle_nodoc', compact('invoice', 'ids', 'paymentMethods'))->render();
    }

    /**
     * Store nodoc settle state
     *
     * @param Request $request
     * @param $invoiceId
     * @return mixed
     */
    public function nodocSettleStore(Request $request, $invoiceId)
    {

        $input = $request->all();
        $input['ids'] = $request->get('ids');
        $input['is_settle'] = $request->get('is_settle', 0);

        if (!empty($input['ids']) && empty($invoiceId)) {
            $ids = explode(',', $input['ids']);
        } else {
            $ids = [$invoiceId];
        }

        foreach ($ids as $invoiceId) {
            $invoice = Invoice::filterSource()
                ->where('doc_type', 'nodoc')
                ->findOrFail($invoiceId);

            $invoice->fill($input);
            $invoice->save();
        }

        return Redirect::back()->with('success', 'Documento liquidado com sucesso.');
    }


    /**
     * Show modal to stettle no doc invoice
     *
     * @param Request $request
     * @param $invoiceId
     * @return string
     * @throws \Throwable
     */
    public function editAutocreate(Request $request, $invoiceId = null)
    {

        $invoice = Invoice::filterSource()->find($invoiceId);

        $series  = Invoice::getApiKeys();

        $paymentMethods = PaymentMethod::filterSource()
            ->isActive()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $data = compact(
            'invoice',
            'paymentMethods',
            'series'
        );

        return view('admin.invoices.sales.modals.autocreate_invoice', $data)->render();
    }

    /**
     * Store autocreate document
     *
     * @param Request $request
     * @param $invoiceId
     * @return mixed
     */
    public function storeAutocreate(Request $request, $invoiceId)
    {

        $input = $request->all();
        $input['apiKey']         = $request->get('apiKey');
        $input['doc_date']       = $request->get('doc_date');
        $input['due_date']       = $request->get('doc_date');
        $input['doc_type']       = $request->get('doc_type');
        $input['payment_method'] = $request->get('payment_method');
        $input['payment_date']   = $request->get('payment_date');
        $input['billing_email']  = $request->get('billing_email');
        $input['send_email']     = $request->get('send_email');

        try {
            $invoice = Invoice::filterSource()->find($invoiceId);
            $invoice->autocreatePaymentDocument($input);
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }

        return Redirect::back()->with('success', 'Documento gerado com sucesso.');
    }

    /**
     * Show modal to edit inicial balance
     *
     * @param Request $request
     * @param $invoiceId
     * @return string
     * @throws \Throwable
     */
    public function editInitialBalance(Request $request)
    {

        $docDate = date('Y').'-01-01';

        if($request->get('entity') == 'providers') {
            $providersControllers = new PurchasesController();
            return $providersControllers->editInitialBalance($request);
        }
          
        $entityType = 'customers';

        $entities = Customer::with('sind_invoice', 'sinc_invoice')
            ->isProspect(false)
            ->isDepartment(false)
            ->orderBy('code', 'asc')
            ->get([
                'id',
                'code',
                'vat',
                'name',
                'billing_name',
                'billing_country'
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

        if($entity = 'providers') {
            $providersControllers = new PurchasesController();
            return $providersControllers->storeInitialBalance($request);
        }

        $debits  = array_filter($input['sind']);
        $credits = array_filter($input['sinc']);
        $dates   = array_filter($input['doc_date']);
  
        try {
            if($debits) {
                foreach($debits as $customerId => $total) {

                    $customer = Customer::find($customerId);

                    $invoice = Invoice::firstOrNew([
                        'doc_type'    => Invoice::DOC_TYPE_SIND,
                        'customer_id' => $customerId,
                        'is_deleted'  => 0,
                    ]);

                    if(empty($invoice->doc_total_pending) && !$invoice->is_settle) {

                        $docDate = @$dates[$customerId] ? $dates[$customerId] : $docDate;

                        //cria ou atualiza o saldo inicial a débito
                        $invoice->source            = $source;
                        $invoice->target            = 'Invoice';
                        $invoice->sort              = '19000000_0'; //força a ficar sempre em primeiro lugar
                        $invoice->customer_id       = $customerId;
                        $invoice->doc_type          = Invoice::DOC_TYPE_SIND;
                        $invoice->doc_series        = 'SIND';
                        $invoice->doc_date          = $docDate;
                        $invoice->due_date          = $docDate;
                        $invoice->doc_subtotal      = 0;
                        $invoice->doc_vat           = 0;
                        $invoice->doc_total         = $total;
                        $invoice->doc_total_debit   = $total;
                        $invoice->vat               = $customer->vat;
                        $invoice->billing_code      = $customer->code;
                        $invoice->billing_name      = $customer->billing_name;
                        $invoice->billing_address   = $customer->billing_address;
                        $invoice->billing_zip_code  = $customer->billing_zip_code;
                        $invoice->billing_city      = $customer->billing_city;
                        $invoice->billing_country   = $customer->billing_country;
                        $invoice->is_draft          = 0;
                        $invoice->save();
                    }
                }
            }
            

            if($credits) {
                foreach($credits as $customerId => $total) {

                    if($total > 0.00) {
                        $total = $total * -1;
                    }
                   
                    $customer = Customer::find($customerId);

                    $invoice = Invoice::firstOrNew([
                        'doc_type'    => Invoice::DOC_TYPE_SINC,
                        'customer_id' => $customerId,
                        'is_deleted'  => 0,
                    ]);

                    if(empty($invoice->doc_total_pending) && !$invoice->is_settle) {

                        $docDate = @$dates[$customerId] ? $dates[$customerId] : $docDate;

                        //cria ou atualiza o saldo inicial a crédito
                        $invoice->source            = $source;
                        $invoice->target            = 'Invoice';
                        $invoice->sort              = '19000001_0'; //força a ficar sempre em segundo lugar
                        $invoice->customer_id       = $customerId;
                        $invoice->doc_type          = Invoice::DOC_TYPE_SINC;
                        $invoice->doc_series        = 'SINC';
                        $invoice->doc_date          = $docDate;
                        $invoice->due_date          = $docDate;
                        $invoice->doc_subtotal      = 0;
                        $invoice->doc_vat           = 0;
                        $invoice->doc_total         = $total;
                        $invoice->doc_total_credit  = $total;
                        $invoice->vat               = $customer->vat;
                        $invoice->billing_code      = $customer->code;
                        $invoice->billing_name      = $customer->billing_name;
                        $invoice->billing_address   = $customer->billing_address;
                        $invoice->billing_zip_code  = $customer->billing_zip_code;
                        $invoice->billing_city      = $customer->billing_city;
                        $invoice->billing_country   = $customer->billing_country;
                        $invoice->is_draft          = 0;
                        $invoice->save();
                    }
                }
            }

        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }

        return Redirect::back()->with('success', 'Saldos gravados com sucesso.');
    }


    /**
     * Check divergences
     *
     * @param Request $request
     * @param $invoiceId
     * @return mixed
     */
    public function checkDivergences(Request $request)
    {


        $rowsPerPage = 25;
        $pages       = 10000;

        for ($i = 0; $i <= ($pages * $rowsPerPage); $i = $i + $rowsPerPage) {

            //obtem as faturas do sistema de faturação via API
            $invoiceGateway = new Document();
            $docs = $invoiceGateway->getDocumentslist('invoice', $i);

            if ($docs) {
                foreach ($docs as $doc) {

                    $divergence = InvoiceDivergence::firstOrNew([
                        'doc_id'     => $doc->IdDoc,
                        'doc_series' => $doc->DocSeries,
                        'doc_type'   => $doc->DocType
                    ]);

                    if ($divergence->exists) {
                        $i = 99999999999; //sai do ciclo
                        break;
                    } else {
                        $divergence->doc_id     = $doc->IdDoc;
                        $divergence->doc_series = $doc->DocSeries;
                        $divergence->doc_type   = $doc->DocType;
                        $divergence->date       = $doc->Date;
                        $divergence->vat        = $doc->NIF;
                        $divergence->total      = $doc->Total;
                        $divergence->save();
                    }
                }
            }
        }

        //tenta associar as faturas na tabela à fatura em sistema
        $divergences = InvoiceDivergence::whereNull('invoice_id')->get();
        $countDivergences = 0;
        $countNotFound = 0;
        foreach ($divergences as $divergence) {

            $invoice = Invoice::where('doc_id', $divergence->doc_id)
                ->where('doc_series_id', $divergence->doc_series)
                ->where('doc_type', 'invoice');
            if ($divergence->vat) {
                $invoice = $invoice->where('vat', $divergence->vat);
            } else {
                $invoice = $invoice->where(function ($q) {
                    $q->whereNull('vat');
                    $q->orWhere('vat', '999999999');
                    $q->orWhere('vat', '999999990');
                });
            }
            $invoice = $invoice->first();

            if ($invoice) {
                $divergence->invoice_id        = $invoice->id;
                $divergence->invoice_doc_total = $invoice->doc_total;
                $divergence->invoice_settle    = $invoice->is_settle;
                $divergence->has_divergence    = 0;

                if ($divergence->total != $invoice->doc_total) {
                    $divergence->has_divergence = 1;
                    $countDivergences++;
                }

                $divergence->save();
            } else {
                $countNotFound++;
            }
        }

        $result = [
            'notfound' => $countNotFound,
            'divergences' => $countDivergences
        ];

        dd($countNotFound . ' documentos não encontrados. ' . $countDivergences . ' valores divergentes.');
    }
}
