<?php

namespace App\Http\Controllers\Admin;

use App\Models\IncidenceType;
use App\Models\Service;
use App\Models\Traceability\ShipmentTraceability;
use App\Models\Vehicle;
use App\Models\Webservice\Base;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Support\Facades\View;
use Html, DB, Auth, Response, Setting;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use App\Models\ShippingStatus;
use App\Models\Agency;
use App\Models\Provider;
use App\Models\User;
use Mpdf\Mpdf;

class PickingManagementController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'picking_management';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',traceability']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $status = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingStatus::CACHE_TAG)
            ->filterSources()
            ->isVisible()
            ->where('is_traceability', 1)
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $shipments = [];

        $data = compact(
            'status',
            'providers',
            'shipments'
        );

        return $this->setContent('admin.picking_management.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $input = $request->all();

        try {
            $shipment = Shipment::where('id', $input['id'])->first();

            if (!empty(@$input['volumes'])) {
                $shipment->volumes = @$input['volumes'];
            }

            if (!empty(@$input['weight'])) {
                $shipment->weight = @$input['weight'];
            }

            if (!empty(@$input['provider'])) {
                $shipment->provider_id = @$input['provider'];
            }


            $prices = Shipment::calcPrices($shipment);


            //SUBMIT BY WEBSERVICE
            $submitWebservice = false;
            if (!empty(Setting::get('webservices_auto_submit')) && (empty($shipment->webservice_method) || (!empty($shipment->webservice_method) && empty($shipment->submited_at)) || in_array($shipment->webservice_method, ['envialia', 'tipsa', 'nacex']))) {
                $submitWebservice = true;
            }

            $debug = false;
            $providerTrk = null;
            if ($submitWebservice && !@$shipment->status->is_final) {
                try {
                    $debug = $request->get('debug', false);
                    $shipment->provider_weight = @$input['label_weight'];
                    $webservice = new Base($debug);
                    $providerTrk = $webservice->submitShipment($shipment);
                } catch (\Exception $e) {}
            }

            try {
                if(hasModule('logistic') && $shipment->pack_dimensions) {
                    $shipment->provider_tracking_code = $providerTrk;
                    $result = $shipment->storeShippingOrder();
                    if(!$result['result']) {
                        throw new \Error($result['feedback']);
                    }
                }
            } catch(\Exception $e) {}


            unset($shipment->provider_weight);

            if ($shipment->payment_at_recipient) {
                $shipment->total_price_for_recipient = $prices['total'];
            } else {
                $shipment->total_price = $prices['total'];
            }

            $shipment->cost_price     = @$prices['cost'];
            $shipment->zone           = @$prices['zone'];
            $shipment->fuel_tax       = @$prices['fuelTax'];
            $shipment->extra_weight   = @$prices['extraKg'];
            $shipment->save();


            $result   = true;
            $feedback = 'Alteração gravada com sucesso.';
            $label    = route('admin.printer.shipments.labels', $shipment->id);
            if ($shipment->hasSyncError()) {
                $result   = false;
                $feedback = $shipment->last_error;
                $label    = null;
            }
        } catch (\Exception $e) {
            $result   = false;
            $feedback = $e->getMessage();
        }

        $result = [
            'result'     => $result,
            'feedback'   => $feedback,
            'printLabel' => $label
        ];

        return response()->json($result);
    }

    /**
     * Get shipment data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getShipment(Request $request) {

        $result         = true;
        $alreadyReaded  = false;
        $statusId       = $request->status;
        $providerId     = $request->provider;
        $weight         = $request->weight;
        $providerWeight = $request->provider_weight;
        $date           = $request->date;
        $code           = trim($request->code);
        $changeProvider = false;
        $checkList      = empty($request->check_list) ? [] : explode(',', $request->check_list);
        $editMode       = true;
        $excludeStatus  = [ShippingStatus::DELIVERED_ID, ShippingStatus::IN_TRANSPORTATION_ID, ShippingStatus::IN_DISTRIBUTION_ID];

        $sourceAgencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterSource()
            ->pluck('id')
            ->toArray();

        //get trk code
        $trkCode = $code;
        if(strlen($code) == 18) { //codigos novos
            $codeParts = [
                strlen($code) == 18 ? substr($code, 0, 12) : $code, //trk
                strlen($code) == 18 ? (int)substr($code, -3) : 'all' //volume
            ];
            $trkCode   = @$codeParts[0];
        } elseif(strlen($code) == 15){ //codigos antigos
            $codeParts = [
                strlen($code) == 15 ? substr($code, 0, 12) : $code, //trk
                strlen($code) == 15 ? (int)substr($code, -3) : 'all' //volume
            ];
            $trkCode   = @$codeParts[0];
        } elseif(strlen($code) <= 12) {
            $trkCode = $code;
        }

        $readedTrk = $trkCode;

        $shipment = Shipment::where(function($q) use($trkCode) {
            $q->where('tracking_code', 'like', '%' . $trkCode);
        })
            ->where(function($q) use($sourceAgencies) {
                $q->whereIn('agency_id', $sourceAgencies);
                $q->orWhereIn('sender_agency_id', $sourceAgencies);
                $q->orWhereIn('recipient_agency_id', $sourceAgencies);
            })
            //->whereNotIn('status_id', $excludeStatus)
            ->orderBy('id', 'desc')
            ->first();

        if(!$shipment) {
            $result = false;
            $shipment = new Shipment();
            $shipment->tracking_code = $trkCode;
        }

        /*if(valueBetween($shipment->weight, 0, 2)) {
            $shipment->label_weight = 1;
        } else if(valueBetween($shipment->weight, 2.01, 5.00)) {
            $shipment->label_weight = 2;
        } else if(valueBetween($shipment->weight, 5.01, 10.00)) {
            $shipment->label_weight = 5;
        } else if(valueBetween($shipment->weight, 10.01, 15.00)) {
            $shipment->label_weight = 10;
        } else if(valueBetween($shipment->weight, 15.01, 20.00)) {
            $shipment->label_weight = 15;
        } else if(valueBetween($shipment->weight, 20.01, 25.00)) {
            $shipment->label_weight = 20;
        } else if(valueBetween($shipment->weight, 25.01, 30.00)) {
            $shipment->label_weight = 20;
        }*/


        if(in_array($shipment->status_id, $excludeStatus)) {
            $editMode = false;
        } else {
            if(!empty($providerId)) {
                $changeProvider = true;
                $shipment->provider_id = $providerId;
            }

            if(!empty($statusId)) {
                $shipment->status_id = $statusId;

                $history = new ShipmentHistory();
                $history->shipment_id = $shipment->id;
                $history->status_id   = $statusId;
                $history->user_id     = Auth::user()->id;
                $history->save();
            }

            if(!empty($date)) {
                $shipment->date = $date;
            }

            if(!empty($weight)) {
                $shipment->weight = $weight;
            }

            if(!empty($providerWeight)) {
                $shipment->weight = $providerWeight;
            }

            //CHANGE PRICE
            if($changeProvider || !empty($weight)) {

                $prices = Shipment::calcPrices($shipment);

                if(@$prices['fillable']) {
                    $shipment->fill($prices['fillable']);

                    if (@$prices['expenses']) {
                        $shipment->storeExpenses($prices);
                    }
                }

                // $shipment->cost_price     = @$prices['cost'];
                // $shipment->zone           = @$prices['zone'];
                // $shipment->fuel_tax       = @$prices['fuelTax'];
                // $shipment->extra_weight   = @$prices['extraKg'];
            }

            $shipment->save();


            //SUBMIT BY WEBSERVICE
            if($changeProvider) {

                $submitWebservice = false;
                if (!empty(Setting::get('webservices_auto_submit')) && (empty($shipment->webservice_method) || (!empty($shipment->webservice_method) && empty($shipment->submited_at)) || in_array($shipment->webservice_method, ['envialia', 'tipsa', 'nacex']))) {
                    $submitWebservice = true;
                }

                $providerTrk = null;
                if ($submitWebservice) {
                    try {
                        $shipment->provider_weight = $providerWeight;
                        $webservice = new Base();
                        $webservice->submitShipment($shipment);
                    } catch (\Exception $e) {}
                }

                try {
                    if(hasModule('logistic') && $shipment->pack_dimensions) {
                        $shipment->provider_tracking_code = $providerTrk;
                        $result = $shipment->storeShippingOrder();
                        if(!$result['result']) {
                            throw new \Exception($result['feedback']);
                        }
                    }
                } catch(\Exception $e) {}
            }
        }

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        return Response::json([
            'result'        => $result,
            'trk'           => $shipment->tracking_code,
            'readedTrk'     => $readedTrk,
            'alreadyReaded' => $alreadyReaded,
            'totalRead'     => $shipment->counter,
            'html'          => view('admin.picking_management.partials.list_item', compact('shipment', 'readedTrk', 'providers', 'editMode'))->render()
        ]);

    }
}