<?php

namespace App\Http\Controllers\Admin\Traceability;

use App\Models\IncidenceType;
use App\Models\Service;
use App\Models\Traceability\ShipmentTraceability;
use App\Models\ShippingExpense;
use App\Models\ShipmentExpense;
use App\Models\ShipmentPackDimension;
use App\Models\Vehicle;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use App\Models\ShippingStatus;
use App\Models\Agency;
use App\Models\Customer;
use App\Models\Provider;
use App\Models\Traceability\Event;
use App\Models\Traceability\Location;
use App\Models\TraceabilityPendingShipment;
use App\Models\User;
use App\Models\Webservice\Delnext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Jenssegers\Date\Date;
use Yajra\Datatables\Facades\Datatables;
use Html, DB, Auth, Response, Setting;

class TraceabilityController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'traceability';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',traceability']);
        validateModule('traceability');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        TraceabilityPendingShipment::truncate();  //delet all data from database when open page traceability

        $events = $this->listEvents(Event::with('agency')
            ->filterAgencies()
            ->ordered()
            ->get());

        if (Auth::user()->isAdmin()) {
            $operators = ['' => ''] + User::listOperators(User::remember(config('cache.query_ttl'))
                    ->cacheTags(User::CACHE_TAG)
                    ->filterAgencies()
                    ->ignoreAdmins()
                    ->where('active', 1)
                    ->orderBy('name', 'asc')
                    ->get(['source', 'id', 'name', 'vehicle', 'provider_id']), true);
            $operators += ['-1' => '- Manter operador atual -'];
        } else {

            $operators = ['' => ''] + User::listOperators(User::remember(config('cache.query_ttl'))
                    ->cacheTags(User::CACHE_TAG)
                    ->where('source', config('app.source'))
                    ->isOperator()
                    ->where('active', 1)
                    ->orderBy('name', 'asc')
                    ->get(['source', 'id', 'name', 'vehicle', 'provider_id']), false);
            $operators += ['-1' => '- Manter operador atual -'];
        }

        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->where('source', config('app.source'))
            ->filterAgencies()
            ->orderBy('code', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
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

        $status = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingStatus::CACHE_TAG)
            ->filterSources()
            ->isVisible()
            ->where('is_traceability', 1)
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $vehicles = Vehicle::listVehicles();
        $trailers = Vehicle::listVehicles(true);

        $shipments = [];


        $data = compact(
            'defaultEvent',
            'events',
            'locations',
            'status',
            'agencies',
            'operators',
            'providers',
            'shipments',
            'incidences',
            'vehicles',
            'trailers'
        );

        return $this->setContent('admin.traceability.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $codes = $request->code;
        $codes = empty($codes) ? [] : $codes;
        $agencyId = $request->agency_id;

        $shipments = Shipment::whereIn('tracking_code', $codes)->get();

        if ($shipments->isEmpty()) {
            return Redirect::back()->with('error', 'Não foram selecionados envios.');
        }

        foreach ($shipments as $shipment) {

            $shipment->status_id = $request->status_id;

            if ($request->operator_id && $request->operator_id > 0) {
                $shipment->operator_id = $request->operator_id;
            }

            if ($request->vehicle) {
                $shipment->vehicle = $request->vehicle;
            }

            if ($request->trailer) {
                $shipment->trailer = $request->trailer;
            }

            $shipment->save();

            $history = new ShipmentHistory();
            $history->shipment_id = $shipment->id;
            $history->operator_id = $shipment->operator_id;
            $history->status_id   = $shipment->status_id;
            $history->agency_id   = $agencyId;
            $history->obs         = $request->obs;
            $history->save();

            $history->sendEmail(false, false, true);
        }

        return Redirect::back()->with('success', 'Estado dos envios alterado com sucesso.');
    }


    // get all ids to print
    public function printLavelsDevolutions()
    {

        $pendingShipments = TraceabilityPendingShipment::with(['shipment' => function ($q) { //cria relação com a tabela shipments
            $q->select(['id', 'reference']);
        }])->get();

        $delnextParcelIds = $pendingShipments->pluck('shipment.reference')->toArray();  //Vai buscar só o campo reference da tabela shipment
        $delnextParcelIds = array_filter($delnextParcelIds); //limpa os valores nulos do array
        $delnextSendBody  = implode(",", $delnextParcelIds);


        //ids dos envios 
        $delnextIds = $pendingShipments->pluck('shipment.id')->toArray();
        
        //assim que é devolvido a incidência fica resolvida
        DB::table('shipments_history')
            ->where('status_id', ShippingStatus::INCIDENCE_ID)
            ->whereIn('shipment_id',  $delnextIds)
            ->update(['resolved' => 1]);

        $data = Delnext::getDevolutionLabels($delnextSendBody);


        TraceabilityPendingShipment::truncate();  //delet all data from database

        $pdfdecod = base64_decode($data);
        header('Content-Type: application/pdf');
        echo ($pdfdecod);
        exit();
    }

    /**
     * Get shipment data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function listShipments(Request $request)
    {

        $agencyId   = $request->agency_id;
        $readPoint  = $request->read_point;
        $dtMin      = $request->date_min;
        $dtMax      = $request->date_max;

        $finalStatus = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingStatus::CACHE_TAG)
            ->filterSources()
            ->isVisible()
            ->where('is_final', 1)
            ->pluck('id')
            ->toArray();

        $shipments = Shipment::with('status', 'service')
            ->whereNotIn('status_id', $finalStatus)
            ->where('is_collection', 0)
            ->whereBetween('date', [$dtMin, $dtMax])
            ->whereDoesntHave('traceability', function ($q) use ($readPoint, $agencyId) {
                $q->where('read_point', $readPoint);
                $q->where('agency_id', $agencyId);
            });

        if ($readPoint == 'in') {
            $shipments = $shipments->where('recipient_agency_id', $agencyId)
                ->where('sender_agency_id', '<>', $agencyId);
        } elseif ($readPoint == 'out') {
            $shipments = $shipments->where('sender_agency_id', $agencyId)
                ->where('recipient_agency_id', '<>', $agencyId);
        }

        $shipments = $shipments->get();

        $totalShipments = $shipments->count();

        $status = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingStatus::CACHE_TAG)
            ->filterSources()
            ->isVisible()
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();



        return Response::json([
            'result'         => true,
            'totalShipments' => $totalShipments,
            'html'           => view('admin.traceability.partials.pending_shipments', compact('shipments', 'status'))->render()
        ]);
    }

    /**
     * Get shipment data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getShipment(Request $request)
    {

        $result        = true;
        $alreadyReaded = false;
        $autosave    = $request->get('autosave', false);
        $autosave    = $autosave == 'false' ? false : true;
        $providerTrk = $request->get('provider_trk', false);
        $providerTrk = $providerTrk == 'false' ? false : true;
        $statusId    = $request->status;
        $eventId     = $request->event;
        $locationId  = $request->location;
        $incidenceId = $request->incidence ? $request->incidence : null;
        $readPoint   = $request->read_point;
        $agencyId    = $request->agency;
        $operatorId  = $request->operator;
        $vehicle     = $request->vehicle;
        $trailer     = $request->trailer;
        $code        = trim($request->code);
        $readAll     = false;
        $checkList   = empty($request->check_list) ? [] : explode(',', $request->check_list);
        $finalStatus = ShippingStatus::where('is_final',1)->pluck('id')->toArray();

        $allowedRefCustomers = [
            '16791', //asfalto
            '16850', //fozpost
            '16851', //nmx
            '16852', //tortuga
            '16925', //vasco santos
            '17007', //aveirofast
            '17010', //sentido suposto
            '17719', //gigantexpress
            '17791', //FVP transportes
            '17818', //TCM
            '67', //MRC
            '17955', //FPRM
            '45', //Utiltrans
            '2', //TRP
            '8', //Transportes Nunes
            '325', //Transbag
            '163', //delnext jamelao
            '17928', // Transportes de Braga - delnext
            '17913', //transportes de braga - moove
        ];

        $sourceAgencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterSource()
            ->pluck('id')
            ->toArray();


        //get trk code
        $trkCode = $code;
        $originalTrk = $code;
        $providerName = '';

        if (strlen($code) == 18) { //codigos enovo
            $codeParts = [
                strlen($code) == 18 ? substr($code, 0, 12) : $code, //trk
                strlen($code) == 18 ? (int)substr($code, -3) : 'all' //volume
            ];
            $trkCode   = @$codeParts[0];
        } elseif (strlen($code) == 15) { //codigos enovo antigos
            $codeParts = [
                strlen($code) == 15 ? substr($code, 0, 12) : $code, //trk
                strlen($code) == 15 ? (int)substr($code, -3) : 'all' //volume
            ];
            $trkCode   = @$codeParts[0];
        } elseif (strlen($trkCode) == 20) { // DSV Labels
            $providerTrk  = true;
        } elseif (strlen($trkCode) == 26) { //sending
            $providerTrk  = true;
            $providerName = 'sending';
            $trkCode      = substr($trkCode, 0, 12);
        } elseif (strlen($trkCode) == 28) { //envialia
            $providerTrk = true;
            $trkCode = substr($trkCode, 6, 10);
        }

        $readedTrk = $trkCode;


        //leitura de volumes nativos do sistema
        $volumeNumber = isset($codeParts[1]) ? $codeParts[1] : 'all';
        $readAll      = $volumeNumber == 'all' ? true : false;

        //obtem o numero do volume lido
        if($providerTrk) {
            //leitura de volumes do parceiro
            if($providerName == 'sending') {
                $volumeNumber = substr($originalTrk, -3);
                $readAll = false;
            }
        }

        $shipment = Shipment::select(['id', 'tracking_code', 'provider_tracking_code', 'reference', 'sender_name', 'provider_id', 'volumes', 'status_id', 'operator_id', 'agency_id', 'sender_agency_id', 'recipient_agency_id', 'weight', 'volumes', 'charge_price', 'total_price_for_recipient', 'customer_id'])
            ->whereNotIn('status_id', $finalStatus)
            ->where(function ($q) use ($trkCode, $allowedRefCustomers, $providerTrk, $originalTrk) {

                if ($providerTrk) {
                    $q->where(function ($q) use ($trkCode, $originalTrk) {
                        $q->where('reference', $trkCode);
                        $q->orWhere('provider_tracking_code', 'like', '%' . $trkCode . '%');
                        $q->orWhere('tracking_code', $trkCode);
                    });
                } else {
                    $q->where('tracking_code', $trkCode);
                }

                $q->orWhere(function ($q) use ($trkCode, $allowedRefCustomers) {
                    $q->where('reference', $trkCode);
                    $q->whereIn('customer_id', $allowedRefCustomers);
                });

                if (config('app.source') == 'corridadotempo') {
                    $q->orWhere('reference2', 'like', '%' . $originalTrk . '%'); //permitir ler campo referencia 2
                }

                if (config('app.source') == 'okestafetas') {
                    $q->orWhere('provider_tracking_code', 'like', '%' . $trkCode . '%'); //permitir ler etiquetas enviadas para o fornecedor delnext
                }

                /*$q->orWhere('provider_tracking_code', 'like', '%' . $trkCode . '%');
                $q->orWhere('reference', 'like', '%' . $trkCode . '%');
                $q->orWhere('reference2', 'like', '%' . $trkCode . '%');*/
            })
            ->where(function ($q) use ($sourceAgencies) {
                $q->whereIn('agency_id', $sourceAgencies);
                $q->orWhereIn('sender_agency_id', $sourceAgencies);
                $q->orWhereIn('recipient_agency_id', $sourceAgencies);
            })
            ->first();

        //não encontrou o envio mas tem o checkbox ativo para ler codigos de fornecedor.
        //Tenta encontrar na base de dados de dimensões a partir do codigo lido
        if (!$shipment && $providerTrk) {

            $packDimension = ShipmentPackDimension::whereHas('shipment')
                ->where('barcode', $originalTrk)
                ->orWhere('barcode2', $originalTrk)
                ->orWhere('barcode3', $originalTrk)
                ->first();

            if($packDimension) {
                $shipment = $packDimension->shipment;
                $readAll = false;
                $volumeNumber = $packDimension->pack_no;
            }
        }

        if (!$shipment) {
            $result   = false;
            $shipment = new Shipment();
            $shipment->tracking_code = $trkCode;
        }

        if ($readAll && count($checkList) < $shipment->volumes) {

            //change shipment status
            if ($autosave) {
                $shipment->status_id = $statusId;

                if (!empty($operatorId) && $operatorId > 0) {
                    $shipment->operator_id = $operatorId;
                }

                if (!empty($vehicle)) {
                    $shipment->vehicle = $vehicle;
                }

                if (!empty($trailer)) {
                    $shipment->trailer = $trailer;
                }

                if ($shipment->status_id == ShippingStatus::DEVOLVED_ID) {
                    $this->processDevolution($shipment->id);
                }

                $shipment->save();

                $history = new ShipmentHistory();
                $history->shipment_id  = $shipment->id;
                $history->status_id    = $shipment->status_id;
                $history->agency_id    = $agencyId;
                $history->incidence_id = $incidenceId;
                $history->obs          = $request->obs;
                $history->operator_id  = $shipment->operator_id;
                $history->save();
            }


            //verifica se o envio é da delnext e guarda na tabela temporaria
            if ($statusId == ShippingStatus::DEVOLVED_ID) {
                $delnextCustomer = Customer::where('vat', 'like', '%513419578%')->first(['id']);
                if (!empty($delnextCustomer)) {
                    if ($shipment->customer_id == $delnextCustomer->id) {
                        $traceabilityPendingShipment = TraceabilityPendingShipment::firstOrNew([
                            'shipment_id' => $shipment->id
                        ]);

                        $traceabilityPendingShipment->shipment_id = $shipment->id;
                        $traceabilityPendingShipment->save();
                    }
                }
            }



            for ($i = 1; $i <= $shipment->volumes; $i++) {
                $checkList[] = $i;
                $traceability = new ShipmentTraceability();
                $traceability->shipment_id = $shipment->id;
                $traceability->operator_id = Auth::user()->id;
                $traceability->event_id    = $eventId;
                $traceability->read_point  = $readPoint;
                $traceability->agency_id   = $agencyId;
                $traceability->location_id = $locationId;
                $traceability->vehicle     = $vehicle;
                $traceability->trailer     = $trailer;
                $traceability->volume      = str_pad($volumeNumber, 3, '0', STR_PAD_LEFT);
                $traceability->barcode     = $providerTrk ? $originalTrk : null;
                $traceability->save();
            }
        } else {
            if ($shipment->exists && !in_array($volumeNumber, $checkList)) {
                $checkList[] = $volumeNumber;

                if ($autosave && count($checkList) == 1) { //adiciona apenas no 1º volume picado

                    //change shipment status
                    $shipment->status_id = $statusId;
                    if (!empty($operatorId) && $operatorId > 0) {
                        $shipment->operator_id = $operatorId;
                    }

                    if (!empty($vehicle)) {
                        $shipment->vehicle = $vehicle;
                    }

                    if (!empty($trailer)) {
                        $shipment->trailer = $trailer;
                    }

                    if ($shipment->status_id == ShippingStatus::DEVOLVED_ID) {
                        $this->processDevolution($shipment->id);
                    }

                    $shipment->save();

                    $history = new ShipmentHistory();
                    $history->shipment_id  = $shipment->id;
                    $history->status_id    = $shipment->status_id;
                    $history->agency_id    = $agencyId;
                    $history->incidence_id = $incidenceId;
                    $history->obs          = $request->obs;
                    $history->operator_id  = $shipment->operator_id;
                    $history->save();
                }

                //store traceability
                $traceability = new ShipmentTraceability();
                $traceability->shipment_id = $shipment->id;
                $traceability->operator_id = Auth::user()->id;
                $traceability->read_point  = $readPoint;
                $traceability->agency_id   = $agencyId;
                $traceability->vehicle     = $vehicle;
                $traceability->trailer     = $trailer;
                $traceability->volume      = str_pad($volumeNumber, 3, '0', STR_PAD_LEFT);
                $traceability->barcode     = $providerTrk ? $originalTrk : null;
                $traceability->save();
            } else {
                $alreadyReaded = true;
            }
        }

        //send sms or email
        if (@$history) {
            $history->sendEmail(false, false, true);
        }

        if ($readAll) {
            $shipment->counter    = $shipment->volumes;
            $shipment->check_list = implode(',', $checkList);
        } else {
            $shipment->counter    = count($checkList);
            $shipment->check_list = implode(',', $checkList);
        }

        //verifica se há dados da delnext
        $hasDelnext = TraceabilityPendingShipment::exists();

        //regista na bd de fails caso seja um código nao reconhecido
        if(!$result) {
            DB::table('traceability_fails')->insert([
                'barcode'       => $readedTrk,
                'read_point'    => $readPoint,
                'operator_id'   => Auth::user()->id,
                'created_at'    => date('Y-m-d H:i:s')
            ]);
        }

        return Response::json([
            'result'        => $result,
            'trk'           => $shipment->tracking_code,
            'readedTrk'     => $readedTrk,
            'alreadyReaded' => $alreadyReaded,
            'allRead'       => $shipment->counter == $shipment->volumes,
            'totalRead'     => $shipment->counter,
            'hasDelnext'    => $hasDelnext,
            'volume'        => @$volumeNumber,
            'html'          => view('admin.traceability.partials.list_item', compact('shipment', 'readedTrk'))->render(),
        ]);
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatableShipments(Request $request)
    {

        $appMode = Setting::get('app_mode');

        //status
        $statusList = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->get(['id', 'name', 'color', 'is_final']);
        $statusList  = $statusList->groupBy('id')->toArray();

        //services
        $servicesList = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->get();
        $servicesList = $servicesList->groupBy('id')->toArray();

        //operator
        $operatorsList = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->get(['source', 'id', 'code', 'code_abbrv', 'name', 'vehicle', 'provider_id']);
        $operatorsList = $operatorsList->groupBy('id')->toArray();


        //providers
        $providersList = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->get();
        $providersList = $providersList->groupBy('id')->toArray();

        //agencies
        $allAgencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->withTrashed()
            ->get(['name', 'code', 'id', 'color', 'source']);
        $agencies = $allAgencies->groupBy('id')->toArray();

        $bindings = [
            'id', 'tracking_code', 'type', 'parent_tracking_code', 'children_tracking_code', 'children_type',
            'agency_id', 'sender_agency_id', 'recipient_agency_id',
            'service_id', 'provider_id', 'status_id', 'operator_id', 'customer_id',
            'sender_name', 'sender_zip_code', 'sender_city', 'sender_phone',
            'recipient_name', 'recipient_zip_code', 'recipient_city', 'recipient_phone', 'recipient_country',
            'obs', 'volumes', 'weight', 'total_price', 'date'
        ];

        $data = Shipment::filterAgencies()
            //->with('service', 'provider', 'status', 'operator', 'customer')
            ->whereHas('status', function ($q) {
                $q->where('is_final', 0);
            })
            ->get($bindings);

        //filter sender agency
        $value = $request->sender_agency;
        if ($request->has('sender_agency')) {
            $data = $data->whereIn('sender_agency_id', $value);
        }

        //filter provider
        $value = $request->provider;
        if ($request->has('provider')) {
            $data = $data->whereIn('provider_id', $value);
        }

        //filter operator
        $value = $request->operator;
        if ($request->has('operator')) {
            $data = $data->whereIn('operator_id', $value);
        }

        //filter status
        $value = $request->status;
        if ($request->has('status')) {
            $data = $data->whereIn('status_id', $value);
        }

        return Datatables::of($data)
            ->edit_column('service_id', function ($row) use ($agencies, $servicesList, $providersList) {
                return view('admin.shipments.shipments.datatables.service', compact('row', 'agencies', 'servicesList', 'providersList'))->render();
            })
            ->edit_column('id', function ($row) use ($agencies) {
                return view('admin.shipments.shipments.datatables.tracking', compact('row', 'agencies'))->render();
            })
            ->edit_column('sender_name', function ($row) {
                return view('admin.shipments.shipments.datatables.sender', compact('row'))->render();
            })
            ->edit_column('recipient_name', function ($row) {
                return view('admin.shipments.shipments.datatables.recipient', compact('row'))->render();
            })
            ->edit_column('status_id', function ($row) use ($statusList, $operatorsList) {
                return view('admin.shipments.shipments.datatables.status', compact('row', 'statusList', 'operatorsList'))->render();
            })
            ->edit_column('date', function ($row) use ($statusList) {
                return view('admin.shipments.shipments.datatables.date', compact('row', 'statusList'))->render();
            })
            ->edit_column('volumes', function ($row) use ($appMode) {
                return view('admin.shipments.shipments.datatables.volumes', compact('row', 'appMode'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.traceability.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Print delivery manifest
     * @param Request $request
     */
    public function deliveryMap(Request $request)
    {

        //status
        $statusList = ShippingStatus::remember(config('cache.query_ttl'))
            ->ordered()
            ->cacheTags(Service::CACHE_TAG)
            ->pluck('name', 'id')
            ->toArray();

        $activeStatusList = Setting::get('mobile_app_status_delivery') ? Setting::get('mobile_app_status_delivery') : ShippingStatus::OPERATORS_DELIVERY_DEFAULT_STATUS;

        //services
        $servicesList = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        //operator
        $operatorsList = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->isActive()
            ->isOperator()
            ->pluck('name', 'id')
            ->toArray();

        //providers
        $providersList = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'statusList',
            'servicesList',
            'operatorsList',
            'providersList',
            'activeStatusList'
        );

        return view('admin.traceability.modals.delivery_manifest', $data)->render();
    }

    /**
     * Process shipment devolution
     */
    public function processDevolution($shipmentId)
    {
        $expense = ShippingExpense::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->whereSource(config('app.source'))
            ->where('type', ShippingExpense::TYPE_CHARGE)
            ->first();

        ShipmentExpense::where('shipment_id', $shipmentId)
            ->where('expense_id', $expense->id)
            ->delete();

        ShipmentExpense::updateShipmentTotal($shipmentId);
    }


    /**
     * Assign provider code to trk code
     * @param Request $request
     */
    public function modalVinculateProviderTrk(Request $request)
    {

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->where('webservice_method', 'ctt_correios')
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        return view('admin.traceability.modals.assign_to_ctt_correios', compact('providers'))->render();
    }

    /**
     * Get shipment data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeVinculateProviderTrk(Request $request)
    {

        $action      = $request->get('action');
        $trk         = trim($request->trk);
        $providerTrk = trim($request->provider_trk);
        $providerId  = $request->provider_id;

        $trkLength = strlen($trk);

        if ($trkLength == 18 || $trkLength == 15) {
            $trk = substr($trk, 0, 12);
        }

        if ($action == 'desvinculate') {
            return $this->desvinculateProviderTrk($request, $trk);
        }

        $provider = Provider::findOrFail($providerId);

        $shipment = Shipment::where('tracking_code', $trk)->first();

        if (!@$shipment) {
            return Response::json([
                'result' => false,
                'feedback' => 'Envio não encontrado'
            ]);
        }

        if ($shipment->hasSync() && $shipment->webservice_method != $provider->webservice_method) {
            return Response::json([
                'result' => false,
                'feedback' => 'Erro: O envio está vinculado a outra rede. Desvincule e tente novamente.'
            ]);
        }

        $shipment->update([
            'provider_id'            => $provider->id,
            'webservice_method'      => $provider->webservice_method,
            'webservice_error'       => null,
            'provider_tracking_code' => $providerTrk,
            'submited_at'            => Date::now()
        ]);

        $html = '<tr>
                    <td>' . $provider->name . '</td>
                    <td>' . $trk . '</td>
                    <th>' . $providerTrk . '</th>
                    <th><i class="fas fa-trash-alt text-red btn-desvinculate" data-trk="' . $trk . '" data-trk="' . $providerTrk . '"></i></th>
                </tr>';

        return Response::json([
            'result' => true,
            'html'   => $html
        ]);
    }

    /**
     * Get shipment data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function desvinculateProviderTrk(Request $request, $trk)
    {

        $shipment = Shipment::where('tracking_code', $trk)->first();

        if (!@$shipment) {
            return Response::json([
                'result' => false,
                'feedback' => 'Envio não encontrado'
            ]);
        }

        $shipment->provider_tracking_code    = null;
        $shipment->provider_sender_agency    = null;
        $shipment->provider_recipient_agency = null;
        $shipment->webservice_method         = null;
        $shipment->submited_at               = null;
        $shipment->save();

        return Response::json([
            'result' => true,
            'html'   => 'Desvinculado com sucesso.'
        ]);
    }
    
    /**
     * Return agency locations array
     *
     * @param Request $request
     * @return void
     */
    public function getAgencyLocations(Request $request) {
        
        $locations = Location::where('agency_id', $request->agency)
                ->ordered()
                ->get();

        $locationsHtml = '<option value=""></option>';
        foreach($locations as $location) {
            $locationsHtml.= '<option value="'.$location->id.'" '.($request->location == $location->id ? 'selected' : '').'>'.$location->name.'</option>';
        }
       

        return response()->json($locationsHtml);
    }

    /**
     * List all events
     *
     * @param [type] $allEvents
     * @return void
     */
    public function listEvents($allEvents)
    {

        if ($allEvents->count() > 1) {
            $events[] = ['value' => '', 'display' => ''];
        } else {
            $events = [];
        }

        foreach ($allEvents as $event) {
            $events[] = [
                'value'         => $event->id,
                'display'       => $event->name,
                'data-action'   => $event->action,
                'data-agency'   => $event->agency_id,
                'data-location' => $event->location_id,
                'data-status'   => $event->status_id,
            ];
        }
        return $events;
    }


}
