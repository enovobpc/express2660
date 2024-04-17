<?php

namespace App\Http\Controllers\Admin;

use App\Models\AddressBook;
use App\Models\Service;
use App\Models\ShipmentHistory;
use App\Models\Statistic;
use App\Models\Agency;
use App\Models\CalendarEvent;
use App\Models\Customer;
use App\Models\Shipment;
use App\Models\ShippingStatus;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Date, Session, DB, Setting, Response, File;
use Mpdf\Mpdf;

class HomeController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){}

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(Auth::user()->id == '679') {
            return Redirect::route('admin.logistic.products.index');
        }

        if(Auth::user()->hasRole('operador')) {
            return Redirect::route('mobile.index');
        }

        if(Auth::user()->hasRole(config('permissions.role.platformer'))) {
            return Redirect::route('admin.traceability.index');
        }

        $now    = new Date();
        $endDay = date('Y-m-d 23:59:59');
        $rememberTime = $now->diffInMinutes($endDay); //remember only to end of day

        $year  = !empty($request->get('billing_year'))  ? $request->get('billing_year')  : date('Y');
        $month = !empty($request->get('billing_month')) ? $request->get('billing_month') : date('m');
        $dates = Statistic::getPeriodDates('monthly', ['year' => $year, 'month' => $month]);
        $startDate = $dates['start_date'];
        $endDate   = $dates['end_date'];

        $startCurDate   = new Date($startDate);
        $startLastMonth = $startCurDate->subMonth()->startOfMonth()->format('Y-m-d H:i:s');
        $endLastMonth   = $startCurDate->endOfMonth()->format('Y-m-d H:i:s');

        $sourceAgencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->whereSource(config('app.source'))
            ->pluck('id')
            ->toArray();

        $calendarEvents = CalendarEvent::filterEvents()
            ->where('start', '>=', date('Y-m-d'))
            ->orderBy('start', 'asc')
            ->take('30')
            ->get(['*', DB::raw('DATE(start) as date')]);
        $calendarEvents = $calendarEvents->groupBy('date');

        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterAgencies($sourceAgencies)
            ->withTrashed()
            ->showOnPricesTable()
            ->get(['id', 'display_code', 'name']);

        $operators = User::whereSource(config('app.source'))
            ->filterAgencies()
            ->isOperator()
            ->orderBy('location_last_update', 'desc')
            ->get();

        $bindings = [
            'id',
            'code',
            'name',
            'zip_code',
            'city',
            DB::raw('(select max(date) from shipments where shipments.customer_id = customers.id and deleted_at is null limit 0,1) as last_shipment'),
            DB::raw('(select count(date) from shipments where shipments.customer_id = customers.id and deleted_at is null) as total_shipments')
        ];

        $inactiveCustomers = Customer::remember($rememberTime)
            ->filterAgencies($sourceAgencies)
            ->filterSeller()
            ->whereHas('shipments', function($q) {
                $q->havingRaw('max(shipments.date) <= CURDATE() -  INTERVAL '. (Setting::get('alert_max_days_without_shipments') ? Setting::get('alert_max_days_without_shipments') : 45) .' DAY');
            })
            ->take(50)
            ->orderBy('last_shipment', 'desc')
            ->get($bindings);

        $pendingShipments = Shipment::with('customer')
            ->whereHas('customer', function($q){
                $q->filterSeller();
                $q->whereSource(config('app.source'));
            })
            ->filterAgencies()
            ->where('status_id', ShippingStatus::PENDING_ID)
            ->groupBy('customer_id')
            ->get([
                'id',
                'customer_id',
                DB::raw('count(*) as total_pending')
            ]);

        /**
         * Global stats
         */
        $bindings = [
            DB::raw('DATE_FORMAT(billing_date, \'%Y-%m\') as billing_date'),
            DB::raw('count(*) as count'),
            DB::raw('avg(weight) as weight'),
            DB::raw('sum(volumes) as volumes'),
            DB::raw('sum(total_price) as price'),
            DB::raw('sum(total_expenses) as expenses'),
            DB::raw('sum(fuel_price) as fuel'),
            DB::raw('sum(total_price_for_recipient) as total_pod')
        ];

        $lastYear = (date('Y') - 1).'-'. date('m').'-01';
        $monthStats = Shipment::remember($rememberTime)
            ->where('is_collection', 0)
            ->filterAgencies()
            ->whereBetween('billing_date', [$lastYear, date('Y-m-d')]);

            if(Auth::user()->isSeller()) { //filtra o vendedor
                $monthStats = $monthStats->whereHas('customer', function ($q) {
                    $q->filterSeller();
                });
            }

        $monthStats = $monthStats->where('status_id', '<>', ShippingStatus::CANCELED_ID)
            ->groupBy(DB::raw('DATE_FORMAT(billing_date, \'%Y-%m\')'))
            ->orderBy('billing_date', 'asc')
            ->get($bindings);

        $incidencesStats = ShipmentHistory::remember($rememberTime)
            ->whereHas('shipment', function($q) use($sourceAgencies) {
                $q->filterAgencies();
                //$q->whereIn('agency_id', $sourceAgencies);
            })
            ->where('status_id', ShippingStatus::INCIDENCE_ID)
            ->groupBy(DB::raw('DATE_FORMAT(created_at, \'%Y-%m\')'))
            ->get([
                DB::raw('DATE_FORMAT(created_at, \'%Y-%m\') as date'),
                DB::raw('count(*) as count')
            ]);


        $chartLabels = $billing = $shipments = $volumes = $incidences = $weight = [];
        foreach ($monthStats as $row) {

            $date = $row->billing_date;
            $incidence = $incidencesStats->filter(function($item) use($date) {
                return $item->date == $date;
            })->first();

            $date = explode('-', $row->billing_date);
            $month = @$date[1];
            $year  = @$date[0];

            $chartLabels[]  = '"'. trans('datetime.month-tiny.' . $month). ' ' . $year. '"';
            $billing[]      = $row->price + $row->expenses + $row->fuel + $row->total_pod;
            $shipments[]    = $row->count;
            $volumes[]      = $row->volumes;
            $weight[]       = $row->weight;
            $incidences[]   = @$incidence->count;
        }

        $globalChartData = [
            'labels'   => implode(',', $chartLabels),
            'billing'   => implode(',', $billing),
            'shipments' => implode(',', $shipments),
            'volumes'   => implode(',', $volumes),
            'weight'    => implode(',', $weight),
            'incidences' => implode(',', $incidences),
        ];


        if(config('app.source') == 'lousaestradas') {

            //LOUSADA
            $monthStats1 = Shipment::remember($rememberTime)
                ->where('is_collection', 0)
                ->filterAgencies()
                ->whereBetween('billing_date', [$lastYear, date('Y-m-d')])
                ->where('agency_id', 141)
                ->where('status_id', '<>', ShippingStatus::CANCELED_ID)
                ->groupBy(DB::raw('DATE_FORMAT(billing_date, \'%Y-%m\')'))
                ->orderBy('billing_date', 'asc')
                ->get($bindings);

            $chartLabels = $billing = $shipments = $volumes = $incidences = $weight = [];
            foreach ($monthStats1 as $row) {

                $date = $row->billing_date;
                $incidence = $incidencesStats->filter(function($item) use($date) {
                    return $item->date == $date;
                })->first();

                $date = explode('-', $row->billing_date);
                $month = @$date[1];
                $year  = @$date[0];

                $chartLabels[]  = '"'. trans('datetime.month-tiny.' . $month). ' ' . $year. '"';
                $billing[]      = $row->price + $row->expenses + $row->total_pod;
                $shipments[]    = $row->count;
                $volumes[]      = $row->volumes;
                $weight[]       = $row->weight;
                $incidences[]   = @$incidence->count;
            }

            $globalChartData = [
                'labels'   => implode(',', $chartLabels),
                'billing'   => implode(',', $billing),
                'shipments' => implode(',', $shipments),
                'volumes'   => implode(',', $volumes),
                'weight'    => implode(',', $weight),
                'incidences' => implode(',', $incidences),
            ];


            //AMARANTE
            $monthStats2 =  Shipment::remember($rememberTime)
                ->where('is_collection', 0)
                ->filterAgencies()
                ->whereBetween('billing_date', [$lastYear, date('Y-m-d')])
                ->where('agency_id', 140)
                ->where('status_id', '<>', ShippingStatus::CANCELED_ID)
                ->groupBy(DB::raw('DATE_FORMAT(billing_date, \'%Y-%m\')'))
                ->orderBy('billing_date', 'asc')
                ->get($bindings);

            $chartLabels = $billing2 = $shipments2 = $volumes2 = $incidences2 = $weight2 = [];
            foreach ($monthStats2 as $row) {

                $date = $row->billing_date;
                $incidence = $incidencesStats->filter(function($item) use($date) {
                    return $item->date == $date;
                })->first();

                $date = explode('-', $row->billing_date);
                $month = @$date[1];
                $year  = @$date[0];

                $chartLabels[]  = '"'. trans('datetime.month-tiny.' . $month). ' ' . $year. '"';
                $billing2[]      = $row->price + $row->expenses + $row->total_pod;
                $shipments2[]    = $row->count;
                $volumes2[]      = $row->volumes;
                $weight2[]       = $row->weight;
                $incidences2[]   = @$incidence->count;
            }

            $globalChart2Data = [
                'labels'   => implode(',', $chartLabels),
                'billing'   => implode(',', $billing2),
                'shipments' => implode(',', $shipments2),
                'volumes'   => implode(',', $volumes2),
                'weight'    => implode(',', $weight2),
                'incidences' => implode(',', $incidences2),
            ];
        }

        $bindings = [
            'id',
            'agency_id',
            'status_id',
            'weight',
            'total_price',
            'total_expenses',
            'fuel_price',
            'service_id',
            'provider_id',
            'recipient_zip_code',
            'recipient_country',
            'sender_country',
        ];

        $allShipments = Shipment::remember(15)
            ->with(['history' => function($q){
                $q->remember(15);
                $q->whereIn('status_id', [ShippingStatus::INCIDENCE_ID]);
            }]);

        if(Auth::user()->isSeller()) { //filtra só do vendedor
            $allShipments = $allShipments->whereHas('customer', function ($q) {
                $q->filterSeller();
            });
        }

        $allShipments = $allShipments->where('is_collection', 0)
            //->whereIn('agency_id', $sourceAgencies)
            ->filterAgencies()
            ->where('status_id', '<>', ShippingStatus::CANCELED_ID)
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->get($bindings);

        $allShipmentsLastMonth = Shipment::remember(15)
            ->with(['history' => function($q){
                $q->remember(15);
                $q->whereIn('status_id', [ShippingStatus::INCIDENCE_ID]);
            }])
            ->filterAgencies()
            //->whereIn('agency_id', $sourceAgencies)
            ->where('status_id', '<>', ShippingStatus::CANCELED_ID)
            ->whereBetween('date', [$startLastMonth, $endLastMonth])
            ->get($bindings);


        //STATS BY SERVICE
        $allShipmentsStatus = Shipment::remember(15)
            //->whereIn('agency_id', $sourceAgencies)
            ->filterAgencies()
            ->whereIn('status_id', [
                ShippingStatus::PENDING_ID,
                ShippingStatus::ACCEPTED_ID,
                ShippingStatus::IN_PICKUP_ID,
                ShippingStatus::IN_TRANSPORTATION_ID,
                ShippingStatus::IN_DISTRIBUTION_ID,
                ShippingStatus::INCIDENCE_ID,
            ])
            ->where('is_collection', 0);

        if(Auth::user()->isSeller()) { //filtra só do vendedor
            $allShipmentsStatus = $allShipmentsStatus->whereHas('customer', function ($q) {
                $q->filterSeller();
            });
        }

        $allShipmentsStatus = $allShipmentsStatus->orderBy('date', 'desc')
            ->get(['id','status_id', 'service_id']);

        $deliveredToday = Shipment::whereIn('agency_id', $sourceAgencies)
            ->whereHas('last_history', function ($q){
                $q->where('status_id', ShippingStatus::DELIVERED_ID);
                $q->whereRaw('DATE(created_at) = "'. date('Y-m-d') .'"');
            })
            ->get(['id','status_id', 'service_id']);

        $groupedServices = $allShipmentsStatus->groupBy('service_id');
        $statusStatistics = [];
        foreach ($groupedServices as $serviceId => $shipments) {

            $shipments = $shipments->groupBy('status_id');

            $delivered = $deliveredToday->filter(function ($item) use($serviceId) { return $item->service_id == $serviceId; })->count();

            $status = [
                'pending'   => count(@$shipments[ShippingStatus::PENDING_ID]),
                'accepted'  => count(@$shipments[ShippingStatus::ACCEPTED_ID]),
                'pickup'    => count(@$shipments[ShippingStatus::IN_PICKUP_ID]),
                'transit'   => count(@$shipments[ShippingStatus::IN_TRANSPORTATION_ID]) + count(@$shipments[ShippingStatus::IN_DISTRIBUTION_ID]),
                'delivered' => $delivered,
                'incidence' => count(@$shipments[ShippingStatus::INCIDENCE_ID]),
            ];

            $statusStatistics[$serviceId] = $status;
        }

        //total customers
        $newCustomers = Customer::remember(15)
            ->filterSource()
            ->isProspect(false)
            ->where('created_at', '>=', $startLastMonth)
            ->get(['created_at']);

        $customersLastMonth = $newCustomers->filter(function ($item) use($endLastMonth) {
            return $item->created_at <= $endLastMonth;
        });
        $customersCurMonth = $newCustomers->filter(function ($item) use($endLastMonth) {
            return $item->created_at > $endLastMonth;
        });

        $totals = [];

        $totals['last_month'] = [
            'total_billing'   => @$allShipmentsLastMonth->sum('total_price') + @$allShipmentsLastMonth->sum('total_expenses') + @$allShipmentsLastMonth->sum('fuel_price'),
            'count_shipments' => @$allShipmentsLastMonth->count(),
            'shipments_day'   => @$allShipmentsLastMonth->count() / 22,
            'new_customers'   => @$customersLastMonth->count(),
            'avg_weight'      => $allShipmentsLastMonth->avg('weight'),
        ];

        $totals['cur_month'] = [
            'total_billing'   => @$allShipments->sum('total_price') + @$allShipments->sum('total_expenses') + @$allShipments->sum('fuel_price'),
            'count_shipments' => @$allShipments->count(),
            'shipments_day'   => @$allShipments->count() / 22,
            'new_customers'   => @$customersCurMonth->count(),
            'avg_weight'      => $allShipments->avg('weight')
        ];

        $totals['balance'] = [
            'total_billing'   => $this->calcBalance(@$totals['last_month']['total_billing'], @$totals['cur_month']['total_billing'], true),
            'count_shipments' => $this->calcBalance(@$totals['last_month']['count_shipments'], @$totals['cur_month']['count_shipments']),
            'shipments_day'   => $this->calcBalance(@$totals['last_month']['shipments_day'], @$totals['cur_month']['shipments_day']),
            'new_customers'   => $this->calcBalance(@$totals['last_month']['new_customers'], @$totals['cur_month']['new_customers']),
            'avg_weight'      => $this->calcBalance(@$totals['last_month']['avg_weight'], @$totals['cur_month']['avg_weight']),
        ];

        if(config('app.source') == 'lousaestradas') {
            $providersChart  = Statistic::getAgenciesChartData($allShipments);
        } else {
            $providersChart  = Statistic::getProvidersChartData($allShipments);
        }


        //STATUS CHART
        $data['"Incidência"'] = $allShipments->filter(function($item){
            return !$item->history->isEmpty();
        })->count();

        $data['"Devolvido"'] = $allShipments->filter(function($item){
            return $item->status_id == ShippingStatus::DEVOLVED_ID && $item->history->isEmpty();
        })->count();

        $data['"Entregue"'] = $allShipments->filter(function($item){
            return $item->status_id == ShippingStatus::DELIVERED_ID && $item->history->isEmpty();
        })->count();

        $statusChart = [
            'labels' => implode(',', array_keys($data)),
            'colors' => '"#ff0000","#ff7a01","#17a72d"',
            'values' => implode(',', $data)
        ];

        $data = compact(
            'totals',
            'services',
            'statusStatistics',
            'inactiveCustomers',
            'pendingShipments',
            'calendarEvents',
            'globalChartData',
            'globalChart2Data',
            'operators',
            'providersChart',
            'recipientsChart',
            'statusChart'
        );

        return $this->setContent('admin.dashboard.index', $data);
    }

    /**
     * Logout a remote login
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function remoteLogout(Request $request) {

        if(Session::has('source_user_id')) {
            $user = User::findOrFail(Session::get('source_user_id'));

            Session::forget('source_user_id');

            $result = Auth::login($user);

            return Redirect::route('admin.dashboard')->with('success', 'Sessão iniciada com sucesso.');
        }

        return Redirect::back()->with('error', 'Nenhuma sessão remota iniciada.');
    }

    /**
     * Show denied page
     *
     * @return \Illuminate\Http\Response
     */
    public function denied(Request $request) {
        return $this->setContent('admin.partials.denied');
    }

    /**
     * Get Weather weadget
     *
     * @return \Illuminate\Http\Response
     */
    public function getWeatherWidget(Request $request) {

        if(!Auth::user()->weather) {
            $city = Auth::user()->getSetting('weather_city');
            $city = empty($city) ? 31995 : $city;

            $url = 'http://api.tempo.pt/index.php?api_lang=pt&localidad='.$city.'&affiliate_id=eb25i6jxkut6&v=3.0';
            $weather = json_decode(file_get_contents($url));

            preg_match("/\[[^\]]*\]/", $weather->location, $matches);
            $weather->city = trim(str_replace(@$matches[0], '', $weather->location));
            $weather->district = trim(str_replace('[', '', str_replace(']', '', @$matches[0])));

            Auth::user()->weather = $weather;
        }

        return view('admin.dashboard.partials.weather', compact('weather'))->render();
    }

    /**
     * Search customers on DB
     *
     * @return type
     */
    public function searchWeatherCities(Request $request) {

        $search = trim($request->get('q'));
        $search = str_replace(' ', '%20', $search);

        $url = 'https://www.tempo.pt/peticionBuscador.php?lang=pt&texto=' . $search;
        $cities = json_decode(file_get_contents($url));

        try {
            $results = [];
            if($cities->status == 0) {
                $results = array();
                foreach($cities->localidad as $city) {

                    $location = $city->jerarquia;
                    array_pop($location);
                    $location = implode(', ', $location);

                    $results[] = [
                        'id'   => $city->id,
                        'text' => $city->nombre . ' (' . $location . ')',
                        'data-city' => $city->nombre
                    ];
                }
            } else {
                $results[] = [
                    'id'  => '',
                    'text' => 'Nenhuma localidade encontrada.'
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
     * Show denied page
     *
     * @return \Illuminate\Http\Response
     */
    public function storeWeatherSettings(Request $request) {

        try {
            $cityName = explode('(', $request->get('weather_setting_city'));
            $cityName = trim(@$cityName[0]);

            $settings = [
                'weather_city'      => $request->get('weather_setting_location'),
                'weather_city_name' => $cityName
            ];

            Auth::user()->storeSettings($settings, true);

        } catch (\Exception $e) {
            return redirect::back()->with('success', 'Erro ao gravar definições: ' . $e->getMessage());
        }

        return redirect::back()->with('success', 'Alterações gravadas com sucesso.');
    }

    /**
     * Calc value
     * @param $lastValue
     * @param $curValue
     */
    public function calcBalance($lastValue, $curValue, $percent = false) {

        if(!$curValue) {
            return 0;
        }

        if($percent) {
            $percent = $lastValue > 0.00 ? ($curValue * 100) / $lastValue : 0;

            if($lastValue > $curValue) {
                $balance = -1 * (100 - $percent); //ex: -83% comparado mês passado
            } else {
                $balance = 100 - $percent; //ex: +30% comparado mês passado
                $balance = $balance < 0.00 ? (-1 * $balance) : $balance;
            }
        } else {
            $balance = $curValue - $lastValue;
        }

        return $balance;
    }

    /**
     * Calc value
     * @param $lastValue
     * @param $curValue
     */
    public function fastSearch(Request $request) {


        if($request->target == 'customers') {

            if(empty($request->fast_search_customer)) {
                return Redirect::back()->with('error', 'Não indicou nenhum termo a pesquisar.');
            }

            $customer = Customer::filterAgencies()
                ->where('source', config('app.source'))
                ->isProspect(false)
                ->where(function($q) use($request) {
                    $q->where('code', $request->fast_search_customer);
                    $q->orWhere('vat', $request->fast_search_customer);
                })
                ->first(['id']);

            if($customer) {
                return Redirect::route('admin.customers.edit', $customer->id);
            } else {
                return Redirect::back()->with('error', 'Cliente não encontrado ou não possui permissão para consultar o cliente');
            }

        } elseif($request->target == 'billing') {

            if(empty($request->fast_search_billing)) {
                return Redirect::back()->with('error', 'Não indicou nenhum termo a pesquisar.');
            }

            $year   = $request->get('fast_search_year', date('Y'));
            $month  = $request->get('fast_search_month', date('m'));
            $period = $request->get('fast_search_period', '30d');

            $customer = Customer::filterAgencies()
                ->where('source', config('app.source'))
                ->isProspect(false)
                ->where(function($q) use($request) {
                    $q->where('code', $request->fast_search_billing);
                    $q->orWhere('vat', $request->fast_search_billing);
                })
                ->first(['id']);

            if($customer) {
                return Redirect::route('admin.billing.customers.show', [$customer->id, 'year' => $year, 'month' => $month, 'period' => $period]);
            } else {
                return Redirect::back()->with('error', 'Cliente não encontrado ou não possui permissão para consultar o cliente');
            }

        } else {

            if(empty($request->fast_search_shipment)) {
                return Redirect::back()->with('error', 'Não indicou nenhum termo a pesquisar.');
            }

            $openMode = $request->get('fs_open_mode', 'show');

            $shipment = Shipment::filterAgencies()
                ->where(function($q) use($request) {
                    $q->where('id', $request->fast_search_shipment);
                    $q->orWhere('tracking_code', 'LIKE', '%'.$request->fast_search_shipment);
                    $q->orWhere('provider_tracking_code', $request->fast_search_shipment);
                })
                ->orderBy('id', 'desc')
                ->first(['id']);

            if($shipment) {
                if($openMode == 'show') {
                    return Redirect::route('admin.shipments.index', ['fsearch' => $shipment->id, 'fsearchmode' => 'show']);
                } else {
                    return Redirect::route('admin.shipments.index', ['fsearch' => $shipment->id, 'fsearchmode' => 'edit']);
                }

            } else {
                return Redirect::back()->with('error', 'Envio não encontrado ou não possui permissão para consultar o envio');
            }

        }

        return Redirect::back()->with('error', 'Não foi definido nenhum tipo de pesquisa.');
    }


    /**
     * Show modal of apk install instructions
     *
     * @param Request $request
     * @return mixed|null|string
     * @throws \Mpdf\QrCode\QrCodeException
     * @throws \Throwable
     */
    public function ApkInstall(Request $request) {

        if($request->mode == 'web') {
            return view('admin.partials.modals.app_install')->render();
        }

        $downloadURL = route('mobile.apk.download');
        $apkUrl      = coreUrl('mobile/enovo_tms.apk');

        $qrCode = new \Mpdf\QrCode\QrCode($downloadURL);
        $qrCode->disableBorder();
        $output = new \Mpdf\QrCode\Output\Png();
        $qrCode = 'data:image/png;base64,'.base64_encode($output->output($qrCode, 200));

        $version  = \App\Models\Core\Setting::get('apk_version');

        $headers = get_headers($apkUrl, true);
        $fileSize = human_filesize($headers['Content-Length']);
        $updated = new Date($headers['Last-Modified']);
        $updated = $updated->format('Y-m-d');

        $hostname = request()->getHttpHost();

        return view('admin.partials.modals.apk_install', compact('qrCode', 'downloadURL', 'version', 'fileSize', 'updated', 'hostname'))->render();
    }

    /**
     * Print apk install instructions
     *
     * @return \Illuminate\Http\Response
     */
    public function ApkInstallPrint(Request $request) {

        $downloadURL = route('mobile.apk.download');
        $apkUrl      = coreUrl('mobile/enovo_tms.apk');

        $qrCode = new \Mpdf\QrCode\QrCode($downloadURL);
        $qrCode->disableBorder();
        $output = new \Mpdf\QrCode\Output\Png();
        $qrCode = 'data:image/png;base64,'.base64_encode($output->output($qrCode, 200));

        $version  = \App\Models\Core\Setting::get('apk_version');

        $headers = get_headers($apkUrl, true);
        $fileSize = human_filesize($headers['Content-Length']);
        $updated = new Date($headers['Last-Modified']);
        $updated = $updated->format('Y-m-d');

        $hostname = request()->getHttpHost();


        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_top'    => 50,
            'margin_bottom' => 20,
            'margin_left'   => 30,
            'margin_right'  => 30,
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        $data = [];
        $data['downloadURL']   = $downloadURL;
        $data['hostname']      = $hostname;
        $data['version']       = $version;
        $data['fileSize']      = $fileSize;
        $data['updated']       = $updated;
        $data['qrCode']        = $qrCode;
        $data['documentTitle'] = 'Instalar App Mobile';
        $data['documentSubtitle'] = 'Versão Android';
        $data['view']             =  'admin.printer.users.app_install';
        $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render());

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;

        return $mpdf->Output('Instalação App Android.pdf', 'I');
        exit;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ApkDownload(Request $request, $environment = null) {

        if(hasModule('app_apk')) {
            if(empty($environment)) {
                return Redirect::to(coreUrl('mobile/enovo_tms.apk'));
            } else if($environment == 'dev'){
                return Redirect::to(coreUrl('mobile/dev/app-debug.apk'));
            }
        }

        return App::abort(404);
    }


    /**
     * Show modal of apk install instructions
     *
     * @param Request $request
     * @return mixed|null|string
     * @throws \Mpdf\QrCode\QrCodeException
     * @throws \Throwable
     */
    public function versionInfo(Request $request) {

        $filepath = app_path().'/Http/Controllers/Admin/.version';
        $version  = File::get($filepath);
        $version  = trim($version);

        $date = filemtime($filepath);
        $date = date('Y-m-d', $date);

        $serverIP = $_SERVER['SERVER_ADDR'];

        $serverDetails = DB::connection('mysql_enovo')
            ->table('servers')
            ->where('ip', $serverIP)
            ->first();

        return view('admin.partials.modals.version_info', compact('version', 'date', 'serverDetails'))->render();
    }

    /**
     * Search providers on db
     *
     * @return type
     */
    public function addressBook(Request $request) {

        $search = trim($request->get('query'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

        $fields = [
            'id',
            'name',
            'email',
            'phone'
        ];

        try {

            $addressBook = AddressBook::filterAgencies()
                ->where(function($q) use($search){
                    $q->where('name', 'LIKE', $search)
                    ->orWhere('phone', 'LIKE', $search)
                    ->orWhere('email', 'LIKE', $search);
                })
                ->take(10)
                ->get($fields);

            if($addressBook) {

                $results = array();
                foreach($addressBook as $address) {
                    $results[] = [
                        'data'     => $address->id,
                        'value'    => $address->email
                    ];
                }

            } else {
                $results = ['Nenhum contacto encontrado.'];
            }

        } catch(\Exception $e) {
            $results = ['Erro interno ao processar o pedido.'];
        }

        $results = [
            'suggestions' => $results
        ];

        return Response::json($results);
    }

    /**
     * @param Request $request
     * @param $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function sendingList(Request $request, $token) {

        if($token != 'E7LfgV61rMDenTN8KnGhIw1V9jvZ3NjYJDBxH5KK') {
            \App::abort(404);
        }

        $syncFile = false;
        if($request->has('action')) {

            $webservice = new \App\Models\Webservice\Sending();

            if(!in_array($request->get('action'), ['import', 'export-tracking', 'export-traceability', 'export-refunds'])) {
                $syncFile = 1;
            } else {
                if ($request->get('action') == 'import') {
                    $webservice->importShipments();
                    $webservice->importIncidencesSolutions();
                } elseif ($request->get('action') == 'export-tracking') {
                    $filename = $webservice->exportTrackings($request->get('agency'), $request->get('date'), $request->get('download'));
                } elseif ($request->get('action') == 'export-traceability') {
                    $filename = $webservice->exportTraceability($request->get('agency'), $request->get('date'), $request->get('download'));
                } elseif ($request->get('action') == 'export-refunds') {
                    $filename = $webservice->exportRefunds($request->get('agency'), $request->get('date'), $request->get('download'));
                }

                if($request->get('download') && $filename) {
                    return \Response::download($filename);
                }
                $syncFile = 2;
            }
        }

        $date = Date::today();
        $today = $date->format('Y-m-d');
        $yesterday = $date->subDay()->format('Y-m-d');

        $dates = [$yesterday, $today];
        if ($request->has('min_date') || $request->has('max_date')) {
            $dates = [$request->get('min_date'), $request->get('max_date')];
        }

        $shipments = Shipment::with('status', 'service');

        if (config('app.source') == 'fozpost') {
            $shipments = $shipments->with('lastHistory');
        }

        $shipments = $shipments->whereHas('customer', function ($q) {
            $q->where('vat', 'A85508299');
        })
        ->whereBetween('date', $dates);

        if ($request->has('status')) {
            $shipments = $shipments->where('status_id', $request->get('status'));
        }

        if ($request->has('delegacion')) {
            $shipments = $shipments->where('provider_recipient_agency', $request->get('delegacion'));
        }

        if ($request->has('albaran')) {
            $shipments = $shipments->where('provider_tracking_code', 'like', '%' . trim($request->get('albaran')) . '%');
        }

        if ($request->has('reco')) {
            $shipments = $shipments->where('is_collection', $request->get('reco'));
        }

        $shipments = $shipments->orderBy('created_at', 'desc')
            ->get();

        $statusList = ShippingStatus::whereIn('id', $shipments->pluck('status_id')->toArray())
            ->pluck('name', 'id')
            ->toArray();

        $pendingTotal = $shipments->filter(function ($item) {
            return $item->status_id == 13;
        })->count();
        $distribuitionTotal = $shipments->filter(function ($item) {
            return $item->status_id == 4;
        })->count();
        $incidenceTotal = $shipments->filter(function ($item) {
            return $item->status_id == 9;
        })->count();
        $deliveredTotal = $shipments->filter(function ($item) {
            return $item->status_id == 5;
        })->count();

        $data = compact(
            'shipments',
            'pendingTotal',
            'distribuitionTotal',
            'incidenceTotal',
            'deliveredTotal',
            'statusList',
            'syncFile'
        );

        return view('default.shipments_list', $data);

    }
}
