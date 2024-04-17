<?php

namespace App\Http\Controllers\DefaultSite;

use App\Models\Shipment;
use App\Models\Agency;
use App\Models\FleetGest\Vehicle;
use App\Models\Webservice\Base;
use App\Models\GpsGateway\Base as GpsGatewayBase;
use App\Models\Map;
use App\Models\ShippingStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Setting, Mail, Auth;

class TrackingController extends \App\Http\Controllers\Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){}

    /**
     * The layout that should be used for responses
     *
     * @var string
     */
    protected $layout = 'layouts.default';
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tracking = null)
    {
        if(empty($tracking)) {
            $tracking = $request->get('tracking');
        }

        $tracking = str_replace(';', ',', $tracking);
        $trackingCodes = explode(',', $tracking);
        $trackingCodes = array_filter($trackingCodes);

        $agencies = Agency::where('source', config('app.source'))
                            ->pluck('id')
                            ->toArray();

        $shipments = Shipment::with(['history' => function($q){
                $q->with('status', 'agency', 'provider_agency', 'resolutions')
                    ->orderBy('created_at', 'desc')
                    ->orderBy('id', 'desc');

                $q->whereHas('status', function ($q) {
                    $q->isPublic();
                });
            }])
            ->where(function($q) use($trackingCodes) {
                $q->whereIn('tracking_code', $trackingCodes);
                $q->orWhereIn('reference', $trackingCodes);
                $q->orWhereIn('provider_tracking_code', $trackingCodes);
            })
            ->whereIn('agency_id', $agencies)
            ->get();
            
        $shipmentsResults = [];
        $ontimeLocationActive = false;
        if(!$shipments->isEmpty()) {

            foreach ($shipments as $shipment) {

                $lastHistory = $shipment->history->first();
                $stepStatus = @$lastHistory->status->tracking_step;
                $ontimeLocationActive  = @$lastHistory->status->is_final ? false : true;
                
                if (empty($stepStatus)) {
                    $stepStatus = 'pending';
                    $stepId = 1;
                } else {
                    if ($stepStatus == 'pending') {
                        $stepId = 1;
                    } elseif ($stepStatus == 'accepted') {
                        $stepId = 2;
                    } elseif ($stepStatus == 'pickup') {
                        $stepId = 3;
                    } elseif ($stepStatus == 'transport') {
                        $stepId = 4;
                    } elseif (in_array($stepStatus, ['delivered', 'incidence', 'returned'])) {
                        $stepId = 5;
                    } elseif (in_array($stepStatus, ['canceled'])) {
                        $stepStatus = 'canceled';
                        $stepId = 4;
                    } else {
                        $stepStatus = 'transport';
                        $stepId = 4;
                    }
                }

                $shipmentsResults[] = [
                    'shipment'   => $shipment,
                    'stepId'     => $stepId,
                    'stepStatus' => $stepStatus
                ];
            }

    
            //estados em que é permitido visualizar o estado da viagem
            $locationStatus = [
                ShippingStatus::READ_BY_COURIER_OPERATOR,
                ShippingStatus::IN_PICKUP_ID,
                ShippingStatus::PICKUP_CONCLUDED_ID,
                ShippingStatus::IN_TRANSPORTATION_ID,
                ShippingStatus::IN_DISTRIBUTION_ID
            ];

            
            $ontimeLocationEnabled = false;
            if(@$lastHistory) {
                if(in_array($lastHistory->status_id, $locationStatus) && $lastHistory->operator_id) {
                    $ontimeLocationEnabled = true;
                }
            }

            //permite aos administradores terem sempre acesso
            if(Auth::check() && Auth::user()->isAdmin()) {
                $ontimeLocationActive  = true;
                $ontimeLocationEnabled = true;
            }
        }

        $data = compact(
            'shipmentsResults',
            'tracking',
            'ontimeLocationEnabled',
            'ontimeLocationActive'
        );

        if($request->has('ajax')) {
            $shipment = $shipmentsResults[0];
            return view('default.partials.card_tracking', compact('shipment'))->render();
        }

        return $this->setContent('default.tracking', $data);
    }

    /**
     * Sync shipment tracking
     * @param $tracking
     */
    public function syncTracking(Request $request, $tracking) {

        $shipment = Shipment::with(['history' => function($q){
            $q->with('status', 'agency', 'provider_agency')
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc');
        }])
        ->where('tracking_code', $tracking)
        ->get();

        try {
            $webservice = new Base();
            $syncResult = $webservice->updateShipmentHistory($shipment);
        } catch (\Exception $e) {}


        $request = new Request(['ajax' => true]);
        $html = $this->index($request, $tracking);

        $result = [
            'html' => $html
        ];

        return response()->json($result);
    }

    /**
     * Show the form for reschedule the specified shipment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $shipment = Shipment::whereId($id)
            ->firstOrfail();
        
        $formOptions = array('route' => array('tracking.reschedule.update', $shipment->id), 'method' => 'PUT');

        $data = compact(
            'formOptions',
            'shipment',
            'oldDate'
        );

        return view('default.modals.reschedule', $data)->render();
    }

    /**
     * Update the shipment resource in reschedule.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $input = $request->all();
        $shipment = Shipment::findOrFail($id);
        
        $oldShipment = $shipment->replicate();

        try {
            Mail::send('emails.reschedule', compact('input', 'shipment', 'oldShipment'), function ($message) use ($input, $shipment) {
                $message->to(Setting::get('company_email'));
                $message->subject('Reagendamento do envio ' . $shipment->tracking_code);
            });
        } catch (\Exception $e) {}

        if ($shipment->validate($input)) {
            $shipment->fill($input);
            $shipment->save();
        }

        return Redirect::back()->withInput()->with('error');
    }

        
    /**
     * Obtem as coordenadas atuais de um motorista de um envio
     **/
    public function getOperatorLocation($shipmentTrackingCode) {

        $shipment = Shipment::with('status')
        ->whereHas('status', function ($q) {
            $q->isPublic();
            $q->isFinal(false);
        })
        ->where('tracking_code', $shipmentTrackingCode)
        ->first();


        if($shipment) {

            //se dados do envio estão vazios, obtem a localização da morada
            if(empty($shipment->map_lat) || empty($shipment->map_lng)) {

                $destination = $shipment->recipient_address.', '.$shipment->recipient_zip_code.' '.$shipment->recipient_city.', '.trans('country.'.$shipment->recipient_country);
                $destination = Map::getCoordinatesFromAddress($destination);
                 
                if(!empty(@$destination['lat'])) {
                    $shipment->map_lat = $destination['lat'];
                    $shipment->map_lng = $destination['lng'];
                    $shipment->save();
                }
            }
            
            $lat = $shipment->map_lat;
            $lng = $shipment->map_lng;
            $ignition   = false;
            $street     = '<i>Sem info rua</i>';
            $statusHtml = '<span style="color: '.@$shipment->status->color.'">'.@$shipment->status->name.'</span>';

            
            if(hasModule('gateway_gps') && Setting::get('gps_gateway') && Setting::get('gps_gateway_apikey')) {

                $vehicle = Vehicle::filterSource()
                    ->where('license_plate', $shipment->vehicle)
                    ->first();

                if($vehicle) {
                    
                    $vehicle->license_plate = '05-RI-87';
                    
                    $locations = new GpsGatewayBase();
                    $location = $locations->getVehicleLocation($vehicle->license_plate);
                    
                    /* $location = [
                        'latitude' => '38.820747',
                        'longitude' => '-9.124049',
                        'gps_city' => 'OLAAAAA'
                        ];
                     */
                    
                    $hour       = @$location['last_location'] ? @$location['last_location']->format('H:i') : date('H:i');
                    $lat        = @$location['latitude'];
                    $lng        = @$location['longitude'];
                    $street     = @$location['gps_city'];
                    $ignition   = @$location['is_ignition_on'];
                }
            }


            $result = [
                'result'        => true,
                'location'      => [
                    'hour'      => $hour ? $hour : date('H:i'),
                    'latitude'  => $lat,
                    'longitude' => $lng,
                    'street'    => $street,
                    'ignition'  => $ignition
                ],
                'destination' => [
                    'latitude'  => $shipment->map_lat,
                    'longitude' => $shipment->map_lng,
                ],
                'operator' => [
                    'photo'   => @$shipment->operator->filepath,
                    'name'    => @$shipment->operator->name,
                    'vehicle' => $shipment->vehicle,
                    'trailer' => $shipment->trailer,
                ],
                'status' => [
                    'status_html'   => $statusHtml,
                    'status_name'   => @$shipment->status->name,
                    'status_date'   => @$shipment->last_history->created_at->format('Y-m-d H:i'),
                ]
            ];
        } else {
            $result = [
                'result'    => false,
            ];
        }

        return response()->json($result);
    }
}
