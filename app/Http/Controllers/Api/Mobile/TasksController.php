<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Models\IncidenceType;
use App\Models\OperatorTask;
use App\Models\Route;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use App\Models\ShippingStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Auth, Validator, Setting, Mail, Date, File;

class TasksController extends \App\Http\Controllers\Api\Mobile\BaseController
{
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Set authorized fields to store
     * @var array
     */
    protected $authorizedFields = [
        'title',
        'description',
        'date',
    ];

    /**
     * Bindings
     *
     * @var array
     */
    protected $bindings = [
        'id',
        'source',
        'name',
        'description',
        'details',
        'full_address',
        'address',
        'zip_code',
        'city',
        'phone',
        'volumes',
        'weight',
        'date',
        'readed',
        'concluded',
        'deleted',
        'last_update',
        'operator_id',
        'customer_id',
        'created_by',
        'operators'
    ];

    /**
     * Lists all shipping status
     *
     * @param Request $request
     * @return mixed
     */
    public function lists(Request $request, $user = null, $status = null) {

        if(empty($user)) {
            $user = $this->getUser($request->get('user'));

            if(!$user) {
                return $this->responseError('login', '-002') ;
            }
        }

        $operatorId = $user->id;
        if(is_null($status)) {
            $status  = $request->get('status', 'pending');
        }

        //store User Location
        $lat = $request->get('latitude');
        $lng = $request->get('longitude');
        if(!empty($lat) && !empty($lng)) {
            $this->storeUserLocation($user, $lat, $lng);
        }

        $date = date('Y-m-d');
        if($request->has('date')) {
            $date = $request->get('date');
        }

        //filter tasks
        $allTasks = OperatorTask::with(['customer' => function($q){
                $q->select(['id', 'code', 'code_abbrv', 'name']);
            }])
            ->with(['operator' => function($q){
                $q->select(['id', 'code', 'code_abbrv', 'name']);
            }])
            ->where('source', config('app.source'))
            ->where(function ($q) use($date) {
                $q->whereRaw('DATE(last_update) <= "' . $date . '"');
                $q->where('date', '<=', $date);
            })
            ->ordered()
            ->orderBy('id', 'desc')
            ->get($this->bindings);

        /**
         * @author Daniel Almeida
         * 
         * This is a temporary fix!
         * Needs to be fixed on mobile app
         */
        foreach ($allTasks as &$task) {
            $task->address = $task->full_address;
        }
        /**-- */

        $statusToRead = [$status];
        if($status == 'all') {
            $statusToRead = [
                OperatorTask::STATUS_PENDING,
                OperatorTask::STATUS_ACCEPTED,
                OperatorTask::STATUS_CONCLUDED,
            ];
        }

        $responseData = [];
        foreach ($statusToRead as $statusName) {

            $tasks = [];
            if($statusName == OperatorTask::STATUS_PENDING) {
                $tasks = $allTasks->filter(function($item) use($operatorId) {
                    $targets = empty($item->operators) ? [] : $item->operators;
                    $pending = !$item->readed && !$item->concluded;
                    
                    if (!$pending) {
                        return false;
                    }
                    
                    return ($item->operator_id == $operatorId) || (empty($item->operator_id) && in_array($operatorId, $targets));
                });
            }
            elseif($statusName == OperatorTask::STATUS_ACCEPTED) {
                $tasks = $allTasks->filter(function ($item) use ($operatorId) {
                    return $item->readed == 1 && $item->concluded == 0 && $item->operator_id == $operatorId;
                });
            }
            elseif($statusName == OperatorTask::STATUS_CONCLUDED) {
                $tasks = $allTasks->filter(function ($item) use ($operatorId, $date) {
                    return $item->concluded == 1 && $item->last_update >= $date . ' 00:00:00' && $item->date <= $date && $item->operator_id == $operatorId; //força a só mostrar concluídos do motorista
                })->sortByDesc('last_update');
            }
            elseif($statusName == OperatorTask::STATUS_OPERATOR) {

                $tasks = $allTasks->filter(function ($item) use ($operatorId) {
                    return $item->concluded == 0 && !empty($item->operator_id) && $item->operator_id != $operatorId;
                })
                    ->sortBy('operator_id')
                    ->sortBy('title');

                $tasks = $tasks->groupBy('operator.name');
            }

            $responseData[$statusName] = $tasks;
        }

        if($status != 'all') {
            $responseData = @$responseData[$status];
        }

        return response($responseData, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Store new task
     *
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request) {

        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        //store User Location
        $lat = $request->get('latitude');
        $lng = $request->get('longitude');
        if(!empty($lat) && !empty($lng)) {
            $this->storeUserLocation($user, $lat, $lng);
        }

        $input = $request->only($this->authorizedFields);
        $operatorId = $user->id;

        $title = $request->get('title');
        if(empty($title)) {
            return $this->responseError('tasks', '-001');
        }

        try {

            $myAgencies = $user->agencies;
            $operators = User::where(function($q) use($myAgencies) {
                    $q->whereNotNull('agencies');
                    $q->where(function($q) use ($myAgencies){
                        foreach($myAgencies as $agency) {
                            $q->orWhere('agencies', 'like', '%"'.$agency.'"%');
                        }
                    });
                })
                ->isOperator()
                ->orderBy('name', 'asc')
                ->pluck('id')
                ->toArray();

            $input['operators'] = $operators;

            $task = new OperatorTask();
            $task->fill($input);
            $task->name         = $title;
            $task->last_update  = date('Y-m-d H:i:s');
            $task->date         = $request->get('date') ? $request->date : date('Y-m-d');
            $task->source       = config('app.source');
            $task->created_by   = $operatorId;
            $task->save();

            $task->notifyAllOperators();

            $result = [
                'code'      => '',
                'feedback'  => 'Task added successfuly',
                'lists'     => $this->lists($request, $user, 'all')
            ];

        } catch (\Exception $e) {
            return $this->responseError('tasks', '-999', $e->getMessage(). ' line '. $e->getLine(). ' file ' . $e->getFile());
        }

        return response($result, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Store shipment status
     *
     * @param Request $request
     * @return mixed
     */
    public function update(Request $request, $taskId) {

        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        //store User Location
        $lat = $request->get('latitude');
        $lng = $request->get('longitude');
        if(!empty($lat) && !empty($lng)) {
            $this->storeUserLocation($user, $lat, $lng);
        }

        $operatorId = $user->id;
        $status     = $request->get('status');


        //validate incidence
        $incidenceId = null;
        if($status == OperatorTask::STATUS_INCIDENCE) {

            if(!$request->get('incidence_id')) {
                return $this->responseError('status', '-004');
            }

            $incidence = IncidenceType::whereId($request->get('incidence_id'))->first();
            if(!$incidence) {
                return $this->responseError('status', '-003') ;
            }

            $incidenceId = $incidence->id;
        }

        if(!empty($status)) {
            if($status == OperatorTask::STATUS_ACCEPTED) {
                $input = [
                    'readed'      => 1,
                    'concluded'   => 0,
                    'operator_id' => $operatorId
                ];
            } elseif($status == OperatorTask::STATUS_CONCLUDED || $status == OperatorTask::STATUS_INCIDENCE) {
                $input = [
                    'readed'      => 1,
                    'concluded'   => 1,
                    'operator_id' => $operatorId
                ];
            } elseif($status == OperatorTask::STATUS_PENDING) {
                $input = [
                    'readed'      => 0,
                    'concluded'   => 0,
                    'operator_id' => null
                ];
            }
        } else {
            return $this->responseError('tasks', '-002');
        }


        $task = OperatorTask::with('operator')
            ->where('source', config('app.source'))
            ->find($taskId);

        if($task) {
            if ($task->deleted) {
                $task->delete();
            } else {
                if (!empty($task->operator_id) && $task->operator_id != $operatorId) {
                    return $this->responseError('tasks', '-003', 'Este serviço já foi aceite por ' . @$task->operator->name);
                } else {
                    $task->fill($input);
                    $task->save();
                }
            }
        }

        //update shipment status
        if($task->readed && !$task->concluded && !empty($task->shipments)) {

            //dos envios envolvidos na task, atualiza só os que não estejam como entregues/devolvidos/concluidos
            $shipmentIds = Shipment::whereIn('id', $task->shipments)
                ->whereNotIn('status_id', [ShippingStatus::DELIVERED_ID, ShippingStatus::DEVOLVED_ID, ShippingStatus::PICKUP_CONCLUDED_ID, ShippingStatus::IN_TRANSPORTATION_ID, ShippingStatus::IN_DISTRIBUTION_ID])
                ->pluck('id')
                ->toArray();

            $statusReaded = '10'; //Estado a recolher

            Shipment::whereIn('id', $shipmentIds)
                ->where('status_id', '<>', ShippingStatus::DELIVERED_ID) //nao atualiza se esta entregue
                ->update(['status_id' => $statusReaded]);

            foreach ($shipmentIds as $shipmentId) {
                $history = new ShipmentHistory();
                $history->shipment_id   = $shipmentId;
                $history->status_id     = $statusReaded;
                $history->operator_id   = $operatorId;
                $history->latitude      = $lat;
                $history->longitude     = $lng;
                $history->save();
            }
        }

        elseif($task->readed && $task->concluded && !empty($task->shipments)) {



            //dos envios envolvidos na task, atualiza só os que não estejam como entregues/devolvidos/concluidos
            $shipmentIds = Shipment::whereIn('id', $task->shipments)
                ->whereNotIn('status_id', [ShippingStatus::DELIVERED_ID, ShippingStatus::DEVOLVED_ID, ShippingStatus::PICKUP_CONCLUDED_ID, ShippingStatus::IN_TRANSPORTATION_ID, ShippingStatus::IN_DISTRIBUTION_ID])
                ->pluck('id')
                ->toArray();

            if($task->is_pickup) {
                $statusPickuped    = ShippingStatus::PICKUP_CONCLUDED_ID; //recolha concluida
                $statusAfterPickup = null; //sem estado apos a recolha    
            } else {
                $statusPickuped    = Setting::get('mobile_app_status_pickuped') ? Setting::get('mobile_app_status_pickuped') : ShippingStatus::SHIPMENT_PICKUPED; //recolhido
                $statusAfterPickup = Setting::get('mobile_app_status_after_pickup') ? Setting::get('app_mobile_status_after_pickup') : null; //transporte    
            }
            
            if($status == OperatorTask::STATUS_INCIDENCE) {
                if($task->is_pickup) {
                    $statusPickuped = ShippingStatus::PICKUP_FAILED_ID;
                } else {
                    $statusPickuped = ShippingStatus::INCIDENCE_ID;
                }
            }

            $changeOperator = false;
            if(in_array($statusAfterPickup, [ShippingStatus::IN_TRANSPORTATION_ID, ShippingStatus::IN_DISTRIBUTION_ID])) {
                //quando passa da lista de recolhas para a lista de entregas, deve mudar o operador
                $changeOperator = true;
            }

            //get file if exists
            $filepath = $filename = '';
            if(!empty($request->get('uploaded_file'))) {

                $fileContent = $request->get('uploaded_file');
                $folder = ShipmentHistory::DIRECTORY;

                if(!File::exists(public_path($folder))) {
                    File::makeDirectory(public_path($folder));
                }

                $filename = strtolower(str_random(8).'.png');
                $filepath = $folder.'/'.$filename;
                File::put(public_path($filepath), base64_decode($fileContent));
            }


            foreach ($shipmentIds as $shipmentId) {

                $history = new ShipmentHistory();
                $history->shipment_id   = $shipmentId;
                $history->status_id     = $statusPickuped;
                $history->operator_id   = $operatorId;
                $history->latitude      = $lat;
                $history->longitude     = $lng;
                $history->obs           = $request->get('obs');
                $history->receiver      = $request->get('receiver');
                $history->signature     = $request->get('signature');
                $history->vat           = $request->get('vat');
                $history->incidence_id  = $incidenceId;

                if($filepath) {
                    $history->filehost = config('app.url');
                    $history->filepath = @$filepath;
                    $history->filename = @$filename;
                }

                $history->save();


                if($statusAfterPickup) {
                    $history = new ShipmentHistory();
                    $history->shipment_id   = $shipmentId;
                    $history->status_id     = $statusAfterPickup;
                    $history->operator_id   = $operatorId;
                    $history->latitude      = $lat;
                    $history->longitude     = $lng;
                    $history->save();
                }
            }

            //ontem rota de recolha e a viatura associada
            $firstShipment  = Shipment::whereIn('id', $shipmentIds)->first();
            $route          = Route::getRouteFromZipCode(@$firstShipment->sender_zip_code, @$firstShipment->service_id, null, 'pickup');
            $routeVehicleId = @$route->vehicle ? $route->vehicle : $user->vehicle;

            if(@$history->status_id) {
                $updateFields = [];
                $updateFields['pickup_operator_id'] = $history->operator_id;
                $updateFields['status_id']          = $history->status_id;
                $updateFields['status_date']        = $history->created_at;

                if($routeVehicleId) {
                    $updateFields['vehicle'] = $routeVehicleId;
                }

                /*if(@$route->id) {
                    $updateFields['pickup_route_id'] = $route->id;
                }*/

                if($changeOperator) {
                    $updateFields['operator_id'] = $operatorId;
                }

                Shipment::whereIn('id', $shipmentIds)
                    ->where('status_id', '<>', ShippingStatus::DELIVERED_ID)//nao atualiza se esta entregue
                    ->update($updateFields);
            }
        }

        $result = [
            'code'      => '',
            'feedback'  => 'Status changed sucessfully',
            'lists'     => $this->lists($request, $user, 'all')
        ];

        return response($result, 200)->header('Content-Type', 'application/json');
    }
}