<?php

namespace App\Http\Controllers\Admin\Trips;

use App\Http\Controllers\Admin\Shipments\HistoryController;
use App\Models\Trip\Trip;
use App\Models\Trip\TripShipment;
use App\Models\Trip\TripPeriod;
use App\Models\Invoice;
use App\Models\PackType;
use App\Models\Service;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use App\Models\ShipmentPackDimension;
use App\Models\ShippingStatus;
use App\Models\Vehicle;
use App\Models\Provider;
use App\Models\User;
use App\Models\Route;
use App\Models\Agency;
use App\Models\Allowance;
use App\Models\IncidenceType;
use App\Models\TransportType;
use App\Models\Trip\TripVehicle;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Jenssegers\Date\Date;
use Yajra\Datatables\Facades\Datatables;
use Response, DB, Auth, Setting;

class TripsController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'delivery_management';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',delivery_management']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->whereSource(config('app.source'))
            ->pluck('name', 'id')
            ->toArray();

        $operators = User::listOperators(User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->ignoreAdmins()
            ->isOperator(true)
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

        $routes = Route::remember(config('cache.query_ttl'))
            ->cacheTags(Route::CACHE_TAG)
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $vehicles = Vehicle::listVehicles();
        $trailers = Vehicle::listVehicles(true);

        $data = compact(
            'operators',
            'providers',
            'agencies',
            'routes',
            'vehicles',
            'trailers'
        );

        return $this->setContent('admin.trips.index', $data);
    }

    /**
     * Show the form for show the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {

        if ($request->has('field') && $request->get('field') == 'code') {
            $trip = Trip::with('expenses', 'vehicles_history.histories')
                    ->where('code', $id)
                    ->firstOrFail(); //o ID recebido é o código e não o id 
        } else {
            $trip = Trip::with('expenses', 'vehicles_history.histories')
                    ->findOrfail($id);
        }

   
        $trip->createHistoryIfNotExists();
        
        $tripVehicles = $trip->vehicles_history;

        $shipments = $trip->shipments;
        $shipments = $shipments->sortBy('pivot.sort');
        $ids = $shipments->pluck('id')->toArray();


        //processa ação persobalizada - atualização da tabela de envios
        //NOTA: este metodo está no show para não criarmos uma nova rota nas rotas.
        if ($request->ajax() && $request->get('action') == 'refresh-table') {
            if(app_mode_cargo()) {
                return view('admin.trips.partials.shipments_table_cargo', compact('shipments', 'trip'))->render();
            }
            return view('admin.trips.partials.shipments_table', compact('shipments', 'trip'))->render();
        }

        $dimensions = ShipmentPackDimension::with('shipment')
            ->whereIn('shipment_id', $ids)
            ->orderByRaw('FIND_IN_SET(shipment_id, "' . implode(',', $ids) . '")')
            ->get();

        $periods = $this->listPeriods(TripPeriod::filterSource()->get());


        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->whereSource(config('app.source'))
            ->pluck('name', 'id')
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

        $routes = Route::remember(config('cache.query_ttl'))
            ->cacheTags(Route::CACHE_TAG)
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

        $incidences = IncidenceType::remember(config('cache.query_ttl'))
            ->cacheTags(IncidenceType::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $transportTypes = TransportType::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $packTypes = PackType::remember(config('cache.query_ttl'))
            ->cacheTags(PackType::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->pluck('name', 'code')
            ->toArray();

        $vehicles = Vehicle::listVehicles();
        $trailers = Vehicle::listVehicles(true);
        $cargoAppMode  = app_mode_cargo();

        $deliveryTimes = trans('admin/shipments.delivery_times');

        $stats = $trip->calcStats();

        $formOptions = array('route' => array('admin.trips.update', $trip->id), 'method' => 'PUT', 'class' => 'form-trip');

        $data = compact(
            'trip',
            'tripVehicles',
            'formOptions',
            'agencies',
            'operators',
            'providers',
            'routes',
            'status',
            'vehicles',
            'trailers',
            'shipments',
            'dimensions',
            'periods',
            'incidences',
            'stats',
            'deliveryTimes',
            'cargoAppMode',
            'packTypes',
            'transportTypes'
        );

        return $this->setContent('admin.trips.show', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $shipmentsIds = null;
        if ($request->id) {
            $shipmentsIds = $request->get('id');
        }

        $defaultLocation  = strtoupper(Setting::get('company_city'));
        $trip = new Trip();
        $trip->start_location = $defaultLocation;
        $trip->end_location   = $defaultLocation;
        $trip->start_date     = date('Y-m-d');

        $operators = User::listOperators(User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->ignoreAdmins()
            ->isOperator(true)
            ->orderBy('source', 'asc')
            ->orderBy('code', 'asc')
            ->orderBy('name', 'asc')
            ->get(['source', 'id', 'name', 'vehicle', 'provider_id']), true);

        $managers = User::listOperators(User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->ignoreAdmins()
            ->isOperator(false)
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

        $routes = Route::remember(config('cache.query_ttl'))
            ->cacheTags(Route::CACHE_TAG)
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $status = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingStatus::CACHE_TAG)
            ->where('is_shipment', 1)
            ->filterSources()
            ->isVisible()
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $vatRates = Invoice::getVatTaxes();

        $deliveryTimes = trans('admin/shipments.delivery_times');

        $vehicles = Vehicle::listVehicles();
        $trailers = Vehicle::listVehicles(true);
        $periods  = $this->listPeriods(TripPeriod::filterSource()->get());

        $action = 'Criar mapa distribuição';
        if (app_mode_cargo()) {
            $action = 'Criar mapa viagem';
        }

        $formOptions = array('route' => array('admin.trips.store'), 'method' => 'POST', 'class' => 'form-trip');

        $data = compact(
            'trip',
            'action',
            'formOptions',
            'operators',
            'managers',
            'providers',
            'routes',
            'deliveryTimes',
            'vehicles',
            'trailers',
            'periods',
            'status',
            'vatRates',
            'shipmentsIds'
        );

        $view = 'admin.trips.edit';
        if (app_mode_cargo()) {
            $view = 'admin.trips.edit_cargo';
        }

        return view($view, $data)->render();
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
    public function edit($id)
    {
        $trip = Trip::findOrfail($id);

        $operators = User::listOperators(User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->ignoreAdmins()
            ->isOperator(true)
            ->orderBy('source', 'asc')
            ->orderBy('code', 'asc')
            ->orderBy('name', 'asc')
            ->get(['source', 'id', 'name', 'vehicle', 'provider_id']), true);

        $managers = User::listOperators(User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->ignoreAdmins()
            ->isOperator(false)
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

        $routes = Route::remember(config('cache.query_ttl'))
            ->cacheTags(Route::CACHE_TAG)
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $status = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingStatus::CACHE_TAG)
            ->where('is_shipment', 1)
            ->filterSources()
            ->isVisible()
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $vatRates = Invoice::getVatTaxes();

        $deliveryTimes = trans('admin/shipments.delivery_times');

        $vehicles = Vehicle::listVehicles();
        $trailers = Vehicle::listVehicles(true);
        $periods  = $this->listPeriods(TripPeriod::filterSource()->get());

        $action = 'Editar mapa distribuição ' . $trip->code;
        if (app_mode_cargo()) {
            $action = 'Editar mapa viagem ' . $trip->code;
        }

        $formOptions = array('route' => array('admin.trips.update', $trip->id), 'method' => 'PUT', 'class' => 'form-trip');


        $data = compact(
            'trip',
            'action',
            'formOptions',
            'operators',
            'managers',
            'providers',
            'routes',
            'status',
            'deliveryTimes',
            'vehicles',
            'trailers',
            'periods',
            'vatRates'
        );

        $view = 'admin.trips.edit';
        if (app_mode_cargo()) {
            $view = 'admin.trips.edit_cargo';
        }

        return view($view, $data)->render();
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
        Trip::flushCache(Trip::CACHE_TAG);

        $input = $request->all();

        $input['hour']       = @$input['hour'] ? $input['hour'] : date('H:i');
        $input['assistants'] = $request->get('assistants', []);
        $onlyAddShipments    = $request->has('assign_manifest_id'); // Associar a um manifesto já existente
        $printManifest       = $request->get('print_manifest', false);

        if (empty(@$input['delivery_route_id'])) {
            $route = Route::filterOperator($input['operator_id'])->first();
            @$input['delivery_route_id'] = @$route->id;
        }

        /*if($input['cost_price'] > 0.00) {
            $vatRate = VatRate::where('code', $input['vat_rate_id'])->first();
            $input['vat_rate'] = @$vatRate->value;
        }*/

        $trip = Trip::findOrNew($id ?? $request->get('assign_manifest_id', null));
        $exists = $trip->exists;


        //verifica se há mudança de motorista/viatura/reboque
        $changeStatus = [];
        if (!empty($trip->operator_id) && $trip->operator_id != $input['operator_id']) {
            $changeStatus['operator'] = $input['operator_id'];
        }

        if (!empty($trip->vehicle) && $trip->vehicle != $input['vehicle']) {
            $changeStatus['vehicle'] = $input['vehicle'];
        }

        if (!empty($trip->trailer) && $trip->trailer != $input['trailer']) {
            $changeStatus['trailer'] = $input['trailer'];
        }



        //interliga o serviço de retorno
        $originalTrip = null;
        if (!empty($request->get('parent_id')) && $request->get('type') == 'R' && empty($trip->parent_id)) {
            $originalTrip = Trip::find($request->get('parent_id'));
            $input['type'] = 'R';
            $input['parent_id']   = $originalTrip->id;
            $input['parent_code'] = $originalTrip->code;
        }


        if ($onlyAddShipments || $trip->validate($input)) {
            if (!$onlyAddShipments) {

                $trip->fill($input);
                $trip->source     = config('app.source');
                $trip->created_by = Auth::user()->id;
                $trip->setCode();

                if ($originalTrip) {
                    $originalTrip->children_type = 'R';
                    $originalTrip->children_code = $trip->code;
                    $originalTrip->children_id   = $trip->id;
                    $originalTrip->save();
                }
            }

            //associa envios caso existam envios a associar
            if ($request->has('ids')) {
                $ids = explode(',', $request->ids);
                $trip->addShipments($ids);
            }

            //regista mudança de motorista/viatura/reboque
            if (!empty($changeStatus)) {

                $shipments = $trip->shipments;
                foreach ($shipments as $shipment) {
                    $history = new ShipmentHistory();
                    $history->shipment_id = $shipment->id;
                    $history->status_id   = ShippingStatus::TRAILER_CHANGED_ID;
                    $history->agency_id   = $trip->agency_id;
                    $history->operator_id = $trip->operator_id;
                    $history->vehicle     = $trip->vehicle;
                    $history->trailer     = $trip->trailer;
                    $history->user_id     = Auth::user()->id;
                    $history->save();
                }
            }

            //se a data de início é inferior à data atual, força os envios a entrarem em transporte.
            $now = date('Y-m-d H:i:s');
            if ($trip->pickup_date <= $now && 0) {

                //obtem todos os envios não finalizados e que não estejam já no estado em transporte
                $inExecutionStatus = ShippingStatus::IN_TRANSPORTATION_ID;
                $finalStatus       = ShippingStatus::isFinal()->pluck('id')->toArray();
                $shipments = $trip->shipments->filter(function ($item) use ($finalStatus, $inExecutionStatus) {
                    return !in_array($item->status_id, $finalStatus) && $item->status_id != $inExecutionStatus;
                });


                foreach ($shipments as $shipment) {
                    $history = new ShipmentHistory();
                    $history->shipment_id = $shipment->id;
                    $history->status_id   = $inExecutionStatus;
                    $history->agency_id   = $trip->agency_id;
                    $history->operator_id = $trip->operator_id;
                    $history->vehicle     = $trip->vehicle;
                    $history->trailer     = $trip->trailer;
                    $history->created_at  = $now; //$trip->pickup_date;
                    $history->user_id     = Auth::user()->id;
                    $history->save();

                    $shipment->update([
                        'status_id'         => $inExecutionStatus,
                        'in_transport_date' => $now
                    ]);
                }
            }

            if ($originalTrip) {
                $originalTrip->children_type = 'R';
                $originalTrip->children_id   = $trip->id;
                $originalTrip->children_code = $trip->code;
            }

            //atualiza todos os envios deste manifesto
            $trip->syncShipmentsData();

            if ($request->ajax()) {
                return response()->json([
                    'result'        => true,
                    'feedback'      => 'Dados gravados com sucesso.',
                    'printManifest' => $printManifest ? route('admin.trips.print', [$trip->id, 'manifest']) : null
                ]);
            }

            if (!$exists) {
                return Redirect::route('admin.trips.show', $trip->id)->with('success', 'Dados gravados com sucesso.');
            }

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }

        return Redirect::route('admin.trips.show', $trip->id)->withInput()->with('error', $trip->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        Trip::flushCache(Trip::CACHE_TAG);

        $trip = Trip::filterSource()
            ->find($id);

        if (!empty($trip)) {

            //apaga relacoes
            if ($trip->parent_id) {
                $trip->parent_manifest->children_type = null;
                $trip->parent_manifest->children_code = null;
                $trip->parent_manifest->children_id   = null;
                $trip->parent_manifest->save();
            }

            Shipment::where('trip_id', $id)->update([
                'trip_id'   => null,
                'trip_code' => null
            ]);
            TripShipment::where('trip_id', $id)->delete();
            $trip->delete();

            return Redirect::back()->with('success', 'Mapa eliminado com sucesso');
        }

        return Redirect::route('admin.trips.index')->with('error', 'Manifesto inexistente');
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

        Trip::flushCache(Trip::CACHE_TAG);

        $ids = explode(',', $request->ids);

        $result = Trip::filterSource()
            ->whereIn('id', $ids)
            ->delete();


        if (!$result) {
            return Redirect::back()->with('error', 'Não foi possível remover um ou mais registos.');
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

        $data = Trip::with(['operator' => function ($q) {
            $q->remember(config('cache.query_ttl'));
            $q->cacheTags(User::CACHE_TAG);
        }])
            ->with(['provider' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Provider::CACHE_TAG);
                $q->select(['id', 'name', 'color']);
            }])
            ->with('shipments', 'period')
            ->filterSource()
            ->select();

        //filter date min
        $dtMin = $request->get('date_min');
        if ($request->has('date_min')) {
            $dtMax = $dtMin;
            if ($request->has('date_max')) {
                $dtMax = $request->get('date_max') . ' 00:00:00';
            }
            $data = $data->whereBetween('pickup_date', [$dtMin . ' 00:00:00', $dtMax . ' 23:59:59']);
        }

        //filter start country
        $value = $request->start_country;
        if ($request->has('start_country')) {
            $data = $data->whereIn('start_country', $value);
        }

        //filter end country
        $value = $request->end_country;
        if ($request->has('end_country')) {
            $data = $data->whereIn('end_country', $value);
        }

        //filter vehicle
        $value = $request->vehicle;
        if ($request->has('vehicle')) {
            $data = $data->whereIn('vehicle', $value);
        }

        //filter trailer
        $value = $request->trailer;
        if ($request->has('trailer')) {
            $data = $data->whereIn('trailer', $value);
        }

        //filter operator
        $value = $request->operator;
        if ($request->has('operator')) {
            $data = $data->whereIn('operator_id', $value);
        }

        //filter provider
        $value = $request->provider;
        if ($request->has('provider')) {
            if ($value[0] == 'all') {
                $data = $data->whereNotNull('provider_id');
            } else {
                $data = $data->whereIn('provider_id', $value);
            }
        }

        //filter pickup route
        $value = $request->pickup_route;
        if ($request->has('pickup_route')) {
            $data = $data->whereIn('pickup_route_id', $value);
        }

        //filter delivery route
        $value = $request->delivery_route;
        if ($request->has('delivery_route')) {
            $data = $data->whereIn('delivery_route_id', $value);
        }

        //filter concluded
        $value = $request->concluded;
        if ($request->has('concluded')) {
            if ($value == '1') {
                $data = $data->whereHas('shipments', function ($q) {
                    $q->whereIn('status_id', [ShippingStatus::DELIVERED_ID, ShippingStatus::DEVOLVED_ID, ShippingStatus::CANCELED_ID]);
                });
            } else {
                $data = $data->whereHas('shipments', function ($q) {
                    $q->whereNotIn('status_id', [ShippingStatus::DELIVERED_ID, ShippingStatus::DEVOLVED_ID, ShippingStatus::CANCELED_ID]);
                });
            }
        }

        //filter agency
        $values = $request->agency;
        if ($request->has('agency')) {

            $data = $data->whereHas('operator', function ($q) use ($values) {
                $q->where(function ($q) use ($values) {
                    foreach ($values as $value) {
                        $q->orWhere('agencies', 'like', '%"' . $value . '"%');
                    }
                });
            });
        }

        // Filter data
        if ($request->has('date_min')) {
            $dtMin = $request->get('date_min');
            $dtMax = $request->get('date_max');
            $dtMax = $dtMax ? $dtMax : $dtMin;
            $data->whereBetween('date', [$dtMin, $dtMax]);
        }

        $value = $request->assistant;
        if ($request->has('assistant')) {
            $data = $data->where('assistants', 'like', '%"' . $value . '"%');
        }

        return Datatables::of($data)
            ->edit_column('sort', function ($row) {
                return view('admin.trips.datatables.code', compact('row'))->render();
            })
            ->edit_column('period_id', function ($row) {
                return view('admin.trips.datatables.period', compact('row'))->render();
            })
            ->edit_column('delivery_route_id', function ($row) {
                return view('admin.trips.datatables.route', compact('row'))->render();
            })
            ->edit_column('children_code', function ($row) {
                return view('admin.trips.datatables.children_code', compact('row'))->render();
            })
            ->edit_column('pickup_date', function ($row) {
                return view('admin.trips.datatables.pickup_date', compact('row'))->render();
            })
            ->edit_column('delivery_date', function ($row) {
                return view('admin.trips.datatables.delivery_date', compact('row'))->render();
            })
            ->edit_column('period_id', function ($row) {
                return view('admin.trips.datatables.period', compact('row'))->render();
            })
            ->edit_column('kms', function ($row) {
                return view('admin.trips.datatables.kms', compact('row'))->render();
            })
            ->edit_column('pickup_hour', function ($row) {
                return $row->pickup_date ? $row->pickup_date->format('H:i') : '';
            })
            ->edit_column('delivery_hour', function ($row) {
                return $row->delivery_date ? $row->delivery_date->format('H:i') : '';
            })
            ->edit_column('start_location', function ($row) {
                return view('admin.trips.datatables.start_location', compact('row'))->render();
            })
            ->edit_column('end_location', function ($row) {
                return view('admin.trips.datatables.end_location', compact('row'))->render();
            })
            ->edit_column('operator_id', function ($row) {
                return view('admin.trips.datatables.operator', compact('row'))->render();
            })
            ->edit_column('vehicle', function ($row) {
                return view('admin.trips.datatables.vehicle', compact('row'))->render();
            })
            ->add_column('count', function ($row) {
                return @$row->shipments->count() > 0 ? $row->shipments->count() : '<i class="fas fa-exclamation-circle text-red"></i>';
            })
            ->add_column('weight', function ($row) {
                return view('admin.trips.datatables.weight', compact('row'))->render();
            })
            ->add_column('volumes', function ($row) {
                return @$row->shipments->sum('volumes') > 0 ? $row->shipments->sum('volumes') : '<i class="fas fa-exclamation-circle text-red"></i>';;
            })
            ->add_column('cod', function ($row) {
                return view('admin.trips.datatables.cod', compact('row'))->render();
            })
            ->add_column('total', function ($row) {
                return view('admin.trips.datatables.total', compact('row'))->render();
            })
            ->add_column('status', function ($row) {
                return view('admin.trips.datatables.status', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.trips.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatableShipments(Request $request, $id)
    {
        $appMode = config('app_mode');

        $excludeShipments = TripShipment::where('trip_id', $id)->pluck('shipment_id')->toArray();

        //status
        $statusList = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->get(['id', 'name', 'color', 'is_final']);
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

        $status = $request->get('status');

        $data = Shipment::filterAgencies()
            ->isPickup(false)
            ->whereNotIn('id', $excludeShipments)
            ->whereHas('status', function ($q) use ($status) {
                if ($status) {
                    $q->whereIn('id', $status);
                } else {
                    $q->where('is_final', 0);
                }
            })
            ->select();

        //filter manifest
        /* $value = $request->manifest;
        if ($request->has('manifest')) {
            if($value) {
                $data = $data->where('manifest_id', $value);
            } else {
                $data = $data->whereNull('manifest_id');
            }
        }*/
        $data = $data->whereNull('trip_code');

        $value = $request->transport_type;
        if($request->has('transport_type')) {
            $data = $data->whereIn('transport_type_id', $value);
        }

        //filter sender agency
        $value = $request->sender_agency;
        if ($request->has('sender_agency')) {
            $data = $data->whereIn('sender_agency_id', $value);
        }

        //filter provider
        $value = $request->provider;
        if ($request->has('provider')) {
            $data = $data->whereIn('provider_id', $value);
        }

        //filter operator
        $value = $request->operator;
        if ($request->has('operator')) {
            $data = $data->whereIn('operator_id', $value);
        }

         //filter delivery_route
         $value = $request->delivery_route;
         if ($request->has('delivery_route')) {
             $data = $data->whereIn('route_id', $value);
         }

          //filter pickup_route
        $value = $request->pickup_route;
        if ($request->has('pickup_route')) {
            $data = $data->whereIn('pickup_route_id', $value);
        }
        
        // Filter pick-up and delivery date 
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
                if ($statusId == 'delivery') {
                    $data = $data->whereBetween('delivery_date', [$dtMin, $dtMax]);
                } elseif ($statusId == 'pickup') {
                    $data = $data->whereBetween('date', [$dtMin, $dtMax]);
                }
            }
        }

        return Datatables::of($data)
            ->edit_column('service_id', function ($row) use ($agencies, $servicesList, $providersList) {
                return view('admin.shipments.shipments.datatables.service', compact('row', 'agencies', 'servicesList', 'providersList'))->render();
            })
            ->edit_column('id', function ($row) use ($agencies) {
                return view('admin.shipments.shipments.datatables.tracking', compact('row', 'agencies'))->render();
            })
            ->edit_column('sender_name', function ($row) {
                return view('admin.shipments.shipments.datatables.sender', compact('row'))->render();
            })
            ->edit_column('recipient_name', function ($row) {
                return view('admin.shipments.shipments.datatables.recipient', compact('row'))->render();
            })
            ->edit_column('status_id', function ($row) use ($statusList, $operatorsList) {
                return view('admin.shipments.shipments.datatables.status', compact('row', 'statusList', 'operatorsList'))->render();
            })
            ->edit_column('date', function ($row) use ($statusList) {
                return view('admin.shipments.shipments.datatables.delivery_date', compact('row', 'statusList'))->render();
            })
            ->edit_column('volumes', function ($row) use ($servicesList, $packTypes, $appMode) {
                return view('admin.shipments.shipments.datatables.volumes', compact('row', 'servicesList', 'packTypes', 'appMode'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.trips.datatables.shipments.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Update the specified resource order in storage.
     * POST /admin/services/sort
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addSingleShipment(Request $request, $id)
    {

        try {
            $shipmentId = $request->get('shipment');

            $trip = Trip::findOrFail($id);
            $trip->addShipments([$shipmentId]);


            //obtem lista de envios atualizada para construir HTML
            $shipments = $trip->shipments;
            $shipments = $shipments->sortBy('pivot.sort');

            $view = 'admin.trips.partials.shipments_table';
            if(app_mode_cargo()) {
                $view = 'admin.trips.partials.shipments_table_cargo';
            }

            $response = [
                'result'   => true,
                'feedback' => 'Serviços adicionados com sucesso.',
                'html'     => view($view, compact('shipments', 'trip'))->render()
            ];
        } catch (\Exception $e) {
            $response = [
                'result'   => false,
                'feedback' => 'Erro ao adicionar serviço. ' . $e->getMessage(),
            ];
        }

        return Response::json($response);
    }

    /**
     * @param $method
     * @param $parameters
     * @return mixed|string|\Symfony\Component\HttpFoundation\Response
     */
    public function addSelectedShipments(Request $request)
    {

        $input = $request->all();
        $ids   = $request->get('id');

        $trips = Trip::with('shipments')
            ->whereNull('end_date');


        $value = $request->get('code');
        if (!empty($value)) {
            $trips = $trips->where('code', 'like', '%' . $value . '%');
        }

        $value = $request->get('start_date');
        if (!empty($value)) {
            $trips = $trips->where('start_date', $value);
        }

        $value = $request->get('start_country');
        if (!empty($value)) {
            $trips = $trips->where('start_country', $value);
        }

        $value = $request->get('end_country');
        if (!empty($value)) {
            $trips = $trips->where('end_country', $value);
        }

        $value = $request->get('operator');
        if (!empty($value)) {
            $trips = $trips->where('operator_id', $value);
        }

        $value = $request->get('vehicle');
        if (!empty($value)) {
            $trips = $trips->where('vehicle', $value);
        }

        $value = $request->get('trailer');
        if (!empty($value)) {
            $trips = $trips->where('trailer', $value);
        }

        $trips = $trips->orderBy('start_date', 'desc')
            ->get();


        $shipments = Shipment::filterAgencies()
            ->whereIn('id', $ids)
            ->get();

        $shipmentsWithManifest = $shipments->filter(function ($item) {
            return !empty($item->trip_code);
        });

        $operators = User::listOperators(User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->ignoreAdmins()
            ->isOperator(true)
            ->orderBy('source', 'asc')
            ->orderBy('code', 'asc')
            ->orderBy('name', 'asc')
            ->get(['source', 'id', 'name', 'vehicle', 'provider_id']), true);

        $vehicles = Vehicle::listVehicles();
        $trailers = Vehicle::listVehicles(true);


        $data = compact(
            'trips',
            'shipmentsWithManifest',
            'shipments',
            'operators',
            'vehicles',
            'trailers',
            'ids'
        );

        $view = 'admin.trips.partials.shipments_table';
        if(app_mode_cargo()) {
            $view = 'admin.trips.partials.shipments_table_cargo';
        }
 
        if ($request->has('filter')) {
            $response = [
                'result' => true,
                'html'   => view($view, $data)->render()
            ];

            return response()->json($response);
        }

        return view('admin.trips.modals.add_selected_shipments', $data);
    }

    /**
     * Update the specified resource order in storage.
     * POST /admin/services/sort
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeSelectedShipments(Request $request)
    {

        try {
            $tripId = $request->get('trip_id');
            $shipmentsIds = explode(',', $request->get('shipments'));

            $finalStatus = ShippingStatus::isFinal()->pluck('id')->toArray();

            $shipmentsIds = Shipment::whereNotIn('status_id', $finalStatus)
                ->whereIn('id', $shipmentsIds)
                ->pluck('id')
                ->toArray();

            if (!empty($shipmentsIds)) {
                $trip = Trip::findOrFail($tripId);
                $trip->addShipments($shipmentsIds);

                $shipments = $trip->shipments;
                $shipments = $shipments->sortBy('pivot.sort');

                $view = 'admin.trips.partials.shipments_table';
                if(app_mode_cargo()) {
                    $view = 'admin.trips.partials.shipments_table_cargo';
                }

                $response = [
                    'result'   => true,
                    'feedback' => 'Serviços adicionados com sucesso.',
                    'html'     => view($view, compact('shipments', 'trip'))->render()
                ];
            } else {
                $response = [
                    'result' => false,
                    'feedback' => 'Não há serviços possíveis adicionar ao mapa.'
                ];
            }
        } catch (\Exception $e) {
            $response = [
                'result'   => false,
                'feedback' => 'Erro ao adicionar serviços. ' . $e->getMessage(),
            ];
        }

        return Response::json($response);
    }

    /**
     * Delete shipment from manifest
     * @param Request $request
     * @param $id
     * @return mixed
     * @throws \Throwable
     */
    public function deleteShipment(Request $request, $id, $shipmentId = null)
    {
        if ($shipmentId == '0') {
            $shipmentId = $request->shipment;
        }

        try {
            //atualiza informações geral dos envios do manifesto
            $trip = Trip::find($id);
            $result = $trip->deleteShipments([$shipmentId]);

            // Resets the vehicle and trailer attributes
            if ($shipmentId) {    
                Shipment::find($shipmentId)->update([
                    'vehicle' => '',
                    'trailer' => ''
                ]);
            }
            //--

            if ($result) {

                if ($request->ajax()) {

                    //obtem lista de envios atualizada para construir HTML
                    $shipments = $trip->shipments;
                    $shipments = $shipments->sortBy('pivot.sort');

                    if(app_mode_cargo()) {
                        $view = view('admin.trips.partials.shipments_table_cargo', compact('shipments', 'trip'))->render();
                    } else {
                        $view = view('admin.trips.partials.shipments_table', compact('shipments', 'trip'))->render();
                    }

                    $response = [
                        'result'   => true,
                        'feedback' => 'Serviços removidos com sucesso.',
                        'html'     => $view
                    ];

                    return response()->json($response);
                }

                return Redirect::back()->with('success', 'Registo removido com sucesso.');
            }
        } catch (\Exception $e) {

            if ($request->ajax()) {
                $response = [
                    'result'   => false,
                    'feedback' => 'Erro ao remover serviços. ' . $e->getMessage(),
                ];

                return response()->json($response);
            } else {
                return Redirect::back()->with('error', 'Erro ao remover serviços.');
            }
        }
    }

    /**
     * Update the specified resource order in storage.
     * POST /admin/services/sort
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function importShipment(Request $request, $id)
    {

        try {
            $shipmentId = $request->get('shipment');

            $trip = Trip::findOrFail($id);
            $trip->addShipments([$shipmentId]);

            //obtem lista de envios atualizada para construir HTML
            $shipments = $trip->shipments;
            $shipments = $shipments->sortBy('pivot.sort');

            $view = 'admin.trips.partials.shipments_table';
            if(app_mode_cargo()) {
                $view = 'admin.trips.partials.shipments_table_cargo';
            }

            $response = [
                'result'   => true,
                'feedback' => 'Serviço adicionado com sucesso.',
                'html'     => view($view, compact('shipments', 'trip'))->render()
            ];
        } catch (\Exception $e) {
            $response = [
                'result'   => false,
                'feedback' => 'Erro ao adicionar envio. ' . $e->getMessage(),
            ];
        }

        return Response::json($response);
    }

    /**
     * Update the specified resource order in storage.
     * POST /admin/services/sort
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sortShipments(Request $request)
    {

        try {

            $ids = $request->ids;

            foreach ($ids as $key => $id) {
                TripShipment::where('shipment_id', $id)->update(['sort' => $key + 1]);
            }

            $shipment = Shipment::whereIn('id', $ids)->first();
            $trip = $shipment->trips->update(['is_route_optimized' => 0]);

            $response = [
                'result'  => true,
                'message' => 'Ordenação gravada com sucesso.',
            ];
        } catch (\Exception $e) {
            $response = [
                'result'  => false,
                'message' => 'Erro ao gravar ordenação. ' . $e->getMessage(),
            ];
        }

        return response()->json($response);
    }

    /**
     * Print delivery manifest
     * @param $method
     * @param $parameters
     * @return mixed|string|\Symfony\Component\HttpFoundation\Response
     */
    public function printDocument(Request $request, $id, $docType = 'manifest')
    {
        $showPrices = false;
        if (hasPermission('billing')) {
            $showPrices = $request->get('prices');
        }

        $trip = Trip::with('shipments')
            ->with('operator')
            ->find($id);

        $shipments = $trip->shipments;
        $shipments = $shipments->sortBy('pivot.sort');
        $ids = $shipments->pluck('id')->toArray();

        if ($docType == 'summary') {
            if ($showPrices) {
                return Trip::printBillingSummary([$id]);
            } else {
                return Trip::printSummary([$id]);
            }
        } elseif ($docType == 'shipments') {
            return Shipment::printShipments($ids);
        } elseif ($docType == 'goods') {
            return Shipment::printGoodsManifest($ids, null);
        } elseif ($docType == 'labels') {
            return Shipment::printAdhesiveLabels($ids, null);
        } elseif ($docType == 'transport-guide') {
            return Shipment::printTransportGuide($ids, null);
        } else {
            $shipments = [@$trip->operator->name => $shipments];

            $params = [
                'code' => $trip->code,
                'date' => $trip->pickup_date
            ];

            return Shipment::printDeliveryMap($shipments, $params);
        }
    }

    /**
     * Undocumented function
     *
     * @param [type] $id
     * @return void
     */
    public function deliveryShipmentsMap(Request $request, $id)
    {

        $trip = Trip::findOrfail($id);

        $shipments = Shipment::with('status', 'service', 'customer')
            ->isFinalStatus(false)
            /* ->where(function($q){
                $q->whereNull('vehicle');
                $q->whereNull('operator_id');
            }) */
            ->whereNull('trip_id')
            ->where('is_collection', 0);

        if ($request->has('date_min')) {
            $dtMin = $request->get('date_min');
            $dtMax = $request->get('date_max');
            $dtMax = $dtMax ? $dtMax : $dtMin;
            $shipments->whereBetween('date', [$dtMin, $dtMax]);
        }

        if ($request->has('service')) {
            $value = $request->get('service');
            $shipments->whereIn('service_id', $value);
        }

        if ($request->has('provider')) {
            $value = $request->get('provider');
            $shipments->whereIn('provider_id', $value);
        }

        if ($request->has('service')) {
            $value = $request->get('service');
            $shipments->whereIn('service_id', $value);
        }

        if ($request->has('route')) {
            $value = $request->get('route');
            $shipments->whereIn('route_id', $value);
        }

        if ($request->has('sender_country')) {
            $value = $request->get('sender_country');
            $shipments->whereIn('sender_country', $value);
        }

        if ($request->has('recipient_country')) {
            $value = $request->get('recipient_country');
            $shipments->whereIn('recipient_country', $value);
        }


        $shipments = $shipments->orderBy('date', 'desc')
            ->take(100)
            ->get([
                'sender_name', 'sender_address', 'sender_zip_code', 'sender_city', 'sender_country', 'sender_latitude', 'sender_longitude',
                'recipient_name', 'recipient_address', 'recipient_zip_code', 'recipient_city', 'recipient_country', 'recipient_latitude', 'recipient_longitude',
                'id', 'tracking_code', 'customer_id', 'status_id', 'service_id', 'date', 'volumes', 'packaging_type', 'has_assembly'
            ]);

        //dd($shipments->toArray());

        $status = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingStatus::CACHE_TAG)
            ->where('is_shipment', 1)
            ->filterSources()
            ->isVisible()
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $transportTypes = TransportType::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $allServices = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->ordered()
            ->isCollection(false)
            ->get();


        $services = $allServices->pluck('name', 'id')
            ->toArray();

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $packTypes = PackType::remember(config('cache.query_ttl'))
            ->cacheTags(PackType::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->pluck('name', 'code')
            ->toArray();

        $routes = Route::remember(config('cache.query_ttl'))
            ->cacheTags(Route::CACHE_TAG)
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'shipments',
            'trip',
            'allServices',
            'services',
            'status',
            'routes',
            'providers',
            'packTypes',
            'transportTypes'
        );


        if ($request->ajax() && $request->has('filter')) {
            return view('admin.trips.partials.shipments_markers', $data)->render();
        }

        return view('admin.trips.modals.shipments_map', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function optimizeRouteEdit($id)
    {

        $trip = Trip::with('shipments')->find($id);

        if (empty($trip->start_location)) {
            $trip->start_location = Setting::get('company_zip_code') . ' ' . Setting::get('company_city');
        }

        if (empty($trip->end_location)) {
            $trip->end_location = Setting::get('company_zip_code') . ' ' . Setting::get('company_city');
        }

        $periods = $this->listPeriods(TripPeriod::filterSource()->get());

        $deliveryTimes = trans('admin/shipments.delivery_times');

        $data = compact(
            'trip',
            'deliveryTimes',
            'periods'
        );

        return view('admin.trips.modals.optimize_route', $data)->render();
    }

    /**
     * @param $method
     * @param $parameters
     * @return mixed|string|\Symfony\Component\HttpFoundation\Response
     */
    public function optimizeRouteStore(Request $request, $id)
    {
        $input = $request->all();

        $trip = Trip::with('shipments')->find($id);
        $trip->is_route_optimized = 0; //força a estar não otimizada e se tudo correr bem repõe o valor a 1
        $trip->fill($input);
        $trip->save();

        $trip->avg_delivery_time = empty($trip->avg_delivery_time) ? '00:05' : $trip->avg_delivery_time;

        $shipments = $trip->shipments;

        // $origin      = Setting::get('company_zip_code') . ',' . Setting::get('company_city');
        // $destination = $origin;
        $origin = $trip->start_location;
        $origin  = $origin . ',Portugal';
        $destination = $trip->end_location;
        $destination = $destination . ',Portugal';

        if ($origin == "") {
            return Redirect::back()->with('error', 'Insira uma localização de início');
        }
        if ($destination == "") {
            return Redirect::back()->with('error', 'Insira uma localização de fim');
        }


        $url = 'https://maps.googleapis.com/maps/api/directions/json?key=' . getGoogleMapsApiKey();
        $url .= '&origin=' . urlencode($origin);
        $url .= '&destination=' . urlencode($destination) . '&waypoints=optimize:true|';

        $shipmentsOriginalOrder = [];
        foreach ($shipments as $shipment) {
            //$url.=urlencode($shipment->recipient_address).','.urlencode($shipment->recipient_zip_code).','.urlencode($shipment->recipient_city);
            $url .= urlencode($shipment->recipient_zip_code) . ',' . urlencode($shipment->recipient_city);
            $url .= '|';

            $shipmentsOriginalOrder[] = $shipment->id;
        }
        $url = substr($url, 0, -1); //removing the last |
        $url .= '&sensor=false';
        $response = file_get_contents($url);
        $response = json_decode($response);


        if ($response->status != 'OK') {
            return Redirect::back()->with('error', 'Não foi possível calcular a rota.');
        }


        $positions = @$response->routes[0];
        $positions = $positions->waypoint_order;

        /*  $positions = [
              0 => 0,
              1 => 5,
              2 => 4,
              3 => 2,
              4 => 1,
              5 => 6,
              6 => 3,
        ];*/

        $correctShipmentsOrder = [];
        foreach ($positions as $key => $shipmentPos) {
            $correctShipmentsOrder[] = $shipmentsOriginalOrder[$shipmentPos];
        }

        foreach ($correctShipmentsOrder as $sort => $shipmentId) {
            TripShipment::where('shipment_id', $shipmentId)
                ->where('trip_id', $id)
                ->update(['sort' => $sort]);
        }

        $shipments = TripShipment::where('trip_id', $id)
            ->orderBy('sort', 'ASC')
            ->get();

        $values = @$response->routes;
        if (count($shipments) != count($values[0]->legs) - 1)
            return Redirect::back()->with('error', 'Não foi possível calcular os horários dos envios');


        $hourBegin = new DateTime($trip->start_hour);
        $hourEndDelivery = "";
        if (isset($values)) {
            for ($i = 0; $i < count($shipments); $i++) {
                $seconds = $values[0]->legs[$i]->duration->value;


                $time = gmdate("H:i:s", $seconds);

                $arrTime = explode(":", $time);

                if ($hourEndDelivery != "") {
                    $hourBegin = new DateTime($hourEndDelivery);
                }

                $hourBegin->add(new DateInterval('PT' . $arrTime[0] . 'H' . $arrTime[1] . 'M' . $arrTime[2] . 'S'));
                $hourBegin = $hourBegin->format('H:i:s');

                $hourBeginNotChange = $hourBegin;

                $hourEnd = new DateTime($hourBegin);
                $arrMargin = explode(":", $trip->avg_delivery_time);
                $hourEnd->add(new DateInterval('PT' . $arrMargin[0] . 'H' . $arrMargin[1] . 'M'));
                $hourEnd = $hourEnd->format('H:i');

                //hourbegin + time_Deliring + time_assembly
                $shipment = Shipment::where('id', $shipments[$i]->shipment_id)
                    ->with('customer')
                    ->first();

                $timeAssembly   = @$shipment->customer->time_assembly ? @$shipment->customer->time_assembly : '00:02:00';
                $timeDelivering = @$shipment->customer->time_delivering ? @$shipment->customer->time_delivering : '00:02:00';

                $arrTimeAssembly   = explode(":", $timeAssembly);
                $arrTimeDelivering = explode(":", $timeDelivering);

                $hourEndDelivery = new DateTime($hourBeginNotChange);
                $hourEndDelivery->add(new DateInterval('PT' . @$arrTimeAssembly[0] . 'H' . @$arrTimeAssembly[1] . 'M'));
                $hourEndDelivery->add(new DateInterval('PT' . @$arrTimeDelivering[0] . 'H' . @$arrTimeDelivering[1] . 'M'));
                $hourEndDelivery = $hourEndDelivery->format('H:i');

                Shipment::where('id', $shipments[$i]->shipment_id)
                    ->update(['start_hour' => $hourBegin, 'end_hour' => $hourEnd, 'estimated_delivery_finish' => $hourEndDelivery]);
            }

            $trip->is_route_optimized = 1;
            $trip->save();
        }


        return Redirect::back()->with('success', 'Rota otimizada com sucesso.');
    }

    /**
     *
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function notifyCustomers(Request $request, $id)
    {

        $shipments = Shipment::where('trip_id', $id)->get();

        foreach ($shipments as $shipment) {
            try {
                if (Setting::get('time_window_email') == 1) {
                    $shipment->sendEmailTimeWindow();
                } else {
                    $shipment->sendEmail();
                }
            } catch (\Exception $e) {
            }

            try {
                $shipment->sendSms();
            } catch (\Exception $e) {
            }
        }

        return  Redirect::back()->with('success', 'Notificações enviadas com sucesso');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createReturn(Request $request, $id)
    {

        $originalTrip = Trip::findOrFail($id);

        if ($originalTrip->is_return) {
            return Redirect::back()->with('error', 'O mapa selecionado já é um retorno.');
        }

        $trip = new Trip();
        $trip->fill($originalTrip->toArray());
        $trip->type           = 'R';
        $trip->parent_id      = $originalTrip->id;
        $trip->parent_type    = $originalTrip->type;
        $trip->parent_code    = $originalTrip->code;

        $trip->start_location = $originalTrip->end_location;
        $trip->start_country  = $originalTrip->end_country;
        $trip->start_date     = $originalTrip->end_date;
        $trip->start_hour     = $originalTrip->end_hour;
        $trip->start_kms      = $originalTrip->end_kms;

        $trip->end_location   = null;
        $trip->end_country    = null;
        $trip->end_date       = null;
        $trip->end_hour       = null;
        $trip->end_kms        = null;
        $trip->allowances_price = null;
        $trip->weekend_price    = null;


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

        $routes = Route::remember(config('cache.query_ttl'))
            ->cacheTags(Route::CACHE_TAG)
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $status = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingStatus::CACHE_TAG)
            ->where('is_shipment', 1)
            ->filterSources()
            ->isVisible()
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $deliveryTimes = trans('admin/shipments.delivery_times');

        $vehicles = Vehicle::listVehicles();
        $trailers = Vehicle::listVehicles(true);
        $periods  = $this->listPeriods(TripPeriod::filterSource()->get());
        $shipmentsIds = [];
        $vatRates = Invoice::getVatTaxes();

        $action = 'Criar Retorno';

        $formOptions = array('route' => array('admin.trips.store'), 'method' => 'POST', 'class' => 'form-trip');

        $data = compact(
            'trip',
            'action',
            'formOptions',
            'operators',
            'providers',
            'routes',
            'deliveryTimes',
            'vehicles',
            'trailers',
            'periods',
            'status',
            'shipmentsIds',
            'vatRates'
        );

        if (Setting::get('app_mode') == 'cargo') {
            return view('admin.trips.edit_cargo', $data)->render();
        }

        return view('admin.trips.edit', $data)->render();
    }

    /**
     * Create delivery manifest return
     * 
     * @param Request $request 
     * @param Trip $trip 
     * @return mixed
     */
    public function createDirectReturn(Request $request, $id)
    {


        $trip = Trip::filterSource()
            ->findOrFail($id);

        if ($trip->is_return) {
            return Redirect::back()->with('error', 'O mapa selecionado já é um retorno.');
        }

        $return = $trip->replicate([
            'code',
            'end_date',
            'end_hour',
            'end_location',
            'end_country',
            'end_kms'
        ]);

        // Alterar horas
        $return->start_date     = $trip->end_date ?? date('Y-m-d');
        $return->start_hour     = '00:00';

        // Alterar localizações
        $return->start_location = $trip->end_location;
        $return->start_country  = $trip->end_country;
        $return->start_kms      = $trip->end_kms;

        if ($request->get('direct')) {
            $return->end_location = $trip->start_location;
            $return->end_country  = $trip->start_country;
        }

        $return->type           = 'R';
        $return->parent_code    = $trip->code;
        $return->parent_id      = $trip->id;
        $return->created_by     = Auth::user()->id;
        $return->code           = 'R' . $trip->code;
        $return->sort           = substr($trip->sort, 0, -1) . '1'; //o retorno termina com '0' para que ao ordenar fique junto com o mapa original
        $return->save();
        //$return->setCode();

        //atualiza mapa inicial
        $trip->children_type = 'R';
        $trip->children_code = $return->code;
        $trip->children_id   = $return->id;
        $trip->save();

        return Redirect::back()->with('success', 'Retorno gerado com sucesso.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editChangeStatus(Request $request, $id)
    {
        $trip = Trip::filterSource()->findOrFail($id);
        $trip->has_trips = true;

        $operators = User::listOperators(User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->ignoreAdmins()
            ->orderBy('source', 'asc')
            ->orderBy('code', 'asc')
            ->orderBy('name', 'asc')
            ->get(['source', 'id', 'name', 'vehicle', 'provider_id']), true);

        $status = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingStatus::CACHE_TAG)
            ->where('is_shipment', 1)
            ->filterSources()
            ->isVisible()
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $incidences = IncidenceType::remember(config('cache.query_ttl'))
            ->cacheTags(IncidenceType::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $vehicles = Vehicle::listVehicles();
        $trailers = Vehicle::listVehicles(true);

        $shipment = $trip->shipments->first();
        $shipment->hide_checkbox_notifications = true;

        $formOptions = array('route' => array('admin.trips.change-status.store', $trip->id), 'method' => 'POST', 'class' => 'form-update-history');

        $data = compact(
            'trip',
            'formOptions',
            'operators',
            'vehicles',
            'trailers',
            'status',
            'shipment',
            'incidences'
        );

        return view('admin.shipments.history.edit', $data)->render();
    }

    /**
     * Store change status
     *
     * @param Request $request
     * @param Trip $trip
     * @return mixed
     */
    public function storeChangeStatus(Request $request, $id)
    {

        $trip = Trip::filterSource()
            ->findOrFail($id);


        if ($request->get('operator_id') > 0) {
            $trip->operator_id = $request->get('operator_id');
        }

        if ($request->get('vehicle') > 0) {
            $trip->vehicle = $request->get('vehicle');
        }

        if ($request->get('trailer') > 0) {
            $trip->trailer = $request->get('trailer');
        }

        $trip->save();


        $finalStatus = ShippingStatus::isFinal()
            ->pluck('id')
            ->toArray();

        $shipmentsIds = $trip->shipments->filter(function ($q) use ($finalStatus) {
                return !in_array($q->status_id, $finalStatus);
            })
            ->pluck('id')
            ->toArray();

        $request->ids = implode(',', $shipmentsIds);
        $controller = new HistoryController();
        return $controller->store($request);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editChangeTrailer(Request $request, $id)
    {

        $trip = Trip::filterSource()
            ->findOrFail($id);

        $operators = User::listOperators(User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->ignoreAdmins()
            ->isOperator(true)
            ->orderBy('source', 'asc')
            ->orderBy('code', 'asc')
            ->orderBy('name', 'asc')
            ->get(['source', 'id', 'name', 'vehicle', 'provider_id']), true);

        $status = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingStatus::CACHE_TAG)
            ->where('is_shipment', 1)
            ->filterSources()
            ->isVisible()
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $vehicles = Vehicle::listVehicles();
        $trailers = Vehicle::listVehicles(true);


        $formOptions = array('route' => array('admin.trips.change-trailer.store', $trip->id), 'method' => 'POST');

        $data = compact(
            'trip',
            'formOptions',
            'operators',
            'vehicles',
            'trailers',
            'status'
        );

        return view('admin.trips.change_trailer', $data)->render();
    }

    /**
     * Store change trailer/vehicle
     *
     * @param Request $request
     * @param Trip $trip
     * @return mixed
     */
    public function storeChangeTrailer(Request $request, $id)
    {

        $operator   = $request->get('operator_id');
        $assistants = $request->get('assistants');
        $vehicle    = $request->get('vehicle');
        $trailer    = $request->get('trailer');
        $createdAt  = $request->get('date') . ' ' . $request->get('hour');
        $obs        = $request->get('obs');

        $city = $request->get('city');
        if ($request->get('country')) {
            $city = empty($city) ? $request->get('country') : $city . ', ' . trans('country.' . $request->get('country'));
        }


        $trip = Trip::filterSource()
            ->findOrFail($id);

        $trip->operator_id = $operator;
        $trip->assistants  = $assistants;
        $trip->vehicle     = $vehicle;
        $trip->trailer     = $trailer;
        $trip->save();

        $shipments = $trip->shipments;
        foreach ($shipments as $shipment) {

            //atualiza envio sem alterar o estado
            $shipment->operator_id = $operator;
            $shipment->vehicle     = $vehicle;
            $shipment->trailer     = $trailer;
            $shipment->save();

            //atualiza historico
            $history = new ShipmentHistory();
            $history->insert([
                'shipment_id' => $shipment->id,
                'status_id'   => ShippingStatus::TRAILER_CHANGED_ID,
                'operator_id' => $operator,
                'vehicle'     => $vehicle,
                'trailer'     => $trailer,
                'city'        => $city,
                'obs'         => $obs,
                'created_at'  => $createdAt,
                'agency_id'   => Auth::user()->agency_id,
                'user_id'     => Auth::user()->id
            ]);
        }

        return Redirect::back()->with('success', 'Alteração gravada com sucesso.');
    }

    /**
     * List periods with data attributes
     *
     * @param $allPeriods
     * @return array
     */
    public function listPeriods($allPeriods)
    {
        if ($allPeriods->count() > 1) {
            $arr[] = ['value' => '', 'display' => ''];
        } else {
            $arr = [];
        }

        foreach ($allPeriods as $item) {

            $arr[] = [
                'value'      => $item->id,
                'display'    => $item->name,
                'data-start' => $item->start_hour,
                'data-end'   => $item->end_hour,
            ];
        }
        return $arr;
    }

    /**
     * Confirm docs reception
     * @param Request $request
     */
    public function confirmDocsReception(Request $request, $id)
    {

        $ids = $request->id;

        $shipments = Shipment::filterAgencies()
            ->whereIn('id', $ids)
            ->get();

        foreach ($shipments as $shipment) {

            $shipment->status_id = ShippingStatus::DOCS_RECEIVED_ID;
            $shipment->save();

            $history = new ShipmentHistory();
            $history->shipment_id  = $shipment->id;
            $history->status_id    = $shipment->status_id;
            $history->user_id      = Auth::user()->id;
            $history->save();
        }

        return Redirect::back()->with('success', 'Confirmação de documentos com sucesso.');
    }

    /**
     * Edit activity declaration before print
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editActivityDeclaration(Request $request, $id)
    {

        $trip = Trip::findOrFail($id);
        $trip->edit_modal = true;

        $operators = [];

        if ($trip->operator_id) {

            $lastTrip = Trip::where('operator_id', $trip->operator_id)
                ->orderBy('delivery_date', 'desc')
                ->first();

            if ($lastTrip) {
                $lastDate = new Date($lastTrip->end_date);
                $nextDate = new Date($trip->start_date);

                $diffInHours = $nextDate->diffInHours($lastDate);
                $diffInDays  = $nextDate->diffInDays($lastDate);

                $lastDate = $lastDate->format('Y-m-d');
                $lastHour = @$trip->end_hour;

                $trip->last_date  = $lastDate;
                $trip->last_hour  = $lastHour;
                $trip->last_manifest_code  = $lastTrip->code;
                $trip->last_manifest_id    = $lastTrip->id;
                $trip->last_manifest_days  = $diffInDays;
                $trip->last_manifest_hours = $diffInHours;
            }

            $operators[$trip->operator_id] = $trip->operator->name;
        }

        if ($trip->assistants) {
            $operators += User::filterSource()
                ->isOperator()
                ->whereIn('id', $trip->assistants)
                ->pluck('name', 'id')
                ->toArray();
        }

        if (Auth::user()->isManager()) {
            $managers = User::listOperators(User::remember(config('cache.query_ttl'))
                ->cacheTags(User::CACHE_TAG)
                ->filterAgencies()
                ->ignoreAdmins()
                ->isOperator(false)
                ->orderBy('source', 'asc')
                ->orderBy('code', 'asc')
                ->orderBy('name', 'asc')
                ->get(['source', 'id', 'name', 'vehicle', 'provider_id']), true);
        } else {
            $managers = [Auth::user()->id => Auth::user()->name];
        }

        $data = compact(
            'trip',
            'lastTrip',
            'managers',
            'operators'
        );

        return view('admin.trips.modals.print_activity_declaration', $data)->render();
    }

    /**
     * Confirm docs reception
     * @param Request $request
     */
    public function checkOperator(Request $request)
    {

        $operatorId = $request->get('operator_id');
        $startDate  = $request->get('start_date');

        $result = false;
        if ($operatorId) {

            //last trip
            $trip = Trip::where('operator_id', $operatorId)
                /* ->whereNotNull('end_date')
                ->orderBy('end_date', 'desc')*/
                ->where('start_date', '<=', date('Y-m-d'))
                ->orderBy('start_date', 'desc')
                ->first();

            if ($trip) {

                $lastDate    = null;
                $lastHour    = null;
                $nextDate    = new Date($startDate);
                $result      = false;
                $finished    = false; //ultima viagem terminada
                $diffInDays  = 0;
                $diffInHours = 0;

                if ($trip->end_date) {
                    $finished = true;
                    $lastDate = new Date($trip->end_date);

                    $diffInHours = $nextDate->diffInHours($lastDate);
                    $diffInDays  = $nextDate->diffInDays($lastDate);

                    $lastDate = $lastDate->format('Y-m-d');
                    $lastHour = @$trip->end_hour;

                    if ($diffInHours >= 72) {
                        $result = true;
                    }
                }

                return response()->json([
                    'result' => $result,
                    'hours'  => $diffInHours,
                    'last_manifest' => [
                        'date'        => $lastDate,
                        'hour'        => $lastHour,
                        'code'        => @$trip->code,
                        'code_url'    => '<a href="' . route('admin.trips.show', $trip->id) . '" target="_blank">' . $trip->code . '</a>',
                        'days'        => $diffInDays,
                        'operator_id' => $trip->operator_id,
                        'operator'    => @$trip->operator->name,
                        'finished'    => $finished
                    ]
                ]);
            }
        }

        return response()->json([
            'result' => false
        ]);
    }

    /**
     * Calculate allowances
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculateAllowances(Request $request, int $id = null)
    {
        $input = $request->all();
        $trip = Trip::findOrNew($id);
        $trip->fill($input);

        return Response::json([
            'result' => true,
            'data'   => Allowance::calculateTrip($trip)
        ]);
    }
}
