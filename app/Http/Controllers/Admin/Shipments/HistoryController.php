<?php

namespace App\Http\Controllers\Admin\Shipments;

use App\Models\BroadcastPusher;
use App\Models\Customer;
use App\Models\Trip\Trip;
use App\Models\Trip\TripShipment;
use App\Models\OperatorTask;
use App\Models\Provider;
use App\Models\Route;
use App\Models\ShipmentExpense;
use App\Models\ShippingExpense;
use Html, Response, Cache, Setting, Date, Auth, DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\ShipmentHistory;
use App\Models\ShippingStatus;
use App\Models\IncidenceType;
use App\Models\Webservice;
use App\Models\Shipment;
use App\Models\Service;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\ShipmentHistoryAttachament;


use function foo\func;

class HistoryController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'shipments';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',shipments']);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $shipmentId)
    {
        $shipment = Shipment::findOrFail($shipmentId);

        $status   = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingStatus::CACHE_TAG)
            ->filterSources()
            ->isVisible();

        if ($shipment->is_collection) {
            $status = $status->isCollection();
        } else {
            $status = $status->isShipment();
        }

        $status = $status->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $operators = User::listOperators(User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->isActive()
            ->isOperator()
            ->orderBy('name', 'asc')
            ->get(['source', 'id', 'name', 'vehicle', 'provider_id',]), true);

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
        $hours = listHours(5);

        $source      = '';
        $action      = 'Alterar Estado do Envio';
        $formOptions = array('route' => array('admin.shipments.history.store', $shipment->id), 'method' => 'POST', 'files' => true, 'class' => 'form-update-history');

        if ($request->source == 'dashboard') {
            $source      = 'dashboard';
            $action      = 'Alterar Estados dos Envios Pendentes';
            $customerId  = $shipmentId;
            $formOptions = array('route' => array('admin.shipments.status.assign-pending', $customerId), 'method' => 'POST', 'files' => true, 'class' => 'form-update-history');
        }

        $data = compact(
            'shipment',
            'action',
            'formOptions',
            'operators',
            'status',
            'vehicles',
            'trailers',
            'hours',
            'incidences',
            'source'
        );

        return view('admin.shipments.history.edit', $data)->render();
    }

    /**
     * Assign status to all selected resources from storage.
     * GET /admin/shipments/selected/assign-status
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = null)
    {
        if (is_null($id)) {
            $ids = explode(',', $request->ids);
        } else {
            $ids = [$id];
        }

        $statusId              = $request->get('status_id');
        $inputOperatorId       = $request->get('operator_id');
        $inputPickupOperatorId = $request->get('pickup_operator_id');
        $vehicle               = $request->get('vehicle');
        $trailer               = $request->get('trailer');
        $devolution            = $request->get('devolution', false);
        $forceCustomerEmail    = $request->get('customer_email', false);
        $forceRecipientEmail   = $request->get('recipient_email', false);
        $forceCustomerSms      = $request->get('customer_sms', false);
        $forceRecipientSms     = $request->get('recipient_sms', false);
        $notifyOperator        = $request->get('notify_operator', false);
        $createManifest        = $request->get('create_manifest', false);
        $hour                  = $request->hour == '00:00' || empty($request->hour) ? date('H:i') : $request->hour;
        $statusDate            = $request->date . ' ' . $hour . ':59';

        //check if some id has linked tracking ids
        /*$linkedIds = Shipment::whereNotNull('linked_tracking_code')
            ->whereIn('id', $ids)
            ->pluck('linked_tracking_code')->toArray();

        if (!empty($linkedIds)) {
            $linkedIds = Shipment::whereIn('tracking_code', $linkedIds)->pluck('id')->toArray();
            $ids       = array_unique(array_merge($ids, $linkedIds));
        }*/

        $shipments = Shipment::with(['customer' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Customer::CACHE_TAG);
            }])
            ->whereIn('id', $ids)
            ->get();

        //verifica se existem envios que sejam agrupados
        if (!Setting::get('shipment_list_detail_master')) {
            $existsGrouped = $shipments->filter(function ($item) {
                return $item->children_type == 'M';
            });

            // dd($existsGrouped->toArray());
            foreach ($existsGrouped as $shipment) { //obtem todos os envios agrupados e junta-os ao array de resultados

                if ($shipment->children_type == 'M') {
                    $masterTrk = $shipment->children_tracking_code;
                } else {
                    $masterTrk = $shipment->parent_tracking_code;
                }

                if (!empty($masterTrk)) { // if is null updates every shipment (EVERY)
                    $shipmentsMaster = Shipment::where(function ($q) use ($masterTrk) {
                            //$q->where('children_tracking_code', $masterTrk);
                            $q->orWhere('parent_tracking_code', $masterTrk);
                        })
                        ->where('id', '<>', $shipment->id)
                        ->get();

                    if (!$shipmentsMaster->isEmpty()) {
                        $shipments = $shipments->merge($shipmentsMaster);
                        $ids = $shipments->pluck('ids')->toArray();
                    }
                }
            }
        }

        $operatorAux       = User::find($inputOperatorId);
        $pickupOperatorAux = User::find($inputPickupOperatorId);

        $hasIncidence = false;
        $shipmentsIds = [];
        foreach ($shipments as $shipment) {

            // Distinção entre operador normal ou operador de recolha
            $key        = 'operator_id';
            $operator   = $operatorAux;
            $operatorId = $inputOperatorId;
            if ($shipment->is_pickup) {
                $key        = 'pickup_operator_id';
                $operator   = $pickupOperatorAux;
                $operatorId = $inputPickupOperatorId;
            }
            //--

            $oldOperatorId = $shipment->{$key};

            if (!empty($operator->provider_id) && $operator->provider_id != $shipment->provider_id) {
                $shipment->provider_id = $operator->provider_id; //atualiza o fornecedor
            }

            //try update price if shipment isnt blocked and if price is empty
            if (!$shipment->price_fixed && !$shipment->is_blocked && !$shipment->invoice_id && (empty($shipment->total_price) || $shipment->total_price == 0.00)) {
                $shipment->updatePrices();
            }

            if (!empty($operatorId) && $operatorId != '-1') {
                $shipment->{$key} = $operatorId;
            }

            if (!empty($vehicle) && $vehicle != '-1') {
                $shipment->vehicle = $vehicle;
            } elseif (!$shipment->vehicle) {
                $operator = User::find($shipment->{$key});
                $shipment->vehicle = @$operator->vehicle; //atribui a viatura do motorista
            }

            if ($trailer != '-1') {
                $shipment->trailer = $trailer;
            }

            if (config('app.source') == 'ontimeservices' && $statusId == ShippingStatus::DELIVERED_ID) {
                $shipment->billing_date = date('Y-m-d');
            }

            if ($shipment->status_id == ShippingStatus::INCIDENCE_ID) {
                $hasIncidence = true;
            }

            $shipment->status_id   = $statusId;
            $shipment->status_date = $statusDate;
            unset($shipment->original_provider_id);
            unset($shipment->pack_type);
            unset($shipment->pack_qty);
            unset($shipment->pack_weight);
            unset($shipment->pack_fator_m3);

            $shipment->save();

            //Create devolution
            if (!empty($devolution)) {
                $controller = new ShipmentsController();
                $controller->createDevolution($shipment->id, false);
            }

            //DEVOLUTION
            if ($shipment->status_id == ShippingStatus::DEVOLVED_ID) {

                //delete charge expense if devolved
                if ($shipment->charge_price) {
                    $expense = ShippingExpense::remember(config('cache.query_ttl'))
                        ->cacheTags(Service::CACHE_TAG)
                        ->whereSource(config('app.source'))
                        ->where('type', ShippingExpense::TYPE_CHARGE)
                        ->first();

                    if ($expense) {
                        ShipmentExpense::where('shipment_id', $shipment->id)
                            ->where('expense_id', $expense->id)
                            ->delete();

                        ShipmentExpense::updateShipmentTotal($shipment->id);
                    }
                }

                $shipment->storeDevolutionExpenseIfExists();
            }

            //create shipment if pickup success
            if (empty($shipment->children_tracking_code) && in_array($statusId, [ShippingStatus::PICKUP_DONE_ID, ShippingStatus::PICKUP_CONCLUDED_ID])) {
                //$shipment->createShipmentFromPickup();
            }

            //pickup failed, delete assigned shipment if exists
            if (!empty($shipment->children_tracking_code) && $statusId == ShippingStatus::PICKUP_FAILED_ID) {
                Shipment::where('tracking_code', $shipment->children_tracking_code)->delete();
                $shipment->children_tracking_code = null;
                $shipment->children_type = null;
                $shipment->save();
            }

            //cancel payment
            if ($statusId == ShippingStatus::CANCELED_ID) {
                $shipment->walletRefund(); //refund payment
            }


            //add pickup failed expense
            if ($statusId == ShippingStatus::PICKUP_FAILED_ID) {
                $price = $shipment->addPickupFailedExpense();
                $shipment->walletPayment(null, null, $price); //discount payment
            }


            //save history
            $history = new ShipmentHistory();
            $history->shipment_id   = $shipment->id;
            $history->operator_id = $operatorId;
            if ($operatorId == '-1') {
                $history->operator_id = $shipment->{$key};
            }
            $history->status_id     = $shipment->status_id;
            $history->receiver      = $request->receiver;
            $history->incidence_id  = $request->incidence_id;
            $history->agency_id     = $request->agency_id;
            $history->city          = $request->city;
            $history->vehicle       = $shipment->vehicle;
            $history->trailer       = $shipment->trailer;
            $history->obs           = $shipment->status_id == ShippingStatus::PICKUP_CONCLUDED_ID ? 'Gerado TRK' . $shipment->children_tracking_code : $request->obs;
            $history->created_at    = $statusDate;

            if ($request->hasFile('attachment')) {
                if (!$history->upload($request->file('attachment'), true, 20)) {
                    return Redirect::back()->withInput()->with('error', 'Erro ao carregar imagem POD.');
                }
            } else {
                $history->save();
            }

            //force concluded status if pickup is done
            if ($statusId == ShippingStatus::PICKUP_DONE_ID) {
                $history = new ShipmentHistory();
                $history->shipment_id   = $shipment->id;
                $history->operator_id   = $shipment->{$key};
                $history->agency_id     = $request->agency_id;
                $history->status_id     = ShippingStatus::PICKUP_CONCLUDED_ID;
                $history->obs           = 'Gerado TRK' . $shipment->children_tracking_code;
                $history->created_at    = $request->date . ' ' . $request->hour . ':01';
            }

            $history->shipment = $shipment;

            $shipmentsIds[] = $shipment->id;
            $ignoreLastStatusIds[] = $history->id;


            if ($shipment->is_collection && in_array($history->status_id, [
                    ShippingStatus::PENDING_OPERATOR, 
                    ShippingStatus::WAINTING_REALIZATION, 
                    ShippingStatus::IN_PICKUP_ID, 
                    ShippingStatus::PICKUP_ACCEPTED_ID, 
                    ShippingStatus::READ_BY_COURIER_OPERATOR])) {
                $shipment->notifyOperators(false, false);
            }

            //NOTIFY OPERATOR
            if ($shipment->operator_id != $oldOperatorId) {
                $data['id'] = time(); //ID para a aplicação.
                if ($shipment->is_pickup) {
                    $data['title']   = 'Recolha em ' . $shipment->sender_name;
                    $data['message'] = 'Novo pedido de recolha';
                } else {
                    $data['title']   = 'Serviço de ' . $shipment->sender_name;
                    $data['message'] = 'Lembrete de serviço';
                }
                $history->setNotification(BroadcastPusher::getOperatorChannel($shipment->{$key}), $data);
            }

            try {
                $history->sendEmail($forceCustomerEmail, $forceRecipientEmail);
                $history->sendSms($forceCustomerSms, $forceRecipientSms);
            } catch (\Exception $e) {
                return Redirect::back()->with('warning', 'Estado dos envios alterado. ' . $e->getMessage());
            }
        }


        if ($createManifest) {
            $this->createManifest($shipments, $request->toArray());
        }

        //set incidences as resolved
        if ($hasIncidence) {

            $setResolved = ShipmentHistory::whereIn('shipment_id', $shipmentsIds)
                ->where('status_id', ShippingStatus::INCIDENCE_ID);

            if ($statusId) {
                $setResolved = $setResolved->where('id', '<>', $ignoreLastStatusIds); //não muda o ultimo registo de estado inserido caso seja incidencia
            }

            $setResolved->update(['resolved' => 1]);
        }

        return Redirect::back()->with('success', 'Estado dos envios alterado com sucesso.');
    }

    /**
     * Create delivery manifest
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function createManifest($shipments, $input)
    {

        $route = Route::filterOperator($input['operator_id'])
            ->first();

        $manifest = new Trip();
        $manifest->source        = config('app.source');
        $manifest->delivery_date = @$input['date'];
        $manifest->operator_id   = @$input['operator_id'];
        $manifest->vehicle       = @$input['vehicle'];
        $manifest->trailer       = @$input['trailer'];
        $manifest->delivery_route_id = @$route->id;
        $manifest->setCode();

        foreach ($shipments as $shipment) {
            $manifestShipment = TripShipment::firstOrNew([
                'trip_id' => $manifest->id,
                'shipment_id' => $shipment->id
            ]);
            $manifestShipment->save();
        }

        //Atualiza dados do envio
        $updateFields = [];

        if ($manifest->vehicle) {
            $updateFields['vehicle'] = $manifest->vehicle;
        }

        if ($manifest->vehicle) {
            $updateFields['trailer'] = $manifest->trailer;
        }

        if ($manifest->delivery_date) {
            $updateFields['delivery_date'] = $manifest->delivery_date;
        }

        if ($manifest->operator_id) {
            $updateFields['operator_id'] = $manifest->operator_id;
        }

        if (!empty($updateFields)) {
            $ids = $shipments->pluck('id')->toArray();
            Shipment::whereIn('id', $ids)->update($updateFields);
        }
    }

    /**
     * Mass store status
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function massUpdate(Request $request)
    {
        $request->status_id   = $request->assign_status_id;
        $request->operator_id = $request->assign_operator_id;
        return $this->store($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($shipmentId, $id)
    {

        try {

            $shipment = Shipment::find($shipmentId);

            $result = ShipmentHistory::where('shipment_id', $shipmentId)
                ->where('id', $id)
                ->update([
                    'deleted_by' => Auth::user()->id,
                    'deleted_at' => date('Y-m-d H:i:s')
                ]);

            if ($result) {
                $lastStatus = ShipmentHistory::where('shipment_id', $shipmentId)
                    ->orderBy('created_at', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();

                if ($lastStatus) {
                    Shipment::where('id', $shipmentId)->update([
                        'status_id'   => @$lastStatus->status_id,
                        'status_date' => @$lastStatus->created_at
                    ]);
                }

                if (!$result) {
                    return response()->json([
                        'result'   => false,
                        'feedback' => 'Ocorreu um erro ao tentar remover o estado.'
                    ]);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'result'   => false,
                'feedback' => $e->getMessage() . ' line ' . $e->getLine()
            ]);
        }

        $shipmentHistory = ShipmentHistory::with(['status' => function ($q) {
            $q->remember(config('cache.query_ttl'));
            $q->cacheTags(ShippingStatus::CACHE_TAG);
        }])
            ->with(['operator' => function ($q) {
                $q->remember(config('cache.query_ttl'))
                    ->cacheTags(User::CACHE_TAG);
            }])
            ->where('shipment_id', $shipment->id)
            ->withTrashed()
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $attachamentHistory = ShipmentHistoryAttachament::where('shipment_id', $shipment->id);


        return response()->json([
            'result'   => true,
            'feedback' => 'Estado eliminado com sucesso.',
            'target'   => '#tab-status',
            'html'     => view('admin.shipments.shipments.partials.show.history', compact('shipmentHistory', 'shipment', 'attachamentHistory'))->render()
        ]);
    }


    /**
     * Restore shipment
     * @param $shipmentId
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($shipmentId, $id)
    {

        try {

            $shipment = Shipment::find($shipmentId);

            $result = ShipmentHistory::withTrashed()
                ->where('shipment_id', $shipmentId)
                ->where('id', $id)
                ->update([
                    'deleted_by' => null,
                    'deleted_at' => null
                ]);

            if ($result) {

                $lastStatus = ShipmentHistory::where('shipment_id', $shipmentId)
                    ->orderBy('created_at', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();

                if ($lastStatus) {
                    Shipment::where('id', $shipmentId)->update([
                        'status_id'   => $lastStatus->status_id,
                        'status_date' => $lastStatus->created_at
                    ]);
                }

                if (!$result) {
                    return response()->json([
                        'result'   => false,
                        'feedback' => 'Ocorreu um erro ao tentar restaurar o estado.'
                    ]);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'result'   => false,
                'feedback' => $e->getMessage()
            ]);
        }

        $shipmentHistory = ShipmentHistory::with(['status' => function ($q) {
            $q->remember(config('cache.query_ttl'));
            $q->cacheTags(ShippingStatus::CACHE_TAG);
        }])
            ->with(['operator' => function ($q) {
                $q->remember(config('cache.query_ttl'))
                    ->cacheTags(User::CACHE_TAG);
            }])
            ->where('shipment_id', $shipment->id)
            ->withTrashed()
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $attachamentHistory = ShipmentHistoryAttachament::where('shipment_id', $shipment->id);


        return response()->json([
            'result'   => true,
            'feedback' => 'Estado restaurado com sucesso.',
            'target'   => '#tab-status',
            'html'     => view('admin.shipments.shipments.partials.show.history', compact('shipmentHistory', 'shipment', 'attachamentHistory'))->render()
        ]);
    }


    /**
     * Update history from webservice
     *
     * @param integer $shipmentId
     * @return type
     */
    public function syncHistory($shipmentId)
    {

        try {

            $shipment = Shipment::with(['customer' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Customer::CACHE_TAG);
            }])
                ->with(['provider' => function ($q) {
                    $q->remember(config('cache.query_ttl'));
                    $q->cacheTags(Provider::CACHE_TAG);
                }])
                ->filterAgencies()
                ->findOrFail($shipmentId);

            $attachamentHistory = ShipmentHistoryAttachament::where('shipment_id', $shipment->id);

            $webservice = new Webservice\Base();
            $syncResult = $webservice->updateShipmentHistory($shipment);

            

            if ($syncResult) {

                $shipmentHistory = ShipmentHistory::with(['status' => function ($q) {
                    $q->remember(config('cache.query_ttl'));
                    $q->cacheTags(ShippingStatus::CACHE_TAG);
                }])
                    ->with(['operator' => function ($q) {
                        $q->remember(config('cache.query_ttl'))
                            ->cacheTags(User::CACHE_TAG);
                    }])
                    ->where('shipment_id', $shipment->id)
                    ->withTrashed()
                    ->orderBy('created_at', 'desc')
                    ->orderBy('id', 'desc')
                    ->get();



                $result = [
                    'result'   => true,
                    'html'     => view('admin.shipments.shipments.partials.show.history', compact('shipmentHistory', 'shipment', 'attachamentHistory'))->render(),
                    'feedback' => 'Estados atualizados com sucesso'
                ];
            } else {
                $result = [
                    'result'   => false,
                    'feedback' => 'Não existem estados para atualizar.'
                ];
            }
        } catch (\Exception $e) {
            $result = [
                'result'   => false,
                'feedback' => $e->getMessage()
            ];
        }

        return Response::json($result);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function changePendingStatus($customerId)
    {
        $source = 'dashboard';

        $shipment = new Shipment(); //só para nao dar erro na janela que abre

        $status = ShippingStatus::filterSources()
            ->where('slug', '<>', ShippingStatus::PENDING)
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $action = 'Alterar Estados dos Envios Pendentes';

        $formOptions = array('route' => array('admin.shipments.status.assign-pending', $customerId), 'method' => 'POST', 'files' => true);

        $operators = User::filterAgencies()
            ->isActive()
            ->isOperator()
            ->where('id', '>', 1)
            ->orderBy('name', 'asc')
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

        $hours = listHours(5);

        $lastHour = Date::now();

        switch ($lastHour->minute) {
            case $lastHour->minute >= 0 && $lastHour->minute < 15:
                $minute = 0;
                break;
            case $lastHour->minute >= 15 && $lastHour->minute < 30:
                $minute = 15;
                break;
            case $lastHour->minute >= 30 && $lastHour->minute < 45:
                $minute = 30;
                break;
            case $lastHour->minute >= 45 && $lastHour->minute < 60:
                $minute = 45;
                break;
        }

        $lastHour = ($lastHour->hour + 1) . ':' . $minute;

        return view('admin.shipments.history.edit', compact('shipment', 'action', 'formOptions', 'operators', 'status', 'hours', 'lastHour', 'incidences', 'source'))->render();
    }


    /**
     * Assign status to all selected resources from storage.
     * GET /admin/shipments/selected/assign-status
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massChangePendingStatus(Request $request, $customerId)
    {
        $ids = Shipment::filterAgencies()
            ->where('customer_id', $customerId)
            ->where('status_id', ShippingStatus::PENDING_ID)
            ->pluck('id')
            ->toArray();

        $statusId   = $request->status_id;

        $operatorId = $request->assign_operator_id;

        foreach ($ids as $id) {

            $shipment = Shipment::find($id);
            $shipment->status_id = $statusId;

            if (!empty($operatorId) && $operatorId != '-1') {
                $shipment->operator_id = $operatorId;
            }

            if (!$shipment->price_fixed && !$shipment->is_blocked && !$shipment->invoice_id && (empty($shipment->total_price) || $shipment->total_price == 0.00)) {

                $prices = Shipment::calcPrices($shipment);
                $shipment->cost_price     = @$prices['cost'];
                $shipment->total_price    = @$prices['total'];
                $shipment->fuel_tax       = @$prices['fuelTax'];
                $shipment->extra_weight   = @$prices['extraKg'];
            }

            $shipment->save();

            $history = new ShipmentHistory();
            $history->shipment_id = $shipment->id;
            if ($operatorId != '-1') {
                $history->operator_id = $shipment->operator_id;
            }
            $history->status_id   = $shipment->status_id;
            $history->agency_id   = $shipment->agency_id;
            $history->created_at  = $request->date . ' ' . $request->hour . ':00';
            $history->save();
        }

        return Redirect::back()->with('success', 'Estado dos envios alterado com sucesso.');
    }
}
