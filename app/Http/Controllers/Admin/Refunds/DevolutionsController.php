<?php

namespace App\Http\Controllers\Admin\Refunds;

use App\Models\PackType;
use App\Models\Shipment;
use App\Models\Agency;
use App\Models\Provider;
use App\Models\ShipmentHistory;
use App\Models\ShippingStatus;
use App\Models\Customer;
use App\Models\Service;
use App\Models\User;
use App\Models\CacheSetting;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Response, View, Auth, Setting;

class DevolutionsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'devolutions';
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',devolutions']);
        validateModule('cod');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $agencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterAgencies()
            ->orderBy('code', 'asc')
            ->get());

        $operators = User::listOperators(User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->isOperator()
            ->ignoreAdmins()
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

        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'agencies',
            'operators',
            'providers',
            'services'
        );

        return $this->setContent('admin.refunds.devolutions.index', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function edit($id) {
//    }
    
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function update(Request $request, $id) {
//    }
    
    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable(Request $request) {

        //services
        $servicesList = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->get();
        $servicesList = $servicesList->groupBy('id')->toArray();

        //providers
        $providersList = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->get();
        $providersList = $providersList->groupBy('id')->toArray();

        $packTypes = PackType::remember(config('cache.query_ttl'))
            ->cacheTags(PackType::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'code')
            ->toArray();

        $bindings = [
            'shipments.id',
            'tracking_code',
            'provider_tracking_code',
            'sender_name',
            'sender_zip_code',
            'sender_city',
            'recipient_name',
            'recipient_zip_code',
            'recipient_city',
            'volumes',
            'kms',
            'weight',
            'volumetric_weight',
            'date',
            'delivery_date',
            'shipments.agency_id',
            'shipments.sender_agency_id',
            'shipments.recipient_agency_id',
            'payment_at_recipient',
            'total_price_for_recipient',
            'customer_id',
            'requested_by',
            'provider_id',
            'service_id',
            'zone',
            'shipments.status_id',
            'ignore_billing',
            'invoice_id',
            'invoice_doc_id',
            'devolution_conferred'
        ];

        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->get(['name', 'code', 'id', 'color', 'source']);

        $sourceAgencies = $agencies->filter(function ($item) { return $item->source == config('app.source'); })->pluck('id')->toArray();

        $agencies = $agencies->groupBy('id')->toArray();

        $data = Shipment::whereRaw('shipments.agency_id in (' . implode(',', $sourceAgencies).')')
            ->with(['provider' => function($q){
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Provider::CACHE_TAG);
                $q->select(['id', 'name', 'color']);
            }])
            ->with(['status' => function($q){
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(ShippingStatus::CACHE_TAG);
                $q->select(['id', 'name', 'color', 'is_final']);
            }])
            ->with(['customer' => function($q){
                $q->withTrashed();
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Customer::CACHE_TAG);
                $q->select(['id', 'code', 'name', 'contact_email', 'refunds_email', 'iban_refunds']);
            }])
            ->with(['last_history' => function($q){
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(ShipmentHistory::CACHE_TAG);
                $q->select(['shipment_id', 'created_at']);
            }])
            ->whereRaw('shipments.status_id = "'. ShippingStatus::DEVOLVED_ID.'"')
            ->select($bindings);

        //limit search
        $value = $request->limit_search;
        if($request->has('limit_search') && !empty($value)) {
            $minId = (int) CacheSetting::get('shipments_limit_search');
            if($minId) {
                $data = $data->where('id', '>=', $minId);
            }
        }

        //filter date min
        $dtMin = $request->get('date_min');
        if($request->has('date_min')) {
            $dtMax = $dtMin;
            if($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }
            if($request->has('date_unity') && !empty($request->has('date_unity'))) { //filter by shipment status date
                $dtMin = $dtMin . ' 00:00:00';
                $dtMax = $dtMax . ' 23:59:59';
                $statusId = $request->get('date_unity');

                $data = $data->whereHas('history',function($q) use($dtMin, $dtMax, $statusId) {
                    $q->where('status_id', $statusId)->whereBetween('created_at', [$dtMin, $dtMax]);
                });
            } else { //filter by shipment date
                $data = $data->whereBetween('date', [$dtMin, $dtMax]);
            }
        }

        //filter date min
        $dtMin = $request->get('dev_date_min');
        if($request->has('dev_date_min')) {
            $dtMax = $dtMin;
            if($request->has('dev_date_max')) {
                $dtMax = $request->get('dev_date_max');
            }

            $data = $data->whereHas('last_history', function($q) use($dtMin, $dtMax) {
                $q->whereBetween('date', [$dtMin, $dtMax]);
            });
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
            if(in_array('not-assigned', $value)) {
                $data = $data->where(function($q) use($value) {
                    $q->whereNull('operator_id');
                    $q->orWhereIn('operator_id', $value);
                });
            } else {
                $data = $data->whereIn('operator_id', $value);
            }
        }

        //filter agency
        $value = $request->sender_agency;
        if(!empty($value)) {
            $data = $data->whereIn('sender_agency_id', $value);
        }

        //filter recipient agency
        $value = $request->recipient_agency;
        if(!empty($value)) {
            $data = $data->whereIn('recipient_agency_id', $value);
        }

        //filter agency
        $value = $request->conferred;
        if($request->has('conferred')) {
            $data = $data->where('devolution_conferred', $value);
        }

        //filter customer
        $value = $request->customer;
        if($request->has('customer')) {
            $data = $data->where(function($q) use($value) {
                $q->where(function($q) use($value){
                    $q->where('customer_id', $value);
                    $q->where(function($q) use($value){
                        $q->where('requested_by',$value)
                            ->orWhereNull('requested_by');
                    });
                })
                ->orWhere(function($q) use($value){
                    $q->where('customer_id', '<>',$value)
                        ->where('requested_by',$value);
                });
            });
        }

        if(Auth::user()->isGuest()) {
            $data = $data->where('agency_id', '99999'); //hide data to gest agency role
        }

        return Datatables::of($data)
            ->edit_column('id', function($row) use($agencies) {
                return view('admin.shipments.shipments.datatables.tracking', compact('row', 'agencies'))->render();
            })
            ->edit_column('requested_customer.code', function($row) {
                return view('admin.refunds.devolutions.datatables.requested_customer', compact('row'))->render();
            })
            ->edit_column('sender_name', function($row) {
                return view('admin.shipments.shipments.datatables.sender', compact('row'))->render();
            })
            ->edit_column('recipient_name', function($row) {
                return view('admin.shipments.shipments.datatables.recipient', compact('row'))->render();
            })
            ->edit_column('provider_id', function($row) use($agencies, $servicesList, $providersList) {
                return view('admin.shipments.shipments.datatables.service', compact('row', 'agencies', 'servicesList', 'providersList'))->render();
            })
            ->edit_column('status', function($row) {
                return view('admin.refunds.devolutions.datatables.status', compact('row'))->render();
            })
            ->edit_column('volumes', function($row) use($servicesList, $packTypes) {
                return view('admin.shipments.shipments.datatables.volumes', compact('row', 'servicesList', 'packTypes'))->render();
            })
            ->edit_column('date', function($row) {
                return view('admin.refunds.devolutions.datatables.date', compact('row'))->render();
            })
            ->edit_column('last_history.created_at', function($row) {
                return view('admin.refunds.devolutions.datatables.devolution_date', compact('row'))->render();
            })
            ->edit_column('devolution_conferred', function($row) {
                return view('admin.refunds.devolutions.datatables.conferred', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.refunds.devolutions.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Mass confirm shipments list
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function massConfirmShipments(Request $request) {

        $ids = $request->ids;
        $ids = explode(',', $ids);

        $shipments = Shipment::filterAgencies()
            ->whereIn('id', $ids)
            ->get();

        foreach ($shipments as $shipment) {
            if($request->has('confirm_status')) {
                $shipment->devolution_conferred = $request->get('confirm_status', false);
            } else {
                $shipment->devolution_conferred = !$shipment->devolution_conferred;
            }

            $shipment->save();
        }

        $feedback = 'Envio confirmado com sucesso.';
        if(count($ids) > 1) {
            $feedback = 'Envios selecionados confirmados com sucesso.';
        }

        $row = $shipment;

        if($request->ajax()) {
            return Response::json([
                'result'   => true,
                'feedback' => $feedback,
                'html'     => view('admin.refunds.devolutions.datatables.conferred', compact('row'))->render()
            ]);
        }

        return Redirect::back()->with('success', $feedback);
    }
}
