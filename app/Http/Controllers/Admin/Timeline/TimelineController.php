<?php

namespace App\Http\Controllers\Admin\Timeline;

use App\Models\Agency;
use App\Models\Timeline\Event;
use App\Models\Timeline\EventType;
use App\Models\FleetGest\Vehicle;
use App\Models\Service;
use App\Models\Shipment;
use App\Models\User;
use Cocur\Slugify\Slugify;
use Illuminate\Http\Request;
use Html, Cache, Response, Auth, Redirect;
use Jenssegers\Date\Date;

class TimelineController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_tag_cargo_planning';

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'cargo_planning';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',cargo_planning']);
    }
    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        $startDate = date('Y-m-d H:i:s');
        if($request->has('start')) {
            $startDate = new Date($request->get('start'));
            $startDate = $startDate->format('Y-m-d H:i:s');
        }

        $calendarResources = $this->getCalendarResources($request);

        $eventsTypes = EventType::filterSource()->ordered()->get();

        $vehiclesList = \App\Models\Vehicle::listVehicles();

        $operatorsList = User::listOperators(User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->isActive()
            ->isOperator()
            ->orderBy('source', 'asc')
            ->orderBy('name', 'asc')
            ->get(['source', 'id', 'name', 'vehicle', 'provider_id']), Auth::user()->isAdmin() ? true : false);

        $servicesList = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->ordered()
            ->isCollection(false)
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'vehiclesList',
            'operatorsList',
            'servicesList',
            'calendarResources',
            'eventsTypes',
            'startDate'
        );

        return $this->setContent('admin.timeline.index_calendar', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        $event = Event::filterSource()->findOrfail($id);

        $action = 'Editar tipo de evento';

        $formOptions = array('route' => array('admin.timeline.update', $event->id), 'method' => 'PUT');

        $colors = trans('admin/global.colors');

        $event->start_hour = $event->start_date->format('H:i');
        $event->end_hour   = $event->end_date->format('H:i');

        $event->start = $event->start_date->format('Y-m-d');
        $event->end   = $event->end_date->format('Y-m-d');

        $data = compact(
            'event',
            'action',
            'formOptions',
            'colors'
        );

        return view('admin.timeline.edit', $data)->render();
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

        if(@$input['start']) {
            $input['start_date'] = @$input['start'].' '.@$input['start_hour'].':00';
        }

        if(@$input['end']) {
            $input['end_date'] = @$input['end'].' '.@$input['end_hour'].':00';
        }


        $input['end_date'] = @$input['end_date'] ? $input['end_date'] : $input['start_date'];

        $startDate = new Date($input['start_date']);
        $input['start_date'] = $startDate->format('Y-m-d H:i:s');
        $endDate = new Date($input['end_date']);
        $input['end_date']  = $endDate->format('Y-m-d H:i:s');

        $event = Event::filterSource()->findOrNew($id);

        if(!$event->exists) {
            $eventType = EventType::filterSource()->find($input['type_id']);

            $input['resource'] = $request->resource;
            $input['title']    = $eventType->title;
            $input['type_id']  = $eventType->id;
            $input['color']    = $eventType->color;
        }

        if ($event->validate($input)) {
            $event->fill($input);
            $event->source = config('app.source');
            $event->save();

            if($request->has('redirect') && $request->get('redirect') == 'back') {
                return Redirect::back()->with('success', 'Gravado com sucesso.');
            }

            return response()->json([
                'result'     => true,
                'feedback'   => 'Gravado com sucesso.',
                'id'         => $event->id,
                'target_url' => route('admin.timeline.update', $event->id)
            ]);
        }

        if($request->has('redirect') && $request->get('redirect') == 'back') {
            return Redirect::back()->with('error', $event->errors()->first());
        }

        return response()->json([
            'result'    => false,
            'feedback'  => $event->errors()->first(),
            'id'        => '',
            'edit_url'  => ''
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        Event::flushCache(Event::CACHE_TAG);

        $result = Event::filterSource()
            ->whereId($id)
            ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o evento');
        }

        return Redirect::back()->with('success', 'Evento eliminado com sucesso.');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCalendarResources(Request $request) {

        if(1) {
            $list = $this->getVehicleResources($request);
        }

        //$list = $this->getCountryResources($request);

        return json_encode($list);
    }

    /**
     * Return lisr of vehicles resources
     * @param Request $request
     */
    public function getVehicleResources(Request $request) {

        $vehicles = Vehicle::filterSource();
        if($request->has('vehicle')) {
            $vehiclesIds = explode(',', $request->get('vehicle'));
            $vehicles = $vehicles->whereIn('license_plate', $vehiclesIds);
        }

        $vehicles = $vehicles->isActive()
            ->where('type', '<>', 'trailer')
            ->orderBy('name')
            ->get();


        $list = [[
            'id'         => '000',
            'title'      => 'Sem veículo'
        ]];

        foreach ($vehicles as $vehicle) {
            $list[] = [
                'id'    => trim(slugify($vehicle->license_plate)),
                'title' => $vehicle->name,
                'html'  => '<br/><small>'. $vehicle->license_plate .' &bull; ' . trans('admin/fleet.vehicles.types.' . $vehicle->type) . '</small>',
                //'eventColor' => 'red'
            ];
        }

        return $list;
    }

    /**
     * Return lisr of vehicles resources
     * @param Request $request
     */
    public function getCountryResources(Request $request) {

        $countries = ['pt', 'es', 'fr', 'de', 'be'];

        foreach ($countries as $country) {
            $list[] = [
                'id'    => $country,
                'title' => trans('country.' . $country),
                'html'  => '<i class="flag-icon flag-icon-'.$country.'"></i> ',
                'html_pos' => 'before',
                'background' => '',
            ];
        }

        return $list;
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCalendarEvents(Request $request) {

        $input = $request->all();

        $startDate = new Date($request->start);
        $endDate = new Date($request->end);

        $shippingEvents = $this->getShippingEvents($startDate, $endDate, $input);
        $customEvents   = $this->getCustomEvents($startDate, $endDate, $input);
        $events = array_merge($customEvents, $shippingEvents);

        return response()->json($events);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCustomEvents($startDate, $endDate, $data = null) {

        $startDate = $startDate->format('Y-m-d H:i:s');
        $endDate   = $endDate->format('Y-m-d H:i:s');

        $planningEvents = Event::filterSource()
            ->where(function($q) use($startDate, $endDate) {
                $q->where(function($q) use($startDate, $endDate) {
                    $q->where('start_date', '>=', $startDate)
                      ->where('start_date', '<=', $endDate);
                });
                $q->orWhere(function($q) use($startDate, $endDate) {
                    $q->where('end_date', '>=', $startDate)
                      ->where('end_date', '<=', $endDate);
                });
            })
            ->get();

        $events = [];
        foreach ($planningEvents as $planningEvent) {
            $events[] = [
                'target'            => 'custom',
                'id'                => $planningEvent->id,
                'title'             => $planningEvent->title,
                'start'             => $this->formatDate($planningEvent->start_date),
                'end'               => $this->formatDate($planningEvent->end_date),
                'resourceId'        => $planningEvent->resource,
                'color'             => $planningEvent->color,
                'target_url'        => route('admin.timeline.update', $planningEvent->id)
            ];
        }

        return $events;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getShippingEvents($startDate, $endDate, $data = null) {

        $startDate = $startDate->format('Y-m-d H:i:s');
        $endDate   = $endDate->format('Y-m-d H:i:s');

        $agencies = Agency::filterSource()->pluck('id')->toArray();
        $shipments = Shipment::with('customer')
            ->whereIn('agency_id', $agencies)
            ->where(function($q) use($startDate, $endDate) {
                $q->where(function($q) use($startDate, $endDate){
                    $q->where('shipping_date', '>=', $startDate);
                    $q->where('shipping_date', '<=', $endDate);
                });
                $q->orWhere(function($q) use($startDate, $endDate){
                    $q->where('delivery_date', '>=', $startDate);
                    $q->where('delivery_date', '<=', $endDate);
                });
            });
           /* ->where(function ($q) {
                $q->whereNotNull('vehicle');
                $q->where('vehicle', '<>', '');
            });*/

        if(@$data['vehicle']) {
            $vals = explode(',', $data['vehicle']);
            $shipments = $shipments->whereIn('vehicle', $vals);
        }

        if(@$data['operator']) {
            $vals = explode(',', $data['operator']);
            $shipments = $shipments->whereIn('operator_id', $vals);
        }

        if(@$data['service']) {
            $vals = explode(',', $data['service']);
            $shipments = $shipments->whereIn('service_id', $vals);
        }

        if(@$data['sender_country']) {
            $vals = explode(',', $data['sender_country']);
            $shipments = $shipments->whereIn('sender_country', $vals);
        }

        if(@$data['recipient_country']) {
            $vals = explode(',', $data['recipient_country']);
            $shipments = $shipments->whereIn('recipient_country', $vals);
        }

        $shipments = $shipments->get([
                'id',
                'customer_id',
                'tracking_code',
                'vehicle',
                'sender_name',
                'sender_city',
                'sender_country',
                'recipient_name',
                'recipient_city',
                'recipient_zip_code',
                'recipient_country',
                'shipping_date',
                'delivery_date'
            ]);

        
        $events = [];
        foreach ($shipments as $shipment) {

            if($shipment->sender_country == $shipment->recipient_country) {
                $flagHtml = '<i class="flag-icon flag-icon-'.$shipment->sender_country.'"></i>';
            } else {
                $flagHtml = '<i class="flag-icon flag-icon-'.$shipment->sender_country.'"></i> <i class="fas fa-angle-right"></i> <i class="flag-icon flag-icon-'.$shipment->recipient_country.'"></i>';
            }

            $locationCode = strtoupper($shipment->recipient_country). substr($shipment->recipient_zip_code, 0, 2);

            //add cargo event
            $events[] = [
                'target'            => 'shipment',
                'id'                => $shipment->id,
                'title'             => $flagHtml . ' | <b>'. $locationCode . '</b> | '. $shipment->customer->name . ($shipment->trailer ? ' | ' . $shipment->trailer : '' ),
                'start'             => $this->formatDate($shipment->shipping_date),
                'end'               => $this->formatDate($shipment->delivery_date),
                'resourceId'        => empty($shipment->vehicle) ? '000' : trim(slugify($shipment->vehicle)),
                'color'             => '#50aeff',
                'sender_name'       => $shipment->sender_name,
                'sender_city'       => $shipment->sender_city,
                'recipient_name'    => $shipment->sender_name,
                'recipient_city'    => $shipment->recipient_city,
                'target_url'        => route('admin.shipments.edit', $shipment->id),
                'update_url'        => route('admin.shipments.update', $shipment->id)
            ];
        }

        return $events;
    }

    /**
     * Format date
     * @param $date
     * @return string
     */
    public function formatDate($date, $isStartDate = true) {
        $time = $date->format('H:i');

       /* if($time == '00:00') {
            $time = $isStartDate ? '09:00' : '19:00';
        }*/

        $date = $date->format('Y-m-d');

        return $date . 'T'.$time.':00+00:00';
    }
}
