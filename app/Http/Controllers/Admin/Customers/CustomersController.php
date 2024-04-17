<?php

namespace App\Http\Controllers\Admin\Customers;

use App\Models\Billing\Item;
use App\Models\CustomerBusinessHistory;
use App\Models\Invoice;
use App\Models\InvoiceGateway\Base;
use App\Models\PackType;
use App\Models\PaymentCondition;
use App\Models\PaymentMethod;
use App\Models\PickupPoint;
use App\Models\ServiceGroup;
use App\Models\ShippingStatus;
use App\Models\ZipCode;
use Yajra\Datatables\Facades\Datatables;
use Jenssegers\Date\Date;
use Carbon\Carbon;
use App\Models\CustomerRanking;
use App\Models\CustomerRecipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Models\Agency;
use App\Models\Bank;
use App\Models\BankInstitution;
use App\Models\BillingZone;
use App\Models\CustomerBalance;
use App\Models\Meeting;
use App\Models\PriceTable;
use App\Models\Provider;
use App\Models\Route;
use App\Models\ShippingExpense;
use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\CustomerService;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Auth, Response, DB, Croppa, Setting, File, View;

class CustomersController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'customers';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',customers']);
    }

    /**
     * Display a listing of the resource
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if ($request->has('scode')) {
            $customer = Customer::filterAgencies()
                ->where('source', config('app.source'))
                ->isProspect(false)
                ->where('code', $request->scode)
                ->first(['id']);

            if ($customer) {
                return Redirect::route('admin.customers.edit', $customer->id);
            } else {
                return Redirect::back()->with('error', 'Cliente não encontrado ou não possui permissão para consultar o cliente');
            }
        }

        $sourceAgencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->whereSource(config('app.source'))
            ->pluck('id')
            ->toArray();

        $agencies = Auth::user()->listsAgencies(false);

        $types = CustomerType::remember(config('cache.query_ttl'))
            ->cacheTags(CustomerType::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $sellers = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies($sourceAgencies)
            //->isSeller(true)
            ->isOperator(false)
            ->where('id', '>', 1)
            ->pluck('name', 'id')
            ->toArray();

        $operators = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies($sourceAgencies)
            ->isOperator()
            ->where('id', '>', 1)
            ->pluck('name', 'id')
            ->toArray();

        $pricesTables = PriceTable::remember(config('cache.query_ttl'))
            ->cacheTags(PriceTable::CACHE_TAG)
            ->filterAgencies()
            ->isActive()
            ->pluck('name', 'id')
            ->toArray();

        $routes = Route::listsWithCode(Route::remember(config('cache.query_ttl'))
            ->cacheTags(Route::CACHE_TAG)
            ->filterSource()
            ->ordered()
            ->get());

        $paymentConditions = PaymentCondition::filterSource()
            ->isSalesVisible()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        if(hasModule('account_wallet')) {
            $paymentConditions = ['wallet'=> 'Pré-pagamento'] + $paymentConditions;
        }

        $banks = BankInstitution::listWithData();

        $recipientCounties = [];
        $recipientDistrict = $request->get('fltr_recipient_district');
        if ($request->has('fltr_recipient_district')) {
            $recipientCounties = trans('districts_codes.counties.pt.' . $recipientDistrict);
        }

        $unvalidated = Customer::filterSource()
            ->filterAgencies()
            ->where('is_active', 1)
            ->where('is_validated', 0)
            ->count();

        $data = compact(
            'types',
            'agencies',
            'sellers',
            'pricesTables',
            'routes',
            'operators',
            'recipientCounties',
            'unvalidated',
            'paymentConditions',
            'banks'
        );

        return $this->setContent('admin.customers.customers.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $action = 'Novo Cliente';

        $customer = new Customer;

        $customer->code = $customer->setCode(false);

        $types = CustomerType::remember(config('cache.query_ttl'))
            ->cacheTags(CustomerType::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $sellers = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->isOperator(false)
            ->pluck('name', 'id')
            ->toArray();

        $operators = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->isOperator()
            ->where('id', '>', 1)
            ->pluck('name', 'id')
            ->toArray();

        $routes = Route::listsWithCode(Route::remember(config('cache.query_ttl'))
            ->cacheTags(Route::CACHE_TAG)
            ->filterSource()
            ->ordered()
            ->get());

        $banks = BankInstitution::listWithData();

        $paymentConditions = PaymentCondition::filterSource()
            ->isSalesVisible()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $existingVats = Customer::filterSource()
            ->where(function ($q) {
                $q->where('vat', '<>', '999999990');
                $q->where('vat', '<>', '');
                $q->whereNotNull('vat');
            })
            ->pluck('vat')
            ->toArray();

        $vatBlocked = false;

        $agencies = Auth::user()->listsAgencies();

        $paymentMethods = PaymentMethod::filterSource()
            ->pluck('name', 'id')
            ->toArray() ?? [];

        $formOptions = array('route' => array('admin.customers.store'));

        $data = compact(
            'action',
            'formOptions',
            'customer',
            'types',
            'agencies',
            'sellers',
            'operators',
            'routes',
            'existingVats',
            'vatBlocked',
            'paymentConditions',
            'banks',
            'paymentMethods'
        );

        return $this->setContent('admin.customers.customers.create', $data);
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

        $action = 'Editar Cliente';

        $customer = Customer::filterAgencies()
            ->isProspect(false)
            ->findOrFail($id);

        //store map if dont exists
        if (!$customer->map_preview && $customer->map_lat && $customer->map_lng) {
            $customer->map_preview = $customer->storeMapPreview();
            $customer->save();
        }

        $ranking = CustomerRanking::getByCustomer($customer->id);

        $graphsData = CustomerRanking::whereCustomerId($id)
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $graphData = [];
        foreach ($graphsData as $data) {
            $graphData['labels'][]    = trans('datetime.month-tiny.' . $data->month) . '/' . $data->year;
            $graphData['shipments'][] = $data->shipments;
            $graphData['volumes'][]   = $data->volumes;
            $graphData['billing'][]   = $data->billing;
        }

        if (!empty($graphData)) {
            $graphData['labels']    = '"' . implode('","', $graphData['labels']) . '"';
            $graphData['shipments'] = implode(',', $graphData['shipments']);
            $graphData['volumes']   = implode(',', $graphData['volumes']);
            $graphData['billing']   = implode(',', $graphData['billing']);
        }

        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->filterCustomer($customer->id)
            ->showOnPricesTable()
            ->ordered()
            ->get();

        $allServices = $services;

        $servicesList = $services->filter(function ($item) {
            return !$item->is_collection;
        })
            ->pluck('name', 'id')
            ->toArray();

        $providersList = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $statusList = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingStatus::CACHE_TAG)
            ->filterSources()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $complementarServices = ShippingExpense::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingExpense::CACHE_TAG)
            ->filterSource()
            ->where(function ($q) {
                $q->isCustomerCustomization();
                $q->orWhere('type', ShippingExpense::TYPE_FUEL);
            })
            ->get();

        $products = Product::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingExpense::CACHE_TAG)
            ->filterSource()
            ->get();

        $types = CustomerType::remember(config('cache.query_ttl'))
            ->cacheTags(CustomerType::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $sourceAgencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterSource()
            ->pluck('id')
            ->toArray();

        $series = Invoice::groupBy('doc_series')
            ->where('doc_series_id', '<>', '')
            ->pluck('doc_series', 'doc_series_id')
            ->toArray();

        $vatRates = Invoice::getVatTaxes();

        $agencies = Auth::user()->listsAgencies();

        $sellers = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies($sourceAgencies)
            ->isOperator(false)
            //->isSeller(true)
            ->where('id', '>', 1)
            ->pluck('name', 'id')
            ->toArray();

        $operators = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies($sourceAgencies)
            ->isOperator()
            ->where('id', '>', 1)
            ->pluck('name', 'id')
            ->toArray();

        $routes = Route::listsWithCode(Route::remember(config('cache.query_ttl'))
            ->cacheTags(Route::CACHE_TAG)
            ->filterSource()
            ->ordered()
            ->get());

        $pricesTables = PriceTable::remember(config('cache.query_ttl'))
            ->cacheTags(PriceTable::CACHE_TAG)
            ->filterAgencies()
            ->isActive()
            ->pluck('name', 'id')
            ->toArray();

        $billingZones = BillingZone::remember(config('cache.query_ttl'))
            ->cacheTags(BillingZone::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'code')
            ->toArray();

        $billingZonesList = $billingZones;

        $departments = Customer::where('customer_id', $customer->id)
            ->pluck('name', 'id')
            ->toArray();

        $packTypes = PackType::remember(config('cache.query_ttl'))
            ->cacheTags(PackType::CACHE_TAG)
            ->filterSource()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $banks = BankInstitution::listWithData();

        $paymentConditions = PaymentCondition::filterSource()
            ->isSalesVisible()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $series = Invoice::groupBy('doc_series')
            ->where('doc_series_id', '<>', '')
            ->pluck('doc_series', 'doc_series_id')
            ->toArray();

        $servicesGroups = ServiceGroup::filterSource()
            ->ordered()
            ->get();

        $servicesGroupsList = ServiceGroup::filterSource()
            ->pluck('name', 'code')
            ->toArray();

        $pudoProviders = Provider::whereHas('pickupPoints')
            ->pluck('name', 'id')
            ->toArray();

        $duplicateRecipients = CustomerRecipient::where('customer_id', $customer->id)->getDuplicates();

        $btnRegularization = Invoice::where('customer_id', $customer->id)
            ->where('is_settle', 0)
            ->where('doc_type', \App\Models\Invoice::DOC_TYPE_INTERNAL_DOC)
            ->count();

        //bloqueia edição do campo NIF, caso este cliente já tenha documentos emitidos.
        $vatBlocked = Invoice::filterSource()
                ->where('customer_id', $customer->id)
                ->count();

        /* $vatBlocked = false;
        if (!empty($customer->vat) && $customer->vat != '999999990' && $customer->vat != '999999999') {
            $vatBlocked = Invoice::filterSource()
                ->where('customer_id', $customer->id)
                ->orWhere('vat', $customer->vat)
                ->count();
        } */

        $existingVats = [];
        if (!$vatBlocked) {
            $existingVats = Customer::filterSource()
                ->where(function ($q) {
                    $q->where('vat', '<>', '999999990');
                    $q->where('vat', '<>', '');
                    $q->whereNotNull('vat');
                })
                ->where('id', '<>', $customer->id)
                ->pluck('vat')
                ->toArray();
        }

        $totalUnpaid      = $customer->balance_total;
        $totalExpired     = $customer->balance_count_expired;

        $customer->origin_zone = $request->origin_zone;
        $pricesTableData  = $this->getPricesTableData($services, $customer, $servicesGroupsList, $request->get('origin_zone'), false);
        $rowsWeight       = @$pricesTableData['rows'];
        $rowsAdicional    = @$pricesTableData['adicional'];
        $pricesTableData  = @$pricesTableData['prices'];

        $defaultWeights = explode(',', Setting::get('default_weights'));

        $paymentMethods = PaymentMethod::filterSource()
            ->pluck('name', 'id')
            ->toArray() ?? [];

        $allBillingItems = Item::filterSource()
            ->isCustomerCustomizable()
            ->get();

        $years  = yearsArr(2016, date('Y'), true);
        $months = array_reverse(trans('datetime.list-month'), true);

        $formOptions = array('route' => array('admin.customers.update', $customer->id), 'method' => 'PUT');

        $data = compact(
            'customer',
            'action',
            'formOptions',
            'years',
            'months',
            'series',
            'types',
            'rowsWeight',
            'pricesTableData',
            'defaultWeights',
            'agencies',
            'servicesList',
            'statusList',
            'sellers',
            'operators',
            'providersList',
            'complementarServices',
            'totalUnpaid',
            'totalExpired',
            'pricesTables',
            'servicesGroups',
            'servicesGroupsList',
            'rowsAdicional',
            'billingZones',
            'routes',
            'departments',
            'duplicateRecipients',
            'ranking',
            'graphData',
            'existingVats',
            'packTypes',
            'vatBlocked',
            'series',
            'paymentConditions',
            'pudoProviders',
            'billingZonesList',
            'vatRates',
            'allServices',
            'banks',
            'products',
            'paymentMethods',
            'allBillingItems',
            'btnRegularization'
        );

        return $this->setContent('admin.customers.customers.edit', $data);
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
        Customer::flushCache(Customer::CACHE_TAG);

        $customer = Customer::filterAgencies()
            ->isProspect(false)
            ->findOrNew($id);

        if ($request->has('save') && $request->get('save') == 'settings') {
            return $this->saveSettings($request, $customer);
        }

        if ($request->has('save') && $request->get('save') == 'business') {
            return $this->saveBusiness($request, $customer);
        }

        $input = $request->all();
        $input['update_billing_software'] = $request->get('update_billing_software', false);
        $input['average_weight']          = $request->get('average_weight', false);
        $input['daily_report']            = $request->get('daily_report', false);
        $input['distance_from_agency']    = $request->get('distance_from_agency', false);
        $input['bank_code']               = $request->get('bank_code', null);
        $input['is_independent']          = $request->get('is_independent', false);

        $input['is_mensal'] = 1;
        if (@$input['payment_method'] == 'wallet') {
            $input['is_mensal'] = 0;
        }

        if(!empty($input['bank_code']) && (empty($input['bank_name']) || empty($input['bank_swift']))) {
            $bank = BankInstitution::where('code', $input['bank_code'])->first();
            $input['bank_name']  = $bank->bank_name;
            $input['bank_swift'] = $bank->bank_swift;
        } elseif(empty($input['bank_code'])) {
            $input['bank_name']    = null;
            $input['bank_swift']   = null;
        }

        if (isset($input['code']) && isset($input['vat'])) {

            //validate nif and code
            $customersExists = Customer::whereSource(config('app.source'))
                ->where(function ($q) use ($id, $input) {
                    $q->where('id', '<>', $id);
                    $q->where('code', $input['code']);
                    $q->where('vat', '<>', '999999990');
                    $q->orWhere(function ($q) use ($input, $id) {
                        $q->where('id', '<>', $id);
                        $q->where('vat', $input['vat']);
                        $q->where('vat', '<>', '');
                        $q->whereNotNull('vat');
                    });
                })
                ->get(['code', 'vat', 'name', 'id']);

            if (!$customersExists->isEmpty()) {
                $codeExists = $customersExists->filter(function ($item) use ($input) {
                    return $item->code == $input['code'];
                })->first();
                $vatExists  = $customersExists->filter(function ($item) use ($input) {
                    return $item->vat == $input['vat'];
                })->first();

                if (!empty($codeExists)) {
                    return Redirect::back()->withInput()->with('error', 'O código ' . $input['code'] . ' já está associado ao cliente: ' . $codeExists->name);
                }

                /*if(!empty($vatExists)) { //valida se o NIF já existe
                    return Redirect::back()->withInput()->with('error', 'O NIF ' . $input['vat'] . ' está associado ao cliente: ' . $vatExists->code . ' - ' . $vatExists->name. '(Ref. Interna: '.$vatExists->id.')');
                }*/
            }
        }

        if (!empty($input['billing_name'])) {
            $input['has_billing_info'] = true;
        } else {
            $input['has_billing_info'] = false;
            unset($input['billing_name'], $input['billing_address'], $input['billing_zip_code'], $input['billing_city']);
        }

        //clean variables to dont subscribe their values on db when save prices table
        if (isset($input['price_table_id'])) {
            $input['has_prices'] = true;
            unset($input['average_weight'], $input['is_parts_shop']);

            $input['prices_tables'] = null; //reset a todas as tabelas persobalizadas por grupo
            if (empty($input['price_table_id'])) { //destroy table if exists
                CustomerService::where('customer_id', $id)->forceDelete();
            }
        }

        //clean complementar services if price is empty
        if (!empty($input['custom_expenses'])) {
            $services = [];
            foreach ($input['custom_expenses'] as $key => $zones) {

                foreach ($zones as $zone => $price) {

                    if (!empty($price)) {
                        $services[$key][$zone] = $price;
                    }
                }
            }
            $input['custom_expenses'] = $services;
        }

        if ($customer->exists) {
            $exists =  true;
        } else {
            $exists = false;
        }

        if ($customer->validate($input)) {
            $customer->fill($input);
            $customer->is_prospect = 0;
            $customer->save();

            //grava na base de dados central
            $customer->storeOnCoreDB();

            if (!empty($customer->price_table_id)) {
                CustomerService::where('customer_id', $customer->id)->delete();
            }

            try {
                if ($input['update_billing_software'] && !empty($customer->vat) && !in_array($customer->vat, ['999999990', '999999999'])) {

                    $class = Base::getNamespaceTo('Customer');
                    $customerKeyinvoice = new $class();
                    $customerKeyinvoice->insertOrUpdateCustomer(
                        $customer->vat,
                        $customer->code,
                        $customer->billing_name,
                        $customer->billing_address,
                        $customer->billing_zip_code,
                        $customer->billing_city,
                        $customer->phone,
                        null,
                        $customer->billing_email ? $customer->billing_email : $customer->email,
                        $customer->obs,
                        $customer->billing_country,
                        $customer->payment_method,
                        $customer
                    );

                    if (config('app.source') == 'activos24') {
                        $onS3Customer = new \App\Models\InvoiceGateway\OnSearch\Customer();
                        $onS3Customer->insertOrUpdateCustomer($customer);
                    }
                }
            } catch (\Exception $e) {
                return Redirect::back()->with('error', $e->getMessage());
            }

            if ($exists) {
                return Redirect::back()->with('success', 'Dados gravados com sucesso.');
            } else {
                return Redirect::route('admin.customers.edit', $customer->id)->with('success', 'Dados gravados com sucesso.');
            }
        }

        return Redirect::back()->withInput()->with('error', $customer->errors()->first());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function saveSettings(Request $request, $customer)
    {

        $input = $request->all();
        $input['show_reference']        = $request->get('show_reference', false);
        $input['hide_budget_btn']       = $request->get('hide_budget_btn', false);
        $input['enabled_packages']      = $request->get('enabled_packages', []);
        $input['enabled_pudo_providers'] = $request->get('enabled_pudo_providers', []);
        $input['sms_enabled']           = $request->get('sms_enabled', false);
        $input['ignore_mass_billing']   = $request->get('ignore_mass_billing', false);
        $input['shipping_status_notify_recipient']  = $request->get('shipping_status_notify_recipient', false);
        $input['shipping_status_notify']            = $request->get('shipping_status_notify', false);
        $input['shipping_services_notify']          = $request->get('shipping_services_notify', false);
        $input['shipping_services_notify']          = $request->get('shipping_services_notify', false);

        $settings = $customer->settings;
        $settings['hide_menu_pickups']                  = $request->get('hide_menu_pickups', false);
        $settings['logistic_stock_only_available']      = $request->get('logistic_stock_only_available', false);
        $settings['logistic_hide_menu']                 = $request->get('logistic_hide_menu', false);
        $settings['show_shipment_attachments']          = $request->get('show_shipment_attachments', false);
        $settings['hide_btn_shipments']                 = $request->get('hide_btn_shipments', false);
        $settings['upload_shipment_attachments']        = $request->get('upload_shipment_attachments', false);
        $settings['hide_incidences_menu']               = $request->get('hide_incidences_menu', false);
        $settings['hide_products_sales']                = $request->get('hide_products_sales', false);
        $settings['customer_block_provider_labels']     = $request->get('customer_block_provider_labels', false);
        $settings['label_template']                     = $request->get('label_template', false);

        $customer->fill($input);
        $customer->settings = $settings;
        $result = $customer->save();

        //update departments configs
        $departments = Customer::where('customer_id', $customer->id)->get();
        foreach ($departments as $department) {
            $department->unpaid_invoices_limit  = $customer->unpaid_invoices_limit;
            $department->unpaid_invoices_credit = $customer->unpaid_invoices_credit;
            $department->monthly_plafound       = $customer->monthly_plafound;
            $department->active                 = $customer->active;
            $department->save();
        }

        if ($result) {
            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', 'Não foi possível gravar as alterações.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function saveBusiness(Request $request, $customer)
    {

        $input = $request->all();
        $input['pickup_daily'] = $request->get('pickup_daily', false);

        if (isset($input['business_status']) && $customer->business_status != $input['business_status']) {
            $history = new CustomerBusinessHistory();
            $history->customer_id = $customer->id;
            $history->status      = $input['business_status'];
            $history->operator_id = Auth::user()->id;
            $history->save();
        }

        $customer->fill($input);
        $result = $customer->save();

        if ($result) {
            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', 'Não foi possível gravar as alterações.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateLogin(Request $request, $id)
    {

        Customer::flushCache(Customer::CACHE_TAG);

        $customer  = Customer::filterAgencies()
            ->isProspect(false)
            ->findOrNew($id);

        $input = $request->all();
        $input['active']                 = !$request->get('active', false);
        $input['hide_old_shipments']     = $request->get('hide_old_shipments', false);
        $input['is_commercial']          = $request->get('is_commercial', 0);
        $input['hide_btn_shipments']     = $request->get('hide_btn_shipments', 0);
        $input['hide_budget_btn']        = $request->get('hide_budget_btn', false);
        $input['view_parent_shipments']  = $request->get('view_parent_shipments', false);
        $input['show_reference']         = $request->get('show_reference', false);
        $input['always_cod']             = $request->get('always_cod', false);
        $input['enabled_services']       = $request->get('enabled_services', []);
        $input['enabled_providers']      = $request->get('enabled_providers', []);
        $input['shipping_status_notify'] = $request->get('shipping_status_notify', []);
        $input['uncrypted_password']     = null;
        $input['shipping_status_notify_recipient'] = $request->get('shipping_status_notify_recipient', []);

        if (!$input['active']) { //se bloqueado
            $input['remember_token'] = null;
        }

        if (empty($customer->password) && empty($customer->email)) {
            $input['login_created_at'] = date('Y-m-d H:i:s'); //when login account is created

            $email = Customer::where('email', $input['email'])
                ->where('source', config('app.source'))
                ->first();

            if ($email) {
                return Redirect::back()->withInput()->with('error', 'Já existe outro cliente com o e-mail ' . $input['email']);
            }
        }

        $changePass = false;
        $feedback = 'Dados gravados com sucesso.';

        $rules = [];
        if ($customer->exists && empty($input['password'])) {
            $rules['display_name']  = 'required';
            $rules['email']         = 'required';
        } elseif ($customer->exists) {
            $changePass = true;
            $feedback = 'Palavra-passe alterada com sucesso.';
            if (!empty($customer->password)) {
                $rules['password'] = 'confirmed';
            }
        }

        $validator = Validator::make($input, $rules);

        if ($validator->passes()) {

            if (empty($input['password'])) {
                unset($input['password']);
            } else {
                $input['uncrypted_password'] = $input['password'];
                $input['password'] = bcrypt($input['password']);
            }

            $customer->fill($input);

            //delete image
            if ($input['delete_photo'] && !empty($customer->filepath)) {
                Croppa::delete($customer->filepath);
                $customer->filepath = null;
                $customer->filename = null;
                $customer->filehost = null;
            }

            //upload image
            if ($request->hasFile('image')) {

                if ($customer->exists && !empty($customer->filepath) && File::exists(public_path() . '/' . $customer->filepath)) {
                    Croppa::delete($customer->filepath);
                }

                if (!$customer->upload($request->file('image'), true, 20)) {
                    return Redirect::back()->withInput()->with('error', 'Não foi possível alterar a imagem do perfil.');
                }
            } else {
                $customer->save();
            }

            //send email with password
            if (@$input['send_email']) {

                $input['password'] = @$input['uncrypted_password'];

                Mail::send('emails.customers.password', compact('input', 'customer'), function ($message) use ($input, $customer) {
                    $message->to($customer->email)
                        ->from(config('mail.from.address'), config('mail.from.name'))
                        ->subject('Dados de acesso à área de cliente');
                });
            }

            //update all departaments
            $updateData = [
                'unpaid_invoices_limit'  => $customer->unpaid_invoices_limit,
                'unpaid_invoices_credit' => $customer->unpaid_invoices_credit
            ];
            Customer::where('customer_id', $customer->id)->update($updateData);



            return Redirect::back()->with('success', $feedback);
        }

        return Redirect::back()->withInput()->with('error', $validator->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        Customer::flushCache(Customer::CACHE_TAG);

        $customer = Customer::filterAgencies()
            ->isFinalConsumer(false)
            ->isProspect(false)
            ->whereId($id)
            ->firstOrFail();

        $invoices = Invoice::where('customer_id', $customer->id)->count();
        if ($invoices) {
            return Redirect::back()->with('error', 'Não é possível eliminar o cliente porque já existem documentos fiscais associados.');
        }

        //apaga do keyinvoice
        if (!empty($customer->vat) && !in_array($customer->vat, ['999999999', '999999990'])) {
            try {

                $class = Base::getNamespaceTo('Customer');
                $customerKeyinvoice = new $class();
                $customerKeyinvoice->destroyCustomer($customer->vat);
            } catch (\Exception $e) {
            }
        }

        $customer->email = '_' . time() . '_' . $customer->email;
        $customer->save();

        $result = $customer->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o cliente.');
        }

        return Redirect::back()->with('success', 'Cliente removido com sucesso.');
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

        Customer::flushCache(Customer::CACHE_TAG);

        $ids = explode(',', $request->ids);

        $result = Customer::filterSource()
            ->whereIn('id', $ids)
            ->isProspect(false)
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
    public function datatable(Request $request)
    {

        $data = Customer::with(['price_table' => function ($q) {
            $q->remember(config('cache.query_ttl'));
            $q->cacheTags(PriceTable::CACHE_TAG);
        }])
            ->with(['route' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Route::CACHE_TAG);
            }])
            ->with('seller')
            ->filterSource()
            ->filterAgencies()
            ->filterSeller()
            ->isProspect(false)
            ->isDepartment(false)
            ->select(
                'customers.*',
                DB::raw('CAST(code as UNSIGNED) as unsigned_code'),
                DB::raw('(select max(date) from shipments where shipments.customer_id = customers.id and deleted_at is null limit 0,1) as last_shipment'),
                DB::raw('(select count(date) from shipments where shipments.customer_id = customers.id and deleted_at is null) as total_shipments')
            );

        //filter active
        $value = $request->active;
        if ($request->has('active')) {
            $data = $data->where('is_active', $value);
        }

        //filter validated
        $value = $request->validated;
        if ($request->has('validated')) {
            $data = $data->where('is_validated', $value);
        }

        //filter code
        $value = $request->code;
        if ($request->has('code')) {
            $data = $data->where('code', $value);
        }

        //filter type
        $value = $request->type_id;
        if ($request->has('type_id')) {
            $data = $data->where('type_id', $value);
        }

        //filter country
        $value = $request->country;
        if ($request->has('country')) {
            $data = $data->where('country', $value);
        }

        //filter agency
        $value = $request->agency;
        if ($request->has('agency')) {
            $data = $data->where('agency_id', $value);
        }

        //filter seller
        $value = $request->seller;
        if ($request->has('seller')) {
            $data = $data->where('seller_id', $value);
        }

        //filter operator
        $value = $request->operator;
        if ($request->has('operator')) {
            $data = $data->where('operator_id', $value);
        }

        //filter payment method
        $value = $request->payment_method;
        if ($request->has('payment_method')) {
            $data = $data->where('payment_method', $value);
        }

        //filter particular
        $value = $request->particular;
        if ($request->has('particular')) {
            if ($value == '-1') {
                $data = $data->where('is_particular', 0);
            } else {
                $data = $data->where('is_particular', 1);
            }
        }

        //filter prices
        $value = $request->prices;
        if ($request->has('prices')) {
            if ($value == '-1') {
                $data = $data->where('has_prices', 0);
            } elseif ($value == '0') {
                $data = $data->where('has_prices', 1)->whereNull('price_table_id');
            } else {
                $data = $data->where('price_table_id', $value);
            }
        }

        //filter route
        $value = $request->route;
        if ($request->has('route')) {
            if ($value == '0') {
                $data = $data->whereNull('route_id');
            } else {
                $data = $data->where('route_id', $value);
            }
        }

        //filter webservices
        $value = $request->webservices;
        if ($request->has('webservices')) {
            $data = $data->where('has_webservices', $value);
        }

        //filter login
        $value = $request->login;
        if ($request->has('login')) {
            if ($value == '1') {
                $data = $data->whereNotNull('password');
            } elseif ($value == '2') {
                $data = $data->where('is_active', 0);
            } else {
                $data = $data->whereNull('password');
            }
        }

        //filter country
        $value = $request->country;
        if ($request->has('country')) {
            $data = $data->where('country', $value);
        }

        //filter billing country
        $value = $request->billing_country;
        if ($request->has('billing_country')) {
            $data = $data->where('billing_country', $value);
        }

        //filter last_shipment
        $value = $request->last_shipment;
        if ($request->has('last_shipment')) {
            $days = Setting::get('alert_max_days_without_shipments');
            $limitDate = Date::today()->subDays($days)->format('Y-m-d');

            if ($value == 1) { // <= N days
                $data = $data->having(DB::raw('last_shipment'), '>', $limitDate);
            } elseif ($value == 2) { // > N days
                $data = $data->having(DB::raw('last_shipment'), '<=', $limitDate);
            } elseif ($value == 3) { //empty shipments
                $data = $data->whereDoesntHave('shipments');
            }
        }

        //filter recipient district
        $district = $request->get('district');
        $county   = $request->get('county');
        if ($request->has('district') || $request->has('county')) {

            $zipCodes = ZipCode::remember(config('cache.query_ttl'))
                ->where('district_code', $district)
                ->where('country', Setting::get('app_country'));

            if ($county) {
                $zipCodes = $zipCodes->where('county_code', $county);
            }

            $zipCodes = $zipCodes->groupBy('zip_code')
                ->pluck('zip_code')
                ->toArray();

            $data = $data->where(function ($q) use ($zipCodes) {
                $q->where('country', Setting::get('app_country'));
                $q->whereIn(DB::raw('SUBSTRING(`zip_code`, 1, 4)'), $zipCodes);
            });
        }

        if (Auth::user()->isGuest()) {
            $data = $data->where('agency_id', '99999'); //hide data to gest agency role
        }

        $types = CustomerType::remember(config('cache.query_ttl'))
            ->cacheTags(CustomerType::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $agencies = Agency::withTrashed()->remember(config('cache.query_ttl'))->cacheTags(Agency::CACHE_TAG)->get(['code', 'color', 'name', 'id']);

        $agencies = $agencies->groupBy('id')->toArray();

        return Datatables::of($data)
            ->add_column('photo', function ($row) {
                return view('admin.partials.datatables.photo', compact('row'))->render();
            })
            ->edit_column('unsigned_code', function ($row) use ($agencies) {
                return view('admin.customers.customers.datatables.code', compact('row', 'agencies'))->render();
            })
            ->edit_column('name', function ($row) use ($types) {
                return view('admin.customers.customers.datatables.name', compact('row', 'types'))->render();
            })
            ->edit_column('wallet_balance', function ($row) use ($types) {
                return view('admin.customers.customers.datatables.wallet_balance', compact('row'))->render();
            })
            ->edit_column('seller_id', function ($row) {
                return view('admin.customers.customers.datatables.seller', compact('row'))->render();
            })
            ->edit_column('last_shipment', function ($row) {
                return view('admin.customers.customers.datatables.last_shipment', compact('row'))->render();
            })
            ->edit_column('phone', function ($row) {
                return view('admin.customers.customers.datatables.contacts', compact('row'))->render();
            })
            ->edit_column('country', function ($row) {
                return view('admin.customers.customers.datatables.country', compact('row'))->render();
            })
            ->add_column('login', function ($row) {
                return view('admin.customers.customers.datatables.login', compact('row'))->render();
            })
            ->edit_column('route', function ($row) {
                return view('admin.customers.customers.datatables.route', compact('row'))->render();
            })
            ->edit_column('prices', function ($row) {
                return view('admin.customers.customers.datatables.prices', compact('row'))->render();
            })
            ->edit_column('webservices', function ($row) {
                return view('admin.customers.customers.datatables.webservices', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.customers.customers.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }


    /**
     * APAGAR QUANDO POSSIVEL. INATIVO EM 25/09/2023
     *
     * @return Datatables
     */
    public function datatableBalance(Request $request, $customerId)
    {

        $data = CustomerBalance::whereHas('customer', function ($q) {
            $q->filterAgencies();
        })
            ->withInvoice()
            ->where('customers_balance.customer_id', $customerId)
            ->select([
                'customers_balance.*',
                'invoices.created_by',
                'invoices.payment_method',
                DB::raw('(select doc_total_pending from invoices where invoices.customer_id=customers_balance.customer_id and invoices.doc_type=customers_balance.doc_type and invoices.doc_id=customers_balance.doc_id and invoices.doc_series_id=customers_balance.doc_serie_id and is_settle=0 and (doc_total_pending is not null or doc_total_pending <> "") limit 0,1) as pending')
            ]);

        //filter hide payment notes or receipts
        $value = $request->hide_payments;
        if ($request->has('hide_payments')) {
            if ($value) {
                //$data = $data->whereNotIn('customers_balance.doc_type', ['payment-note','receipt','regularization']);
                $data = $data->where(function ($q) {
                    $q->whereNotIn('customers_balance.doc_type', ['payment-note', 'receipt', 'regularization']);
                    $q->whereRaw('not(customers_balance.doc_type = "invoice-receipt" and sense = "credit")'); //adicionado em 2023/03/23 e substituido o abaixo
                    //$q->orWhereRaw('(customers_balance.doc_type = "invoice-receipt" and sense = "debit")'); //oculta os registos negativos de pagamento das FR (linha de registo do recibo)
                });
            }
        }

        //filter sense
        $value = $request->sense;
        if ($request->has('sense')) {
            if ($value == 'hidden') {
                $data = $data->where('customers_balance.is_hidden', 1);
            } else {
                $data = $data->where('sense', $value)
                    ->where('customers_balance.is_hidden', 0);
            }
        }

        //filter is paid
        $value = $request->paid;
        if ($request->has('paid')) {
            if ($value == '2') {
                $data = $data->withTrashed()
                    ->whereNotNull('deleted_at');
            } elseif ($value == '3') {
                $data = $data->where('invoices.doc_total_pending', '>', '0.00')
                    ->whereRaw('invoices.doc_total_pending <> doc_total');
            } else {
                if ($value == 1) {
                    $data = $data->where('is_paid', $value);
                } else {
                    $data = $data->where('is_paid', $value)
                        ->whereNotIn('customers_balance.doc_type', ['receipt', 'regularization']);
                }
            }
        }

        //filter date min
        $dtMin = $request->get('date_min');
        if ($request->has('date_min')) {
            $dtMax = $dtMin;
            if ($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }
            $data = $data->whereBetween('date', [$dtMin, $dtMax]);
        }

        //filter expired
        $value = $request->expired;
        if ($request->has('expired')) {
            $data = $data->where('customers_balance.due_date', '<', $value);
        }

        //filter serie
        $value = $request->serie;
        if ($request->has('serie')) {
            $data = $data->whereIn('doc_serie_id', $value);
        }

        //filter year
        $value = $request->year;
        if ($request->has('year')) {
            $data = $data->whereRaw('YEAR(date) = ' . $value);
        }

        //filter month
        $value = $request->month;
        if ($request->has('month')) {
            $data = $data->whereRaw('MONTH(date) = ' . $value);
        }

        //filter doc id
        $value = $request->doc_id;
        if ($request->has('doc_id')) {
            $data = $data->where('customers_balance.doc_id', $value);
        }

        //filter doc type
        $value = $request->doc_type;
        if ($request->has('doc_type')) {
            $data = $data->whereIn('customers_balance.doc_type', $value);
        } else {
            $data = $data->where('customers_balance.doc_type', '<>', 'nodoc');
        }

        //filter operator
        $value = $request->operator;
        if ($request->has('operator')) {
            $data = $data->whereIn('invoices.created_by', $value);
        }

        //filter payment method
        $value = $request->payment_method;
        if ($request->has('payment_method')) {
            $data  = $data->whereIn('invoices.payment_method', $value);
        }

        //filter deleted
        $value = $request->deleted;
        if ($request->has('deleted') && empty($value)) {
            $data = $data->where('canceled', $value);
        }

        $today = Carbon::today();

        return Datatables::of($data)
            ->edit_column('date', function ($row) {
                $date = new Date($row->date);
                return $date->format('d F Y');
            })
            ->add_column('debit', function ($row) {
                return view('admin.billing.balance.datatables.documents.debit', compact('row'))->render();
            })
            ->add_column('credit', function ($row) {
                return view('admin.billing.balance.datatables.documents.credit', compact('row'))->render();
            })
            ->edit_column('doc_serie', function ($row) {
                return view('admin.billing.balance.datatables.documents.serie', compact('row'))->render();
            })
            ->edit_column('doc_type', function ($row) {
                return view('admin.billing.balance.datatables.documents.type', compact('row'))->render();
            })
            ->edit_column('is_paid', function ($row) {
                return view('admin.billing.balance.datatables.documents.paid', compact('row'))->render();
            })
            ->edit_column('due_date', function ($row) use ($today) {
                return view('admin.billing.balance.datatables.documents.due_date', compact('row', 'today'))->render();
            })
            ->edit_column('total', function ($row) {
                return view('admin.billing.balance.datatables.documents.total', compact('row', 'today'))->render();
            })
            ->add_column('pending', function ($row) {
                return view('admin.billing.balance.datatables.documents.pending', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.billing.balance.datatables.documents.actions', compact('row'))->render();
            })
            ->make(true);
    }


    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatableMeetings(Request $request, $customerId)
    {

        $data = Meeting::with('customer')
            ->with(['seller' => function ($q) {
                $q->withTrashed();
            }])
            ->where('customer_id', $customerId)
            ->select();

        //filter date min
        $dtMin = $request->get('date_min');
        if ($request->has('date_min')) {

            $dtMax = $dtMin;

            if ($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }

            $data = $data->whereBetween('date', [$dtMin, $dtMax]);
        }

        //filter status
        $value = $request->status;
        if ($request->has('status')) {
            $data = $data->where('status', $value);
        }

        //filter seller
        $value = $request->seller;
        if ($request->has('seller')) {
            $data = $data->where('seller_id', $value);
        }

        return Datatables::of($data)
            ->edit_column('date', function ($row) {
                return view('admin.meetings.datatables.date', compact('row'))->render();
            })
            ->edit_column('seller_id', function ($row) {
                return view('admin.meetings.datatables.seller', compact('row'))->render();
            })
            ->edit_column('objectives', function ($row) {
                return view('admin.meetings.datatables.objectives', compact('row'))->render();
            })
            ->edit_column('occurrences', function ($row) {
                return view('admin.meetings.datatables.occurrences', compact('row'))->render();
            })
            ->edit_column('charges', function ($row) {
                return view('admin.meetings.datatables.charges', compact('row'))->render();
            })
            ->edit_column('status', function ($row) {
                return view('admin.meetings.datatables.status', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.meetings.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Store provider services and prices.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeServices(Request $request, $id)
    {

        Customer::flushCache(Customer::CACHE_TAG);

        $originZone = $request->get('origin_zone', null);
        $originZone = empty($originZone) ? null : $originZone;

        $customer = Customer::filterAgencies()
            ->findOrfail($id);

        $input = $request->all();

        if ($request->has('source') && $request->get('source') == 'group-price-table') {
            $customPricesTables = $input['prices_tables'] + $customer->prices_tables;
            // array_merge($customer->prices_tables, $input['prices_tables']);
            $customPricesTables = array_filter($customPricesTables);

            $customer->prices_tables = $customPricesTables;
            $customer->has_prices    = true;
            $customer->save();

            $servicesIds = Service::whereIn('group', array_keys($customPricesTables))->pluck('id')->toArray();

            //apaga todos os preços gravados na tabela para este cliente e estes grupos
            $oldData = CustomerService::where('customer_id', $id)
                ->whereIn('service_id', $servicesIds);
            if ($originZone) {
                $oldData = $oldData->where('origin_zone', $originZone);
            } else {
                $oldData = $oldData->whereNull('origin_zone');
            }
            $oldData->forceDelete();
        } else {

            if ($request->has('max')) {
                asort($input['max']);

                $unities  = $input['max'];
                $services = $input['price'];
                $adicionalRows    = $input['is_adicional'];
                $adicionalUnities = $input['adicional_unity'];
            }


            $detachServiceIds = Service::where('group', $input['group'])
                ->pluck('id')
                ->toArray();

            $customer->has_prices = 0;
            $customer->price_table_id = null;
            //$customer->services()->detach($detachServiceIds); //metodo anrtigo antes da variavel origin_zone

            //apaga todos os preços gravados na tabela para este cliente e os serviços do grupo atual
            $oldData = CustomerService::where('customer_id', $id)
                ->whereIn('service_id', $detachServiceIds);
            if ($originZone) {
                $oldData = $oldData->where('origin_zone', $originZone);
            } else {
                $oldData = $oldData->whereNull('origin_zone');
            }
            $oldData->forceDelete();


            if ($request->has('max')) {
                $nextMinValue = 0;

                foreach ($unities as $rowNumber => $max) {

                    foreach ($services as $serviceId => $zones) {

                        if (empty($zones)) {
                            $zones = [Setting::get('app_country')];
                        }

                        $isAdicional    = 0;
                        $adicionalUnity = null;
                        if (@$adicionalRows[$rowNumber]) {
                            $isAdicional = 1;
                            $adicionalUnity = @$adicionalUnities[$rowNumber] ? @$adicionalUnities[$rowNumber] : 1;
                        }

                        foreach ($zones as $zone => $data) {
                            if (!empty($data[$rowNumber])) { //if exists one or more price, exists table
                                $customer->has_prices = true;
                            }

                            $customer->services()->attach($serviceId, [
                                'origin_zone' => $originZone,
                                'zone'  => $zone,
                                'min'   => $nextMinValue,
                                'max'   => $max,
                                'price' => $data[$rowNumber],
                                'is_adicional'    => $isAdicional,
                                'adicional_unity' => $adicionalUnity
                            ]);
                        }
                    }

                    $nextMinValue = $max + 0.01;
                }
            }
        }

        //dd($customer->has_prices);
        $customer->save();


        return Redirect::back()->with('success', 'Tabela de preços gravada com sucesso.');
    }

    /**
     * Copy services from another user
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function importServices(Request $request, $id)
    {

        Customer::flushCache(Customer::CACHE_TAG);

        $input = $request->all();

        $customer = Customer::filterAgencies()->findOrFail($id);

        if (@$input['source'] == 'prices-table') {
            $sourceCustomer = PriceTable::filterAgencies()->findOrFail($input['import_global_prices_id']);
        } else {
            $sourceCustomer = Customer::filterAgencies()->findOrFail($input['import_customer_id']);
        }

        if (!empty($input['import_target'])) {

            $targetServices = Service::filterAgencies()
                ->where('group', $input['import_target'])
                ->ordered()
                ->pluck('id')
                ->toArray();

            $services = $sourceCustomer->services->filter(function ($item) use ($targetServices) {
                return in_array($item->id, $targetServices);
            });
        } else {
            $services = $sourceCustomer->services;
        }

        if (empty($services)) {
            return Redirect::back()->with('warning', 'O cliente que selecionou não tem definida nenhuma tabela de preços.');
        }

        if (!empty($input['import_target'])) {
            $customer->services()->wherePivotIn('service_id', $targetServices)->detach();
        } else {
            $customer->services()->detach();
        }



        foreach ($services as $service) {
            $customer->services()->attach($service->pivot->service_id, [
                'zone'  => $service->pivot->zone,
                'min'   => $service->pivot->min,
                'max'   => $service->pivot->max,
                'price' => $service->pivot->price,
                'is_adicional'    => $service->pivot->is_adicional,
                'adicional_unity' => $service->pivot->adicional_unity
            ]);
        }

        return Redirect::back()->with('success', 'Tabela de preços importada com sucesso.');
    }

    /**
     * Start a remote login 
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function remoteLogin(Request $request, $customerId)
    {

        $customer = Customer::findOrFail($customerId);

        if ($customer->password) {

            $result = Auth::guard('customer')->login($customer);

            return Redirect::route('account.index')->with('success', 'Sessão iniciada com sucesso.');
        }
        return Redirect::back()->with('error', 'O cliente não possui conta criada.');
    }

    /**
     * Search customers on DB
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchCustomer(Request $request)
    {

        $search = $request->get('q');
        $search = '%' . str_replace(' ', '%', $search) . '%';

        $withDepartments = $request->get('with_departments', false);

        try {
            $customers = Customer::filterAgencies()
                ->filterSeller()
                ->isProspect(false)
                ->where(function ($q) use ($search) {
                    $q->where('code', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search)
                        ->orWhere('vat', 'LIKE', $search)
                        ->orWhere('phone', 'LIKE', $search);
                });
            
            if (!$withDepartments) {
                $customers = $customers->isDepartment(false);
            }

            $customers = $customers->get(['id', 'code', 'name', 'address', 'zip_code', 'city', 'country', 'mobile', 'phone']);

            if ($customers) {

                $results = array();
                foreach ($customers as $customer) {
                    $results[] = [
                        'id'       => $customer->id,
                        'text'     => $customer->code . ' - ' . str_limit($customer->name, 40),
                        'address'  => $customer->address,
                        'zip_code' => $customer->zip_code,
                        'city'     => $customer->city,
                        'country'  => $customer->country,
                        'phone'    => $customer->mobile ? $customer->mobile : $customer->phone,
                    ];
                }
            } else {
                $results = [['id' => '', 'text' => 'Nenhum cliente encontrado.']];
            }
        } catch (\Exception $e) {
            $results = [['id' => '', 'text' => 'Erro interno ao processar o pedido.']];
        }

        return Response::json($results);
    }

    /**
     * Inactivate customer and send it to prospects list
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function inactivate($id)
    {

        Customer::flushCache(Customer::CACHE_TAG);

        $customer  = Customer::filterAgencies()->findOrNew($id);

        $customer->is_active = !$customer->is_active;
        $customer->active = $customer->is_active; //bloqueo de acesso à conta de cliente
        $customer->remember_token = null;
        $customer->save();

        $feedback = $customer->is_active ? 'Cliente ativo com sucesso' : 'Cliente inativo com sucesso.';

        return Redirect::back()->with('success', $feedback);
    }

    /**
     * Convert customer to prospect and send it to prospects list
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function convertProspect($id)
    {

        Customer::flushCache(Customer::CACHE_TAG);

        $prospect  = Customer::filterAgencies()->findOrNew($id);

        $prospect->is_prospect = 1;
        $prospect->save();

        return Redirect::back()->with('success', 'Cliente convertido com sucesso.');
    }

    /**
     * Inactivate all selected resources from storage.
     * GET /admin/customers/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massInactivate(Request $request)
    {

        Customer::flushCache(Customer::CACHE_TAG);


        if ($request->ids == 'all') {
            $days = $request->inactivate_limit_days;
            $limitDate = Date::today()->subDays($days)->format('Y-m-d');

            $bindings = [
                'customers.id',
                'customers.code',
                'customers.name',
                DB::raw('(select max(date) from shipments where shipments.customer_id = customers.id and deleted_at is null limit 0,1) as last_shipment'),
            ];

            $customersInactive = Customer::filterSource()
                ->filterSeller()
                ->isProspect(false)
                ->isDepartment(false)
                ->isActive()
                ->having(DB::raw('last_shipment'), '<=', $limitDate)
                ->select($bindings)
                ->pluck('id')
                ->toArray();

            $emptyShipments = Customer::filterSource()
                ->filterSeller()
                ->isProspect(false)
                ->isDepartment(false)
                ->isActive()
                ->whereDoesntHave('shipments')
                ->pluck('id')
                ->toArray();

            $ids = array_merge($customersInactive, $emptyShipments);
        } else {
            $ids = explode(',', $request->ids);
        }

        $result = Customer::whereIn('id', $ids)->update(['is_active' => 0]);


        if (!$result) {
            return Redirect::back()->with('error', 'Não foi possível inativar os registos selecionados');
        }

        return Redirect::back()->with('success', 'Registos selecionados inativados com sucesso.');
    }

    /**
     * Assign same prices table to all selected customers
     * GET /admin/customers/selected/mass-assign-customers
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massUpdate(Request $request)
    {

        Customer::flushCache(Customer::CACHE_TAG);

        $ids = explode(',', $request->ids);

        $updateFields = [];

        //agency
        if (!empty($request->assign_agency_id)) {
            $updateFields['agency_id'] = $request->assign_agency_id;
        }

        //seller
        if (!empty($request->assign_seller_id)) {
            $updateFields['seller_id'] = $request->assign_seller_id;
            if ($updateFields['seller_id'] == '-1') {
                $updateFields['seller_id'] = null;
            }
        }

        //route
        if (!empty($request->assign_route_id)) {
            $updateFields['route_id'] = $request->assign_route_id;
            if ($updateFields['route_id'] == '-1') {
                $updateFields['route_id'] = null;
            }
        }

        //price table
        if (!empty($request->assign_price_table_id)) {
            $updateFields['price_table_id'] = $request->assign_price_table_id;
            $updateFields['has_prices']     = 1;
        }

        //type
        if (!empty($request->assign_type_id)) {
            $updateFields['type_id'] = $request->assign_type_id;
        }

        //payment method
        if (!empty($request->assign_payment_method)) {
            $updateFields['payment_method'] = $request->assign_payment_method;

            if (@$updateFields['payment_method'] == 'wallet') {
                $updateFields['is_mensal'] = 0;
            } else {
                $updateFields['is_mensal'] = 1;
            }
        }

        if(!empty(@$request->bank_code) && (empty(@$request->bank_name) || empty($request->bank_swift))) {
            $bank = BankInstitution::where('code', $input['bank_code'])->first();
            $updateFields['bank_code']  = $bank->code;
            $updateFields['bank_name']  = $bank->bank_name;
            $updateFields['bank_swift'] = $bank->bank_swift;
        }

        //contact_email
        if (!empty($request->contact_email)) {
            $updateFields['contact_email'] = $request->contact_email;
        }

        $result = false;
        if (!empty($updateFields)) {
            $result = Customer::whereIn('id', $ids)->update($updateFields);
        }

        if (!$result) {
            return Redirect::back()->with('error', 'Não foi possível atualizar os envios selecionados');
        }

        return Redirect::back()->with('success', 'Registos selecionados atualizados com sucesso.');
    }

    /**
     * Inactivate all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function listEmails(Request $request)
    {

        $ids = explode(',', $request->ids);

        $emails = Customer::filterAgencies()
            ->where('contact_email', '<>', '');

        if ($request->has('agency')) {
            $emails = $emails->where('agency_id', $request->get('agency'));
        }

        if ($request->has('payment_method')) {
            $emails = $emails->where('payment_method', $request->get('payment_method'));
        }

        if ($request->has('type')) {
            $emails = $emails->where('type_id', $request->get('type'));
        }

        $emails = $emails->select(['contact_email'])
            ->pluck('contact_email')
            ->toArray();

        $emails = implode(';', $emails);

        return [
            'total' => count($emails),
            'emails' => strtolower($emails)
        ];
    }

    /**
     * Return prices table data
     * @param $services
     * @return array
     */
    public function getPricesTableData($services, $customer, $servicesGroupsList = null, $originZone = null, $withData = true)
    {

        if (!$servicesGroupsList) {
            $servicesGroupsList = ServiceGroup::filterSource()->pluck('name', 'code')->toArray();
        }

        $assignedPricesTables = $customer->prices_tables;

        $pricesTables  = [];
        $rowsAdicional = [];
        $rows = [];


        $allGroupRows = CustomerService::with(['service' => function ($q) {
            $q->remember(config('cache.query_ttl'));
            $q->cacheTags(Service::CACHE_TAG);
        }])
            ->whereHas('service', function ($q) use ($servicesGroupsList) {
                $q->whereIn('group', array_keys($servicesGroupsList));
            });

        if (empty($originZone)) {
            $allGroupRows = $allGroupRows->whereNull('origin_zone');
        } else {
            $allGroupRows = $allGroupRows->where('origin_zone', $originZone);
        }

        if ($customer->price_table_id) {
            $allGroupRows = $allGroupRows->where('price_table_id', $customer->price_table_id);
        } else {
            $allGroupRows = $allGroupRows->where('customer_id', $customer->id);

            if (!empty($assignedPricesTables)) { //exclui dos resultados os ID dos serviços que pertençam a grupos que têm uma tabela definida
                $customServicesIds  = Service::whereIn('group', array_keys($assignedPricesTables))->pluck('id')->toArray();
                $allGroupRows = $allGroupRows->whereNotIn('service_id', $customServicesIds);
            }
        }

        $allGroupRows = $allGroupRows->get();

        //junta à variavel $allGroupRows as linhas de preço dos serviços  que pertençam a grupos que têm uma tabela definida
        if (!empty($assignedPricesTables)) {

            foreach ($assignedPricesTables as $groupCode => $priceTableId) {
                $priceTableRows = CustomerService::with(['service' => function ($q) {
                    $q->remember(config('cache.query_ttl'));
                    $q->cacheTags(Service::CACHE_TAG);
                }])
                    ->whereHas('service', function ($q) use ($groupCode) {
                        $q->where('group', $groupCode);
                    });

                if (empty($originZone)) {
                    $priceTableRows = $priceTableRows->whereNull('origin_zone');
                } else {
                    $priceTableRows = $priceTableRows->where('origin_zone', $originZone);
                }

                $priceTableRows = $priceTableRows->where('price_table_id', $priceTableId)
                    ->get();


                if (!$priceTableRows->isEmpty()) {
                    $allGroupRows = $allGroupRows->merge($priceTableRows);
                }
            }
        }

        foreach ($servicesGroupsList as $groupCode => $groupName) {

            $groupServices = $services->filter(function ($item) use ($groupCode) {
                return $item->group == $groupCode;
            });

            /**
             * Only load group service services 
             * and don't load the table information
             */
            if (!$withData) {
                $pricesTables[$groupCode] = $groupServices;
                $rows[$groupCode] = [];
                $rowsAdicional[$groupCode] = [];
                continue;
            }

            $groupRows = $allGroupRows->filter(function ($item) use ($groupCode) {
                return $item->service->group == $groupCode;
            });

            $groupRows = $groupRows->sortBy('min')->groupBy('max');

            $arr = [];
            $adicionalRows = [];

            foreach ($groupRows as $weight => $row) {
                $service = $row->groupBy('service_id');
                $rowServices = [];
                foreach ($service as $serviceId => $zone) {
                    $zones = $zone->groupBy('zone')->toArray();
                    $rowServices[$serviceId] = $zones;
                }
                $arr[$weight] = $rowServices;

                $adicionalRows[$weight] = [
                    'is_adicional'    => @$row[0]['is_adicional'],
                    'adicional_unity' => @$row[0]['adicional_unity'],
                ];
            }

            $groupRows = $arr;

            if (!$groupServices->isEmpty()) {
                $pricesTables[$groupCode] = $groupServices;
                $rows[$groupCode] = $groupRows;
                $rowsAdicional[$groupCode] = $adicionalRows;
            }
        }

        return [
            'prices'    => $pricesTables,
            'adicional' => $rowsAdicional,
            'rows'      => $rows
        ];
    }

    /**
     * Render price table
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $customerId
     * @param int $groupId
     * @return string
     */
    public function priceTable(Request $request, int $customerId, int $groupId) {
        $customer = Customer::filterAgencies()
            ->findOrFail($customerId);

        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->filterCustomer($customer->id)
            ->showOnPricesTable()
            ->ordered()
            ->get();

        $servicesGroups = ServiceGroup::filterSource()
            ->where('id', $groupId)
            ->ordered()
            ->get();

        $servicesGroupsList = ServiceGroup::filterSource()
            ->where('id', $groupId)
            ->pluck('name', 'code')
            ->toArray();

        $pricesTables = PriceTable::remember(config('cache.query_ttl'))
            ->cacheTags(PriceTable::CACHE_TAG)
            ->filterAgencies()
            ->isActive()
            ->pluck('name', 'id')
            ->toArray();

        $billingZones = BillingZone::remember(config('cache.query_ttl'))
            ->cacheTags(BillingZone::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'code')
            ->toArray();

        $billingZonesList = $billingZones;

        $customer->origin_zone = $request->origin_zone;

        $pricesTableData  = $this->getPricesTableData($services, $customer, $servicesGroupsList, $request->get('origin_zone'));
        $rowsWeight       = @$pricesTableData['rows'];
        $rowsAdicional    = @$pricesTableData['adicional'];
        $pricesTableData  = @$pricesTableData['prices'];

        $data = compact(
            'customer', 'services', 'servicesGroups', 'servicesGroupsList', 'pricesTables',
            'billingZones', 'billingZonesList', 'rowsWeight', 'rowsAdicional', 'pricesTableData'
        );

        return view('admin.customers.customers.partials.prices.price_table_data', $data)->render();
    }


    /**
     * Reset balance account
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function resetBalance($id)
    {

        $result = CustomerBalance::where('customer_id', $id)
            ->whereNull('receipt_part')
            ->forceDelete();

        Customer::where('customer_id', $id)->update([
            'balance_total_unpaid' => 0,
            'balance_count_unpaid' => 0
        ]);

        $feedback = $result ? 'Reset concluído com sucesso' : 'Falha ao fazer reset.';

        return Redirect::back()->with('success', $feedback);
    }

    /**
     * Validate customers
     *
     * @param Request $request
     * @return string
     * @throws \Throwable
     */
    public function validateCustomers(Request $request)
    {

        $customers = Customer::filterSource()
            ->filterAgencies()
            ->where('is_active', 1)
            ->where('is_validated', 0);

        if ($request->has('customer')) {
            $customers = $customers->where('id', $request->customer);
        }

        $customers = $customers->get();

        return view('admin.customers.customers.modals.validate_customers', compact('customers'))->render();
    }

    /**
     * Validate or invalidade customer
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeValidation(Request $request, $id)
    {

        $validated = $request->get('validated');
        $validated = $validated ? 1 : 0;

        try {
            $customer = Customer::filterSource()
                ->filterAgencies()
                ->find($id);

            $customer->is_validated = $validated;
            $customer->is_active    = $validated ? 1 : 0;
            $customer->save();

            //send wellcome message
            $subject = 'O seu pedido de registo foi aprovado.';
            if (!$validated) {
                $subject = 'O seu pedido de registo foi rejeitado.';
            }

            Mail::send('emails.customers.validated', compact('customer'), function ($message) use ($customer, $subject) {
                $message->to($customer->email)
                    ->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject($subject);
            });

            return response()->json([
                'result'    => true,
                'feedback'  => 'Validado com sucesso.',
                'validated' => $validated
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result'    => false,
                'feedback'  => $e->getMessage(),
                'validated' => false
            ]);
        }
    }

    /**
     * Search banks
     * 
     * @return type
     */
    public function searchBanksInstitutions(Request $request)
    {

        $search = $request->get('q');
        $search = '%' . str_replace(' ', '%', $search) . '%';

        try {

            $banks = BankInstitution::where(function ($q) use ($search) {
                    $q->where('code', 'LIKE', $search)
                        ->orWhere('bank_name', 'LIKE', $search)
                        ->orWhere('bank_swift', 'LIKE', $search)
                        ->orWhere('bank_iban', 'LIKE', $search);
                })
                ->get();

            if ($banks) {

                $results = array();
                foreach ($banks as $bank) {
                    $results[] = array(
                        'id' => $bank->code, 
                        'text' => '[' . strtoupper($bank->country) . '] ' . $bank->bank_code . ' - '.$bank->bank_name,
                        'name'  => $bank->bank_name,
                        'swift' => $bank->bank_swift,
                        'code'  => $bank->bank_code
                    );
                }
            } else {
                $results = [[
                    'id' => '',
                    'text' => 'Nenhum banco encontrado.'
                    ]];
            }
        } catch (\Exception $e) {
            $results = [['id' => '', 'text' => 'Erro interno ao processar o pedido.']];
        }

        return response()->json($results);
    }


    /**
     * Create new customer
     *
     * @param Request $request
     * @return array
     */
    public function createCustomerFromModal(Request $request)
    {
        try {
            $input = $request->all();
            $input['agency_id'] = $request->get('customer_agency_id');

            $customer = new Customer();

            if ($customer->validate($input)) {
                $customer->fill($input);
                $customer->setCode();

                $customer = $customer->toArray();
                $customer['payment_condition'] = $customer['payment_method'];

                $response = [
                    'result'   => true,
                    'feedback' => 'Cliente criado com sucesso.',
                    'customer' => $customer
                ];
            } else {
                $response = [
                    'result'   => false,
                    'feedback' => $customer->errors()->first()
                ];
            }
        } catch (\Exception $e) {
            $response = [
                'result'   => false,
                'feedback' => 'Falha na criação do cliente. ' . $e->getMessage()
            ];
        }

        return response()->json($response);
    }


    /**
     * Create new mandate code
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createMandate(Request $request)
    {

        $customer = new Customer();
        $code = $customer->setBankMandateCode(false);

        $response = [
            'result'       => true,
            'feedback'     => 'Criado com sucesso',
            'mandate_code' => $code,
            'mandate_date' => date('Y-m-d')
        ];

        return response()->json($response);
    }
}
