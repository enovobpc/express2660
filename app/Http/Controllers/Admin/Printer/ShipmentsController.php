<?php

namespace App\Http\Controllers\Admin\Printer;

use App\Models\CacheSetting;
use App\Models\Trip\Trip;
use App\Models\Trip\TripShipment;
use App\Models\Route;
use App\Models\ShippingStatus;
use Date, Setting, Response, Auth, DB, File;
use App\Models\Agency;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use App\Models\User;
use App\Models\Webservice\Base;
use \Mpdf\Mpdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;

class ShipmentsController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = '';

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
     * Create proof file
     *
     * @param type $shipmentId
     * @return type
     */
    public function shipmentProof(Request $request, $shipmentId)
    {
        try {
            return Shipment::printShipmentProof([$shipmentId]);
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * Create Transportation Guide
     *
     * @param type $shipmentId
     * @return type
     */
    public function transportGuide(Request $request, $shipmentId = null)
    {
        try {

            $data = $request->toArray();

            if (empty($shipmentId) && !empty($data)) {
                return Shipment::printTransportGuide($request->id, null, $data, @$data['grouped']);
            } else {
                return Shipment::printTransportGuide([$shipmentId]);
            }
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erro ao gerar documento. ' . $e->getMessage());
        }
    }


    /**
     * Create Transportation Guide
     *
     * @param type $shipmentId
     * @return type
     */
    public function pickupManifest(Request $request, $shipmentId = null)
    {
        try {

            $data = $request->toArray();

            if (isset($data['id']) && !empty($data['id'])) {
                $ids = $data['id'];
            } else {
                $ids = [$shipmentId];
            }

            return Shipment::printPickupManifest($ids);
        } catch (\Exception $e) {
            if (Auth::user()->isAdmin()) {
                return Redirect::back()->with('error', 'Erro ao gerar documento. ' . $e->getMessage() . ' on ' . $e->getFile() . ' line '  . $e->getLine());
            } else {
                return Redirect::back()->with('error', 'Erro ao gerar documento. ' . $e->getMessage());
            }
        }
    }

    /**
     * Create Value Statement
     *
     * @param type $shipmentId
     * @return type
     */
    public function valueStatement(Request $request, $shipmentId = null)
    {
        try {

            $data = $request->toArray();

            if (empty($shipmentId) && !empty($data)) {
                return Shipment::printValueStatement($request->id);
            } else {
                return Shipment::printValueStatement([$shipmentId]);
            }
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erro ao gerar documento. ' . $e->getMessage());
        }
    }

    /**
     * Create Global Transportation Guide
     *
     * @param type $shipmentId
     * @return type
     */
    public function globalTransportGuide(Request $request)
    {

        try {
            $input = $request->all();

            $agency = Agency::remember(config('cache.query_ttl'))
                ->cacheTags(Agency::CACHE_TAG)
                ->filterSource()
                ->first();

            $veichles = empty($input['guide_vehicles']) ? [] : $input['guide_vehicles'];

            $shipments = [];
            foreach ($veichles as $veichle) {
                $shipment = new Shipment();

                $shipment->tracking_code = '0000' . date('Ymd');
                $shipment->agency_id = $agency->id;
                $shipment->sender_agency_id = $agency->id;
                $shipment->recipient_agency_id = $agency->id;
                $shipment->sender_name = @$input['guide_sender'];
                $shipment->sender_address = @$input['guide_sender_address'];
                $shipment->sender_zip_code = @$input['guide_sender_zip_code'];
                $shipment->sender_city = @$input['guide_sender_city'];
                $shipment->sender_country = @$input['guide_sender_country'];

                $shipment->recipient_name = @$input['guide_recipient'];
                $shipment->recipient_address = @$input['guide_recipient_address'];
                $shipment->recipient_zip_code = @$input['guide_recipient_zip_code'];
                $shipment->recipient_city = @$input['guide_recipient_city'];
                $shipment->recipient_country = @$input['guide_recipient_country'];

                $shipment->volumes = @$input['volumes'];
                $shipment->weight = @$input['weight'];
                $shipment->date = @$input['guide_date'];
                $shipment->created_at = new Date(@$input['date'] . ' ' . @$input['hour'] . ':00');
                $shipment->service_id = '8';
                $shipment->vehicle = $veichle;

                $shipments[] = $shipment;
            }

            if (empty($shipments)) {
                return Redirect::back()->with('error', 'Erro ao gerar documento. Não selecionou nenhuma viatura.');
            }

            $shipments = new Collection($shipments);

            return Shipment::printTransportGuide(null, $shipments);
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erro ao gerar documento. ' . $e->getMessage());
        }
    }

    /**
     * Create adhesive labels
     *
     * @param type $shipmentId
     * @return type
     */
    public function labels(Request $request, $shipmentId = null)
    {

        if ($request->get('label_format') == 'A4') {
            return $this->labelsA4($request, $shipmentId);
        }

        try {
            if (empty($shipmentId) && !empty($request->id)) {
                $shipmentsIds = $request->id;
            } else {
                $shipmentsIds = [$shipmentId];
            }

            $file = Shipment::printAdhesiveLabels($shipmentsIds);

            if (@$file['external_url']) {
                header('Location: ' . @$file['external_url']);
            }
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * Create adhesive labels A4 format
     *
     * @param type $shipmentId
     * @return type
     */
    public function labelsA4(Request $request, $shipmentId = null)
    {

        try {
            if (empty($shipmentId) && !empty($request->id)) {
                $shipmentsIds = $request->id;
            } else {
                $shipmentsIds = [$shipmentId];
            }
            return Shipment::printAdhesiveLabelsA4($shipmentsIds, $request->get('label_start'));
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * Create Shipping Instructions file
     *
     * @param type $shipmentId
     * @return type
     */
    public function shippingInstructions(Request $request, $shipmentId)
    {
        try {
            $data = $request->all();
            return Shipment::printShippingInstructions([$shipmentId], null, $data);
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * Create Reimbursement Guide
     *
     * @param type $shipmentId
     * @return type
     */
    public function reimbursementGuide($shipmentId)
    {
        try {
            return Shipment::printReimbursementGuide([$shipmentId]);
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * Create CMR
     *
     * @param type $shipmentId
     * @return type
     */
    public function Cmr(Request $request, $shipmentId)
    {
        try {
            if($request->has('ecmr')) {
                return Shipment::printECMR([$shipmentId]);
            }

            return Shipment::printCmr([$shipmentId]);
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * Create Delivery Manifest
     *
     * @param type $shipmentId
     * @return type
     */
    public function deliveryMap(Request $request, $operatorId = null)
    {
        try {

            if (empty($operatorId)) {
                $operatorId = $request->get('operator', null);
            }

            $request->trk = empty($request->trk) ? [] : $request->trk;

            if (empty($request->trk) && empty($request->id)) {

                $type      = $request->get('manifest_type');
                $startDate = $request->get('manifest_date_start', date('Y-m-d'));
                $endDate   = $request->get('manifest_date_end', date('Y-m-d'));
                $operators = $request->get('manifest_operators');
                $status    = $request->get('manifest_status');
                $services  = $request->get('manifest_services');
                $providers = $request->get('manifest_providers');
                $sortField = $request->get('manifest_sort', 'zip_code');
                $sortField = empty($sortField) ? 'zip_code' : $sortField;
                $sortField = in_array($sortField, ['tracking_code']) ? $sortField : 'recipient_' . $sortField;
                $sortDir   = $request->get('manifest_sort_dir', 'asc');

                $shipments = Shipment::whereIn('status_id', $status)
                    ->whereIn('provider_id', $providers)
                    ->whereIn('service_id', $services);

                if (!empty($operators)) {
                    $shipments = $shipments->whereIn('operator_id', $operators);
                }

                if (empty($type)) {
                    $shipments = $shipments->whereBetween('date', [$startDate, $endDate]);
                }

                $shipments = $shipments->orderBy($sortField, $sortDir)
                    ->get();

                $date = $startDate . ' - ' . $endDate;
                if ($startDate == $endDate) {
                    $date = $startDate;
                }

                $shipmentsGrouped = $shipments->groupBy('operator_id');
            }

            //TRACEABILITY
            else {
                // dd($request->all());

                if (!empty($request->trk)) { //traceability

                    if (Auth::user()->hasRole('operador')) {
                        $operatorId = Auth::user()->id;
                        $operator   = Auth::user();
                    } else {
                        $operatorId = $request->get('operator');
                        $operator   = User::find($operatorId  == '-1' ? 0 : $operatorId);
                    }

                    $shipments = Shipment::whereIn('tracking_code', $request->trk);

                    if (config('app.source') == 'fozpost') { //na fozpost ordena por codigo postal Nos outros casos ordena por codigo de envio
                        $shipments = $shipments->orderBy('recipient_zip_code', 'asc');
                    } else {
                        $shipments = $shipments->orderByRaw(DB::raw('FIELD(tracking_code,"' .  implode('","', $request->trk) . '")'));
                    }

                    $shipments = $shipments->get();

                    $shipmentsGrouped = $shipments->groupBy('operator_id');

                    $date = date('Y-m-d');

                    //CREATE MANIFEST
                    $route = Route::filterOperator($operatorId)->first();

                    $trip = new Trip();
                    $trip->delivery_route_id = @$route->id;
                    $trip->operator_id = $operatorId > 0 ? $operatorId : null;
                    $trip->vehicle     = $request->get('vehicle');
                    $trip->trailer     = $request->get('trailer') == 'undefined' ? '' : $request->get('trailer');
                    $trip->pickup_date = date('Y-m-d H:i:s');
                    $trip->created_by  = Auth::user()->id;
                    $trip->source      = config('app.source');
                    $trip->setCode();

                    foreach ($shipments as $key => $shipment) {
                        $tripShipment = new TripShipment();
                        $tripShipment->trip_id     = $trip->id;
                        $tripShipment->shipment_id = $shipment->id;
                        $tripShipment->sort = $key + 1;
                        $tripShipment->save();
                    }

                    if ($request->get('save') && $request->has('status_id')) {
                        $shipment->status_id = $request->get('status_id');

                        if (!empty($operatorId) && $operatorId > 0) {
                            $shipment->operator_id = $operatorId;
                        }

                        $vehicle = $request->get('vehicle');
                        if ($vehicle) {
                            $shipment->vehicle = $vehicle;
                        }

                        $trailer = $request->get('trailer');
                        if ($trailer) {
                            $shipment->trailer = $trailer;
                        }
                        $shipment->save();

                        $history = new ShipmentHistory();
                        $history->shipment_id  = $shipment->id;
                        $history->status_id    = $shipment->status_id;
                        $history->agency_id    = $request->get('agency_id');
                        $history->operator_id  = $operatorId;
                        $history->save();
                    }
                }

                //MASSIVE PRINT ON SHIPMENTS MENU
                else {

                    $ids = $request->id;
                    $ids = empty($ids) ? [] : $ids;
                    $shipments = Shipment::whereIn('id', $ids);

                    if ($request->sortBy == 'picking-order') {
                        $shipments = $shipments->orderByRaw(DB::raw('FIELD(id,"' .  implode('","', $request->id) . '")'));
                    }

                    $shipments = $shipments->orderBy('recipient_zip_code', 'asc')->get();

                    $uniqueOperators = $shipments->groupBy('operator_id')->toArray();

                    if (count($uniqueOperators) == 1) {
                        $operatorId = array_keys($uniqueOperators);
                        $operatorId = @$operatorId[0];
                        $operator = User::whereId($operatorId)->first();
                    } else {
                        $operator = new User();
                        $operator->name = 'Vários Operadores';
                    }

                    $shipmentsGrouped = $shipments->groupBy('operator_id');

                    $date = 'Várias Datas';
                }
            }

            if ($shipmentsGrouped->isEmpty()) {
                return Redirect::back()->with('error', 'Não existem envios para listar.');
            }

            $params = [
                'code' => '',
                'date' => $date
            ];

            return Shipment::printDeliveryMap($shipmentsGrouped, $params, 'I');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * Print shipments cargo manifest
     *
     * @param type $shipmentId
     * @return type
     */
    public function cargoManifest(Request $request, $groupBy = 'customers')
    {
        // '' - not grouped;
        try {
            $ids = Shipment::getIdsFromFilters($request);
            return Shipment::printShipmentsCargoManifest($ids, 'I', $groupBy);
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * Print itenerary summary
     *
     * @param type $shipmentId
     * @return type
     */
    public function itenerary(Request $request, $shipmentId)
    {
        try {
            return Shipment::printIteneraryManifest([$shipmentId]);
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * Print shipments cargo manifest
     *
     * @param type $shipmentId
     * @return type
     */
    public function coldManifest(Request $request, $groupByCustomer = 1)
    {
        try {
            $temperature = $request->get('temperature', 21);
            $humidity    = $request->get('humidity', 15);
            return Shipment::printShipmentsColdManifest($request->id, 'I', $groupByCustomer, $temperature, $humidity);
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * Print shipments summary
     *
     * @param type $shipmentId
     * @return type
     */
    public function summary(Request $request, $groupByCustomer = 0)
    {
        try {
            $ids = Shipment::getIdsFromFilters($request);
            return Shipment::printShipments($ids, 'I', $groupByCustomer);
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * Print shipments summary
     *
     * @param type $shipmentId
     * @return type
     */
    public function goodsManifest(Request $request, $groupByCustomer = 1)
    {
        try {
            $ids = Shipment::getIdsFromFilters($request);
            return Shipment::printGoodsManifest($ids, 'date', 'I', $groupByCustomer);
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get POD
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getPod($id)
    {

        try {
            $shipment = Shipment::filterMyAgencies()->findOrfail($id);

            $webservice = new Base();
            $url = $webservice->getPodUrl($shipment);

            header('Content-type: image/png');
            echo file_get_contents($url);
            exit;
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }
}
