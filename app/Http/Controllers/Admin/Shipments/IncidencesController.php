<?php

namespace App\Http\Controllers\Admin\Shipments;

use App\Models\AddressBook;
use App\Models\Agency;
use App\Models\BroadcastPusher;
use App\Models\CacheSetting;
use App\Models\IncidenceResolutionType;
use App\Models\IncidenceType;
use App\Models\Provider;
use App\Models\Service;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use App\Models\ShipmentIncidenceResolution;
use App\Models\ShippingStatus;
use App\Models\User;
use App\Models\Webservice\Base;
use Html, Cache, Response, Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Mail;

class IncidencesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'incidences';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',incidences']);
        validateModule('incidences');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->ordered()
            ->isCollection(false)
            ->pluck('name', 'id')
            ->toArray();

        $operators = User::listOperators(User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->isOperator()
            ->orderBy('source', 'asc')
            ->orderBy('name', 'asc')
            ->get(['source', 'id', 'name', 'vehicle', 'provider_id']), Auth::user()->isAdmin() ? true : false);

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
            ->pluck('name', 'id')
            //->ordered()
            ->toArray();

        $data = compact(
            'services',
            'operators',
            'providers',
            'incidences'
        );

        return $this->setContent('admin.shipments.incidences.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $shipmentId) {

        $action = 'Adicionar Resolução de Incidência';

        $shipment = Shipment::findOrFail($shipmentId);

        $resolution = new ShipmentIncidenceResolution();

        $formOptions = array('route' => array('admin.shipments.incidences.store', $shipment->id), 'method' => 'POST', 'class' => 'form-incidence-resolution');

        $resolutionsTypes = IncidenceResolutionType::with('status')->ordered();

        if($shipment->webservice_method != 'enovo_tms' && in_array($shipment->webservice_method, ShipmentIncidenceResolution::AVAILABLE_PROVIDERS)) {
            $resolutionsTypes = $resolutionsTypes->where('available_methods', 'like', '%"'.$shipment->webservice_method.'"%');
        }

        $resolutionsTypes = $this->listResolutionsTypes($resolutionsTypes->get());

        if($request->has('history')) {
            $history = ShipmentHistory::with('incidence')
                ->where('shipment_id', $shipmentId)
                ->where('id', $request->history)
                ->first();

            $resolution->shipment_history_id = $history->id;
            $resolution->history = $history;
        } else {
            $incidences = $this->listIncidences(ShipmentHistory::with('incidence')
                ->where('shipment_id', $shipmentId)
                ->where('status_id', ShippingStatus::INCIDENCE_ID)
                //->where('resolved', 0)
                ->orderBy('created_at', 'desc')
                ->get());
        }

        $data = compact(
            'action',
            'formOptions',
            'resolution',
            'shipment',
            'resolutionsTypes',
            'incidences',
            'method'
        );

        return view('admin.shipments.incidences.edit', $data)->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $shipmentId) {
        return $this->update($request, $shipmentId);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($shipmentId, $id) {

        $action = 'Editar Resolução de Incidência';

        $resolution = ShipmentIncidenceResolution::where('shipment_id', $shipmentId)
            ->whereId($id)
            ->firstOrFail();

        $shipment = $resolution->shipment;

        $formOptions = array('route' => array('admin.shipments.incidences.update',$resolution->shipment_id, $resolution->id), 'class' => 'form-incidence-resolution', 'method' => 'PUT');

        $resolutionsTypes = IncidenceResolutionType::with('status')->ordered();

        if(in_array($shipment->webservice_method, ShipmentIncidenceResolution::AVAILABLE_PROVIDERS)) {
            $resolutionsTypes = $resolutionsTypes->where('available_methods', 'like', '%"'.$shipment->webservice_method.'"%');
        }

        $resolutionsTypes = $this->listResolutionsTypes($resolutionsTypes->get());

        $incidences = $this->listIncidences(ShipmentHistory::with('incidence')
            ->where('shipment_id', $shipmentId)
            ->where('status_id', ShippingStatus::INCIDENCE_ID)
            ->where('resolved', 0)
            ->orderBy('created_at', 'desc')
            ->get());

        $data = compact(
            'action',
            'formOptions',
            'resolution',
            'shipment',
            'resolutionsTypes',
            'incidences'
        );

        return view('admin.shipments.incidences.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $shipmentId, $id = null) {

        $input = $request->all();
        $input['send_email']   = $request->get('send_email', false);
        $input['set_resolved'] = $request->get('set_resolved', false);

        $resolution = ShipmentIncidenceResolution::firstOrNew(['shipment_id' => $shipmentId, 'id' => $id]);
        $resolution->fill($input);
        $resolution->operator_id = Auth::user()->id;

        $shipment = Shipment::withTrashed()
            ->with('expenses')
            ->findOrFail($shipmentId);

        $shipmentIncidencesResolutions = ShipmentIncidenceResolution::with('operator', 'resolution')
            ->where('shipment_id', $shipmentId)
            ->orderBy('id', 'desc')
            ->get();

        if($resolution->exists) {
            $result = $resolution->save();
        } else {
            $result = $resolution->setCode();
        }

        if(@$input['submit_webservice']) {
            try {
                $webservice = new Base();

                if(in_array($shipment->webservice_method, ['envialia', 'tipsa']) && $resolution->resolution_type_id != 1) {

                    //grava resposta com a solução real
                    $webservice->submitIncidenceResolution($resolution->id, $resolution);

                    //adiciona resolução livre com os dados
                    $resolutionDescription = new ShipmentIncidenceResolution();
                    $resolutionDescription->fill($resolution->toArray());
                    $resolutionDescription->resolution_type_id = 1;
                    $resolutionDescription->obs = '#'.@$resolution->type->name.'# - '.$resolutionDescription->obs;
                    $webservice->submitIncidenceResolution($resolution->id, $resolutionDescription);
                } else {
                    $webservice->submitIncidenceResolution($resolution->id, $resolution);
                }

                $resolution->submited_at = date('Y-m-d H:i:s');
                $resolution->save();

            } catch (\Exception $e) {
                return Response::json([
                    'result'   => false,
                    'feedback' => $e->getMessage(),
                ]);
            }
        }

        if(!$result) {
            return Response::json([
                'result'   => false,
                'feedback' => 'Erro ao gravar a resolução de incidência.',
                'html'     => ''
            ]);
        }


        //change shipment status
        $statusId = @$resolution->type->status_id;
        $customStatusId = @$resolution->type->custom_status_id;
        if($input['set_resolved'] && empty($statusId)) {

        } elseif(!empty($statusId)) {

            $statusId = @$customStatusId[config('app.source')] ? $customStatusId[config('app.source')] : $statusId;

            $history = $resolution->history;
            $history->resolved = 1;
            $history->save();

            $history = new ShipmentHistory();
            $history->shipment_id = $shipment->id;
            $history->status_id   = $statusId;
            $history->save();

            $shipment->status_id = $history->status_id;
            $shipment->save();
        }

        //Notify intervenients
        if(1 || $request->get('notify', false)) {
            $resolution->setNotification(BroadcastPusher::getGlobalChannel(), 'Resposta à incidência do envio ' . $shipment->tracking_code);
        }


        if($input['send_email']) {
            $emails = validateNotificationEmails($input['email']);

            if(!empty($emails['error'])) {
                return Response::json([
                    'result'   => true,
                    'feedback' => 'Envio gravado com sucesso. Não foi possível enviar e-mail para '.implode(',', $emails['error'])
                ]);
            }

            if(!empty($emails['valid'])) {

                AddressBook::storeEmails($emails['valid']);

                try {
                    Mail::send(transEmail('emails.shipments.incidence_solution', $shipment->recipient_country), compact('input', 'shipment', 'resolution'), function ($message) use ($input, $emails, $shipment) {

                        $prefix = $shipment->tracking_code;
                        if($shipment->hasSync() && in_array($shipment->webservice_method, ShipmentIncidenceResolution::AVAILABLE_PROVIDERS)) {
                            $prefix = $shipment->provider_tracking_code;
                        }

                        $message->to($emails['valid']);
                        $message->subject('Resolução de Incidência - Envio #' . $prefix );
                    });
                } catch (\Exception $e) {}
            }
        }

        return Response::json([
            'result'   => true,
            'feedback' => 'Resolução de incidência adicionada com sucesso.',
            'html'     => view('admin.shipments.shipments.partials.show.incidence_resolution_table', compact('shipment', 'shipmentIncidencesResolutions'))->render()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($shipmentId, $id) {

        $result = ShipmentIncidenceResolution::where('shipment_id', $shipmentId)
            ->where('id', $id)
            ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a resolução de incidência.');
        }

        return Redirect::route('admin.shipments.index')->with('success', 'Resolução de Incidência removida com sucesso.');
    }
    
    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable(Request $request) {

        $minId = (int) CacheSetting::get('shipments_limit_search');

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

        $user = Auth::user();
        if($user->isAdmin()) {
            $myAgencies = $allAgencies->filter(function($item){
                return $item->source == config('app.source');
            })->pluck('id')->toArray();

        } else {
            $myAgencies = $user->agencies;
        }

        $agencies = $allAgencies->groupBy('id')->toArray();


        $incidences = IncidenceType::remember(config('cache.query_ttl'))
            ->cacheTags(IncidenceType::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->pluck('name', 'id')
            ->toArray();

        $resolutionsTypes = IncidenceResolutionType::remember(config('cache.query_ttl'))
            ->cacheTags(IncidenceResolutionType::CACHE_TAG)
            ->pluck('name', 'id')
            ->toArray();

        $bindings = [
            'shipments.tracking_code',
            'shipments.provider_tracking_code',
            'shipments.reference',
            'shipments.agency_id',
            'shipments.sender_agency_id',
            'shipments.recipient_agency_id',
            'shipments.sender_name',
            'shipments.sender_address',
            'shipments.sender_zip_code',
            'shipments.sender_city',
            'shipments.recipient_name',
            'shipments.recipient_address',
            'shipments.recipient_zip_code',
            'shipments.recipient_city',
            'shipments.service_id',
            'shipments.provider_id',
            'shipments.status_id',
            'shipments.volumes',
            'shipments.weight',
            'shipments.obs',
            'shipments.date',
            'shipments.charge_price',
            'shipments_history.id',
            'shipments_history.shipment_id',
            'shipments_history.id as history_id',
            'shipments_history.operator_id',
            'shipments_history.incidence_id',
            'shipments_history.obs as incidence_obs',
            'shipments_history.resolved',
            'shipments_history.created_at',
        ];

        $data = ShipmentHistory::with('resolutions')
            ->join('shipments', function ($join) {
                $join->on('shipments_history.shipment_id', '=', 'shipments.id');
            })
            ->where('shipments_history.status_id', ShippingStatus::INCIDENCE_ID)
            ->whereIn('shipments.agency_id', $myAgencies)
            ->whereNull('shipments.deleted_at');


        //limit search
        $value = $request->limit_search;
        if($request->has('limit_search') && !empty($value)) {
            $data = $data->where('shipments_history.shipment_id', '>=', $minId);
        }

        //filter resolved
        $value = $request->resolved;
        if($request->has('resolved')) {
            if ($value == 0) {
                $data = $data->where(function($q){
                    $q->where('resolved', '0');
                    $q->orWhereNull('resolved');
                });
            } elseif ($value == 1) {
                $data = $data->whereNotNull('resolved');
            }
        }

        //filter solution
        $value = $request->solution;
        if($request->has('solution')) {
            if ($value == 0) {
                $data = $data->has('resolutions', '=', 0);
            } elseif ($value == 1) {
                $data = $data->has('resolutions');
            }
        }

        //filter incidence
        $value = $request->get('incidence');
        if(!empty($value)) {
            $data = $data->whereIn('incidence_id', $value);
        }

        //filter service
        $value = $request->get('service');
        if(!empty($value)) {
            $data = $data->whereIn('service_id', $value);
        }

        //filter provider
        $value = $request->get('provider');
        if(!empty($value)) {
            $data = $data->whereIn('provider_id', $value);
        }

        //filter operator
        $value = $request->operator;
        if($request->has('operator')) {
            $data = $data->whereIn('shipments_history.operator_id', $value);
        }

        //filter customer
        $value = $request->get('customer');
        if(!empty($value)) {
            $data = $data->where('customer_id', $value);
        }

        //filter date min
        $dtMin = $request->get('date_min');
        if($request->has('date_min')) {
            $dtMax = $dtMin;
            if($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }
            $data = $data->whereBetween('shipments_history.created_at', [$dtMin.' 00:00:00', $dtMax.' 23:59:59']);
        }

        $data = $data->select($bindings);

        return Datatables::of($data)
            ->edit_column('id', function($row) use($agencies) {
                return view('admin.shipments.incidences.datatables.tracking', compact('row', 'agencies'))->render();
            })
            ->edit_column('sender_name', function($row) {
                return view('admin.shipments.shipments.datatables.sender', compact('row'))->render();
            })
            ->edit_column('recipient_name', function($row) {
                return view('admin.shipments.shipments.datatables.recipient', compact('row'))->render();
            })
            ->edit_column('service_id', function($row) use($agencies, $servicesList, $providersList) {
                return view('admin.shipments.incidences.datatables.service', compact('row', 'agencies', 'servicesList', 'providersList'))->render();
            })
            ->edit_column('volumes', function($row) use($servicesList) {
                return view('admin.shipments.incidences.datatables.volumes', compact('row', 'servicesList'))->render();
            })
            ->edit_column('created_at', function($row) use($operatorsList) {
                return view('admin.shipments.incidences.datatables.incidence_date', compact('row', 'operatorsList'))->render();
            })
            ->add_column('reason', function($row) use($incidences) {
                return view('admin.shipments.incidences.datatables.reason', compact('row', 'incidences'))->render();
            })
            ->add_column('solution', function($row) use($resolutionsTypes) {
                return view('admin.shipments.incidences.datatables.solution', compact('row', 'resolutionsTypes'))->render();
            })
            ->add_column('resolved', function($row) {
                return view('admin.shipments.incidences.datatables.resolved', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.shipments.incidences.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Mass resolve incidences
     * GET /admin/shipments/selected/update
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massResolve(Request $request) {

        $ids = explode(',', $request->ids);

        try {
            ShipmentHistory::whereIn('id', $ids)->update(['resolved' => 1]);

            return Redirect::back()->with('success', 'Incidências marcadas como resolvidas.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erro ao alterar dados: ' . $e->getMessage());
        }
    }

    /**
     * Mark incidence as resolved
     * @param $shipmentId
     * @param $id
     */
    public function resolveIncidence(Request $request, $shipmentId) {

        $resolved  = $request->get('resolved', false) == 'false' ? false : true;

        $historyId = $request->get('historyId');

        $history = ShipmentHistory::where('shipment_id', $shipmentId)
            ->where('id', $historyId)
            ->firstOrFail();

        $history->resolved = $resolved;
        $result = $history->save();

        if (!$result) {
            return Response::json([
                'result'   => false,
                'feedback' => 'Ocorreu um erro ao marcar a incidência como resolvida.'
            ]);
        }

        return Response::json([
            'result'   => true,
            'feedback' => 'Incidência marcada como resolvida com sucesso.'
        ]);
    }

    /**
     * Prepare incidences history list
     *
     * @param $historyCollection
     * @return array
     */
    public function listIncidences($historyCollection) {

        $arr = [];

        foreach ($historyCollection as $history) {

            $text = $history->created_at . ' - ' . (@$history->incidence->name ? $history->incidence->name : 'Sem detalhe');

            $arr[$history->id] = $text;
        }

        return $arr;
    }

    /**
     * Return list of services with data attributes
     *
     * @param type $allServices
     * @return type
     */
    public function listResolutionsTypes($allTypes) {

        //$resultArr[] = ['value' => '', 'display'=> ''];
        foreach ($allTypes as $type) {

            $statusName = @$type->status->name;
            //$statusName = @$type->custom_status_id[config('app.source')] ? $type->custom_status_id[config('app.source')] : $statusName;

            $resultArr[] = [
                'value'       => $type->id,
                'display'     => $type->name,
                'data-status' => $statusName,
            ];
        }
        return $resultArr;
    }
}
