<?php

namespace App\Http\Controllers\Admin\Exports;

use App\Models\Agency;
use App\Models\CustomerCovenant;
use App\Models\ProductSale;
use App\Models\Provider;
use App\Models\ShippingStatus;
use App\Models\Customer;
use App\Models\CustomerBilling;
use App\Models\Billing;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Http\Request;
use Auth, Excel, File, DB, Date, Response;

class BillingController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = '';

    /**
     * Store last row of each iteration
     *
     * @var type
     */
    protected $lastRow = null;

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
     * Export simple file
     *
     * @return \Illuminate\Http\Response
     */
    public function customerShipments(Request $request, $customerId)
    {

        $year   = $request->year  ? $request->year : date('Y');
        $month  = $request->month ? $request->month : date('n');
        $period = $request->period ? $request->period : '30d';
        $ids    = $request->has('id') ? $request->id : []; //se nao tiver o has('id'), vai retornar valores errados em algumas rotas pelo facto da rota ter o parametro ID
        $exportString    = $request->exportString;

        $periodDates    = Billing::getPeriodDates($year, $month, $period);
        $periodFirstDay = $periodDates['first'];
        $periodLastDay  = $periodDates['last'];

        $shipments = Shipment::filterAgencies()
            ->where(function ($q) {
                $q->where('is_collection', 0);
                $q->orWhere(function ($q) {
                    $q->where('is_collection', 1);
                    $q->where('status_id', ShippingStatus::PICKUP_FAILED_ID);
                });
            })
            ->applyCustomerBillingRequestFilters($request)
            ->whereCustomerId($customerId);

        if ($ids) {
            $shipments = $shipments->whereIn('id', $ids);
        } else {
            $shipments = $shipments->whereBetween('billing_date', [$periodFirstDay, $periodLastDay]);
        }

        if (Auth::user()->isGuest()) {
            $shipments = $shipments->where('agency_id', '99999'); //hide data to gest agency role
        }

        if (empty($ids)) {
            $ids = $shipments->orderBy('billing_date', 'asc')
                ->pluck('id')
                ->toArray();
        }

        $request = new \Illuminate\Http\Request();
        $request->id            = $ids;
        $request->filename      = 'Resumo de Envios - ' . Billing::getPeriodName($year, $month, $period);
        $request->exportString  = $exportString;
        $request->doc_source    = 'billing';


        $controller = new ShipmentsController();
        return $controller->export($request);
    }


    /**
     * Export simple file
     *
     * @return \Illuminate\Http\Response
     */
    public function customerMassShipments(Request $request, $customerId)
    {
        $year   = $request->year  ? $request->year : date('Y');
        $month  = $request->month ? $request->month : date('n');
        $period = $request->period ? $request->period : '30d';

        $exportString    = $request->exportString;

        $periodDates    = Billing::getPeriodDates($year, $month, $period);
        $periodFirstDay = $periodDates['first'];
        $periodLastDay  = $periodDates['last'];

        $shipments = Shipment::filterAgencies()
            ->where(function ($q) {
                $q->where('is_collection', 0);
                $q->orWhere(function ($q) {
                    $q->where('is_collection', 1);
                    $q->where('status_id', ShippingStatus::PICKUP_FAILED_ID);
                });
            })
            ->whereCustomerId($customerId);


        $shipments = $shipments->whereBetween('billing_date', [$periodFirstDay, $periodLastDay]);

        if (Auth::user()->isGuest()) {
            $shipments = $shipments->where('agency_id', '99999'); //hide data to gest agency role
        }

        if (empty($ids)) {
            $ids = $shipments->orderBy('billing_date', 'asc')
                ->pluck('id')
                ->toArray();
        }

        $request = new \Illuminate\Http\Request();
        $request->id            = $ids;
        $request->filename      = 'Resumo de Envios - ' . Billing::getPeriodName($year, $month, $period);
        $request->exportString  = $exportString;
        $request->doc_source    = 'billing';


        $controller = new ShipmentsController();
        return $controller->export($request);
    }

    /**
     * Export simple file
     *
     * @return \Illuminate\Http\Response
     */
    public function providerShipments(Request $request, $providerId)
    {

        $year   = $request->year  ? $request->year : date('Y');
        $month  = $request->month ? $request->month : date('n');
        $period = $request->period ? $request->period : '30d';
        $ids    = $request->get('id');
        $exportString    = $request->exportString;

        $periodDates    = Billing::getPeriodDates($year, $month, $period);
        $periodFirstDay = $periodDates['first'];
        $periodLastDay  = $periodDates['last'];

        $provider = Provider::whereId($providerId)->first();

        $shipments = Shipment::filterAgencies()
            ->whereProviderId($providerId);

        if ($ids) {
            $shipments = $shipments->whereIn('id', $ids);
        } else {
            $shipments = $shipments->whereBetween('billing_date', [$periodFirstDay, $periodLastDay]);
        }

        if (Auth::user()->isGuest()) {
            $shipments = $shipments->where('agency_id', '99999'); //hide data to gest agency role
        }

        if (empty($ids)) {
            $ids = $shipments->orderBy('billing_date', 'asc')
                ->pluck('id')
                ->toArray();
        }

        $request = new \Illuminate\Http\Request();
        $request->id            = $ids;
        $request->filename      = 'Resumo Envios ' . $provider->name . ' - ' . Billing::getPeriodName($year, $month, $period);
        $request->exportString  = $exportString;
        $request->doc_source    = 'billing-providers';


        $controller = new ShipmentsController();
        return $controller->export($request);
    }

    /**
     * Export simple file
     *
     * @return \Illuminate\Http\Response
     */
    public function operatorShipments(Request $request, $operatorId)
    {

        $year   = $request->year  ? $request->year : date('Y');
        $month  = $request->month ? $request->month : date('n');
        $period = $request->period ? $request->period : '30d';
        $exportString    = $request->exportString;

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $periodFirstDay = $request->start_date;
            $periodLastDay  = $request->end_date;

            $periodName = $periodFirstDay . ' a ' . $periodLastDay;
        } else {
            $periodDates    = Billing::getPeriodDates($year, $month, $period);
            $periodFirstDay = $periodDates['first'];
            $periodLastDay  = $periodDates['last'];

            $periodName = Billing::getPeriodName($year, $month, $period);
        }

        $operator = User::whereId($operatorId)->first(['id', 'name']);

        $shipments = Shipment::filterAgencies()
            ->where('status_id', '<>', ShippingStatus::CANCELED_ID)
            ->where('is_collection', 0)
            ->whereBetween('billing_date', [$periodFirstDay, $periodLastDay])
            ->whereOperatorId($operatorId);

        if (Auth::user()->isGuest()) {
            $shipments = $shipments->where('agency_id', '99999'); //hide data to gest agency role
        }

        if (empty($ids)) {
            $ids = $shipments->orderBy('billing_date', 'asc')
                ->pluck('id')
                ->toArray();
        }

        $request = new \Illuminate\Http\Request();
        $request->id            = $ids;
        $request->filename      = 'Resumo de Faturação ' . $operator->name . ' - ' . $periodName;
        $request->exportString  = $exportString;
        $request->doc_source    = 'billing-operators';


        $controller = new ShipmentsController();
        return $controller->export($request);
    }

    /**
     * Export simple file
     *
     * @return \Illuminate\Http\Response
     */
    public function exportFileToSoftware(Request $request, $software = null)
    {

        $file   = storage_path() . '/counters.json';
        $year   = $request->get('year') ? $request->get('year') : date('Y');
        $month  = $request->get('month') ? $request->get('month') : date('m');
        $period = '30d';

        $nextMonth = new Date($year . '-' . $month . '-01');
        $nextMonth = $nextMonth->addMonth(1)->format('Ym');

        $customers = Customer::where('source', 'papiro')->pluck('id')->toArray();

        $it = 1;

        $codeToStart = $request->get('code_export_sap');
        if (!empty($codeToStart)) {
            $it = $codeToStart;
        }


        // if (File::exists($file)) {
        //     $fileContents = json_decode(File::get($file), true);
        //     $it = @$fileContents[$year . $month];
        // }

        $fileRows = [];

        foreach ($customers as $customerId) {

            $billing = CustomerBilling::getBilling($customerId, $month, $year, $period);

            $lineCode     = 'PAP' . str_pad($it, 4, '0', STR_PAD_LEFT);

            $totalPrice   = $billing->total_month;
            $customerCode = $billing->code;
            $billingCode  = $billing->billing_code;

            $date = new Date($year . '-' . $month . '-01');
            $date = $date->endOfMonth()->format('Y/m/d');
            $monthAbrv = trans('datetime.list-month-tiny.' . $month);

            if ($totalPrice > 0.00) {

                $totalPrice2Digit = str_replace('.', '', $totalPrice) . '.00';
                $totalPrice6Digit = number($totalPrice, 6);

                $totalPrice2Digit = $totalPrice2Digit * 1000 . '.00';


                $totalPriceFuel2Dig = str_replace('.', '', $billing['fuel_tax_total_vat']) . '.00';
                $totalPriceFuel6Dig = number($billing['fuel_tax_total_vat'], 6);

                $totalPriceFuel6DigPrimavera = $totalPriceFuel6Dig * 1000;
                $totalPriceFuel6DigPrimavera = number_format($totalPriceFuel6DigPrimavera, 2, '.', '');

                $row = [
                    '5100',         //2. Código de empresa SAP
                    $lineCode,      //1. ID do ficheiro PAP+Nº sequencial
                    $billingCode ? $billingCode : 'XXXXXXX',
                    '',     //6. Referencia da fatura
                    'Estafetagem ' . $monthAbrv . '.' . $year . '-' . $lineCode,             //3. Vazio
                    // $customerCode ? $customerCode : 'XXXXXXX',  //4. Codigo Cliente
                    $date,  //7. Data do ficheiro
                    '000000000000053581',        //8. Código de estafetagem
                    'ESTAFETAGEM',  //9. Não utilizado
                    '',
                    $totalPrice2Digit,    //11. Preço total da linha
                    '1',            //12. Quantidade
                    $totalPrice6Digit,    //13. Não usado
                    '',      //14. Designação do serviço 1
                    '',             //15. Designação do serviço 2
                    '',             //16. Texto do artigo
                    '',     //17. Referencia fatura
                ];



                $fileRows[] = $row;

                $row = [
                    '5100',         //2. Código de empresa SAP
                    $lineCode,      //1. ID do ficheiro PAP+Nº sequencial
                    $billingCode ? $billingCode : 'XXXXXXX',
                    '',     //6. Referencia da fatura
                    'Estafetagem ' . $monthAbrv . '.' . $year . '-' . $lineCode,             //3. Vazio
                    // $customerCode ? $customerCode : 'XXXXXXX',  //4. Codigo Cliente
                    $date,  //7. Data do ficheiro
                    '11930005',        //8. Código de taxa de combustivel
                    'Taxa de Combustivel',  //9. Não utilizado
                    '',
                    $totalPriceFuel6DigPrimavera,    //11. Preço total da linha
                    '1',            //12. Quantidade
                    $totalPriceFuel6Dig,    //13. Não usado
                    '',      //14. Designação do serviço 1
                    '',             //15. Designação do serviço 2
                    '',             //16. Texto do artigo
                    '',     //17. Referencia fatura
                ];

                // $fileRow = implode(',', array_map('strval', $row));
                // $fileRows[] = $fileRow . "\r\n";
                $fileRows[] = $row;

                $it++;
            }
        }

        // $fileContents[$nextMonth] = $it;
        // File::put($file, json_encode($fileContents));

        $filename = 'sap_' . $month . '_' . $year . '.csv';
        $filepath = storage_path() . '/billing/';

        $fp = fopen($filepath . $filename, 'w');

        foreach ($fileRows as $row) {
            fputcsv($fp, $row, ";", '"');
        }
        fclose($fp);

        if (!File::exists($filepath)) {
            File::makeDirectory($filepath);
        }

        // File::put($filepath . $filename, $fileRows);

        return Response::download($filepath . $filename, $filename);
    }

    /**
     * Export summary of all period
     *
     * @return \Illuminate\Http\Response
     */
    public function periodSummary(Request $request)
    {

        $year   = $request->has('year') ? $request->year : date('Y');
        $month  = $request->has('month') ? $request->month : date('n');
        $period = $request->has('period') ? $request->period : '30d';

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $periodFirstDay = $request->start_date;
            $periodLastDay  = $request->end_date;

            $periodName = $periodFirstDay . ' a ' . $periodLastDay;
            $filename = 'Resumo de Faturação - ' . $periodName;
        } else {
            $periodDates    = Billing::getPeriodDates($year, $month, $period);
            $periodFirstDay = $periodDates['first'];
            $periodLastDay  = $periodDates['last'];

            $periodName = Billing::getPeriodName($year, $month, $period);
            $filename = 'Resumo de Faturação - ' . $periodName;
        }

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
            'customers.vat as vat',
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
            /*$subSqlQueries[] = 'COALESCE((select COALESCE(sum(total_price), 0) + COALESCE(sum(fuel_price), 0) + COALESCE(sum(total_expenses), 0)
                         from shipments
                         where deleted_at is null and ignore_billing = 0 and
                         status_id not in (' . ShippingStatus::CANCELED_ID . ') and
                         cod is null and
                         (billing_date between "' . $periodFirstDay . '" and "' . $periodLastDay . '") and
                         customer_id = customers.id), 0)';*/

            $subSqlQueries[] = 'COALESCE((select COALESCE(sum(billing_subtotal), 0) + COALESCE(sum(fuel_price), 0) + COALESCE(sum(total_expenses), 0)  
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
                $data = $data->whereRaw($subSql . ' = 0');
            } else {
                $data = $data->whereRaw($subSql . ' > 0');
            }
        }

        $data = $data->get($bindings);


        $header = [
            'De',
            'Até',
            'Código',
            'Cliente',
            'NIF',
            'Documento',
            'Nº Serviços',
            'Total Envios',
            'Total Avenças',
            'Total Outros',
            'Por faturar',
            'Total Periodo',
            'Custos',
            'Saldo'
        ];

        $excel = Excel::create($filename, function ($file) use ($data, $header, $periodFirstDay, $periodLastDay) {

            $file->sheet('Listagem', function ($sheet) use ($data, $header, $periodFirstDay, $periodLastDay) {

                $sheet->row(1, $header);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#ee7c00');
                    $row->setFontColor('#ffffff');
                });

                $sheet->setColumnFormat(array(
                    'A' => '@', //first date
                    'B' => '@', //last date
                    'C' => '@', //code
                    'D' => '@', //name
                    'E' => '@', //vat
                    'F' => '@', //documento
                    //'G' => '', //nº servicos
                    'H' => '0.00',
                    'I' => '0.00',
                    'J' => '0.00',
                    'K' => '0.00',
                    'L' => '0.00',
                    'M' => '0.00',
                    'N' => '0.00',
                ));

                foreach ($data as $billing) {

                    $customer = $billing;
                    $price = (@$billing->shipments->sum('shipping_price') + @$billing->shipments->sum('expenses_price') + @$billing->shipments->sum('fuel_price'));
                    $cost  = @$billing->shipments->sum('cost_shipping_price') + @$billing->shipments->sum('cost_expenses_price');



                    //get total month
                    $total = $price;
                    $total += @$billing->productsBought->sum('subtotal');
                    $total += @$billing->covenants->sum('amount');

                    //get total unbilled
                    $shipmentsIds = [];
                    $covenantsIds = [];
                    $productsIds  = [];
                    if ($billing->billing) {
                        foreach ($billing->billing as $billedInfo) {
                            $shipmentsIds = array_merge($shipmentsIds, (array) $billedInfo->shipments);
                            $covenantsIds = array_merge($covenantsIds, (array) $billedInfo->covenants);
                            $productsIds  = array_merge($productsIds, (array) $billedInfo->products);
                        }
                    }


                    //filter shipments, covenants and products that not in filtred ids
                    $unbilledShipments = null;
                    if (!is_array($billing->shipments)) {
                        $unbilledShipments = $billing->shipments ? @$billing->shipments->filter(function ($item) use ($shipmentsIds) {
                            return !in_array($item->id, $shipmentsIds) && !$item->ignore_billing && !$item->payment_at_recipient;
                        }) : null;
                    }

                    $unbilledCovenants = $billing->covenants ? @$billing->covenants->filter(function ($item) use ($covenantsIds) {
                        return !in_array($item->id, $covenantsIds);
                    }) : null;

                    $unbilledProducts = $billing->productsBouth ? @$billing->productsBouth->filter(function ($item) use ($productsIds) {
                        return !in_array($item->id, $productsIds);
                    }) : null;

                    $totalUnbilled = @$unbilledShipments ? (@$unbilledShipments->sum('shipping_price')
                        + @$unbilledShipments->sum('expenses_price')
                        + @$unbilledShipments->sum('fuel_price')) : 0;

                    if ($unbilledCovenants) {
                        $totalUnbilled += @$unbilledCovenants->sum('subtotal');
                    }

                    if ($unbilledProducts) {
                        $totalUnbilled += @$unbilledProducts->sum('amount');
                    }

                    $rowData = [
                        $periodFirstDay,
                        $periodLastDay,
                        strtoupper($customer->code),
                        strtoupper($customer->name),
                        $customer->vat,
                        $customer->default_invoice_type ? trans('admin/billing.types_code.' . $customer->default_invoice_type) : '',
                        is_array(@$billing->shipments) ? 0 : (@$billing->shipments ? @$billing->shipments->count() : 0),
                        $price,
                        @$billing->covenants ? $billing->covenants->sum('amount') : 0,
                        @$billing->productsBought ? $billing->productsBought->sum('subtotal') : 0,
                        $totalUnbilled,
                        $total,
                        $cost,
                        $total - $cost
                    ];


                    $sheet->appendRow($rowData);
                }
            });
        });

        if ($request->exportString) {
            return file_get_contents($excel->store("xlsx", false, true)['full']);
        }

        return $excel->export('xlsx');
    }
}