<?php

namespace App\Http\Controllers\Admin\Refunds;

use Auth, DB, Response, Setting;

use App\Models\Movement;
use App\Models\CashierSession;
use App\Models\PaymentAtRecipientControl;
use App\Models\RefundControl;
use App\Models\ShippingStatus;
use App\Models\Shipment;
use App\Models\Provider;
use App\Models\Service;
use App\Models\Agency;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;

class OperatorsControlController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'refunds_operators';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',refunds_operators']);
        validateModule('refunds_operators');
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
            ->whereSource(config('app.source'))
            ->filterAgencies()
            ->ignoreAdmins()
            ->orderBy('source', 'asc')
            ->orderBy('code', 'asc')
            ->orderBy('name', 'asc')
            ->get(['source', 'id', 'name', 'vehicle', 'provider_id']), true);

        $data = compact(
            'operators',
            'agencies'
        );

        return $this->setContent('admin.refunds.operators.index', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {

        $dtMin   = $request->get('date_min');
        $dtMax   = $request->get('date_max');
        $dtUnity = $request->get('date_unity');
        $dtMin   = empty($dtMin) ? date('Y-m-d') : $dtMin;
        $dtMax   = empty($dtMax) ? date('Y-m-d') : $dtMax;

        $operator = User::findOrFail($id);

        $bindings = [
            DB::raw('count(*) as guides'),
            DB::raw('sum(charge_price) as charge_price'),
            DB::raw('sum(total_price_for_recipient) as total_price_for_recipient'),
        ];

        $totals = Shipment::filterAgencies()
            ->with(['operator' => function($q){
                $q->withTrashed();
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(User::CACHE_TAG);
                $q->select(['id', 'code', 'name']);
            }])
            ->where(function($q){
                $q->whereNotNull('charge_price');
                $q->orWhereNotNull('total_price_for_recipient');
            })
            ->whereNotIn('status_id', [ShippingStatus::CANCELED_ID])
            ->where('operator_id', $operator->id);

            if(!empty($dtUnity)) { //filter by shipment status date
                $dtMin = $dtMin . ' 00:00:00';
                $dtMax = $dtMax . ' 23:59:59';
                $statusId = $dtUnity;

                $totals = $totals->whereHas('history',function($q) use($dtMin, $dtMax, $statusId) {
                    $q->where('status_id', $statusId)
                        ->whereBetween('created_at', [$dtMin, $dtMax]);
                });
            } else { //filter by shipment date
                $totals = $totals->whereBetween('date', [$dtMin, $dtMax]);
            }

        $totals = $totals->select($bindings)
            ->first();

        $operator->total_refunds   = $totals->charge_price;
        $operator->total_recipient = $totals->total_price_for_recipient;

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'operator',
            'providers',
            'dtMin',
            'dtMax',
            'dtUnity'
        );

        return view('admin.refunds.operators.edit', $data)->render();
    }
    
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $shipmentId) {
        
        $input = $request->all();
        $input['ignore_billing'] = $request->get('ignore_billing');
        $input['ignore_billing'] = $input['ignore_billing'] == 'false' ? false : true;
        $input['regist_cashier'] = $request->get('regist_cashier');
        $input['regist_cashier'] = $input['regist_cashier'] == 'false' ? false : true;
        $input['print_proof']    = @$input['print_proof'] == 'false' ? false : true;
        $feedback = 'Alterações gravadas com sucesso.';

        $shipment = Shipment::findOrFail($shipmentId);

        //store refund control
        if(!empty($shipment->charge_price)) {
            $refund = RefundControl::firstOrNew(['shipment_id' => $shipmentId]);
            $refund->received_method = $input['refund_payment_method'];
            $refund->received_date   = date('Y-m-d');

            if(@$input['obs_refund']) {
                $refund->obs = $input['obs_refund'];
            }

            if(@$input['obs_customer']) {
                $refund->customer_obs = $input['obs_customer'];
            }

            $refund->save();

            if($shipment->charge_price != @$input['charge_price']) {
                $shipment->charge_price = @$input['charge_price'];
            }
        }

        //store payment at recipient
        if(!empty($shipment->total_price_for_recipient)) {

            $payment = PaymentAtRecipientControl::firstOrNew(['shipment_id' => $shipmentId]);
            $payment->payment_method  = @$input['recipient_payment_method'];
            $payment->payment_date = date('Y-m-d');

            if(@$input['obs_recipient']) {
                $payment->obs = @$input['obs_recipient'];
            }

            $payment->save();

            if($shipment->total_price_for_recipient != @$input['total_price_for_recipient']) {
                $shipment->total_price_for_recipient = @$input['total_price_for_recipient'];
            }
            $shipment->total_price    = 0;
            $shipment->ignore_billing = $input['ignore_billing'];
        }

        if(!empty($input['customer_id'])) { //altera cliente e converte o pagamento no destino em pagamento final do mes
            if(empty($shipment->requested_by) || $shipment->requested_by == $shipment->customer_id) {

                $shipment->requested_by = $shipment->customer_id;

                $shipment->customer_id  = $input['customer_id'];
                $shipment->total_price  = $shipment->total_price_for_recipient;
                $shipment->total_price_for_recipient = null;
                $shipment->payment_at_recipient = false;
                $shipment->save();
            }
        }

        $shipment->conferred = date('Y-m-d H:i:s');
        $shipment->save();

        //store at cashier
        if($input['regist_cashier']) {

            $operator   = Auth::user();
            $operatorId = $operator->id;

            $session = CashierSession::findOrStart($operatorId);

            if(!$session) {
                $feedback = 'Alterações gravadas. Não foi gravado na caixa porque não está autorizado a lançar movimentos.';
            } else {

                $movement = new Movement();

                if($movement->validate($input)) {
                    $movement->description = 'Envio TRK' . $shipment->tracking_code;
                    $movement->amount      = $shipment->charge_price + $shipment->total_price_for_recipient;
                    $movement->date        = date('Y-m-d');
                    $movement->operator_id = Auth::user()->id;
                    $movement->session_id  = $session->id;
                    $movement->sense       = 'credit';
                    $movement->setMovementCode();
                }
            }
        }

        $printProof = null;
        if($input['print_proof'] && !empty($refund)) {
            $printProof = route('admin.printer.refunds.customers.proof', $refund->shipment_id);
        }

        $result = [
            'result'   => true,
            'feedback' => $feedback,
            'html'     => view('admin.shipments.shipments.modals.popup_denied')->render(),
            'printProof' => $printProof,
        ];

        
        return Response::json($result);
    }
    
    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable(Request $request) {

        $dtMin = $request->get('date_min');
        $dtMax = $request->get('date_max');

        $dtMin = empty($dtMin) ? date('Y-m-d') : $dtMin;
        $dtMax = empty($dtMax) ? date('Y-m-d') : $dtMax;

        $sourceAgencies = Agency::filterSource()->pluck('id')->toArray();

        $bindings = [
            'id',
            'date',
            'operator_id',
            DB::raw('count(*) as guides'),
            DB::raw('sum(charge_price) as charge_price'),
            DB::raw('sum(total_price_for_recipient) as total_price_for_recipient'),
            DB::raw('(select sum(charge_price) + sum(total_price_for_recipient) from shipments where DATE(conferred) between "'.$dtMin.'" and "'.$dtMax.'" and operator_id = shipments.operator_id) as total_conferred')
        ];

        $data = Shipment::filterAgencies($sourceAgencies)
            ->with(['operator' => function($q){
                $q->withTrashed();
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(User::CACHE_TAG);
                $q->select(['id', 'name']);
            }])
            ->where(function($q){
                $q->whereNotNull('charge_price');
                $q->orWhereNotNull('total_price_for_recipient');
            })
            ->whereNotIn('status_id', [ShippingStatus::CANCELED_ID, ShippingStatus::INCIDENCE_ID])
            ->groupBy('operator_id')
            ->select($bindings);

        //filter date min
       /* $dtMin = $request->get('date_min');
        if($request->has('date_min')) {
            $dtMax = $dtMin;
            if($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }

            $data = $data->whereBetween('date', [$dtMin, $dtMax]);
        }*/

        //filter date min
        $dtMin   = $request->get('date_min');
        $dtUnity = $request->get('date_unity');
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
                    $q->where('status_id', $statusId)
                        ->whereBetween('created_at', [$dtMin, $dtMax]);
                });
            } else { //filter by shipment date
                $data = $data->whereBetween('date', [$dtMin, $dtMax]);
            }
        }

        //filter operator
        $value = $request->operator;
        if($request->has('operator')) {
            $data = $data->whereIn('operator_id', $value);
        }

        //filter date
        $value = $request->date;
        if($request->has('date')) {

        } else {
            $data = $data->where('date', '<=', date('Y-m-d'));
        }

        //filter operator
        $values = $request->agency;
        if($request->has('agency')) {
            $data = $data->whereHas('operator', function($q) use($values) {
                $q->where(function ($q) use($values) {
                    foreach ($values as $value) {
                        $q->where('agencies', 'like', '%"' . $value . '"%');
                    }
                });
            });
        }

        return Datatables::of($data)
                ->edit_column('operator', function($row) use($dtMin, $dtMax) {
                    return view('admin.refunds.operators.datatables.operator', compact('row', 'dtMin', 'dtMax'))->render();
                })
                ->edit_column('guides', function($row) {
                    return $row->guides;
                })
                ->edit_column('charge_price', function($row) {
                    return view('admin.refunds.operators.datatables.charge_price', compact('row'))->render();
                })
                ->edit_column('total_price_for_recipient', function($row) {
                    return view('admin.refunds.operators.datatables.recipient_price', compact('row'))->render();
                })
                ->edit_column('total', function($row) {
                    return '<b>' . money($row->charge_price + $row->total_price_for_recipient, Setting::get('app_currency')) . '</b>';
                })
                ->add_column('total_conferred', function($row) {
                    return view('admin.refunds.operators.datatables.total_conferred', compact('row'))->render();
                })
                ->add_column('select', function($row) {
                    return view('admin.partials.datatables.select', compact('row'))->render();
                })
                ->add_column('actions', function($row) use($dtMin, $dtMax, $dtUnity) {
                    return view('admin.refunds.operators.datatables.actions', compact('row', 'dtMin', 'dtMax', 'dtUnity'))->render();
                })
                ->make(true);
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatableShipments(Request $request, $operatorId) {

        $dtMin   = $request->date_min;
        $dtMax   = $request->date_max;
        $dtUnity = $request->date_unity;
        $dtMin   = empty($dtMin) ? date('Y-m-d') : $dtMin;
        $dtMax   = empty($dtMax) ? date('Y-m-d') : $dtMax;

        //services
        $servicesList = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->get();
        $servicesList = $servicesList->groupBy('id')->toArray();

        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->get(['name', 'code', 'id', 'color']);

        $agencies = $agencies->groupBy('id')->toArray();

        $bindings = [
            'id',
            'tracking_code',
            'type',
            'parent_tracking_code',
            'children_tracking_code',
            'children_type',
            'agency_id',
            'sender_agency_id',
            'recipient_agency_id',
            'service_id',
            'provider_id',
            'status_id',
            'operator_id',
            'customer_id',
            'sender_name',
            'sender_address',
            'sender_zip_code',
            'sender_city',
            'sender_phone',
            'recipient_name',
            'recipient_address',
            'recipient_zip_code',
            'recipient_city',
            'recipient_phone',
            'recipient_country',
            'obs',
            'volumes',
            'weight',
            'charge_price',
            'total_price_for_recipient',
            'date',
            'conferred'
        ];

        $data = Shipment::filterAgencies()
            ->with(['service' => function($q){
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Service::CACHE_TAG);
                $q->select(['id', 'name', 'display_code']);
            }])
            ->with(['provider' => function($q){
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Provider::CACHE_TAG);
                $q->select(['id', 'name', 'color']);
            }])
            ->where('operator_id', $operatorId)
            ->where(function($q){
                $q->whereNotNull('charge_price');
                $q->orWhereNotNull('total_price_for_recipient');
            });


            if($dtUnity) { //filter by shipment status date
                $dtMin = $dtMin . ' 00:00:00';
                $dtMax = $dtMax . ' 23:59:59';
                $statusId = $dtUnity;

                $data = $data->whereHas('history',function($q) use($dtMin, $dtMax, $statusId) {
                    $q->where('status_id', $statusId)
                        ->whereBetween('created_at', [$dtMin, $dtMax]);
                });
            } else {
                $data = $data->whereBetween('date', [$dtMin, $dtMax]);
            }

            $data = $data->select($bindings);


        //filter provider
        $value = $request->provider;
        if($request->has('provider')) {
            $data = $data->where('provider_id', $value);
        }

        return Datatables::of($data)
            ->edit_column('service_id', function($row) use($agencies) {
                return view('admin.refunds.operators.datatables.shipments.service', compact('row', 'agencies'))->render();
            })
            ->edit_column('id', function($row) use($agencies, $dtMin, $dtMax) {
                return view('admin.refunds.operators.datatables.shipments.tracking', compact('row', 'agencies', 'dtMin', 'dtMax'))->render();
            })
            ->edit_column('recipient_name', function($row) {
                return view('admin.refunds.operators.datatables.shipments.recipient', compact('row'))->render();
            })
            ->add_column('customer', function($row) {
                return view('admin.refunds.operators.datatables.shipments.customer', compact('row'))->render();
            })
            ->edit_column('volumes', function($row) {
                return view('admin.refunds.operators.datatables.shipments.volumes', compact('row'))->render();
            })
            ->add_column('refund', function($row) {
                return view('admin.refunds.operators.datatables.shipments.refund', compact('row'))->render();
            })
            ->add_column('cod', function($row) {
                return view('admin.refunds.operators.datatables.shipments.cod', compact('row'))->render();
            })
            ->add_column('extra', function($row) {
                return view('admin.refunds.operators.datatables.shipments.extra', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.refunds.operators.datatables.shipments.actions', compact('row'))->render();
            })
            ->make(true);
    }
}
