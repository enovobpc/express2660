<?php

namespace App\Http\Controllers\Admin;

use App\Models\Agency;
use App\Models\Customer;
use App\Models\FleetGest\Vehicle;
use App\Models\GpsGateway\Base;
use App\Models\Provider;
use App\Models\Service;
use App\Models\Shipment;
use App\Models\ShippingStatus;
use App\Models\User;
use App\Models\UserLocation;
use Illuminate\Http\Request;
use App\Http\Requests;
use Html, Response, Setting, Auth, Excel, DB, Datatables;

class MapsController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'maps';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',maps']);
    }

    /**
     * Display index page
     *
     * @param Request $request
     * @return type
     */
    public function index(Request $request)
    {
        $address = trim($request->get('address'));
        $searchbox = $address;

        $vehicles = $vehiclesList = [];
        if(hasModule('gateway_gps') && Setting::get('gps_gateway') && Setting::get('gps_gateway_apikey')) {
             $locations = new Base();
             $locations->syncVehiclesInfo();

            $vehicles = $this->getVehicles();
            $vehiclesList = $vehicles->pluck('name', 'gps_id')->toArray();
        }

        $customers = Customer::filterSource(config('app.source'))
            ->filterAgencies()
            ->isProspect(false)
            ->where('code', '<>', 'CFINAL')
            ->get(['id', 'name', 'code', 'address', 'zip_code', 'city', 'map_lat', 'map_lng']);

        $status = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingStatus::CACHE_TAG)
            ->where('is_shipment', 1)
            ->filterSources()
            ->isVisible()
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->ordered()
            ->isCollection(false)
            ->pluck('name', 'id')
            ->toArray();

        $agencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterAgencies()
            ->orderBy('code', 'asc')
            ->get());

        $operators = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->isOperator()
            ->ignoreAdmins()
            ->orderBy('source', 'asc')
            ->orderBy('name', 'asc')
            ->get(['source', 'id', 'name', 'vehicle', 'provider_id', 'location_enabled', 'location_lat', 'location_lng', 'location_marker']);

        $operatorsList = User::listOperators($operators, Auth::user()->isAdmin() ? true : false);

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'searchbox',
            'operators',
            'operatorsList',
            'customers',
            'status',
            'agencies',
            'providers',
            'services',
            'vehicles',
            'vehiclesList'
        );

        return $this->setContent('admin.maps.index', $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getOperatorDeliveries(Request $request)
    {
        ini_set("memory_limit", "-1");

        $operatorId = $request->get('operator');
        $date       = $request->get('date');
        $date       = empty($date) ? date('Y-m-d') : $date;

        $shipments = Shipment::with('operator', 'status', 'last_history')
            ->whereHas('last_history', function($q) use($date) {
                $q->whereRaw('DATE(created_at) = "'.$date.'"');
            });

        if(!empty($operatorId)) {
            $shipments = $shipments->where('operator_id', $operatorId);
        }


        $shipments = $shipments->filterAgencies()
                            ->take(10)
                            ->get();

        foreach ($shipments as $shipment) {

            if($shipment->last_history->latitude && $shipment->last_history->longitude) {
                $shipment->map_lat = $shipment->last_history->latitude;
                $shipment->map_lng = $shipment->last_history->longitude;
                $shipment->save();
            }

            //if($shipment->status->is_final) {
                /*$address = $shipment->recipient_address;
                $zipCode = $shipment->recipient_zip_code;
                $city    = $shipment->recipient_city;

                if($shipment->is_collection) {
                    $address = $shipment->sender_address;
                    $zipCode = $shipment->sender_zip_code;
                    $city    = $shipment->sender_city;
                }

                $address = $address. ' '.$zipCode .', '.$city;
                $address = str_replace(" ", "+", $address); //replace all the white space with "+" sign to match with google search pattern
                $url = "http://maps.google.com/maps/api/geocode/json?sensor=false&address=$address";
                $response = file_get_contents($url);
                $json = json_decode($response,TRUE);

                if(!empty($json['results'])) {
                    $lat = $json['results'][0]['geometry']['location']['lat'];
                    $lng = $json['results'][0]['geometry']['location']['lng'];

                    $shipment->map_lat = $lat;
                    $shipment->map_lng = $lng;
                } else {
                    $shipment->map_lat = $shipment->map_lng = null;
                }

                $shipment->save();
                */
            //}
        }

        //Show delivery traject
        $locations = UserLocation::with('operator')
            ->select([
                'id',
                'operator_id',
                'latitude',
                'longitude',
                DB::raw('created_at as date')
            ]);

        if (!empty($operatorId)) {
            $locations = $locations->where('operator_id', $operatorId);
        }

        if (!empty($date)) {
            $locations = $locations->whereRaw('DATE(created_at) = "'. $date .'"');
        }

        $locations = $locations->orderBy('id', 'desc')->get();

        return Response::json([
            'html' => view('admin.maps.partials.deliveries_list', compact('shipments', 'locations'))->render()
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getOperatorHistory(Request $request)
    {
        ini_set("memory_limit", "-1");

        if(hasModule('gateway_gps')) {
            return $this->getVehicleHistory($request);
        }

        $operatorId = $request->get('operator');
        $date = $request->get('date');
        $date = empty($date) ? date('Y-m-d') : $date;

        $locations = UserLocation::with('operator')
            ->select([
                DB::raw('CONCAT(latitude,longitude) as latlng'),
                'id',
                'operator_id',
                'latitude',
                'longitude',
                DB::raw('created_at as date')
            ]);

        if (!empty($operatorId)) {
            $locations = $locations->where('operator_id', $operatorId);
        }

        if (!empty($date)) {
            $locations = $locations->whereRaw('DATE(created_at) = "'. $date .'"');
        }

        $locations = $locations ->groupBy('latlng')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.maps.partials.history_list', compact('locations'))->render();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getVehicleHistory(Request $request)
    {
        ini_set("memory_limit", "-1");

        $vehicleId = $request->get('vehicle');
        $startDate = $request->get('date');
        $startDate = empty($startDate) ? date('Y-m-d') : $startDate;

        $vehicle = Vehicle::filterSource()->where('gps_id', $vehicleId)->first();

        $locations = new Base();
        $locations = $locations->getRoute($vehicleId, $startDate, $startDate);

        return view('admin.maps.partials.vehicle_route_list', compact('locations', 'vehicle'))->render();
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatableShipments(Request $request) {

        $appMode = Setting::get('app_mode');

        $bindings = [
            'id', 'tracking_code', 'type', 'parent_tracking_code', 'children_tracking_code', 'children_type',
            'agency_id', 'sender_agency_id', 'recipient_agency_id',
            'service_id', 'provider_id', 'status_id', 'operator_id', 'customer_id',
            'sender_name', 'sender_address','sender_zip_code', 'sender_city', 'sender_phone',
            'recipient_name', 'recipient_address','recipient_zip_code', 'recipient_city', 'recipient_phone', 'recipient_country',
            'obs', 'volumes', 'weight', 'total_price', 'date'
        ];


        $sourceAgencies = Agency::where('source', config('app.source'))
            ->pluck('id')
            ->toArray();

        $data = Shipment::filterAgencies()
            ->with('customer')
            ->where('is_collection', 0)
            ->whereHas('status', function ($q){
                $q->where('is_final', 0);
            })
            ->whereIn('agency_id', $sourceAgencies)
            ->select($bindings);


        //limit search
        $value = $request->limit_search;
        if($request->has('limit_search') && !empty($value)) {
            $minId = (int) CacheSetting::get('shipments_limit_search');
            if($minId) {
                $data = $data->where('id', '>=', $minId);
            }
        }


        $agencies = Agency::filterAgencies()->remember(5)->get(['name', 'code', 'id', 'color']);
        $agencies = $agencies->groupBy('id')->toArray();

        //status
        $statusList = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->get(['id', 'name', 'color', 'is_final']);
        $finalStatus = $statusList->filter(function($item) { return $item->is_final; })->pluck('id')->toArray();
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

        //filter sender agency
        $value = $request->sender_agency;
        if($request->has('sender_agency')) {
            $data = $data->where('sender_agency_id', $value);
        }

        //filter recipient agency
        $value = $request->recipient_agency;
        if($request->has('recipient_agency')) {
            $data = $data->where('recipient_agency_id', $value);
        }

        //filter provider
        $value = $request->provider;
        if($request->has('provider')) {
            $data = $data->where('provider_id', $value);
        }

        //filter operator
        $value = $request->operator;
        if($request->has('operator')) {
            $data = $data->where('operator_id', $value);
        }

        //filter status
        $value = $request->status;
        if($request->has('status')) {
            $data = $data->where('status_id', $value);
        }

        return Datatables::of($data)
            ->edit_column('service_id', function($row) use($agencies, $servicesList, $providersList) {
                return view('admin.shipments.shipments.datatables.service', compact('row', 'agencies', 'servicesList', 'providersList'))->render();
            })
            ->edit_column('id', function($row) use($agencies) {
                return view('admin.shipments.shipments.datatables.tracking', compact('row', 'agencies'))->render();
            })
            ->edit_column('sender_name', function($row) {
                return view('admin.shipments.shipments.datatables.sender', compact('row'))->render();
            })
            ->edit_column('recipient_name', function($row) {
                return view('admin.shipments.shipments.datatables.recipient', compact('row'))->render();
            })
            ->edit_column('status_id', function($row) use($statusList) {
                return view('admin.shipments.shipments.datatables.status', compact('row', 'statusList'))->render();
            })
            ->edit_column('volumes', function($row) use($appMode) {
                return view('admin.shipments.shipments.datatables.volumes', compact('row', 'appMode'))->render();
            })
            ->edit_column('date', function($row) {
                return view('admin.shipments.shipments.datatables.date', compact('row'))->render();
            })
            ->add_column('read_sender', function($row) {
                return view('admin.maps.datatables.read_sender', compact('row'))->render();
            })
            ->add_column('read_recipient', function($row) {
                return view('admin.maps.datatables.read_recipient', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Display operators location
     *
     * @param Request $request
     * @return type
     */
    public function operatorsLocation(Request $request)
    {
        $operators = User::with('last_location')
            ->filterSource()
            ->filterAgencies()
            ->isOperator()
            ->isActive()
            ->get();

        $operators = $operators->sortByDesc('last_location.created_at');

        $operatorsList = $operators->pluck('name', 'id')->toArray();

        $vehicles = $this->getVehicles();
        $vehiclesList = $vehicles->pluck('name', 'id')->toArray();

        return view('admin.maps.modals.operators', compact('operators', 'operatorsList', 'vehiclesList', 'vehicles'));
    }

    /**
     * Get vehicles
     */
    public function getVehicles() {

        $vehicles = Vehicle::with('brand', 'operator')
            ->filterSource()
            ->isActive()
            ->whereNotNull('latitude')
            ->orderBy('is_ignition_on', 'desc')
            ->get([
                'id',
                'operator_id',
                'brand_id',
                'name',
                'license_plate',
                'latitude',
                'longitude',
                'speed',
                'fuel_level',
                'gps_city',
                'gps_country',
                'gps_id',
                'is_ignition_on',
                'last_location',
                'counter_km'
            ]);

        return $vehicles;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function syncLocation(Request $request, $type = 'vehicles')
    {
        ini_set("memory_limit", "-1");

        if($type == 'vehicles') {
            $locations = new Base();
            $locations->syncVehiclesInfo();

            $vehicles = $this->getVehicles();
        } elseif($type == 'operators') {
            $operators = User::with('last_location')
                ->filterSource()
                ->filterAgencies()
                ->isOperator()
                ->isActive()
                ->get();

            $operators = $operators->sortByDesc('last_location.created_at');
        }

        if($request->has('target') && $request->target == 'modal') {
            if($type == 'vehicles') {
                $html = view('admin.maps.partials.modal_vehicles_list', compact('vehicles'))->render();
            } elseif($type == 'operators') {
                $html = view('admin.maps.partials.modal_operators_list', compact('operators'))->render();
            }
        } else {
            $html = view('admin.maps.partials.map_bottom', compact('vehicles'))->render();
        }

        $result = [
            'result' => true,
            'html'   => $html
        ];

        return response()->json($result);
    }
}
