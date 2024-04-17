<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Models\OperatorTask;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use App\Models\ShippingStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Auth, Setting, DB;

class StatsController extends \App\Http\Controllers\Api\Mobile\BaseController
{
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}


    /**
     * Lists all shipping status
     *
     * @param Request $request
     * @return mixed
     */
    public function getStatistics(Request $request) {


        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        $dtMin = date('Y-m-d').' 00:00:00';
        $dtMax = date('Y-m-d').' 23:59:59';

        /*$dtMin = '2021-10-28 00:00:00';
        $dtMax = '2021-10-28 23:59:59';

        $user = User::find(4);*/

        $codGuides = $this->getCodGuides($user, $dtMin, $dtMax);

        $statsArr = [
            'eficacy'        => $this->getEficacy($user, $dtMin, $dtMax),
            'cod_guides'     => $codGuides->toArray(),
            'cod_totals'     => $this->getCodTotals($codGuides),
            'cod_details'    => $this->getCodDetails($codGuides),
            //'refund_methods' => $this->getRefundMethodsList()
        ];

        return response($statsArr, 200)->header('Content-Type', 'application/json');
    }


    /**
     * Get eficacy
     *
     * @param Request $request
     * @return mixed
     */
    public function getEficacy($user, $dtMin, $dtMax) {

        $statusIds = [36,38,37,31,20,21,22,20,16,3,4,9,5];
        if(Setting::get('mobile_app_status_delivery')) {
            $statusIds = Setting::get('mobile_app_status_delivery');
            $statusIds[]+= ShippingStatus::DELIVERED_ID;
            $statusIds[]+= ShippingStatus::INCIDENCE_ID;
        }

        //DELIVERY STATS
        $shipments = Shipment::join('shipments_history', 'shipments.id', '=', 'shipments_history.shipment_id')
            ->where('shipments_history.operator_id', $user->id)
            ->where('shipments.operator_id', $user->id)
            ->whereBetween('shipments_history.created_at', [$dtMin, $dtMax])
            ->whereIn('shipments_history.status_id', $statusIds)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get([
                'shipments.id',
                'shipments_history.status_id',
                'shipments_history.created_at',
                'shipments.delivery_date'
            ]);


        $shipments = $shipments->groupBy('id')->transform(function($q){
            return $q->first()->status_id;
        });

        //dd($shipments->toArray());

        $deliveryStats = [
            'total'     => count($shipments),
            'delivered' => 0,
            'incidence' => 0,
            'timeout'   => 0,
            'distribuition' => 0,
        ];

        foreach ($shipments as $shipmentId => $statusId) {

            if($statusId == ShippingStatus::DELIVERED_ID) {
                $deliveryStats['delivered'] = @$deliveryStats['delivered'] + 1;

            } elseif($statusId == ShippingStatus::INCIDENCE_ID) {
                $deliveryStats['incidence'] = @$deliveryStats['incidence'] + 1;

            } else {
                $deliveryStats['distribuition'] = @$deliveryStats['distribuition'] + 1;
            }

            if(!empty($shipment->delivery_date) && $shipment->created_at >= $shipment->delivery_date) {
                $deliveryStats['timeout'] = @$deliveryStats['timeout'] + 1;
            }
        }

        $eficacy = $deliveryStats['total'] ? ((@$deliveryStats['delivered'] + @$deliveryStats['incidence']) * 100) / $deliveryStats['total'] : 0;

        if($deliveryStats['timeout']) {
            $coeficientePenalizacao = (50/($deliveryStats['total']/2))/2; //formula para que se todos forem entregues fora do prazo, a eficácia seja apenas 50%
            $eficacy = $eficacy - ($deliveryStats['timeout'] * $coeficientePenalizacao);
        }

        $deliveryStats['eficacy_value'] = number($eficacy, 0);
        $deliveryStats['eficacy_color'] = getPercentColor($deliveryStats['eficacy_value']);



        /*//PICKUPS STATS
        $tasks = OperatorTask::where('operator_id', $user->id)
            ->whereBetween('last_update', [$dtMin, $dtMax])
            ->get();


        $pickupStats = [
            'total'    => $tasks->count(),
            'pickuped' => 0,
            'failed'   => 0,
            'pending'  => 0
        ];

        foreach ($tasks as $task) {

            if($task->concluded && !$task->incidence) {
                $pickupStats['pickuped'] = $pickupStats['pickuped'] + 1;
            } elseif($task->concluded && $task->incidence) {
                $pickupStats['failed'] = $pickupStats['failed'] + 1;
            } elseif($task->readed && !$task->concluded){
                $pickupStats['pending'] = $pickupStats['pending'] + 1;
            }
        }

        $pickupStats['eficacy_value'] = $pickupStats['total'] ? number(((@$pickupStats['pickuped'] + @$pickupStats['failed']) * 100) / $pickupStats['total'], 0) : 0;
        $pickupStats['eficacy_color'] = getPercentColor($pickupStats['eficacy_value']);


        $stats = [
            'delivery' => $deliveryStats,
            'pickup'   => $pickupStats
        ];*/

        $statusIds = [];
        $statusIds[]+= ShippingStatus::PICKUP_CONCLUDED_ID;
        $statusIds[]+= ShippingStatus::PICKUP_DONE_ID;
        $statusIds[]+= ShippingStatus::PICKUP_FAILED_ID;
        $statusIds[]+= ShippingStatus::PICKUP_ACCEPTED_ID;
        $statusIds[]+= ShippingStatus::IN_PICKUP_ID;
        $statusIds[]+= ShippingStatus::SHIPMENT_PICKUPED;

        //DELIVERY STATS
        $shipments = Shipment::join('shipments_history', 'shipments.id', '=', 'shipments_history.shipment_id')
            ->where('shipments_history.operator_id', $user->id)
            ->whereBetween('shipments_history.created_at', [$dtMin, $dtMax])
            ->whereIn('shipments_history.status_id', $statusIds)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get([
                'shipments.id',
                'shipments_history.status_id',
                'shipments_history.created_at',
                'shipments.delivery_date'
            ]);

        $shipments = $shipments->groupBy('id')->transform(function($q){
            return $q->first()->status_id;
        });

        //dd($shipments->toArray());

        $pickupStatus = [
            'total' => count($shipments),
            'pickuped'=> 0,
            'failed'  => 0,
            'pending' => 0
        ];

        foreach ($shipments as $shipmentId => $statusId) {

            if($statusId == ShippingStatus::PICKUP_DONE_ID || $statusId == ShippingStatus::PICKUP_CONCLUDED_ID || $statusId == ShippingStatus::SHIPMENT_PICKUPED) {
                $pickupStatus['pickuped'] = @$pickupStatus['pickuped'] + 1;

            } elseif($statusId == ShippingStatus::PICKUP_FAILED_ID) {
                $pickupStatus['failed'] = @$pickupStatus['failed'] + 1;

            } else {
                $pickupStatus['pending'] = @$pickupStatus['pending'] + 1;
            }
        }

        $eficacy = $pickupStatus['total'] ? ((@$pickupStatus['pickuped'] + @$pickupStatus['failed']) * 100) / $pickupStatus['total'] : 0;

        $pickupStatus['eficacy_value'] = number($eficacy, 0);
        $pickupStatus['eficacy_color'] = getPercentColor($pickupStatus['eficacy_value']);

        $stats = [
            'delivery' => $deliveryStats,
            'pickup'   => $pickupStatus
        ];

        return $stats;
    }

    /**
     * Lists all shipments with COD
     *
     * @param Request $request
     * @return mixed
     */
    public function getCodGuides($user, $dtMin, $dtMax) {

        $codGuides = Shipment::where('operator_id', $user->id)
            ->where(function($q){
                $q->whereNotNull('charge_price');
                $q->orWhere('charge_price', '>', 0.00);
                $q->orWhere('total_price_for_recipient', '>', 0.00);
            })
            ->where('status_id', ShippingStatus::DELIVERED_ID)
            ->whereBetween('status_date', [$dtMin, $dtMax])
            ->get(['id', 'tracking_code', 'sender_name', 'sender_address', 'sender_zip_code', 'sender_city', 'sender_country',
                'recipient_name', 'recipient_zip_code', 'recipient_city', 'recipient_country',
                'volumes', 'weight', 'charge_price', 'total_price_for_recipient', 'status_id', 'operator_id',
                'refund_method', 'cod_method'
            ]);

        return $codGuides;
    }

    /**
     * Return COD totals
     *
     * @param $codGuides
     * @return array
     */
    public function getCodTotals($shipments) {

        $refundGuides = $shipments->filter(function($item) {
           return $item->charge_price > 0.00;
        });

        $codGuides = $shipments->filter(function($item) {
            return $item->total_price_for_recipient > 0.00;
        });

        $totals = [
            'refunds' => [
                'count' => $refundGuides->count(),
                'price' => number($refundGuides->sum('charge_price'), 2, true),
            ],
            'cod' => [
                'count' => $codGuides->count(),
                'price' => number($codGuides->sum('total_price_for_recipient'), 2, true),
            ],
        ];

        $totals['totals'] = [
            'count' => $shipments->count(),
            'price' => number($totals['refunds']['price'] + $totals['cod']['price'], 2, true),
        ];

        return $totals;
    }


    public function getCodDetails($shipments) {

        if($shipments->isEmpty()) {
            return [
                'code' => '',
                'name' => 'Não definido',
                'refunds' => [
                    'total' => 0,
                    'count' => 0
                ],
                'cod' => [
                    'total' => 0,
                    'count' => 0
                ]
            ];
        }

        $arr = [];
        $refundMethod = $codMethod = "";
        foreach ($shipments as $shipment) {

            $refundMethod = $shipment->refund_method;
            $codMethod    = $shipment->cod_method; //para o caso futuro de existir distincao de pagamento reembolso e portes

            //charge price
            $arr[$refundMethod]['code'] = $refundMethod;
            $arr[$refundMethod]['name'] = $refundMethod ? trans('admin/refunds.payment-methods.' . $refundMethod) : 'Não definido';
            $arr[$refundMethod]['refunds']['total'] = @$arr[$refundMethod]['refunds']['total'] + $shipment->charge_price;
            $arr[$refundMethod]['refunds']['count'] = @$arr[$refundMethod]['refunds']['count'] + ($shipment->charge_price > 0.00 ? 1 : 0);

            //total price for recipient
            $arr[$codMethod]['code'] = $codMethod;
            $arr[$codMethod]['name'] = $codMethod ? trans('admin/refunds.payment-methods.' . $codMethod) : 'Não definido';
            $arr[$codMethod]['cod']['total'] = @$arr[$codMethod]['cod']['total'] + $shipment->total_price_for_recipient;
            $arr[$codMethod]['cod']['count'] = @$arr[$codMethod]['cod']['count'] + ($shipment->total_price_for_recipient > 0.00 ? 1 : 0);
        }


        $arr[$refundMethod]['refunds']['total'] = number(@$arr[$refundMethod]['refunds']['total'], 2, true);
        $arr[$codMethod]['cod']['total']        = number(@$arr[$codMethod]['cod']['total'], 2, true);

        return $arr;
    }

    /**
     * Lists all shipping status
     *
     * @param Request $request
     * @return mixed
     */
    public function getRefundMethodsList() {
        $methods = trans('admin/refunds.payment-methods');
        return $methods;
    }
}