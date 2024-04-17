<?php

namespace App\Http\Controllers\Admin\Refunds;

use App\Models\CacheSetting;
use App\Models\Route;
use App\Models\Service;
use Response, View, Auth, Setting;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;

use App\Models\PaymentAtRecipientControl;
use App\Models\Shipment;
use App\Models\Agency;
use App\Models\Provider;
use App\Models\ShippingStatus;
use App\Models\Customer;
use App\Models\User;

class CodController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'cod_control';
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',cod_control']);
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

        $status = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingStatus::CACHE_TAG)
            ->where('is_shipment', 1)
            ->filterSources()
            ->isVisible()
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $routes = Route::listsWithCode(Route::remember(config('cache.query_ttl'))
            ->cacheTags(Route::CACHE_TAG)
            ->filterSource()
            ->ordered()
            ->get());

        $data = compact(
            'agencies',
            'operators',
            'providers',
            'services',
            'status',
            'routes'
        );

        return $this->setContent('admin.refunds.cod.index', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        $shipment = Shipment::filterAgencies()
                    ->with('cod_control')
                    ->findOrFail($id);
        
        $refund = empty($shipment->cod_control) ? new PaymentAtRecipientControl : $shipment->cod_control;
        
        $refund->shipment_id = $shipment->id;

        return view('admin.refunds.cod.edit', compact('refund', 'shipment'))->render();
    }
    
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        
        $input = $request->all();
        $input['paid'] = $request->get('paid', false);

        $ignoreBilling = ($input['paid'] == false) ? 0 : 1;

        $refund = PaymentAtRecipientControl::firstOrNew(['shipment_id' => $id]);

        if ($refund->validate($input)) {
            $refund->fill($input);
            $refund->save();

            Shipment::where('id', $id)->update([
                'ignore_billing' => $ignoreBilling,
                'total_price_for_recipient' => $input['price']
            ]);

            $result = [
                'result'   => true,
                'feedback' => 'Alterações gravadas com sucesso.',
            ];
        } else {
            $result = [
                'result'   => false,
                'feedback' => $refund->errors()->first()
            ];
        }
        
        return Response::json($result);
    }
    
    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable(Request $request) {


        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->get(['name', 'code', 'id', 'color', 'source']);

        $sourceAgencies = $agencies->filter(function ($item) { return $item->source == config('app.source'); })->pluck('id')->toArray();

        $agencies = $agencies->groupBy('id')->toArray();

        $data = Shipment::whereIn('agency_id', $sourceAgencies)
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
          /*  ->with(['requested_customer' => function($q){
                $q->withTrashed();
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Customer::CACHE_TAG);
                $q->select(['id', 'code', 'name', 'contact_email', 'refunds_email', 'iban_refunds']);
            }])*/
            ->with('last_history')
            ->with('cod_control')
            ->where('payment_at_recipient', 1)
            ->whereNotIn('status_id', [ShippingStatus::DEVOLVED_ID, ShippingStatus::CANCELED_ID])
            ->select();

        //limit search
        $value = $request->limit_search;
        if($request->has('limit_search') && !empty($value)) {
            $minId = (int) CacheSetting::get('shipments_limit_search');
            if($minId) {
                $data = $data->where('id', '>=', $minId);
            }
        }

        //filter status
        $value = $request->get('status');
        if($request->has('status')) {
            if ($value == '1') { //pending
                $data = $data->has('cod_control', '=', 0)
                    ->where('ignore_billing', 0);
            } else if ($value == '2') { //received and not paid
                $data = $data->where(function($q){
                    $q->has('cod_control')
                        ->orWhere('ignore_billing', 1);
                });
            }
        }

        //filter payment_method
        $value = $request->get('payment_method');
        if($request->has('payment_method')) {
            $data = $data->whereHas('cod_control', function($q) use($value) {
                $q->where('payment_method', $value);
            });
        }

        //filter payment date
        $value = $request->get('payment_date');
        if($request->has('payment_date')) {
            $data = $data->whereHas('cod_control', function($q) use($value) {
                $q->where('payment_date', $value);
            });
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

        //filter shipment status
        $value = $request->get('shipment_status');
        if(!empty($value)) {
            $data = $data->whereIn('status_id', $value);
        }

        //filter route
        $value = $request->get('route');
        if(!empty($value)) {
            $data = $data->whereIn('route_id', $value);
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
        $value = $request->agency;
        if(!empty($value)) {
            $data = $data->whereIn('agency_id', $value);
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
                return view('admin.refunds.cod.datatables.requested_customer', compact('row'))->render();
            })
            ->edit_column('customer.code', function($row) {
                return view('admin.refunds.cod.datatables.customer', compact('row'))->render();
            })
            ->edit_column('sender_name', function($row) {
                return view('admin.shipments.shipments.datatables.sender', compact('row'))->render();
            })
            ->edit_column('recipient_name', function($row) {
                return view('admin.shipments.shipments.datatables.recipient', compact('row'))->render();
            })
            ->edit_column('date', function($row) {
                return view('admin.refunds.cod.datatables.date', compact('row'))->render();
            })
            ->edit_column('delivery_date', function($row) {
                return view('admin.refunds.customers.datatables.delivery_date', compact('row'))->render();
            })
            ->edit_column('total_price_for_recipient', function($row) {
                return view('admin.refunds.cod.datatables.price', compact('row'))->render();
            })
            ->add_column('payment_method', function($row) {
                return view('admin.refunds.cod.datatables.payment_method', compact('row'))->render();
            })
            ->edit_column('obs', function($row) {
                return @$row->cod_control->obs;
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.refunds.cod.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }
    
    /**
     * Mass update 
     * 
     * @param type $shipmentId
     * @return type
     */
    public function massUpdate(Request $request){

        $input = $request->all();
        $input['paid'] = $request->get('paid', false);
        
        $ids = explode(',', $request->ids);

        $shipments = Shipment::filterAgencies()
                            ->whereIn('id', $ids)
                            ->get();
        
        foreach ($shipments as $shipment) {
            $refund = PaymentAtRecipientControl::firstOrNew(['shipment_id' => $shipment->id]);
            $refund->fill($input);
            $refund->save();

            $shipment->ignore_billing = 1;
            $shipment->save();
        }
        
        return Redirect::back()->with('success', 'Registos selecionados alterados com sucesso.');
    }

    /**
     * Assign selected resources to same customer
     * GET /admin/billing/payments-at-recipient/selected/assign-customer
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massAssignCustomers(Request $request) {

        $ids = explode(',', $request->ids);

        $customer = Customer::findOrFail($request->assign_customer_id);

        foreach($ids as $id) {
            $shipment = Shipment::find($id);

            if(empty($shipment->requested_by) || $shipment->requested_by == $shipment->customer_id) {
                $shipment->requested_by = $shipment->customer_id;
                $shipment->customer_id  = $customer->id;
                $shipment->cod          = null; //remove portes no destino

                $shipment->save();
            }
        }

        return Redirect::back()->with('success', 'Registos selecionados associados com sucesso ao cliente '.$customer->code.'.');
    }
}
