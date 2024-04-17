<?php

namespace App\Http\Controllers\Api\Partners;

use App\Models\Shipment;
use App\Models\ShipmentHistory;
use App\Models\ShippingStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Auth, Validator, Setting, Mail, Log, DB;

class ShipmentsController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Bindings
     *
     * @var array
     */
    protected $bindings = [
        'tracking_code',
        'type',
        'parent_tracking_code',
        'provider_tracking_code',
        'reference',
        'sender_attn',
        'sender_vat',
        'sender_name',
        'sender_address',
        'sender_zip_code',
        'sender_city',
        'sender_country',
        'sender_phone',
        'recipient_attn',
        'recipient_vat',
        'recipient_name',
        'recipient_address',
        'recipient_zip_code',
        'recipient_city',
        'recipient_country',
        'recipient_phone',
        'recipient_attn',
        'recipient_email',
        'volumes',
        'weight',
        'volumetric_weight',
        'fator_m3',
        'charge_price',
        'total_price',
        'payment_at_recipient',
        'complementar_services',
        'is_collection',
        'date',
        'obs',
        'obs_delivery',
        'provider_id',
        'service_id',
        'status_id',
        'agency_id',
        'customer_id',
        'operator_id',
        'pickup_operator_id',
        'route_id'
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
    }

    /**
     * Lists all shipments by given parameters
     *
     * @param Request $request
     * @return mixed
     */
    public function lists(Request $request) {
        $request = new Request(['_partner_api_' => true, 'bindings' => $this->bindings] + $request->toArray());
        $shipmentCollection = new \App\Http\Controllers\Api\Customers\ShipmentsController($request);
        return $shipmentCollection->lists($request, 'partners');
    }

    /**
     * Obtem o token de autenticação para um utilizador.
     *
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request) {
        return $this->update($request, null);
    }

    /**
     * Obtem o token de autenticação para um utilizador.
     *
     * @param Request $request
     * @return mixed
     */
    public function update(Request $request, $tracking = null) {
        $request = new Request(['_partner_api_' => true] + $request->toArray());
        $shipmentCollection = new \App\Http\Controllers\Api\Customers\ShipmentsController($request);
        return $shipmentCollection->update($request, 'partners', $tracking);
    }

    /**
     * Delete a shipment
     *
     * @param Request $request
     * @return mixed
     */
    public function destroy(Request $request, $tracking) {
        $request = new Request(['_partner_api_' => true] + $request->toArray());
        $shipmentCollection = new \App\Http\Controllers\Api\Customers\ShipmentsController($request);
        return $shipmentCollection->destroy($request, 'partners', $tracking);
    }

    /**
     * Return a label of a shipment
     *
     * @param Request $request
     * @return mixed
     */
    public function getLabels(Request $request) {

        $tracking = $request->get('tracking');

        $request = new Request(['_partner_api_' => true] + $request->toArray());
        $shipmentCollection = new \App\Http\Controllers\Api\Customers\ShipmentsController($request);
        return $shipmentCollection->getLabels($request, 'partners', $tracking);
    }

    /**
     * Return a label of a shipment
     *
     * @param Request $request
     * @return mixed
     */
    public function getCargoManifest(Request $request) {
        $request = new Request(['_partner_api_' => true] + $request->toArray());
        $shipmentCollection = new \App\Http\Controllers\Api\Customers\ShipmentsController($request);
        return $shipmentCollection->getCargoManifest($request, 'partners');
    }

    /**
     * Return a transportation guide of a shipment
     *
     * @param Request $request
     * @return mixed
     */
    public function getTransportationGuide(Request $request) {

        $tracking = $request->get('tracking');

        $request = new Request(['_partner_api_' => true] + $request->toArray());
        $shipmentCollection = new \App\Http\Controllers\Api\Customers\ShipmentsController($request);
        return $shipmentCollection->getTransportationGuide($request, 'partners', $tracking);
    }

    /**
     * Return a CMR of a shipment
     *
     * @param Request $request
     * @return mixed
     */
    public function getCMR(Request $request) {

        $tracking = $request->get('tracking');

        $request = new Request(['_partner_api_' => true] + $request->toArray());
        $shipmentCollection = new \App\Http\Controllers\Api\Customers\ShipmentsController($request);
        return $shipmentCollection->getCMR($request, 'partners', $tracking);
    }

    /**
     * Return a POD of a shipment
     *
     * @param Request $request
     * @return mixed
     */
    public function getPOD(Request $request) {

        $tracking = $request->get('tracking');

        $request = new Request(['_partner_api_' => true] + $request->toArray());
        $shipmentCollection = new \App\Http\Controllers\Api\Customers\ShipmentsController($request);
        return $shipmentCollection->getPOD($request, 'partners', $tracking);
    }

    /**
     * Permite consultar os dados de um envio dado o seu código.
     *
     * @param Request $request
     * @return mixed
     */
    public function show(Request $request, $tracking) {
        $request = new Request(['_partner_api_' => true, 'bindings' => $this->bindings] + $request->toArray());
        $shipmentCollection = new \App\Http\Controllers\Api\Customers\ShipmentsController($request);
        return $shipmentCollection->show($request, 'partners', $tracking);
    }

    /**
     * Permite consultar os dados de um envio dado o seu código.
     *
     * @param Request $request
     * @return mixed
     */
    public function history(Request $request, $tracking, $massive = false) {
        $request = new Request(['_partner_api_' => true] + $request->toArray());
        $shipmentCollection = new \App\Http\Controllers\Api\Customers\ShipmentsController($request);
        return $shipmentCollection->history($request, 'partners', $tracking, $massive);
    }

    /**
     * Permite consultar o histórico de multiplos envios
     *
     * @param Request $request
     * @return mixed
     */
    public function massHistory(Request $request) {
        $tracking = $request->trackings;
        return $this->history($request, $tracking, true);
    }


    /**
     * Regista um novo estado de envio
     *
     * @param Request $request
     * @return mixed
     */
    public function storeHistory(Request $request)
    {
        $shipmentCollection = new \App\Http\Controllers\Api\Customers\ShipmentsController($request);

        $statusArr = $request->all();

        if(empty($statusArr)) {
            return $shipmentCollection->responseError('store', '-001', 'Empty data.');
        }


        //get shipments and check required fields
        $shipmentsTrk = [];
        foreach ($statusArr as $input) {

            $shipmentsTrk[] = @$input['tracking_code'];

            if(empty(@$input['tracking_code'])
                || empty(@$input['status'])
                || empty(@$input['date'])
                || empty(@$input['hour'])) {
                return $shipmentCollection->responseError('store', '-001', 'Missing mandatory fields.');
            }
        }

        //get shipments array
        $shipments = Shipment::whereIn('tracking_code', $shipmentsTrk)->get();
        $shipments = $shipments->groupBy('tracking_code')->toArray();

        $failedTrks = [];
        foreach ($statusArr as $input) {
            try {

                $shipment = @$shipments[$input['tracking_code']][0];

                $input['shipment_id'] = @$shipment['id'];
                $input['agency_id']   = @$shipment['agency_id'];

                $input['status_id'] = $input['status'];

                if($input['status_id'] == ShippingStatus::INCIDENCE_ID) {
                    $input['incidence_id'] = $input['incidence'];
                }

                if(@$input['operator']) {
                    $operator = User::filterSource()->where('code', $input['operator'])->first();
                    $input['operator_id'] = @$operator->id;
                }

                if($input['date'] && $input['hour']) {
                    $input['created_at'] = $input['date'].' '.$input['hour'];
                }

                $filepath = $filename = '';
                    if(!empty($input['photo'])) {

                      $fileContent = $input['photo'];
                      $folder = ShipmentHistory::DIRECTORY;

                      if(!File::exists(public_path($folder))) {
                         File::makeDirectory(public_path($folder));
                      }
            
                      $filename = strtolower(str_random(8).'.png');
                      $filepath = $folder.'/'.$filename;
                      File::put(public_path($filepath), base64_decode($fileContent));
                }

                $history = new ShipmentHistory();
                $history->fill($input);
                $history->api = 1;
                $history->save();


                //cancel payment
                if ($input['status_id'] == ShippingStatus::CANCELED_ID) {
                    $shipment->walletRefund(); //refund payment
                }


                //add pickup failed expense
                if ($input['status_id'] == ShippingStatus::PICKUP_FAILED_ID) {
                    $price = $shipment->addPickupFailedExpense();
                    $shipment->walletPayment(null, null, $price); //discount payment
                }

                $history->sendEmail();
                $history->sendSms();

            } catch (\Exception $e) {
                $failedTrks[$input['tracking_code']];
            }
        }


        if(!empty($failedTrks)) {

            $failedTrks = implode(',', $failedTrks);

            $response = [
                'code'          => '',
                'message'       => 'Status changed. TRK '.$failedTrks.' failed.',
            ];
        } else {
            $response = [
                'code'          => '',
                'message'       => 'Status saved successfully.',
            ];
        }


        return response($response, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Permite inserir uma solução para uma incidência
     *
     * @param Request $request
     * @return mixed
     */
    public function resolveIncidence(Request $request) {
        $request = new Request(['_partner_api_' => true] + $request->toArray());
        $shipmentCollection = new \App\Http\Controllers\Api\Customers\ShipmentsController($request);
        return $shipmentCollection->resolveIncidence($request, 'partners');
    }

    /**
     * Permite devolver o histórico de reembolsos
     *
     * @param Request $request
     * @return mixed
     */
    public function getCOD(Request $request) {
        $request = new Request(['_partner_api_' => true] + $request->toArray());
        $shipmentCollection = new \App\Http\Controllers\Api\Customers\ShipmentsController($request);
        return $shipmentCollection->getCOD($request, 'partners');
    }

    /**
     * Permite calcular o preço de um envio
     *
     * @param Request $request
     * @return mixed
     */
    public function getPrice(Request $request) {
        $request = new Request(['_partner_api_' => true] + $request->toArray());
        $shipmentCollection = new \App\Http\Controllers\Api\Customers\ShipmentsController($request);
        return $shipmentCollection->calcPrice($request, 'partners');
    }

    /**
     * Get list of services
     * @param Request $request
     * @return array
     */
    public function listsServices(Request $request) {
        $request = new Request(['_partner_api_' => true] + $request->toArray());
        $shipmentCollection = new \App\Http\Controllers\Api\Customers\ShipmentsController($request);
        return $shipmentCollection->listsServices($request, 'partners');
    }

    /**
     * Get list of status
     * @param Request $request
     * @return array
     */
    public function listsStatus(Request $request) {
        $request = new Request(['_partner_api_' => true] + $request->toArray());
        $shipmentCollection = new \App\Http\Controllers\Api\Customers\ShipmentsController($request);
        return $shipmentCollection->listsStatus($request, 'partners');
    }

    /**
     * Get list of pick-up and drop over pointw
     * @param Request $request
     * @return array
     */
    public function listsPudo(Request $request) {
        $request = new Request(['_partner_api_' => true] + $request->toArray());
        $shipmentCollection = new \App\Http\Controllers\Api\Customers\ShipmentsController($request);
        return $shipmentCollection->listsPudo($request, 'partners');
    }

    /**
     * Close CTT shipments
     * 
     * @param Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function closeCtt(Request $request) {
        $request = new Request(['_partner_api_' => true] + $request->toArray());
        $shipmentCollection = new \App\Http\Controllers\Api\Customers\ShipmentsController($request);
        return $shipmentCollection->closeCtt($request, 'partners');
    }

     /**
    * GET list of traceability 
    * @param Request $request
    * @return array
    */
    public function traceabilityHistory(Request $request, $tracking){
        
        $request = new Request(['_partner_api_' => true] + $request->toArray());
        $traceabilityCollection = new \App\Http\Controllers\Api\Customers\ShipmentsController($request);
        return $traceabilityCollection->traceabilityHistory($request, 'partners', $tracking);
 
    }
}