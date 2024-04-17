<?php

namespace App\Http\Controllers\Admin\Billing;

use App\Http\Controllers\Admin\Exports\BillingController;
use App\Models\CalendarEvent;
use App\Models\PackType;
use App\Models\PaymentCondition;
use App\Models\PaymentMethod;
use App\Models\Route;
use App\Models\ShippingExpense;
use DB, Auth, Response, Setting, Mail;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Jenssegers\Date\Date;
use Yajra\Datatables\Facades\Datatables;

use App\Models\Billing;
use App\Models\Item;
use App\Models\BillingZone;
use App\Models\CustomerCovenant;
use App\Models\CustomerType;
use App\Models\Invoice;
use App\Models\ShipmentExpense;
use App\Models\User;
use App\Models\Customer;
use App\Models\Shipment;
use App\Models\Service;
use App\Models\Provider;
use App\Models\Agency;
use App\Models\ShippingStatus;
use App\Models\CustomerBilling;
use App\Models\ProductSale;

class CustomersController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'billing';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',billing']);
        validateModule('billing_customers');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $month      = $request->has('month') ? $request->month : date('n');
        $year       = $request->has('year') ? $request->year : date('Y');
        $period     = $request->has('period') ? $request->period : '30d';
        $years      = yearsArr(2016, date('Y') + 1, true);
        $curPeriod  = Setting::get('billing_method') ? Setting::get('billing_method') : '30d';

        if ($month == date('n') && date('d') <= '5') {
            $month = date("n", strtotime("previous month"));

            if (date('n') == '1') {
                $year = $year - 1;
            }
        }

        if ($curPeriod != '30d') {
            if (date('d') < '16') {
                $curPeriod = '1q';
            } else {
                $curPeriod = '2q';
            }
        }

        $months = trans('datetime.list-month');
        /*if($year == date('Y')) {
            $curMonth = 2; date('m');

            $months = [];
            for ($i = 1 ; $i < $curMonth + 1 ; $i++) {
                if($curMonth <= 12) {
                    $months[$i] = trans('datetime.list-month.' . $i);
                }
            }
        }*/

        //previne faturas deste mes que estejam com billing_date a 0000-00-00
        \DB::statement("UPDATE shipments SET billing_date=date WHERE billing_date='0000-00-00' and date between '".$year."-".$month."-01' and '".$year."-".$month."-31'");

        $agencies = Auth::user()->listsAgencies();

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $types = CustomerType::remember(config('cache.query_ttl'))
            ->cacheTags(CustomerType::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $sellers = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterSource()
            ->isSeller()
            ->where('id', '>', 1)
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

        $data = compact(
            'paymentConditions',
            'years',
            'months',
            'agencies',
            'providers',
            'year',
            'month',
            'period',
            'curPeriod',
            'types',
            'sellers',
            'routes'
        );

        return $this->setContent('admin.billing.customers.index', $data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {

        $year   = $request->year;
        $month  = $request->month;
        $period = $request->period ? $request->period : '30d';

        $periodDates    = Billing::getPeriodDates($year, $month, $period);
        $periodFirstDay = $periodDates['first'];
        $periodLastDay  = $periodDates['last'];

        $billing = CustomerBilling::whereCustomerId($id)
            ->where('month', $month)
            ->where('year', $year)
            ->where('period', $period)
            ->get();

        $reminders   = CalendarEvent::where('customer_id', $id)
            ->where('type', 'billing')
            ->where('concluded', 0)
            ->whereBetween('start', [$periodFirstDay, $periodLastDay])
            ->get();

        $billedItems = CustomerBilling::getBilledShipments($id, $year, $month, $period, $billing);

        $customer    = CustomerBilling::getBilling($id, $month, $year, $period, null, @$billedItems['ids']);

        //dd($customer->toArray());
        $departments = Customer::where('customer_id', $customer->id)
            ->pluck('name', 'id')
            ->toArray();

        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $agencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterAgencies()
            ->orderBy('code', 'asc')
            ->get());

        $billingZones = BillingZone::remember(config('cache.query_ttl'))
            ->cacheTags(BillingZone::CACHE_TAG)
            ->filterSource()
            ->orderBy('name')
            ->pluck('name', 'code')
            ->toArray();

        $operators = User::listOperators(User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->ignoreAdmins()
            ->orderBy('source', 'asc')
            ->orderBy('code', 'asc')
            ->orderBy('name', 'asc')
            ->get(['source', 'id', 'name', 'vehicle', 'provider_id']), true);

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $status = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingStatus::CACHE_TAG)
            ->filterSources()
            ->isVisible()
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $expensesTypes = ShippingExpense::filterSource()
            ->pluck('name', 'id')
            ->toArray();

        if ($request->has('tab') && $request->get('tab') == 'stats') {

            $bindings = [
                'id',
                'total_price',
                'cost_price',
                'fuel_price',
                'cod',
                'recipient_country',
                'sender_country',
                'service_id',
                'ignore_billing',
                'charge_price',
                'billing_date'
            ];

            $allShipments = Shipment::with('service')
                ->filterAgencies()
                ->whereNotIn('status_id', [ShippingStatus::CANCELED_ID])
                ->whereBetween('billing_date', [$periodFirstDay, $periodLastDay]);

            if (!$customer->exists) {
                $allShipments = $allShipments->whereNull('customer_id');
            } else {
                $allShipments = $allShipments->where('customer_id', $customer->id);
            }

            $allShipments = $allShipments->get($bindings);

            //Stats by type of service
            $shipmentsService = $allShipments->groupBy('service.name');
            $shipments = [];
            foreach ($shipmentsService as $service => $shipment) {
                $withVat = $shipment->filter(function ($item) {
                    return ($item->sender_country != Setting::get('app_country') && $item->recipient_country == Setting::get('app_country')) || ($item->sender_country == Setting::get('app_country') && $item->recipient_country == Setting::get('app_country'));
                });

                $withoutVat = $shipment->filter(function ($item) {
                    return $item->sender_country == Setting::get('app_country') && $item->recipient_country != Setting::get('app_country');
                });

                $shipments[$service] = [
                    'count_vat' => $withVat->count(),
                    'total_vat' => $withVat->sum('total_price'),
                    'count_no_vat' => $withoutVat->count(),
                    'total_no_vat' => $withoutVat->sum('total_price')
                ];
            }

            $typeOfServices = $shipments;

            $exportShipments = $allShipments->filter(function ($item) {
                return $item->sender_country == Setting::get('app_country') && $item->recipient_country != Setting::get('app_country');
            })->groupBy('recipient_country');

            $importShipments = $allShipments->filter(function ($item) {
                return $item->sender_country != Setting::get('app_country') && $item->recipient_country == Setting::get('app_country');
            })->groupBy('sender_country');
        }

        $compact = compact(
            'year',
            'month',
            'customer',
            'services',
            'expensesTypes',
            'typeOfServices',
            'importShipments',
            'exportShipments',
            'period',
            'billing',
            'providers',
            'operators',
            'agencies',
            'billingZones',
            'status',
            'billedItems',
            'departments',
            'reminders'
        );

        return $this->setContent('admin.billing.customers.show', $compact);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $customerId)
    {

        if ($request->has('billlist')) {
            return $this->editBillingSelected($request, $customerId);
        }

        $billingMonth = true;
        $year   = $request->has('year') ? $request->year : date('Y');
        $month  = $request->has('month') ? $request->month : date('n');
        $period = $request->period ? $request->period : '30d';

        $curMonth = date('n');
        $total = $request->total;

        $customer = Customer::find($customerId);

        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->whereSource(config('app.source'))
            ->filterAgencies()
            ->orderBy('code', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $billing = CustomerBilling::whereCustomerId($customerId)
            ->where('month', $month)
            ->where('year', $year)
            ->where('period', $period)
            ->first();

        if (!$billing || empty($billing->lines)) {
            $billedItems = CustomerBilling::getBilledShipments($customerId, $year, $month, $period);
            $billing = CustomerBilling::getBilling($customerId, $month, $year, $period, null, @$billedItems['ids']);
        }

        $billing->billing_type = 'month';
        $billing->payment_condition = $billing->payment_method;
        $billing->reference         = $billing->exists ? CustomerBilling::getDefaultReference($year, $month, $period) : null;

        if ($customer->billing_reference) {
            $billing->reference = $customer->billing_reference;
        }

        if (in_array($billing->payment_method, ['dbt', 'prt'])) {
            $invoiceLimitDays = PaymentCondition::getDays($billing->payment_method); //obtem dias de pagamento pela base de dados
        } else {
            $invoiceLimitDays = str_replace('d', '', $billing->payment_method);
        }

        if (Setting::get('billing_force_today')) {
            $docDate      = date('Y-m-d');
            $docLimitDate = Carbon::today()->addDays($invoiceLimitDays)->format('Y-m-d');
        } else {
            if ($curMonth == $month) {
                $docDate = date('Y-m-d');
                /*$docLimitDate = new Carbon('last day of next month');
                $docLimitDate = $docLimitDate->format('Y-m-d');*/
                $docLimitDate = new Carbon();
                $docLimitDate = $docLimitDate->addDays($invoiceLimitDays)->format('Y-m-d');
            } else {
                $docDate = new Carbon('last day of last month');
                $docDate = $docDate->format('Y-m-d');
                $docLimitDate = new Carbon('last day of last month');
                $docLimitDate = $docLimitDate->addDays($invoiceLimitDays)->format('Y-m-d');
            }
        }


        $apiKeys  = Invoice::getApiKeys($customer->company_id);
        $vatTaxes = Invoice::getVatTaxes();
        $newCustomerCode = $customer->setCode(false);


        $billing->reference_period = $billing->exists ? (trans('datetime.month-tiny.' . $month) . '/' . $year . ($period != '30d' ? '/' . strtoupper($period) : '')) : null;
        
        if(Setting::get('invoices_obs_auto')) { //obs automatica das faturas
            $billing->obs = 'Referente aos serviços no período ' . $billing->reference_period . " (Conforme resumo de serviços em anexo)\n";
        }

        $billing->obs.= Setting::get('invoice_obs'); //junta observacoes personalizadas
        $billing->obs = Invoice::prefillObs($billing->obs, $billing);


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
            'customer'  => $customer->id,
            'target'    => Invoice::TARGET_CUSTOMER_BILLING,
            'month'     => $month,
            'year'      => $year,
            'period'    => $period
        ]);

        $appCountry = Setting::get('app_country');

        $customerCategories = CustomerType::remember(config('cache.query_ttl'))
            ->cacheTags(CustomerType::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $formOptions = ['url' => $route, 'method' => 'POST', 'class' => 'form-billing'];

        $action = 'Emitir fatura total';
        

        $data = compact(
            'billing',
            'billingMonth',
            'customer',
            'year',
            'month',
            'total',
            'docDate',
            'docLimitDate',
            'apiKeys',
            'period',
            'vatTaxes',
            'action',
            'formOptions',
            'agencies',
            'newCustomerCode',
            'paymentConditions',
            'paymentMethods',
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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatable(Request $request)
    {

        //AO ALTERAR ESTA FUNÇÃO, CORRIGIR TAMBEM A FUNÇÃO EXPORT E FUNCAO PRINT PDF

        $year    = $request->has('year') ? $request->year : date('Y');
        $month   = $request->has('month') ? $request->month : date('n');
        $period  = $request->has('period') ? $request->period : '30d';

        $periodDates    = Billing::getPeriodDates($year, $month, $period);
        $periodFirstDay = $periodDates['first'];
        $periodLastDay  = $periodDates['last'];

        $agencies   = Agency::withTrashed()->pluck('name', 'id')->toArray();
        $myAgencies = Auth::user()->agencies;

        if (Auth::user()->hasRole(config('permissions.role.admin'))) {
            $myAgencies = Agency::whereSource(config('app.source'))->pluck('id')->toArray();
        }

        $covenantsCustomers = CustomerCovenant::leftJoin('customers', 'customers.id', '=', 'customers_covenants.customer_id')
            ->where('start_date', '<=', $periodFirstDay)
            ->where('end_date', '>=', $periodLastDay)
            ->where('type', 'fixed')
            ->whereIn('customers.agency_id', $myAgencies)
            ->pluck('customers.id')
            ->toArray();

        $customersBilling = CustomerBilling::where('month', $month)
            ->where('year', $year)
            ->get();

        $shipmentsIds = [];
        $covenantsIds = [];
        $productsIds = [];
        foreach ($customersBilling as $item) {
            if ($item->shipments) {
                $shipmentsIds = array_merge($shipmentsIds, $item->shipments);
            }

            if ($item->covenants) {
                $covenantsIds = array_merge($covenantsIds, $item->covenants);
            }

            if ($item->products) {
                $productsIds = array_merge($productsIds, $item->products);
            }
        }

        $bindings = [
            DB::raw($month . ' as month'),
            DB::raw($year . ' as year'),
            'customers.id',
            'customers.code as code',
            'customers.agency_id as agency_id',
            'customers.name as name',
            'customers.default_invoice_type as default_invoice_type',
            'shipments.customer_id',
        ];

        $data = Customer::leftJoin('shipments', function ($q) use ($periodFirstDay, $periodLastDay) {
                $q->on('customers.id', '=', 'shipments.customer_id');
                $q->whereBetween('shipments.billing_date', [$periodFirstDay, $periodLastDay]);
                $q->whereNull('shipments.deleted_at');
                $q->where('shipments.status_id', '<>', ShippingStatus::CANCELED_ID);
            })
            ->with(['shipments' => function ($q) use ($periodFirstDay, $periodLastDay) {
                $q->whereBetween('billing_date', [$periodFirstDay, $periodLastDay]);
                $q->where('status_id', '<>', ShippingStatus::CANCELED_ID);
                $q->where('is_collection', 0);
            }])
            ->with(['productsBought' => function ($q) use ($periodFirstDay, $periodLastDay) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(ProductSale::CACHE_TAG);
                $q->whereBetween('date', [$periodFirstDay, $periodLastDay]);
            }])
            ->with(['covenants' => function ($q) use ($periodFirstDay, $periodLastDay) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(CustomerCovenant::CACHE_TAG);
                $q->filterBetweenDates($periodFirstDay, $periodLastDay);
            }])
            ->with(['billing' => function ($q) use ($year, $month, $period) {
                $q->where('year', $year);
                $q->where('month', $month);
                $q->where('period', $period);
            }])
            ->where(function ($q) use ($periodFirstDay, $periodLastDay, $covenantsCustomers) {
                $q->whereBetween('billing_date', [$periodFirstDay, $periodLastDay]);
                $q->orWhereIn('customers.id', $covenantsCustomers);
            })
            ->filterSeller()
            ->groupBy('customers.name');


        if ($myAgencies) {
            $data = $data->whereIn('customers.agency_id', $myAgencies);
        }

        if (Auth::user()->isGuest()) {
            $data = $data->where('customers.id', '999999999');
        }

        $data = $data->select($bindings);

        //filter agency
        $value = $request->department;
        if ($request->has('department')) {
            if ($value == 1) {
                $data = $data->has('departments');
            } else {
                $data = $data->has('departments', '=', 0);
            }
        }

        //filter agency
        $value = $request->agency;
        if ($request->has('agency')) {
            $data = $data->whereIn('customers.agency_id', $value);
        }

        //filter type
        $value = $request->type;
        if ($request->has('type')) {
            $data = $data->whereIn('customers.type_id', $value);
        }

        //filter payment condition
        $value = $request->payment_condition;
        if ($request->has('payment_condition')) {
            $data = $data->whereIn('customers.payment_method', $value);
        }

        //filter seller
        $value = $request->seller;
        if ($request->has('seller')) {
            $data = $data->whereIn('customers.seller_id', $value);
        }

        //filter route
        $value = $request->route;
        if ($request->has('route')) {
            $data = $data->whereIn('customers.route_id', $value);
        }

        $subSqlQueries = [];

        if ($shipmentsIds) { //quando já há faturados
            $subSqlQueries[] = 'COALESCE((select COALESCE(sum(total_price), 0) + COALESCE(sum(fuel_price), 0) + COALESCE(sum(total_expenses), 0) 
                         from shipments
                         where deleted_at is null and ignore_billing = 0 and 
                         status_id not in (' . ShippingStatus::CANCELED_ID . ') and 
                         cod is null and is_collection = 0 and
                         (billing_date between "' . $periodFirstDay . '" and "' . $periodLastDay . '") and 
                         shipments.id not in (' . implode(',', $shipmentsIds) . ') and 
                         customer_id = customers.id), 0)';
        } else {
            $subSqlQueries[] = 'COALESCE((select COALESCE(sum(total_price), 0) + COALESCE(sum(fuel_price), 0) + COALESCE(sum(total_expenses), 0)  
                         from shipments
                         where deleted_at is null and ignore_billing = 0 and 
                         status_id not in (' . ShippingStatus::CANCELED_ID . ') and 
                         cod is null and 
                         (billing_date between "' . $periodFirstDay . '" and "' . $periodLastDay . '") and 
                         customer_id = customers.id), 0)';
        }

        if ($covenantsIds) {
            $subSqlQueries[] = 'COALESCE((select sum(amount) 
                        from customers_covenants  
                        where deleted_at is null and
                        start_date <= "' . $periodFirstDay . '" and end_date >= "' . $periodLastDay . '" and
                        customers_covenants.id not in (' . implode(',', $covenantsIds) . ') and
                        customer_id = customers.id), 0)';
        } else {
            $subSqlQueries[] = 'COALESCE((select sum(amount) 
                        from customers_covenants  
                        where deleted_at is null and
                        start_date <= "' . $periodFirstDay . '" and end_date >= "' . $periodLastDay . '" and 
                        customer_id = customers.id), 0)';
        }

        if ($productsIds) {
            $subSqlQueries[] = 'COALESCE((select sum(subtotal) 
                        from products_sales  
                        where deleted_at is null and
                        (date between "' . $periodFirstDay . '" and "' . $periodLastDay . '") and
                        products_sales.id not in (' . implode(',', $productsIds) . ') and
                        customer_id = customers.id), 0)';
        } else {
            $subSqlQueries[] = 'COALESCE((select sum(subtotal) 
                        from products_sales  
                        where deleted_at is null and
                        (date between "' . $periodFirstDay . '" and "' . $periodLastDay . '") and 
                        customer_id = customers.id), 0)';
        }


        $subSql = implode(' + ', $subSqlQueries);

        if ($subSql) {
            $subSql = '(select COALESCE(' . $subSql . ', 0))';
        } else {
            $subSql = '0';
        }


        //filter billed
        $value = $request->billed;
        if ($request->has('billed') && $value != 'all') {

            if ($value == "1") {

                if (!empty(Setting::get('billing_auto_hide_zero'))) {
                    $data = $data->whereRaw($subSql . ' = 0'); //original. Coloca todos os envios com preço a 0,00 na lista de faturados.
                } else {
                    $data = $data->where(function ($q) use ($subSql, $year, $month, $period) {
                        $q->whereRaw($subSql . ' = 0');
                        $q->whereHas('billing', function ($q) use ($year, $month, $period) { //força a que tenha de
                            $q->where('year', $year);
                            $q->where('month', $month);
                            $q->where('period', $period);
                        });
                    });
                }
            } else {
                $data = $data->whereRaw($subSql . ' > 0');
            }
        }


        return Datatables::of($data)
            ->edit_column('name', function ($row) use ($agencies, $period) {
                return view('admin.billing.customers.datatables.customer', compact('row', 'agencies', 'period'))->render();
            })
            ->add_column('default_invoice_type', function ($row) {
                if (!empty($row->default_invoice_type)) {
                    return trans('admin/billing.types_code.' . $row->default_invoice_type);
                }
            })
            ->edit_column('month', function ($row) use ($period) {
                return view('admin.billing.customers.datatables.period', compact('row', 'period'))->render();
            })
            ->edit_column('count_shipments', function ($row) {
                $html = @$row->shipments->count();
                return $html;
            })
            ->add_column('total_shipments', function ($row) {
                $price = (@$row->shipments->sum('total_price') +
                    @$row->shipments->sum('total_expenses') +
                    @$row->shipments->sum('fuel_price') +
                    @$row->shipments->sum('total_price_for_recipient'));
                return view('admin.billing.customers.datatables.price', compact('row', 'price'))->render();
            })
            ->add_column('total_products', function ($row) {
                $price = @$row->productsBought->sum('subtotal');
                return view('admin.billing.customers.datatables.price', compact('row', 'price'))->render();
            })
            ->add_column('total_covenants', function ($row) {
                $price = @$row->covenants->sum('amount');
                return view('admin.billing.customers.datatables.price', compact('row', 'price'))->render();
            })
            ->add_column('total_cost', function ($row) {
                $price = @$row->shipments->sum('cost_shipping_price') + @$row->shipments->sum('cost_expenses_price');
                return view('admin.billing.customers.datatables.price', compact('row', 'price'))->render();
            })
            ->add_column('total_month', function ($row) use ($period) {
                $total = @$row->shipments->sum('total_price');
                $total += @$row->shipments->sum('total_expenses');
                $total += @$row->shipments->sum('fuel_price');
                $total += @$row->shipments->sum('total_price_for_recipient');
                $total += @$row->productsBought->sum('subtotal');
                $total += @$row->covenants->sum('amount');
                return view('admin.billing.customers.datatables.total_month', compact('row', 'total'))->render();
            })
            ->add_column('total_billing', function ($row) use ($period) {

                //run all customer billing rows and create array with all billed ids for shipment, covenants and products
                $shipmentsIds = [];
                $covenantsIds = [];
                $productsIds  = [];
                if ($row->billing) {
                    foreach ($row->billing as $billing) {
                        $shipmentsIds = array_merge($shipmentsIds, (array) $billing->shipments);
                        $covenantsIds = array_merge($covenantsIds, (array) $billing->covenants);
                        $productsIds  = array_merge($productsIds, (array) $billing->products);
                    }
                }

                //filter shipments, covenants and products that not in filtred ids
                $billingShipments = $row->shipments->filter(function ($item) use ($shipmentsIds) {
                    return !in_array($item->id, $shipmentsIds) && !$item->ignore_billing && !$item->payment_at_recipient;
                });

                //dd($billingShipments->pluck('id')->toArray());

                $billingCovenants = $row->covenants->filter(function ($item) use ($covenantsIds) {
                    return !in_array($item->id, $covenantsIds);
                });

                $billingProducts = $row->covenants->filter(function ($item) use ($productsIds) {
                    return !in_array($item->id, $productsIds);
                });


                $total = @$billingShipments->sum('total_price');
                $total += @$billingShipments->sum('total_expenses');
                $total += @$billingShipments->sum('fuel_price');
                $total += @$billingProducts->sum('subtotal');
                $total += @$billingCovenants->sum('amount');

                return view('admin.billing.customers.datatables.total_billing', compact('row', 'total', 'period'))->render();
            })
            ->edit_column('invoice', function ($row) use ($agencies, $period) {

                $invoices = [];
                if ($row->billing) {
                    foreach ($row->billing as $billing) {
                        if ($billing->invoice_type != 'nodoc') {
                            $billing->invoice_type = $billing->invoice_type ? $billing->invoice_type : 'invoice';
                            $invoices[$billing->invoice_doc_id] = [
                                'name'       => trans('admin/billing.types_code.' . $billing->invoice_type) . ' ' . $billing->invoice_doc_id,
                                'invoice_id' => $billing->invoice_id,
                                'type'       => $billing->invoice_type,
                                'key'        => $billing->api_key
                            ];
                        }
                    }
                }

                return view('admin.billing.customers.datatables.invoice', compact('row', 'invoices'))->render();
            })
            ->add_column('profit', function ($row) {
                $total = @$row->shipments->sum('total_price');
                $total += @$row->shipments->sum('total_expenses');
                $total += @$row->shipments->sum('fuel_price');
                $total += @$row->shipments->sum('total_price_for_recipient');
                $total += @$row->productsBought->sum('subtotal');
                $total += @$row->covenants->sum('amount');


                $cost = @$row->shipments->sum('cost_shipping_price') + @$row->shipments->sum('cost_expenses_price');
                $cost += $row->total_products_cost;

                $profit = $total - $cost;
                return view('admin.billing.customers.datatables.profit', compact('row', 'profit'))->render();
            })
            ->add_column('actions', function ($row) use ($period) {
                $total = $row->total_expenses;
                $total += $row->total_shipments;
                $total += $row->total_covenants;
                $total += $row->total_recipient;
                $total += $row->total_products;
                $total -= $row->total_shipments_ignore;

                return view('admin.billing.customers.datatables.actions', compact('row', 'total', 'period'))->render();
            })
            ->make(true);
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatableShipments(Request $request, $customerId)
    {

        $appMode = Setting::get('app_mode');
        $year    = $request->has('year')  ? $request->year : date('Y');
        $month   = $request->has('month') ? $request->month : date('n');
        $period  = $request->has('period') ? $request->period : '30d';
        $pickup  = $request->get('pickup', 0);

    
        //status
        $statusList = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->get(['id', 'name', 'color', 'is_final']);
        $finalStatus = $statusList->filter(function ($item) {
            return $item->is_final;
        })->pluck('id')->toArray();
        $statusList  = $statusList->groupBy('id')->toArray();

        //services
        $servicesList = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->get();
        $servicesList = $servicesList->groupBy('id')->toArray();

        //operator
        $operatorsList = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->get(['source', 'id', 'code', 'code_abbrv', 'name', 'vehicle', 'provider_id']);
        $operatorsList = $operatorsList->groupBy('id')->toArray();

        //providers
        $providersList = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->get();
        $providersList = $providersList->groupBy('id')->toArray();

        //agencies
        $allAgencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->withTrashed()
            ->get(['name', 'code', 'id', 'color', 'source']);
        $agencies = $allAgencies->groupBy('id')->toArray();

        $packTypes = PackType::remember(config('cache.query_ttl'))
            ->cacheTags(PackType::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->pluck('name', 'code')
            ->toArray();

        $billing = CustomerBilling::where('customer_id', $customerId)
            ->where('month', $month)
            ->where('year', $year)
            ->where('period', $period)
            ->get(['invoice_type', 'invoice_id', 'invoice_doc_id', 'shipments', 'billing_type', 'api_key']);

        $billedShipments = [];
        foreach ($billing as $row) {
            if ($row->shipments) {
                foreach ($row->shipments as $shipmentId) {
                    $billedShipments[$shipmentId] = [
                        'invoice_id'     => $row->invoice_id,
                        'invoice_doc_id' => $row->invoice_doc_id,
                        'invoice_type'   => $row->invoice_type,
                        'billing_type'   => $row->billing_type,
                        'api_key'        => $row->api_key
                    ];
                }
            }
        }

        $periodDates    = Billing::getPeriodDates($year, $month, $period);
        $periodFirstDay = $periodDates['first'];
        $periodLastDay  = $periodDates['last'];

        $data = Shipment::with(['customer' => function ($q) {
            $q->remember(config('cache.query_ttl'));
            $q->cacheTags(Customer::CACHE_TAG);
        }]);
        /*->with(['service' => function($q){
                        $q->remember(config('cache.query_ttl'));
                        $q->cacheTags(Service::CACHE_TAG);
                    }])
                    ->with(['agency' => function($q){
                        $q->remember(config('cache.query_ttl'));
                        $q->cacheTags(Agency::CACHE_TAG);
                    }])
                    ->with(['status' => function($q){
                        $q->remember(config('cache.query_ttl'));
                        $q->cacheTags(ShippingStatus::CACHE_TAG);
                    }])
                    ->with(['operator' => function($q){
                        $q->remember(config('cache.query_ttl'));
                        $q->cacheTags(User::CACHE_TAG);
                    }])
                    ->with(['provider' => function($q){
                        $q->remember(config('cache.query_ttl'));
                        $q->cacheTags(Provider::CACHE_TAG);
                        $q->select(['id', 'name', 'color']);
                    }])*/

        $data = $data->filterMyAgencies()
            ->whereBetween('billing_date', [$periodFirstDay, $periodLastDay])
            ->whereNotIn('status_id', [ShippingStatus::CANCELED_ID])
            ->applyCustomerBillingRequestFilters($request)
            ->whereNull('cod')
            ->where('is_collection', $pickup)
            
            ->select();

        if ($customerId != '999999999') {
            $data = $data->whereCustomerId($customerId);
        } else {
            $data = $data->whereNull('customer_id');
        }

        if (Setting::get('billing_ignored_services')) {
            $data = $data->whereNotIn('service_id', Setting::get('billing_ignored_services'));
        }

        //filter date min
        $dtMin = $request->get('date_min');
        if ($request->has('date_min')) {
            $dtMax = $dtMin;
            if ($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }

            if ($request->has('date_unity') && !empty($request->has('date_unity'))) { //filter by shipment status date
                $dtMin = $dtMin . ' 00:00:00';
                $dtMax = $dtMax . ' 23:59:59';
                $statusId = $request->get('date_unity');

                $data = $data->whereHas('history', function ($q) use ($dtMin, $dtMax, $statusId) {
                    $q->where('status_id', $statusId)
                        ->whereBetween('created_at', [$dtMin, $dtMax]);
                });
            } else { //filter by shipment date
                $data = $data->whereBetween('billing_date', [$dtMin, $dtMax]);
            }
        }

        //filter department
        $value = $request->department;
        if ($request->has('department')) {
            if ($value == '-1') {
                $data = $data->whereNull('department_id');
            } else {
                $data = $data->where('department_id', $value);
            }
        }

        //filter cod
        $value = $request->cod;
        if ($request->has('cod')) {
            $data = $data->whereIn('cod', ['D', 'S']);
        } else {
            $data = $data->whereNull('cod');
        }

        //filter invoice
        $value = $request->invoice;
        if ($request->has('invoice')) {
            $data = $data->where('invoice_doc_id', $value);
        }

        //filter expenses
        $value = $request->get('expenses');
        if ($request->has('expenses')) {
            if ($value == '0') {
                $data = $data->where(function ($q) {
                    $q->whereNull('total_expenses');
                    $q->orWhere('total_expenses', 0.00);
                });
            } else {
                $data = $data->where('total_expenses', '>', 0.00);
            }
        }

        //filter expense type
        $value = $request->expense_type;
        if ($request->has('expense_type')) {
            $data = $data->whereHas('expenses', function ($q) use ($value) {
                $q->whereIn('expense_id', $value);
            });
        }

        //filter zone
        $value = $request->zone;
        if ($request->has('zone')) {
            $data = $data->where('zone', $value);
        }

        //filter service
        $value = $request->get('service');
        if ($request->has('service')) {
            if ($value == '-1') {
                $data = $data->whereNull('service_id');
            } else {
                $data = $data->whereIn('service_id', $value);
            }
        }

        //filter provider
        $value = $request->get('provider');
        if ($request->has('provider')) {
            $data = $data->where('provider_id', $value);
        }

        //filter agency
        $value = $request->agency;
        if ($request->has('agency')) {
            //$data = $data->where('agency_id', $value);
            $data = $data->where('sender_agency_id', $value);
        }

        //filter agency
        $value = $request->recipient_agency;
        if ($request->has('recipient_agency')) {
            $data = $data->where('recipient_agency_id', $value);
        }

        //filter operator
        $value = $request->operator;
        if ($request->has('operator')) {
            if ($value == 'not-assigned') {
                $data = $data->whereNull('operator_id');
            } else {
                $data = $data->where('operator_id', $value);
            }
        }

        //filter status
        $value = $request->get('status');
        if ($request->has('status')) {
            $data = $data->whereIn('status_id', $value);
        }

        //filter conferred
        $value = $request->get('conferred');
        if ($request->has('conferred')) {
            if ($value == 0) {
                $data = $data->where(function ($q) {
                    $q->whereNull('customer_conferred');
                    $q->orWhere('customer_conferred', 0);
                });
            } else {
                $data = $data->where('customer_conferred', 1);
            }
        }

        //filter billed
        $value = $request->billed;
        if ($request->has('billed')) {
            if ($value == 1) {
                $data = $data->whereNotNull('invoice_doc_id');
            } else {
                $data = $data->whereNull('invoice_doc_id');
            }
        }

        //filter charge
        $value = $request->charge;
        if ($request->has('charge')) {
            if ($value == 0) {
                $data = $data->whereNull('charge_price');
            } elseif ($value == 1) {
                $data = $data->whereNotNull('charge_price');
            }
        }

        //filter price_fixed
        $value = $request->price_fixed;
        if ($request->has('price_fixed')) {
            $data = $data->where('price_fixed', $value);
        }

        //filter return
        $value = $request->return;
        if ($request->has('return')) {
            if ($value) {
                $data = $data->whereNotNull('has_return');
            } else {
                $data = $data->whereNull('has_return');
            }
        }

        //filter sender country
        $value = $request->get('sender_country');
        if ($request->has('sender_country')) {
            $data = $data->where('sender_country', $value);
        }

        //filter recipient country
        $value = $request->get('recipient_country');
        if ($request->has('recipient_country')) {
            $data = $data->where('recipient_country', $value);
        }

        //filter price 0
        $value = $request->price;
        if ($request->has('price')) {
            if ($value == 1) {
                $data = $data = $data->where(function ($q) {
                    $q->whereNull('total_price');
                    $q->orWhere('total_price', 0.00);
                });
            } else {
                $data = $data->where('total_price', '>', 0.00);
            }
        }

        //filter empty country
        $value = $request->empty_country;
        if ($request->has('empty_country')) {
            if ($value == 1) {
                $data = $data = $data->where(function ($q) {
                    $q->where(function ($q) {
                        $q->whereNull('sender_country');
                        $q->orWhere('sender_country', '');
                    });
                    $q->orWhere(function ($q) {
                        $q->whereNull('recipient_country');
                        $q->orWhere('recipient_country', '');
                    });
                });
            } else {
                $data = $data->where(function ($q) {
                    $q->whereNotNull('sender_country');
                    $q->orWhereNotNull('recipient_country');
                });
            }
        }

        $value = $request->empty_children;
        if ($request->has('empty_children')) {
            if ($value == 1) {
                $data = $data->whereNull('children_tracking_code');
            } else {
                $data = $data->whereNotNull('children_tracking_code');
            }
        }

        return Datatables::of($data)
            ->edit_column('tracking_code', function ($row) use ($agencies) {
                return view('admin.shipments.shipments.datatables.tracking', compact('row', 'agencies'))->render();
            })
            ->edit_column('reference', function ($row) use ($agencies) {
                return view('admin.billing.customers.datatables.shipments.reference', compact('row'))->render();
            })
            ->edit_column('children_tracking_code', function ($row) {
                return view('admin.shipments.pickups.datatables.children_tracking_code', compact('row'))->render();
            })
            ->edit_column('sender_name', function ($row) {
                return view('admin.billing.customers.datatables.shipments.sender', compact('row'))->render();
            })
            ->edit_column('recipient_name', function ($row) {
                return view('admin.billing.customers.datatables.shipments.recipient', compact('row'))->render();
            })
            /*->edit_column('status_id', function($row) {
                return view('admin.shipments.shipments.datatables.status', compact('row'))->render();
            })*/
            ->edit_column('status_id', function ($row) use ($statusList, $operatorsList) {
                return view('admin.shipments.shipments.datatables.status', compact('row', 'statusList', 'operatorsList'))->render();
            })
            /*->edit_column('volumes', function($row) {
                return view('admin.shipments.shipments.datatables.volumes', compact('row'))->render();
            })*/
            ->edit_column('volumes', function ($row) use ($servicesList, $packTypes, $appMode) {
                return view('admin.shipments.shipments.datatables.volumes', compact('row', 'servicesList', 'packTypes', 'appMode'))->render();
            })
            ->edit_column('vehicle', function ($row) {
                return view('admin.shipments.shipments.datatables.vehicle', compact('row'))->render();
            })
            ->edit_column('date', function ($row) use ($statusList) {
                return view('admin.shipments.shipments.datatables.date', compact('row', 'statusList'))->render();
            })
            ->edit_column('delivery_date', function ($row) use ($statusList) {
                return view('admin.shipments.shipments.datatables.delivery_date', compact('row', 'statusList'))->render();
            })
            /*->edit_column('service_id', function($row) use($agencies) {
                return view('admin.shipments.shipments.datatables.service', compact('row', 'agencies'))->render();
            })*/
            ->edit_column('service_id', function ($row) use ($agencies, $servicesList, $providersList) {
                return view('admin.shipments.shipments.datatables.service', compact('row', 'agencies', 'servicesList', 'providersList'))->render();
            })
            ->edit_column('customer_conferred', function ($row) {
                return view('admin.billing.customers.datatables.shipments.conferred', compact('row'))->render();
            })
            ->edit_column('total_price', function ($row) {
                return view('admin.billing.customers.datatables.shipments.total', compact('row'))->render();
            })
            ->edit_column('invoice_id', function ($row) use ($agencies, $billedShipments) {
                return view('admin.billing.customers.datatables.shipments.invoice', compact('row', 'agencies', 'billedShipments'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.billing.customers.datatables.shipments.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatableExpenses(Request $request, $customerId)
    {

        $year   = $request->has('year')  ? $request->year : date('Y');
        $month  = $request->has('month') ? $request->month : date('n');
        $period = $request->has('period') ? $request->period : '30d';

        $periodDates    = Billing::getPeriodDates($year, $month, $period);
        $periodFirstDay = $periodDates['first'];
        $periodLastDay  = $periodDates['last'];

        $data = ShipmentExpense::with('expense', 'shipment')
            ->whereHas('shipment', function ($q) use ($periodFirstDay, $periodLastDay, $customerId) {
                $q->filterAgencies()
                    ->whereBetween('billing_date', [$periodFirstDay, $periodLastDay])
                    ->where('status_id', '<>', ShippingStatus::CANCELED_ID)
                    ->where('is_collection', 0);

                if ($customerId != '999999999') {
                    $q->where('customer_id', $customerId);
                } else {
                    $q->whereNull('customer_id');
                }
            })
            ->select();

        //filter expense type
        $value = $request->get('expense');
        if ($request->has('expense')) {
            $data = $data->where('expense_id', $value);
        }

        return Datatables::of($data)
            ->edit_column('date', function ($row) {
                return $row->date->format('Y-m-d');
            })
            ->add_column('name', function ($row) {
                return @$row->expense->name;
            })
            ->add_column('tracking_code', function ($row) {
                return view('admin.billing.customers.datatables.expenses.tracking', compact('row'))->render();
            })
            ->edit_column('price', function ($row) {
                return money(@$row->expense->price, Setting::get('app_currency'));
            })
            ->edit_column('subtotal', function ($row) {
                return view('admin.billing.customers.datatables.expenses.subtotal', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.billing.customers.datatables.expenses.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatableProducts(Request $request, $customerId)
    {

        $year   = $request->has('year')  ? $request->year : date('Y');
        $month  = $request->has('month') ? $request->month : date('n');
        $period = $request->has('period') ? $request->period : '30d';

        $periodDates    = Billing::getPeriodDates($year, $month, $period);
        $periodFirstDay = $periodDates['first'];
        $periodLastDay  = $periodDates['last'];

        $data = ProductSale::with('product')
            ->where('customer_id', $customerId)
            ->whereBetween('products_sales.date', [$periodFirstDay, $periodLastDay])
            ->select();

        return Datatables::of($data)
            ->add_column('created_at', function ($row) {
                return $row->date;
            })
            ->add_column('name', function ($row) {
                return @$row->product->name;
            })
            ->edit_column('cost_price', function ($row) {
                return money($row->cost_price, Setting::get('app_currency'));
            })
            ->edit_column('price_un', function ($row) {
                return money($row->price, Setting::get('app_currency'));
            })
            ->edit_column('subtotal', function ($row) {
                return view('admin.billing.customers.datatables.products.subtotal', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.billing.customers.datatables.products.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /*/**
     * Assign selected resources to same customer
     * GET /admin/billing/shipments/selected/assign-customer
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massRefreshPrices(Request $request)
    {
        $ids = explode(',', $request->ids);
        return $this->updatePrices($ids);
    }

    /**
     * Assign selected resources to same customer
     * GET /admin/billing/shipments/selected/assign-customer
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePrices($customerId, $month = null, $year = null, $massive = false)
    {

        $month = is_null($month) ? date('n') : $month;
        $year  = is_null($year) ? date('Y') : $year;

        if (is_array($customerId)) {
            $shipments = Shipment::whereIn('id', $customerId)
                ->get();
        } else {
            $shipments = Shipment::whereCustomerId($customerId)
                ->whereRaw('YEAR(billing_date) = ' . $year)
                ->whereRaw('MONTH(billing_date) = ' . $month)
                ->get();
        }

        try {
            foreach ($shipments as $shipment) {

                if (!$shipment->price_fixed && !$shipment->is_blocked && !$shipment->invoice_doc_id && $shipment->recipient_country && $shipment->provider_id && $shipment->service_id && $shipment->agency_id && $shipment->customer_id) {
                    // Não calcular preços de envios com portes e agrupados sem preço
                    if ($shipment->cod || ($shipment->parent_tracking_code && $shipment->type == 'M' && $shipment->shipping_price == 0.00)) {
                        continue;
                    }

                    // Atualizar apenas preços menores que o calculado
                    // $prices = Shipment::calcPrices($shipment);
                    // if (@$prices['fillable'] && $prices['fillable']['shipping_price'] <= $shipment->shipping_price) {
                    //     $shipment->updatePrices($prices);
                    // }

                    $shipment->updatePrices();

                    //update pickup expense
                    if ($shipment->is_collection && !empty($shipment->children_tracking_code)) {
                        $childrenShipment = Shipment::where('tracking_code', $shipment->children_tracking_code)->first();
                        if ($childrenShipment) {
                            $childrenShipment->insertOrUpdadePickupExpense($shipment); //add expense
                        }
                    }

                    unset($shipment);
                }
            }
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Execução abortada: ' . $e->getMessage());
        }

        return Redirect::back()->with('success', 'Preços atualizados com sucesso.');
    }

    /**
     * Update all prices
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function massUpdatePrices(Request $request)
    {

        $month = $request->get('month') ? $request->month : date('n');
        $year  = $request->get('year') ? $request->year : date('Y');

        try {
            $shipments = Shipment::filterMyAgencies()
                ->whereRaw('YEAR(billing_date) = ' . $year)
                ->whereRaw('MONTH(billing_date) = ' . $month)
                ->get(['id'])
                ->pluck('id')
                ->toArray();

            self::updatePrices($shipments, $month, $year);

            return Response::json([
                'result'   => true,
                'feedback' => 'Preços atualizados com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result'    => false,
                'feedback'  => $e->getMessage()
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editBillingSelected(Request $request, $customerId)
    {

        $billingMonth = false;
        $ids      = $request->id;
        $year     = $request->has('year') ? $request->year : date('Y');
        $month    = $request->has('month') ? $request->month : date('n');
        $period   = $request->period ? $request->period : '30d';
        $curMonth = date('m');
        $tab      = $request->tab;

        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->whereSource(config('app.source'))
            ->filterAgencies()
            ->orderBy('code', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        if ($tab == 'covenants') {
            $ids = CustomerCovenant::filterSource()
                ->whereIn('id', $ids)
                ->pluck('id')
                ->toArray();
            $ids = ['covenants' => $ids];
        } elseif (!empty($ids)) {
            $ids = Shipment::filterAgencies()
                ->whereIn('id', $ids)
                ->whereNull('invoice_doc_id')
                ->pluck('id')
                ->toArray();
            $ids = ['shipments' => $ids];
        } else { //faturar lista

            $periodDates    = Billing::getPeriodDates($year, $month, $period);
            $periodFirstDay = $periodDates['first'];
            $periodLastDay  = $periodDates['last'];

            $data = Shipment::filterMyAgencies()
                ->whereBetween('billing_date', [$periodFirstDay, $periodLastDay])
                ->whereNotIn('status_id', [ShippingStatus::CANCELED_ID])
                ->whereNotIn('cod', ['D', 'S'])
                ->where('is_collection', 0)
                ->whereCustomerId($customerId);

            if (Setting::get('billing_ignored_services')) {
                $data = $data->whereNotIn('service_id', Setting::get('billing_ignored_services'));
            }

            //filter date min
            $dtMin = $request->get('date_min');
            if ($request->has('date_min')) {
                $periodFirstDay = $dtMin;
                $dtMax = $dtMin;
                if ($request->has('date_max')) {
                    $dtMax = $request->get('date_max');
                    $periodLastDay = $dtMax;
                }

                if ($request->has('date_unity') && !empty($request->has('date_unity'))) { //filter by shipment status date
                    $dtMin = $dtMin . ' 00:00:00';
                    $dtMax = $dtMax . ' 23:59:59';
                    $statusId = $request->get('date_unity');

                    $data = $data->whereHas('history', function ($q) use ($dtMin, $dtMax, $statusId) {
                        $q->where('status_id', $statusId)
                            ->whereBetween('created_at', [$dtMin, $dtMax]);
                    });
                } else { //filter by shipment date
                    $data = $data->whereBetween('billing_date', [$dtMin, $dtMax]);
                }
            }

            //filter department
            $value = $request->department;
            if ($request->has('department')) {
                if ($value == '-1') {
                    $data = $data->whereNull('department_id');
                } else {
                    $data = $data->where('department_id', $value);
                }
            }

            //filter invoice
            $value = $request->invoice;
            if ($request->has('invoice')) {
                $data = $data->where('invoice_doc_id', $value);
            }

            //filter expenses
            $value = $request->get('expenses');
            if ($request->has('expenses')) {
                if ($value == '0') {
                    $data = $data->where(function ($q) {
                        $q->whereNull('total_expenses');
                        $q->orWhere('total_expenses', 0.00);
                    });
                } else {
                    $data = $data->where('total_expenses', '>', 0.00);
                }
            }

            //filter expense type
            $value = $request->expense_type;
            if ($request->has('expense_type')) {
                $data = $data->whereHas('expenses', function ($q) use ($value) {
                    $q->whereIn('expense_id', $value);
                });
            }

            //filter zone
            $value = $request->zone;
            if ($request->has('zone')) {
                $data = $data->where('zone', $value);
            }

            //filter service
            $value = $request->get('service');
            if ($request->has('service')) {
                if ($value == '-1') {
                    $data = $data->whereNull('service_id');
                } else {
                    $data = $data->where('service_id', $value);
                }
            }

            //filter provider
            $value = $request->get('provider');
            if ($request->has('provider')) {
                $data = $data->where('provider_id', $value);
            }

            //filter agency
            $value = $request->agency;
            if ($request->has('agency')) {
                //$data = $data->where('agency_id', $value);
                $data = $data->where('sender_agency_id', $value);
            }

            //filter agency
            $value = $request->recipient_agency;
            if ($request->has('recipient_agency')) {
                $data = $data->where('recipient_agency_id', $value);
            }

            //filter operator
            $value = $request->operator;
            if ($request->has('operator')) {
                if ($value == 'not-assigned') {
                    $data = $data->whereNull('operator_id');
                } else {
                    $data = $data->where('operator_id', $value);
                }
            }

            //filter status
            $value = $request->get('status');
            if ($request->has('status')) {
                $data = $data->where('status_id', $value);
            }

            //filter conferred
            $value = $request->get('conferred');
            if ($request->has('conferred')) {
                if ($value == 0) {
                    $data = $data->where(function ($q) {
                        $q->whereNull('customer_conferred');
                        $q->orWhere('customer_conferred', 0);
                    });
                } else {
                    $data = $data->where('customer_conferred', 1);
                }
            }

            //filter billed
            $value = $request->billed;
            if ($request->has('billed')) {
                if ($value == 1) {
                    $data = $data->whereNotNull('invoice_doc_id');
                } else {
                    $data = $data->whereNull('invoice_doc_id');
                }
            }

            //filter charge
            $value = $request->charge;
            if ($request->has('charge')) {
                if ($value == 0) {
                    $data = $data->whereNull('charge_price');
                } elseif ($value == 1) {
                    $data = $data->whereNotNull('charge_price');
                }
            }

            //filter price_fixed
            $value = $request->price_fixed;
            if ($request->has('price_fixed')) {
                $data = $data->where('price_fixed', $value);
            }


            //filter sender country
            $value = $request->get('sender_country');
            if ($request->has('sender_country')) {
                $data = $data->where('sender_country', $value);
            }

            //filter recipient country
            $value = $request->get('recipient_country');
            if ($request->has('recipient_country')) {
                $data = $data->where('recipient_country', $value);
            }

            //filter price 0
            $value = $request->price;
            if ($request->has('price')) {
                if ($value == 1) {
                    $data = $data = $data->where(function ($q) {
                        $q->whereNull('total_price');
                        $q->orWhere('total_price', 0.00);
                    });
                } else {
                    $data = $data->where('total_price', '>', 0.00);
                }
            }

            //filter empty country
            $value = $request->empty_country;
            if ($request->has('empty_country')) {
                if ($value == 1) {
                    $data = $data = $data->where(function ($q) {
                        $q->where(function ($q) {
                            $q->whereNull('sender_country');
                            $q->orWhere('sender_country', '');
                        });
                        $q->orWhere(function ($q) {
                            $q->whereNull('recipient_country');
                            $q->orWhere('recipient_country', '');
                        });
                    });
                } else {
                    $data = $data->where(function ($q) {
                        $q->whereNotNull('sender_country');
                        $q->orWhereNotNull('recipient_country');
                    });
                }
            }

            $value = $request->empty_children;
            if ($request->has('empty_children')) {
                if ($value == 1) {
                    $data = $data->whereNull('children_tracking_code');
                } else {
                    $data = $data->whereNotNull('children_tracking_code');
                }
            }


            $ids = $data->pluck('id')->toArray();
            $ids = ['shipments' => $ids];
        }


        $customer = Customer::find($customerId);
        $billing  = CustomerBilling::getBilling($customer->id, $month, $year, $period, $ids);

        if (@$ids['covenants'] && !@$ids['shipments']) { //faturar só avenças
            $dates = null; //para obter a data automática
        } else {
            $dates = Shipment::filterAgencies()
                ->whereIn('id', @$ids['shipments'])
                ->whereNull('invoice_doc_id')
                ->first([DB::raw('min(date) as min'), DB::raw('max(date) as max')])
                ->toArray();
        }

        
        $referencePeriod = $billing->exists ? (trans('datetime.month-tiny.' . $month) . '/' . $year . ($period != '30d' ? '/' . strtoupper($period) : '')) : null;
        $billing->reference_period  = $referencePeriod;
       
        if(Setting::get('invoices_obs_auto')) { //ativa ou não as observações automáticas
            $billing->obs = 'Referente aos serviços no período ' . $referencePeriod . "\n";
            if (@$dates['min']) {
                if(@$dates['min'] != @$dates['max']) {
                    $referencePeriod = '';
                    $dtMin = new Date($dates['min']);
                    $dtMin = $dtMin->format('d/M/Y');
                    $dtMax = new Date($dates['max']);
                    $dtMax = $dtMax->format('d/M/Y');
                    $billing->obs = 'Referente a serviços entre '.$dtMin . ' e ' . $dtMax;
                } else {
                    $dtMin = new Date($dates['min']);
                    $dtMin = $dtMin->format('d/M/Y');
                    $referencePeriod = '';
                    $billing->obs = 'Referente a serviços dia '.$dtMin;
                }
            }
            $billing->obs.= ' (Conforme resumo de serviços em anexo) ';
        }
        
        $billing->obs.= "\n".Setting::get('invoice_obs'); //junta observacoes personalizadas
        $billing->obs = Invoice::prefillObs($billing->obs, $billing);
    
        $billing->billing_type      = 'partial';
        $billing->payment_condition = $billing->payment_method;
        $billing->reference         = $referencePeriod;
        if ($customer->billing_reference) {
            $billing->reference = $customer->billing_reference;
        }

        if (count(@$ids['shipments']) == 1) {
            $billing->billing_type = 'single';
        }

        if (in_array($billing->payment_method, ['prt', 'dbt'])) {
            $invoiceLimitDays = PaymentCondition::getDays($billing->payment_method);
        } else {
            $invoiceLimitDays = str_replace('d', '', $billing->payment_method);
        }

        if (Setting::get('billing_force_today')) {
            $docDate      = date('Y-m-d');
            $docLimitDate = Carbon::today()->addDays($invoiceLimitDays)->format('Y-m-d');
        } else {
            if ($curMonth == $month) {
                $docDate = date('Y-m-d');
                /*$docLimitDate = new Carbon('last day of next month');
                $docLimitDate = $docLimitDate->format('Y-m-d');*/
                $docLimitDate = new Carbon();
                $docLimitDate = $docLimitDate->addDays($invoiceLimitDays)->format('Y-m-d');
            } else {
                $docDate = new Carbon('last day of last month');
                $docDate = $docDate->format('Y-m-d');
                $docLimitDate = new Carbon('last day of last month');
                $docLimitDate = $docLimitDate->addDays($invoiceLimitDays)->format('Y-m-d');
            }
        }

        if (in_array(Setting::get('billing_method'), ['7d', '15d'])) {
            $docLimitDate = new Carbon($docLimitDate);
            $docLimitDate = $docLimitDate->subDays(str_replace('d', '', Setting::get('billing_method')));
            $docLimitDate = $docLimitDate->format('Y-m-d');
        }

        $apiKeys  = Invoice::getApiKeys($customer->company_id);
        $vatTaxes = Invoice::getVatTaxes();
        $newCustomerCode = $customer->setCode(false);

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
            'customer'  => $customer->id,
            'target'    => Invoice::TARGET_CUSTOMER_BILLING,
            'month'     => $month,
            'year'      => $year,
            'period'    => $period
        ]);

        $appCountry = Setting::get('app_country');

        $customerCategories = CustomerType::remember(config('cache.query_ttl'))
            ->cacheTags(CustomerType::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $formOptions = ['url' => $route, 'method' => 'POST', 'class' => 'form-billing'];

        $action = 'Faturar Envios Selecionados';

        $data = compact(
            'billing',
            'billingMonth',
            'agencies',
            'customer',
            'year',
            'month',
            'docDate',
            'docLimitDate',
            'apiKeys',
            'vatTaxes',
            'period',
            'action',
            'formOptions',
            'newCustomerCode',
            'paymentConditions',
            'paymentMethods',
            'appCountry',
            'customerCategories'
        );

        return view('admin.invoices.sales.edit', $data)->render();
    }

    /**
     * Update shipment billing country
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateBillingDate(Request $request)
    {

        $ids = explode(',', $request->ids);
        $billingDate = $request->billing_date;

        $result = Shipment::filterMyAgencies()
            ->whereIn('id', $ids)
            ->whereNull('invoice_doc_id')
            ->update(['billing_date' => $billingDate]);

        if ($result) {
            return Redirect::back()->with('success', 'Alteração de data gravada com sucesso.');
        }

        return Redirect::back()->with('error', 'Erro ao gravar alterações.');
    }

    /**
     * Mass confirm shipments list
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function massConfirmShipments(Request $request)
    {

        $ids = $request->ids;
        $ids = explode(',', $ids);

        $shipments = Shipment::filterAgencies()
            ->whereIn('id', $ids)
            ->get();

        foreach ($shipments as $shipment) {
            if ($request->has('confirm_status')) {
                $shipment->customer_conferred = $request->get('confirm_status', false);
            } else {
                $shipment->customer_conferred = !$shipment->customer_conferred;
            }
            $shipment->save();
        }

        $feedback = 'Envio confirmado com sucesso.';
        if (count($ids) > 1) {
            $feedback = 'Envios selecionados confirmados com sucesso.';
        }

        $row = $shipment;

        if ($request->ajax()) {
            return Response::json([
                'result'   => true,
                'feedback' => $feedback,
                'html'     => view('admin.billing.customers.datatables.shipments.conferred', compact('row'))->render()
            ]);
        }

        return Redirect::back()->with('success', $feedback);
    }

    /**
     * Update shipment billing country
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massUpdate(Request $request)
    {

        $ids              = explode(',', $request->ids);
        $serviceId        = $request->assign_service_id;
        $providerId       = $request->assign_provider_id;
        $senderCountry    = $request->assign_sender_country;
        $recipientCountry = $request->assign_recipient_country;
        $costPrice        = $request->assign_cost;
        $totalPrice       = $request->assign_price;
        $calcPrices       = $request->get('calc_prices', false);
        $blockPrices      = $request->block_prices;

        try {
            if (!empty($serviceId)) {
                $data['service_id'] = $serviceId;
            }

            if (!empty($providerId)) {
                $data['provider_id'] = $providerId;
            }

            if (!empty($senderCountry)) {
                $data['sender_country'] = strtolower($senderCountry);
            }

            if (!empty($recipientCountry)) {
                $data['recipient_country'] = strtolower($recipientCountry);
            }

            if (!empty($costPrice)) {
                $data['cost_price'] = $costPrice;
            }

            if (!empty($totalPrice)) {
                $data['total_price'] = $totalPrice;
            }

            if ($blockPrices == '1' || $blockPrices == '0') {
                $data['price_fixed'] = $blockPrices;
            }

            foreach ($ids as $id) {
                $shipment = Shipment::find($id);
                $shipment->fill($data);
                $shipment->save();

                if ($calcPrices && empty($costPrice) && empty($totalPrice)) {
                    $shipment->updatePrices();
                }
            }

            return Redirect::back()->with('success', 'Alteração gravada com sucesso.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'As alterações não foram gravadas.');
        }
    }

    /**
     * Return customer details by given nif
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateFilters(Request $request)
    {

        $year   = $request->get('year');
        $month  = $request->get('month');
        $period = $request->get('period', '30d');

        $years = yearsArr(2016, date('Y') + 1, true);

        if ($year == date('Y')) {
            $curMonth = date('m');

            if ((int) $month > (int) $curMonth) {
                $month = (int) $curMonth;
            }

            /*$months = [];
            for ($i = 1 ; $i < $curMonth + 1 ; $i++) {
                if($curMonth <= 12) {
                    $months[$i] = trans('datetime.list-month.' . $i);
                }
            }*/

            $months = trans('datetime.list-month');
        } else {
            $months = trans('datetime.list-month');
        }

        $curPeriod = Setting::get('billing_method') ? Setting::get('billing_method') : '30d';

        if ($curPeriod != '30d') {
            if (date('d') < '16') {
                $curPeriod = '1q';
            } else {
                $curPeriod = '2q';
            }
        }

        $result = [
            'year'   => $year,
            'month'  => $month,
            'period' => $period,
            'html'   => view('admin.billing.customers.partials.filters', compact('years', 'months', 'year', 'month', 'period', 'curPeriod'))->render()
        ];

        return Response::json($result);
    }


    /**
     * Print list of shipments
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printShipments(Request $request, $customerId)
    {

        $year   = $request->has('year')  ? $request->year : date('Y');
        $month  = $request->has('month') ? $request->month : date('n');
        $period = $request->has('period') ? $request->period : '30d';
        $apiKey = $request->get('key');

        CustomerBilling::printShipments($customerId, $month, $year, 'I', null, $period, $apiKey);
    }

    /**
     * Open modal to edit billing email
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function editEmail(Request $request, $customerId)
    {

        $year   = $request->get('year');
        $month  = $request->get('month');
        $period = $request->get('period', '30d');

        $customer = Customer::find($customerId);

        $invoices = CustomerBilling::where('customer_id', $customerId)
            ->where('month', $month)
            ->where('year', $year)
            ->where('period', $period)
            ->get([
                'customer_id',
                'invoice_id',
                'invoice_doc_id',
                'invoice_type',
                'invoice_draft',
                'api_key'
            ]);

        $data = compact(
            'customer',
            'invoices',
            'month',
            'year',
            'period'
        );

        return view('admin.billing.customers.modals.email', $data)->render();
    }

    /**
     * Submit billing email
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendEmail(Request $request, $customerId)
    {

        $filesAttach = $request->get('attachments', []);
        $invoices    = $request->get('invoices', []);
        $year        = $request->get('year');
        $month       = $request->get('month');
        $period      = $request->get('period', '30d');
        $email       = $request->get('email');
        $apiKey      = $request->get('key');
        $filename    = Billing::getPeriodName($year, $month, $period);
        $attachments = [];

        try {

            $invoices = CustomerBilling::where('customer_id', $customerId)
                ->where('month', $month)
                ->where('year', $year)
                ->where('period', $period)
                ->whereIn('invoice_doc_id', $invoices)
                ->get([
                    'customer_id',
                    'invoice_id',
                    'invoice_doc_id',
                    'invoice_type',
                    'api_key'
                ]);

            $summary = CustomerBilling::printShipments($customerId, $month, $year, 'array', null, $period, $apiKey);

            //add attachments
            if (in_array('summary', $filesAttach)) {
                $attachments[] = $summary;
            }

            if (in_array('excel', $filesAttach)) {

                $shipments = $summary['shipments'];

                $request = new \Illuminate\Http\Request();
                $request->year   = $year;
                $request->month  = $month;
                $request->period = $period;
                $request->ids    = $shipments->pluck('id')->toArray();
                $request->exportString = true;

                $controller = new BillingController();
                $content    = $controller->customerShipments($request, $customerId);

                $attachments[] = [
                    'mime'      => null,
                    'filename'  => 'Resumo Serviços - ' . $filename . '.xlsx',
                    'content'   => $content
                ];
            }

            //Attach invoices
            if (!$invoices->isEmpty()) {

                foreach ($invoices as $invoice) {

                    if (@$invoice->invoice_doc_id) {

                        $data = [
                            'id'          => $invoice->invoice_id,
                            'customer_id' => $invoice->customer_id,
                            'api_key'     => $invoice->api_key,
                            'doc_id'      => $invoice->invoice_doc_id,
                            'doc_type'    => $invoice->invoice_type
                        ];

                        $content = Invoice::downloadPdf($data, 'string');
                        $content = base64_decode($content);

                        $attachments[] = [
                            'mime'      => 'application/pdf',
                            'filename'  => 'Fatura - ' . trans('admin/billing.types_code.' . $invoice->invoice_type) . ' ' . $invoice->invoice_doc_id  . '.pdf',
                            'content'   => $content
                        ];
                    }
                }
            }

            //validate emails
            $emails = null;
            if (!empty($email)) {
                $emails = validateNotificationEmails($email);
                $emails = $emails['valid'];

                if (!$emails) {
                    throw new \Exception('O e-mail indicado é inválido.');
                }
            }

            //add emails in CC
            $emailsCC = null;
            if (!empty(Setting::get('billing_email_cc'))) {
                $emailsCC = validateNotificationEmails(Setting::get('billing_email_cc'));
                $emailsCC = $emailsCC['valid'];
            }

            $data = [
                'period_name' => $filename,
                'subject'     => 'Resumo de serviços - ' . Billing::getPeriodName($year, $month, $period)
            ];

            Mail::send('emails.billing.customer_month', compact('data'), function ($message) use ($data, $emails, $emailsCC, $attachments) {

                $message->to($emails);

                if ($emailsCC) {
                    $message = $message->cc($emailsCC);
                }

                $message = $message->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject($data['subject']);

                foreach ($attachments as $attachment) {
                    $message->attachData(
                        $attachment['content'],
                        $attachment['filename'],
                        $attachment['mime'] ? ['mime' => $attachment['mime']] : []
                    );
                }
            });

            if (count(Mail::failures()) > 0) {
                return Response::json([
                    'result'   => false,
                    'feedback' => 'Não foi possível enviar o e-mail. Não selecionou nenhum documento para enviar em anexo.'
                ]);
            }

            return Response::json([
                'result'   => true,
                'feedback' => 'E-mail enviado com sucesso.'
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'result'   => false,
                'feedback' => $e->getMessage()
            ]);
        }
    }

    /**
     * Massive billing
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massBillingEdit(Request $request)
    {

        $month = $request->get('month');
        $month = $month ? $month : date('m');

        $year = $request->get('year');
        $year = $year ? $year : date('Y');

        $period = $request->get('period');
        $period = $period ? $period : '30d';

        $paymentConditions = PaymentCondition::filterSource()
            ->ordered()
            ->pluck('name', 'code')
            ->prepend('Definido por cliente', 'auto')
            ->toArray();

        $curMonth = date('n');

        if (Setting::get('billing_force_today')) {
            $docDate = date('Y-m-d');
        } else {
            if ($curMonth == $month) {
                $docDate = date('Y-m-d');
            } else {
                $docDate = new Carbon('last day of last month');
                $docDate = $docDate->format('Y-m-d');
            }
        }

        $apiKeys  = Invoice::getApiKeys();

        $reference = CustomerBilling::getDefaultReference($year, $month, $period);

        $customers = CustomerBilling::getCustomersToBilling($year, $month, $period);

        $data = compact(
            'period',
            'year',
            'month',
            'docDate',
            'apiKeys',
            'reference',
            'customers',
            'paymentConditions'
        );

        return view('admin.billing.customers.modals.mass_billing', $data)->render();
    }

    /**
     * Massive billing
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massBillingStore(Request $request)
    {
        $month = $request->get('month');
        $month = $month ? $month : date('m');

        $year = $request->get('year');
        $year = $year ? $year : date('Y');

        $period = $request->get('period');
        $period = $period ? $period : '30d';

        $paymentCondition = $request->get('payment_condition');
        $paymentCondition = $paymentCondition === 'auto' ? null : $paymentCondition;

        $customersIds = $request->get('customer_id');

        if (empty($customersIds)) {
            return Response::json([
                'result'   => false,
                'feedback' => 'Tem de selecionar pelo menos um cliente a faturar'
            ]);
        }

        try {

            $monthCustomers = CustomerBilling::getCustomersToBilling($year, $month, $period, $customersIds);

            //PROCESSA FATURAÇÃO DO MÊS
            $billedItems = [];
            $stopExecution = false;
            foreach ($monthCustomers as $customer) {

                $customerId = $customer->id;

                if (!$stopExecution) {
                    $params = [
                        'api_key'     => $request->api_key,
                        'docdate'     => $request->docdate,
                        'reference'   => $request->reference,
                        'attachments' => $request->attachments,
                        'send_email'  => $request->get('send_email', false)
                    ];

                    $result = CustomerBilling::autoBillingMonth($customerId, $month, $year, $period, $params, $paymentCondition);

                    if (str_contains($result['feedback'], 'Existe um documento com data superior')) {
                        $stopExecution = $result['feedback'];
                    }
                } else {
                    @$result['feedback'] = $stopExecution; //faz com que todos os registos daqui para a frente fiquem com o mesmo feedback mesmo não tendo sido processados.
                }


                if ($result['result']) {
                    $billedItems[$customerId] = [
                        'result'         => true,
                        'customer_id'    => $customerId,
                        'customer_name'  => $customer->name,
                        'feedback'       => @$result['feedback'],
                        'invoice_id'     => @$result['invoice_id'],
                        'invoice_doc_id' => @$result['invoice_doc_id'],
                    ];
                } else {
                    $billedItems[$customerId] = [
                        'result'         => false,
                        'customer_id'    => $customerId,
                        'customer_name'  => $customer->name,
                        'feedback'       => @$result['feedback'],
                        'invoice_id'     => null,
                        'invoice_doc_id' => null,
                    ];
                }
            }

            return Response::json([
                'result'   => true,
                'feedback' => 'Emissão finalizada.',
                'html'     => view('admin.billing.customers.partials.massive_billing_result', compact('billedItems', 'year', 'month', 'period', 'customerId'))->render()
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'result'   => false,
                'feedback' => $e->getMessage() . ' ' . $e->getFile() . ' linha ' . $e->getLine(),
                'html'     => null
            ]);
        }
    }
}
