<?php

namespace App\Http\Controllers\Mobile;

use App\Models\Agency;
use App\Models\Customer;
use App\Models\Provider;
use App\Models\FleetGest\FuelLog;
use App\Models\FleetGest\Vehicle;
use App\Models\IncidenceType;
use App\Models\OperatorTask;
use App\Models\Route;
use App\Models\ShipmentExpense;
use App\Models\ShippingExpense;
use App\Models\ShippingStatus;
use App\Models\User;
use App\Models\UserLocation;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Html, DB, Auth, Response, Hash, Validator, File, Croppa, Session, Setting, Date, App;
use App\Models\Shipment;
use App\Models\ShipmentHistory;

class BaseController extends \App\Http\Controllers\Admin\Controller {

    /**
     * The layout that should be used for responses
     *
     * @var string
     */
    protected $layout = 'mobile.layouts.master';

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'app';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',app']);
        validateModule('app');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function oldVersion(Request $request) {
        return $this->setContent('mobile.old_version');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadApk(Request $request) {

        if(hasModule('app_apk')) {
            return Redirect::to(coreUrl('mobile/enovo_tms.apk'));
        }

        return App::abort(404);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request) {

        Auth::logout();
        Session::flush();

        return Redirect::route('mobile.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        $lat = $request->get('bg_lat');
        $lng = $request->get('bg_lng');
        if(!empty($lat) && !empty($lng)) {
            $this->storeUserLocation($lat, $lng);
        }

        $operatorId = Auth::user()->id;

        if(Setting::get('mobile_app_basic_mode')) {
            return Redirect::route('mobile.shipments.index');
        }

        $totalTasks = 0;
        if(Setting::get('mobile_app_menu_tasks')) {
            $totalTasks = OperatorTask::whereRaw('DATE(last_update) = "' . date('Y-m-d') . '"')
                ->where('date', '<=', date('Y-m-d'))
                ->whereNull('operator_id')
                ->get();

            $totalTasks = $totalTasks->filter(function ($item) use ($operatorId) {
                $targets = empty($item->operators) ? [] : $item->operators;
                return empty($item->operator_id) && in_array($operatorId, $targets);
            })->count();
        }

        $totalPickups = Shipment::where('operator_id', $operatorId)
                            ->where('is_collection', 1)
                            ->where('date', date('Y-m-d'))
                            ->whereHas('status', function($q){
                                $q->where('is_final', 0);
                            })
                            ->count();

        $totalUnreadShipments = Shipment::where('operator_id', $operatorId)
                    ->where('is_collection', 0)
                    ->whereIn('status_id', [ShippingStatus::PENDING_OPERATOR])
                    ->count();


        if($request->ajax()) {
            $result = [
                'result' => true,
                'html'   => view('mobile.pages.home', compact('totalTasks', 'totalPickups', 'totalUnreadShipments'))->render()
            ];

            return Response::json($result);
        }

        return $this->setContent('mobile.index', compact('totalTasks', 'totalPickups', 'totalUnreadShipments'));
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function shipmentsList(Request $request) {

        $lat = $request->get('bg_lat');
        $lng = $request->get('bg_lng');
        if(!empty($lat) && !empty($lng)) {
            $this->storeUserLocation($lat, $lng);
        }

        $type  = $request->get('type', false);

        $statusIds = [38,37,36,31,20,21,22,20,16,3,4];
        if(Setting::get('mobile_app_status_delivery')) {
            $statusIds = Setting::get('mobile_app_status_delivery');
        }

        $operatorId = Auth::user()->id;

        $shipments = Shipment::with('status', 'customer', 'service');

        if(hasModule('shipment_attachments')) {
            $shipments = $shipments->with(['attachments' => function($q){
                $q->where('operator_visible', 1);
                $q->select([
                    'id',
                    DB::raw('source_id as shipment_id'),
                    'name',
                    'filepath'
                ]);
            }]);
        }

        $shipments = $shipments->where(function($q) use($operatorId) {
                $q->where('operator_id', $operatorId);

                if(!Setting::get('mobile_app_show_scheduled')) {
                    $q->where('date', '<=', date('Y-m-d')); //oculta envios agendados
                }

                if(config('app.source') == 'asfaltolargo') {
                    $q->orWhere(function($q){
                        $q->where('operator_id', 375);
                        $q->where(function($q){
                            $q->where('agency_id', 1);
                            $q->orWhere('recipient_agency_id', 1);
                        });
                    });
                }
            });


        if(in_array($type, ['concluded', 'incidences'])) {

            if($type == 'concluded') {
                $shipments = $shipments->whereHas('status', function($q){
                    $q->where('is_final', 1);

                    if(Setting::get('mobile_app_basic_mode')) {
                         $q->orWhere('id', '9');
                    }
                });
            }

            if($type == 'incidences') {
                $shipments = $shipments->whereStatusId('9');
            }

            $shipments = $shipments->orderBy('date', 'desc')
                                   ->orderBy('recipient_name');

        } else {
            $shipments = $shipments->whereHas('status', function($q){
                $q->where('is_final', 0);
            })
            ->whereIn('status_id', $statusIds)
            ->ordered()
            ->orderBy('date', 'desc')
            ->orderBy('recipient_name')
            ->orderBy('recipient_zip_code', 'asc');
        }

        $shipments = $shipments
                    ->take(100)
                    ->get();

        $incidences = $this->getIncidencesList();

        if($request->ajax()) {
            $result = [
                'result' => true,
                'html'   => view('mobile.pages.shipments_list', compact('shipments', 'concluded', 'incidences'))->render()
            ];

            return Response::json($result);
        } else {
            return $this->setContent('mobile.deliveries', compact('shipments', 'incidences'))->render();
        }
    }

    /**
     * Set shipment as read
     * @param $id
     */
    public function shipmentRead(Request $request, $id) {

        $lat = $request->get('bg_lat');
        $lng = $request->get('bg_lng');
        if(!empty($lat) && !empty($lng)) {
            $this->storeUserLocation($lat, $lng);
        }

        $operatorId = Auth::user()->id;

        $operatorsIds = [$operatorId];

        if(config('app.source') == 'rapex' && $operatorId == 740) {
            $operatorsIds = [740,461];
        }

        $shipment = Shipment::whereIn('operator_id', $operatorsIds)
            ->whereId($id)
            ->firstOrFail();

        //estado pendente estafeta. Altera para "lido pelo motorista"
        $readedByOperator = Setting::get('mobile_app_status_after_read_operator') ? Setting::get('mobile_app_status_after_read_operator') : 38;
        if(in_array($shipment->status_id, [37])) { //37 = pendente estafeta
            $shipment->status_id = $readedByOperator; //lido pelo estafeta
            $shipment->save();

            $history = new ShipmentHistory();
            $history->shipment_id  = $id;
            $history->operator_id  = $operatorId;
            $history->agency_id    = $shipment->sender_agency_id;
            $history->status_id    = $shipment->status_id;
            $history->latitude     = $lat;
            $history->longitude    = $lng;
            $history->save();
        } elseif($shipment->status_id == $readedByOperator && $request->source != 'list') { //se ja foi lido e clicou no botão para mudar estado

            $shipment->status_id = Setting::get('mobile_app_status_after_pickup') ? Setting::get('mobile_app_status_after_pickup') : 4;
            $shipment->save();

            $history = new ShipmentHistory();
            $history->shipment_id  = $id;
            $history->operator_id  = $operatorId;
            $history->agency_id    = $shipment->sender_agency_id;
            $history->status_id    = 36; //recolhido
            $history->latitude     = $lat;
            $history->longitude    = $lng;
            $history->save();

            $history = new ShipmentHistory();
            $history->shipment_id  = $id;
            $history->operator_id  = $operatorId;
            $history->agency_id    = $shipment->sender_agency_id;
            $history->status_id    = $shipment->status_id; //em transporte. colocar primeiro para na lista aparecer primeiro
            $history->latitude     = $lat;
            $history->longitude    = $lng;
            $history->save();
        }

        if($request->source == 'list') {
            $result = true;
        }else {
            return Redirect::route('mobile.index');
        }

        return Response::json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param $source ['web_app', 'mobile_app']
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $source = 'web_app') {

        $feedback       = 'Alterado com sucesso';
        $operatorId     = Auth::user()->id;
        $statusId       = $request->status_id;
        $waintingTime   = $request->wainting_time;
        $returnVolumes  = $request->return_volumes;
        $returnWeight   = $request->return_weight;

        $agencies = Agency::where('source', config('app.source'))->pluck('id')->toArray();

        /*$shipment = Shipment::where(function($q) use($agencies) {
                            $q->whereIn('agency_id', $agencies);
                            $q->orWhereIn('sender_agency_id', $agencies);
                            $q->orwhereIn('recipient_agency_id', $agencies);
                        })
                        ->whereId($request->id)
                        ->firstOrFail();*/

        $shipment = Shipment::whereId($request->id)->firstOrFail();

        $shipment->status_id   = $shipment->is_collection && $statusId == 5 ? 14 : $statusId;
        $shipment->operator_id = $operatorId;
        //$shipment->map_lat = (float) $request->get('latitude');
        //$shipment->map_lng = (float) $request->get('longitude');
        $shipment->save();


        $signature = null;
        $emptySignature = 'data:image/png;base64,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';
        if($request->get('signature') != $emptySignature) {
            $signature = $request->get('signature');
        }

        $history = new ShipmentHistory();
        $history->shipment_id  = $shipment->id;
        $history->operator_id  = $shipment->operator_id;
        $history->status_id    = $shipment->status_id;
        $history->agency_id    = $shipment->recipient_agency_id;
        $history->incidence_id = $request->get('incidence_id');
        $history->signature    = $signature;
        $history->receiver     = $request->get('receiver');
        $history->obs          = $request->get('obs');
        $history->latitude     = (float) $request->get('latitude');
        $history->longitude    = (float) $request->get('longitude');

        /**
         * WAINTING TIME
         */
        if($waintingTime) {

            $customerComplementarServices = $shipment->customer->complementar_services;

            $waintingTimeExpense = ShippingExpense::filterSource()->whereType('wainting_time')->first();
            $zonesArr = $waintingTimeExpense["zones_arr"];
            $zones = array_flip($zonesArr);
            $zone = $shipment->zone;

            $customerValue = null;
            $key = $waintingTimeExpense->id;

            if(!empty($customerComplementarServices[$key][$zone]) || !empty($customerComplementarServices[$key]["qqz"])) {
                $priceQqz = (float) @$customerComplementarServices[$key]["qqz"];
                $customerValue = (float) @$customerComplementarServices[$key][$zone];

                if(empty($customerValue) && !empty($priceQqz)) {
                    $customerValue = $priceQqz;
                }
            }

            $key = 0;
            if(isset($zones[$zone])) {
                $key = $zones[$zone];
            } elseif(isset($zones['qqz'])) {
                $key = $zones['qqz'];
            }

            $value   = (float) @$waintingTimeExpense["values_arr"][$key];
            $value   = $customerValue ? $customerValue : $value;
            $unity   = @$waintingTimeExpense["unity_arr"][$key];

            if($unity == 'percent') {
                $value = $value / 100; //converte % em numérico
            }

            //STORE EXPENSE
            $expense = ShipmentExpense::firstOrNew([
                'shipment_id' => $shipment->id,
                'expense_id'  => $waintingTimeExpense->id
            ]);

            $expense->qty   = (float) $waintingTime;
            $expense->price = $value;
            $expense->subtotal = $waintingTime * $value;
            $expense->unity = $unity;
            $expense->date = date('Y-m-d');
            $expense->save();

            //UPDATE SHIPMENT TOTAL
            ShipmentExpense::updateShipmentTotal($shipment->id);
        }


        if ($request->hasFile('attachment')) {
            if (!$history->upload($request->file('attachment'), true, 20)) {
                $feedback = 'Gravado com sucesso. Não foi possível carregar a fotografia.';
            }
        } else {
            $history->save();
        }

        //se estava em incidencia, marca todas as incidencias como resolvidas.
        ShipmentHistory::where('shipment_id', $history->shipment_id)
            ->where('status_id', ShippingStatus::INCIDENCE_ID)
            ->where('id', '<>', $history->id)
            ->update(['resolved' => 1]);

        //store user location
        if(!empty($history->latitude) && !empty($history->longitude)) {
            $this->storeUserLocation($history->latitude, $history->longitude);
        }

        try {
            $history->sendEmail(false, false, true);
        } catch (\Exception $e) {}

        //create automatic return
        if(Setting::get('mobile_app_autoreturn') && is_array($shipment->has_return) && in_array('rpack', $shipment->has_return) && !empty($returnWeight) && !empty($returnVolumes)) {

            $returnInput = [
                'weight'  => $returnWeight ? $returnWeight : $shipment->weight,
                'volumes' => $returnVolumes ? $returnVolumes : $shipment->volumes
            ];

            $shipment->createDirectReturn($returnInput, true);
        }


        //prepare response
        $operatorsIds = [$operatorId];

        if(config('app.source') == 'rapex' && $operatorId == 740) {
            $operatorsIds = [740,461];
        }

        if(config('app.source') == 'asfaltolargo') {
            $operatorsIds = [$operatorId, 375];
        }

        $shipments = Shipment::with('status', 'customer')
            /*->where(function($q) use($operatorId) {
                $q->where('operator_id', $operatorId);
                $q->orWhere('operator_id', 375);
            })*/
            ->whereIn('operator_id', $operatorsIds)
            ->whereHas('status', function($q){
                $q->where('is_final', 0);
            })
            ->whereIn('status_id', [3,4,21,20,3,4])
            ->ordered()
            ->orderBy('id', 'desc')
            ->take(100)
            ->get();

        $incidences = $this->getIncidencesList();

        $result = [
            'result'   => true,
            'feedback' => $feedback,
            'html'     => view('mobile.pages.shipments_list', compact('shipments', 'incidences'))->render()
        ];

        return Response::json($result);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function shipmentUpdate(Request $request, $id) {

        $input      = $request->all();
        $feedback   = 'Gravado com sucesso';
        $operatorId = Auth::user()->id;

        $agencies = Agency::where('source', config('app.source'))->pluck('id')->toArray();

        $shipment = Shipment::where(function($q) use($agencies) {
            $q->whereIn('agency_id', $agencies);
            $q->orWhereIn('sender_agency_id', $agencies);
            $q->orwhereIn('recipient_agency_id', $agencies);
        })
        ->whereId($id)
        ->firstOrFail();

        $shipment->fill($input);
        $shipment->save();

        $shipments = Shipment::with('status', 'customer')
            ->where(function($q) use($operatorId) {
                $q->where('operator_id', $operatorId);

                if(config('app.source') == 'rapex' && $operatorId == 740) {
                    $q->orWhere('operator_id', 461);
                }

                if(config('app.source') == 'asfaltolargo') {
                    $q->orWhere(function($q){
                        $q->where('agency_id', 1);
                        $q->orWhere('recipient_agency_id', 1);
                        $q->orWhere('operator_id', 375);
                    });
                }

            })
            ->whereHas('status', function($q){
                $q->where('is_final', 1);
            })
            ->orderBy('date', 'desc')
            ->orderBy('recipient_name')
            ->take(100)
            ->get();

        $incidences = $this->getIncidencesList();

        $result = [
            'result'   => true,
            'feedback' => $feedback,
            'html'     => view('mobile.pages.shipments_list', compact('shipments', 'incidences'))->render()
        ];

        return Response::json($result);
    }

    /**
     * Sort shipments list
     * @param Request $request
     * @return mixed
     */
    public function shipmentsSort(Request $request) {

        $result = Shipment::setNewOrder($request->ids);

        $response = [
            'message' => 'Ordenação gravada com sucesso.',
            'type'    => 'success'
        ];

        return Response::json($response);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function shipmentTransfer(Request $request, $shipmentId) {

        $operatorId = Auth::user()->id;

        $operators = User::filterAgencies()
            ->isOperator()
            ->where('id', '<>', $operatorId)
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        if($request->ajax()) {
            $result = [
                'result' => true,
                'html'   => view('mobile.pages.shipment_transfer', compact('operators', 'shipmentId'))->render()
            ];

            return Response::json($result);
        } else {
            return $this->setContent('mobile.shipment_transfer', compact('operators', 'shipmentId'))->render();
        }

        return Response::json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeShipmentTransfer(Request $request) {

        $feedback   = 'Transferido com sucesso';
        $operatorId = $request->operator_id;

        $shipment = Shipment::filterAgencies()
            ->whereId($request->id)
            ->firstOrFail();

        $shipment->operator_id = $operatorId;
        $shipment->save();

        return Redirect::route('mobile.shipments.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function pendingsList(Request $request) {

        $lat = $request->get('bg_lat');
        $lng = $request->get('bg_lng');
        if(!empty($lat) && !empty($lng)) {
            $this->storeUserLocation($lat, $lng);
        }

        $tab = $request->tab_active;

        $operatorId = Auth::user()->id;

        $allTasks = OperatorTask::filterSource()
            ->where(function ($q) {
                $q->whereRaw('DATE(last_update) <= "' . date('Y-m-d') . '"');
                $q->where('date', '<=', date('Y-m-d'));
            })
            ->ordered()
            ->orderBy('id', 'desc')
            ->get();

        $tasksPending = $allTasks->filter(function($item) use($operatorId) {
            $targets = empty($item->operators) ? [] : $item->operators;
            return $item->readed == 0 && $item->concluded == 0 && (empty($item->operator_id) || $item->operator_id == $operatorId) && in_array($operatorId, $targets);
        });

        $tasksAccepted = $allTasks->filter(function($item) use($operatorId) {
            return $item->readed == 1 && $item->concluded == 0 && $item->operator_id == $operatorId;
        });

        $tasksConcluded = $allTasks->filter(function($item) use($operatorId) {
            return $item->concluded == 1 && $item->last_update >= date('Y-m-d').' 00:00:00' && $item->date <= date('Y-m-d');
        })->sortByDesc('last_update');
       

        $tasksOperators = $allTasks->filter(function($item) use($operatorId){
            return $item->concluded == 0 && !empty($item->operator_id) && $item->operator_id != $operatorId;
        })
        ->sortBy('operator_id')
        ->sortBy('title');

        $tasksOperators = $tasksOperators->groupBy('operator.name');

        $totalPickups = Shipment::where('operator_id', $operatorId)
            ->where('is_collection', 1)
            ->where('date', date('Y-m-d'))
            ->whereHas('status', function($q){
                $q->where('is_final', 0);
            })
            ->count();

        if($request->ajax()) {
            $result = [
                'result' => true,
                'html'   => view('mobile.pages.pendings_list', compact('tasksPending', 'tasksAccepted', 'tasksConcluded', 'tasksOperators', 'tab', 'totalPickups'))->render()
            ];

            return Response::json($result);
        } else {
            return $this->setContent('mobile.pendings', compact('tasksPending', 'tasksAccepted', 'tasksConcluded', 'tasksOperators', 'tab', 'totalPickups'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function pendingUpdate(Request $request, $id) {

        $lat = $request->get('bg_lat');
        $lng = $request->get('bg_lng');
        if(!empty($lat) && !empty($lng)) {
            $this->storeUserLocation($lat, $lng);
        }

        $feedback   = '';
        $operatorId = Auth::user()->id;
        $input = $request->all();
        $tab   = $request->tab_active;

        $task = OperatorTask::with('operator')->find($id);

        if($task->deleted) {
            $task->delete();
        } else {
            if(!empty($task->operator_id) && $task->operator_id != $operatorId) {
                $feedback = 'Este serviço já foi aceite por<br/>' . @$task->operator->name;
            } else {
                $task->fill($input);
                $task->operator_id = $operatorId;

                if(empty($task->readed) && empty($task->concluded)) {
                    $task->operator_id = null;
                }

                $task->save();
            }
        }


        if($task->readed && !$task->concluded && !empty($task->shipments)) {

            $shipmentIds = $task->shipments;
            $statusReaded = '10'; //Estado a recolher

            Shipment::whereIn('id', $shipmentIds)->update(['status_id' => $statusReaded]);

            foreach ($shipmentIds as $shipmentId) {
                $history = new ShipmentHistory();
                $history->shipment_id   = $shipmentId;
                //$history->agency_id     = $shipmentId;
                $history->status_id     = $statusReaded;
                $history->operator_id   = $operatorId;
                $history->latitude      = $lat;
                $history->longitude     = $lng;
                $history->save();
            }
        } elseif($task->readed && $task->concluded && !empty($task->shipments)) {

            $shipmentIds = $task->shipments;
            $statusAfterCollected = Setting::get('mobile_app_status_after_pickup') ? Setting::get('app_mobile_status_after_pickup') : null; //transporte

            if(empty($statusAfterCollected)) {
                $statusAfterCollected = 36; //recolhido
            }

            Shipment::whereIn('id', $shipmentIds)->update(['status_id' => $statusAfterCollected]);

            foreach ($shipmentIds as $shipmentId) {

                $history = new ShipmentHistory();
                $history->shipment_id   = $shipmentId;
                $history->status_id     = 36; //recolhido
                $history->operator_id   = $operatorId;
                $history->latitude      = $lat;
                $history->longitude     = $lng;
                $history->save();

                if($statusAfterCollected != 36) {
                    $history = new ShipmentHistory();
                    $history->shipment_id   = $shipmentId;
                    $history->status_id     = $statusAfterCollected;
                    $history->operator_id   = $operatorId;
                    $history->latitude      = $lat;
                    $history->longitude     = $lng;
                    $history->save();
                }
            }
        }

        $allTasks = OperatorTask::filterSource()
            ->where(function ($q) {
                $q->whereRaw('DATE(last_update) <= "' . date('Y-m-d') . '"');
                $q->where('date', '<=', date('Y-m-d'));
            })
            ->ordered()
            ->orderBy('id', 'desc')
            ->get();

        $tasksPending = $allTasks->filter(function($item) use($operatorId) {
            $targets = empty($item->operators) ? [] : $item->operators;
            return $item->readed == 0 && $item->concluded == 0 && (empty($item->operator_id) || $item->operator_id == $operatorId) && in_array($operatorId, $targets);
        });

        $tasksAccepted = $allTasks->filter(function($item) use($operatorId) {
            return $item->readed == 1 && $item->concluded == 0 && $item->operator_id == $operatorId;
        });

        $tasksConcluded = $allTasks->filter(function($item) use($operatorId) {
            return $item->concluded == 1 && $item->last_update >= date('Y-m-d').' 00:00:00';
        })->sortByDesc('last_update');
       

        $tasksOperators = $allTasks->filter(function($item) use($operatorId){
            return $item->concluded == 0 && $item->operator_id != $operatorId;
        });
        $tasksOperators = $tasksOperators->groupBy('operator.name');

        $totalPickups = Shipment::where('operator_id', $operatorId)
            ->where('is_collection', 1)
            ->where('date', date('Y-m-d'))
            ->whereHas('status', function($q){
                $q->where('is_final', 0);
            })
            ->count();

        $result = [
            'result'   => true,
            'feedback' => $feedback,
            'html'     => view('mobile.pages.pendings_list', compact('tasksPending', 'tasksAccepted', 'tasksConcluded', 'tasksOperators', 'feedback', 'tab', 'totalPickups'))->render()
        ];

        return Response::json($result);
    }

    /**
     * Store task
     * @param Request $request
     * @return mixed
     */
    public function pendingsStore(Request $request) {

        $lat = $request->get('bg_lat');
        $lng = $request->get('bg_lng');
        if(!empty($lat) && !empty($lng)) {
            $this->storeUserLocation($lat, $lng);
        }

        $input = $request->all();

        $operatorId = Auth::user()->id;

        $operators = User::filterAgencies()
            ->isOperator()
            ->orderBy('name', 'asc')
            ->pluck('id')
            ->toArray();

        $input['operators'] = $operators;

        $task = new OperatorTask();
        $task->fill($input);
        $task->last_update  = date('Y-m-d H:i:s');
        $task->date         = $request->date_y.'-'.$request->date_m.'-'.$request->date_d;
        $task->source       = config('app.source');
        $task->created_by   = $operatorId;
        $task->save();

        $task->notifyAllOperators();

        $allTasks = OperatorTask::filterSource()
            ->where(function ($q) {
                $q->where(function($q){
                    $q->whereRaw('DATE(last_update) <= "' . date('Y-m-d') . '"');
                    $q->where('date', '<=', date('Y-m-d'));
                })->orWhere('concluded', 0);
            })
            ->ordered()
            ->orderBy('id', 'desc')
            ->get();

        $tasksPending = $allTasks->filter(function($item) use($operatorId) {
            $targets = empty($item->operators) ? [] : $item->operators;
            return $item->readed == 0 && $item->concluded == 0 && (empty($item->operator_id) || $item->operator_id == $operatorId) && in_array($operatorId, $targets);
        });

        $tasksAccepted = $allTasks->filter(function($item) use($operatorId) {
            return $item->readed == 1 && $item->concluded == 0 && $item->operator_id == $operatorId;
        });

        $tasksConcluded = $allTasks->filter(function($item) use($operatorId) {
            return $item->concluded == 1 && $item->last_update >= date('Y-m-d').' 00:00:00';
        })->sortByDesc('last_update');
       

        $tasksOperators = $allTasks->filter(function($item) use($operatorId){
            return $item->concluded == 0 && $item->operator_id != $operatorId;
        });
        $tasksOperators = $tasksOperators->groupBy('operator.name');

        $tab = 'tab-pending';

        $response = [
            'result'   => true,
            'feedback' => 'Tarefa gravada com sucesso.',
            'html'     => view('mobile.pages.pendings_list', compact('tasksPending', 'tasksAccepted', 'tasksConcluded', 'tasksOperators', 'feedback', 'tab'))->render()
        ];

        return Response::json($response);
    }

    /**
     * Sort pending list
     * @param Request $request
     * @return mixed
     */
    public function pendingSort(Request $request) {

        $result = OperatorTask::setNewOrder($request->ids);

        $response = [
            'message' => 'Ordenação gravada com sucesso.',
            'type'    => 'success'
        ];

        return Response::json($response);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function scanner() {
        return view('mobile.scanner');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function scannerQr() {
        return view('mobile.scanner_qr');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function scannerRefs() {
        $operatorId = Auth::user()->id;

        $shipments = Shipment::with('status', 'customer')
            ->where('operator_id', $operatorId)
            ->whereHas('status', function($q){$q->where('is_final', 0);})
            //->whereIn('status_id', [38,37,31,20,21,22,20,3,4])
            ->ordered()
            ->orderBy('date', 'desc')
            ->orderBy('recipient_name')
            ->orderBy('recipient_zip_code', 'asc')
            ->get();

        return view('mobile.scanner_refs', compact('shipments'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function storeScannerRefs(Request $request) {
        $operatorId = Auth::user()->id;

        $references = $request->has('reference');
        $references = array_filter($references);

        if(empty($references)) {
            return Redirect::back()->with('error', 'Não leu nenhum código.');
        } else {
            $references = implode(',', $references);
        }

        $shipment = Shipment::where('operator_id', $operatorId)
                            ->whereId($request->id)
                            ->first();

        if(!$shipment) {
            return Redirect::back()->with('error', 'Não selecionou nenhum envio da lista.');
        } else {
            $shipment->obs = $shipment->obs.' #COD: ' . $references;
            $shipment->save();
        }

        return Redirect::back()->with('success', 'Dados gravados com sucesso.');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function findShipment(Request $request, $trackingCode) {

        $field = 'tracking_code';

        $trackingCode = trim($trackingCode);

        if(strlen($trackingCode) == 18) {
            $trackingCode = substr($trackingCode, 0, -6);
        } elseif(strlen($trackingCode) == 15) {
            $trackingCode = substr($trackingCode, 0, -3);
        }

        if(strlen($trackingCode) == 7) {
            $field = 'id';
        }

        $shipment = Shipment::where($field, $trackingCode)->first();

        $incidences = $this->getIncidencesList();

        if($shipment) {
            if($request->ajax()) {
                $result = [
                    'result'   => true,
                    'html'     => view('mobile.pages.scanner_result', compact('shipment', 'incidences'))->render()
                ];
            } else {
                return $this->setContent('mobile.detail', compact('shipment', 'incidences'));
            }
        } else {

            if($request->ajax()) {
                $result = [
                    'result'   => false,
                    'target'   => '.window',
                    'html'     => view('mobile.pages.scanner_noresult', compact('trackingCode', 'incidences'))->render()
                ];
            } else {
                return $this->setContent('mobile.detail', compact('trackingCode', 'incidences'));
            }

        }

        return Response::json($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function settings(Request $request) {

        if($request->ajax()) {
            $result = [
                'html' => view('mobile.pages.settings_form')->render()
            ];

            return Response::json($result);
        }

        return $this->setContent('mobile.settings')->render();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function settingsUpdate(Request $request) {

        $input = $request->all();
        $input['location_enabled'] = $request->get('location_enabled', false);

        $user = Auth::user();

        if(!empty($request->get('password')) && !empty($request->get('current_password'))) {

            $rules = [
                'current_password' => 'required',
                'password' => 'required|confirmed'
            ];

            $validator = Validator::make($input, $rules);
            if ($validator->passes()) {

                if (!Hash::check($input['current_password'], $user->password)) {
                    $result = false;
                    $feedback = 'A palavra-passe actual está incorreta.';
                } else {
                    $user->password = $input['password'];
                    $user->email = $input['email'];
                    $user->save();

                    $result = true;
                    $feedback = 'Palavra-passe alterada com sucesso.';
                }

            } else {
                $result = false;
                $feedback = 'A nova palavra-passe e a confirmação da mesma não são iguais e é obrigatório o seu preenchimento.';
            }

        } else {
            unset($input['password']);

            $user->fill($input);
            $user->save();

            if ($input['delete_photo'] && !empty($user->filepath)) {
                Croppa::delete($user->filepath);
                $user->filepath = null;
                $user->filename = null;
                $user->location_marker = null;
            }

            if($request->hasFile('attachment')) {

                if ($user->exists && !empty($user->filepath) && File::exists(public_path(). '/'.$user->filepath)) {
                    Croppa::delete($user->filepath);
                }

                if (!$user->upload($request->file('attachment'), true, 20)) {
                    return Redirect::back()->withInput()->with('error', 'Não foi possível alterar a imagem do perfil.');
                } else {
                    $user->createMapMarker();
                }
            }

            $result   = true;
            $feedback = 'Definições gravadas com sucesso.';
        }

        $result = [
            'result' => $result,
            'html'   => view('mobile.pages.settings_form', compact('feedback', 'result'))->render()
        ];

        return Response::json($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fuel(Request $request) {

        $vehicles = Vehicle::filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $providers = Provider::filterSource()
                        ->categoryGasStation()
                        ->pluck('name', 'id')
                        ->toArray();
                        
        if($request->ajax()) {
            $result = [
                'html' => view('mobile.pages.fuel_form', compact('vehicles', 'providers'))->render()
            ];

            return Response::json($result);
        }

        return $this->setContent('mobile.fuel', compact('vehicles', 'providers'))->render();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fuelStore(Request $request) {

        $input = $request->all();
        $input['liters'] = forceDecimal($input['liters']);
        $input['total'] = forceDecimal($input['total']);
        $input['price_per_liter'] = forceDecimal($input['price_per_liter']);

        $fuelLog = new FuelLog();

        if ($fuelLog->validate($input)) {
            $fuelLog->fill($input);
            $fuelLog->operator_id = Auth::user()->id;
            $fuelLog->created_by  = Auth::user()->id;  
            $fuelLog->save();

            //delete file
            if ($request->delete_file && !empty($fuelLog->filepath)) {
                File::delete(public_path().'/'.$fuelLog->filepath);
                $fuelLog->filepath = null;
                $fuelLog->filename = null;
            }

            //upload file
            if($request->hasFile('file')) {

                if ($fuelLog->exists && !empty($fuelLog->filepath)) {
                    File::delete(storage_path().'/'.$fuelLog->filepath);
                }

                if (!$fuelLog->upload($request->file('file'), true, 20)) {
                    $result   = false;
                    $feedback = 'Não foi possível carregar o ficheiro.';
                }

            } else {
                $fuelLog->save();
            }


            FuelLog::updateVehicleCounters($fuelLog->vehicle_id);

            $result   = true;
            $feedback = 'Registo gravado com sucesso.';
        } else {
            $result   = false;
            $feedback = $fuelLog->errors()->first();
        }

        $vehicles = Vehicle::filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $providers = Provider::filterSource()
            ->categoryGasStation()
            ->pluck('name', 'id')
            ->toArray();

        $result = [
            'result' => $result,
            'html'   => view('mobile.pages.fuel_form', compact('feedback', 'result', 'vehicles', 'providers'))->render()
        ];

        return Response::json($result);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function drive(Request $request) {

        $vehicles = Vehicle::filterSource()
            ->pluck('name', 'id')
            ->toArray();

        if($request->ajax()) {
            $result = [
                'html' => view('mobile.pages.drive_form', compact('vehicles'))->render()
            ];

            return Response::json($result);
        }

        return view('mobile.drive', compact('vehicles'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function driveStore(Request $request) {

        $input = $request->all();
        $input['liters'] = forceDecimal($input['liters']);
        $input['total'] = forceDecimal($input['total']);
        $input['price_per_liter'] = forceDecimal($input['price_per_liter']);

        $fuelLog = new FuelLog();

        if ($fuelLog->validate($input)) {
            $fuelLog->fill($input);
            $fuelLog->save();

            //delete file
            if ($request->delete_file && !empty($fuelLog->filepath)) {
                File::delete(public_path().'/'.$fuelLog->filepath);
                $fuelLog->filepath = null;
                $fuelLog->filename = null;
            }

            //upload file
            if($request->hasFile('file')) {

                if ($fuelLog->exists && !empty($fuelLog->filepath)) {
                    File::delete(storage_path().'/'.$fuelLog->filepath);
                }

                if (!$fuelLog->upload($request->file('file'), true, 20)) {
                    $result   = false;
                    $feedback = 'Não foi possível carregar o ficheiro.';
                }

            } else {
                $fuelLog->save();
            }

            FuelLog::updateVehicleCounters($fuelLog->vehicle_id);

            $result   = true;
            $feedback = 'Registo gravado com sucesso.';
        } else {
            $result   = false;
            $feedback = $fuelLog->errors()->first();
        }

        $vehicles = Vehicle::filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $providers = Provider::filterSource()
            ->categoryGasStation()
            ->pluck('name', 'id')
            ->toArray();

        $result = [
            'result' => $result,
            'html'   => view('mobile.pages.fuel_form', compact('feedback', 'result', 'vehicles', 'providers'))->render()
        ];

        return Response::json($result);
    }

    public function getIncidencesList() {
        $incidences = IncidenceType::where('operator_visible', 1)
            ->filterSource()
            ->isActive()
            ->ordered()
            ->get()
            ->pluck('name', 'id');

        return $incidences;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function customersMap(Request $request) {

        $operatorId = Auth::user()->id;

        $routes = Route::filterSource()->get();

        $currentRoute = @$routes->filter(function($item) use($operatorId) {
            return $item->scheduleHas($operatorId);
        })->first()->id;

        if($request->route) {
            $currentRoute = $request->route;
        }

        $routes = $routes->pluck('name', 'id')->toArray();

        if(!$currentRoute) {
            $currentRoute = 99999999;
        }

        $customers = Customer::filterAgencies()
                        ->where('route_id', $currentRoute)
                        ->get();


        if($request->ajax()) {
            $result = [
                'html' => view('mobile.pages.customers_map', compact('routes', 'customers', 'currentRoute'))->render()
            ];

            return Response::json($result);
        }

        return $this->setContent('mobile.customers_map', compact('routes', 'customers', 'currentRoute'))->render();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function operatorsMap(Request $request) {

        $operators = User::filterAgencies()
            ->isOperator()
            ->where('agencies', 'like', '%"1"%')
            ->orderBy('location_enabled', 'desc')
            ->orderBy('location_last_update', 'desc')
            ->get();


        if($request->ajax()) {
            $result = [
                'html' => view('mobile.pages.operators_map', compact('routes', 'operators'))->render()
            ];

            return Response::json($result);
        }

        return $this->setContent('mobile.operators_map', compact('routes', 'operators'))->render();
    }


    public function disableLocation(Request $request) {

        $operatorId = Auth::user()->id;

        $denied = $request->get('denied', false);

        $operator = User::find($operatorId);
        if($denied) {
            $operator->location_enabled = true;
        } else {
            $operator->location_enabled = false;
        }
        $operator->location_denied  = $denied;
        $operator->save();

        if($request->ajax()) {
            return Response::json(true);
        }

        return Redirect::back();
    }

    //enable user location
    public function enableLocation(Request $request) {

        $this->storeUserLocation($request->lat, $request->lng);

        if($request->ajax()) {
            return Response::json(true);
        }

        return Redirect::back();
    }

    /**
     * Store user location
     * @param $lat
     * @param $lng
     */
    public function storeUserLocation($lat, $lng) {

        $operator = Auth::user();

        $now = Date::now();
        $lastUpdate = new Date($operator->location_last_update);

        if(empty($operator->location_last_update) || ($lastUpdate->diffInMinutes($now) >= 1 && $operator->location_lat != $lat && $operator->location_lng != $lng)) {
            $operator->location_last_update = date('Y-m-d H:i:s');
            $operator->location_enabled = true;
            $operator->location_denied  = false;
            $operator->location_lat     = $lat;
            $operator->location_lng     = $lng;
            $operator->save();

            $location = new UserLocation();
            $location->operator_id = $operator->id;
            $location->latitude    = $lat;
            $location->longitude   = $lng;
            $location->save();
        }
    }

    /**
     * Get google maps url
     * @param Request $request
     * @param $shipmentId
     * @return mixed
     */
    public static function getGoogleMapsUrl($shipment, $recipient = true) {

        if($recipient) {
            $address = trim($shipment->recipient_address) . ',' .
                trim($shipment->recipient_zip_code) . ' ' .
                trim($shipment->recipient_city) . ',' .
                trans('country.' . $shipment->recipient_country);
        } else {
            $address = trim($shipment->sender_address) . ',' .
                trim($shipment->sender_zip_code) . ' ' .
                trim($shipment->sender_city) . ',' .
                trans('country.' . $shipment->sender_country);
        }

        $address = str_replace(" ", "+", strtolower($address));
        $address = str_replace("º", '', $address);
        $address = str_replace("ª", '', $address);

        $url = 'https://www.google.com/maps/search/' . $address;

        return $url;
    }
}
