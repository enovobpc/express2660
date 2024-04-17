<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\Shipment;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){}

    /**
     * class of main menu to be activated
     * 
     * @var string 
     */
    protected $menuOption = 'tracking';
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tracking = null)
    {

        if(!$tracking) {
            $tracking = $request->get('tracking');
        }

        $tracking = str_replace(';', ',', $tracking);
        $trackingCodes = explode(',', $tracking);
        $trackingCodes = array_filter($trackingCodes);

        $agencies = Agency::where('source', config('app.source'))
            ->pluck('id')
            ->toArray();

         $shipments = Shipment::with(['history' => function($q){
                $q->with('status', 'agency', 'provider_agency')
                    ->orderBy('created_at', 'desc')
                    ->orderBy('id', 'desc');
            }])
            ->whereIn('tracking_code', $trackingCodes)
            ->whereIn('agency_id', $agencies)
            ->get();

        $shipmentsResults = [];
        if(!$shipments->isEmpty()) {

            foreach ($shipments as $shipment) {

                $lastHistory = $shipment->history->first();
                $stepStatus = @$lastHistory->status->tracking_step;

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
        }

        $data = compact(
            'shipmentsResults',
            'tracking'
        );

        return $this->setContent('tracking', $data);
    }
}
