<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Account\WalletController;
use App\Models\Customer;
use App\Models\Shipment;
use Response, DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\Provider;
use App\Models\User;
use App\Models\GatewayPayment\Base;
use App\Models\Agency;

class GatewayPaymentsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'gateway_payments';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',gateway_payments']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        validateModule('gateway_payments');

        return $this->setContent('admin.gateway_payments.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {

        $phone = '';
        $email = '';

        $shipments = null;
        if($request->has('shipment')) {
            $shipments = Shipment::filterAgencies()
                        ->whereIn('id', $request->shipment)
                        ->get();

            $total = 0;
            foreach ($shipments as $shipment) {

                if($shipment->payment_at_recipient) {
                    $phone = $shipment->recipent_phone;
                    $email = $shipment->recipent_email;
                    $total+= $shipment->total_price_for_recipient;
                } else {
                    $phone = $shipment->customer->phone;
                    $email = $shipment->customer->billing_email;
                    $total+= $shipment->total_price + $shipment->total_expenses;
                }
            }

            if($shipments->count() > 1) {
                $phone = '';
                $email = '';
            }
        }

        $data = compact(
            'shipments',
            'total',
            'phone',
            'email'
        );

        return view('admin.gateway_payments.create', $data)->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $data = [
            'result'   => false,
            'feedback' => 'Erro: Gateway de pagamentos indisponível.',
            'html'     => view('', compact('payment'))->render()
        ];

        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        $payment = Base::findOrfail($id);

        $action = 'Editar Pagamento';

        $formOptions = array('route' => array('admin.gateway.payments.update', $payment->id), 'method' => 'PUT');

        $data = compact(
            'payment',
            'action',
            'formOptions'
        );

        return view('admin.gateway_payments.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        Base::flushCache(Base::CACHE_TAG);

        $input = $request->all();
        $input['zip_codes'] = explode(',', @$input['zip_codes']);

        $route = Base::findOrNew($id);

        if ($route->validate($input)) {
            $route->fill($input);
            $route->source = config('app.source');
            $route->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $route->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        Base::flushCache(Base::CACHE_TAG);

        $payment = Base::whereSource(config('app.source'))
            ->find($id);

        $result = $payment->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o pagamento.');
        }

        return Redirect::route('admin.gateway.payments.index')->with('success', 'Pagamento removido com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        Base::flushCache(Base::CACHE_TAG);

        $ids = explode(',', $request->ids);

        $payments = Base::filterSource()
            ->whereIn('id', $ids)
            ->get();

        $result = true;
        foreach ($payments as $payment) {
            $payment->delete();
        }
        
        if (!$result) {
            return Redirect::back()->with('error', 'Não foi possível remover um ou mais registos.');
        }

        return Redirect::back()->with('success', 'Registos selecionados removidos com sucesso.');
    }
    
    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable(Request $request) {

        $data = Base::filterSource()
                ->whereNull('deleted_at');

        //filter method
        $value = $request->get('method');
        if($request->has('method')) {
            $data = $data->where('method', $value);
        }

        //filter status
        $value = $request->get('status');
        if($request->has('status')) {
            $data = $data->where('status', $value);
        }

        //filter customer
        $value = $request->get('customer');
        if($request->has('customer')) {
            $data = $data->where('customer_id', $value);
        }

        //filter date min
        $dtMin = $request->get('date_min');
        if($request->has('date_min')) {

            $dtMax = $dtMin;

            if($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }
            
            $data = $data->whereBetween('date', [$dtMin, $dtMax]);
        }
        
        return Datatables::of($data)
            ->edit_column('gateway', function($row) {
                return view('admin.gateway_payments.datatables.gateway', compact('row'))->render();
            })
            ->edit_column('description', function($row) {
                return view('admin.gateway_payments.datatables.description', compact('row'))->render();
            })
            ->edit_column('method', function($row) {
                return view('admin.gateway_payments.datatables.method', compact('row'))->render();
            })
            ->edit_column('value', function($row) {
                return view('admin.gateway_payments.datatables.value', compact('row'))->render();
            })
            ->edit_column('reference', function($row) {
                return $row->reference;
            })
            ->edit_column('payment_details', function($row) {
                return view('admin.gateway_payments.datatables.payment_details', compact('row'))->render();
            })
            ->edit_column('paid_at', function($row) {
                return view('admin.gateway_payments.datatables.paid_at', compact('row'))->render();
            })
            ->edit_column('status', function($row) {
                return view('admin.gateway_payments.datatables.status', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.gateway_payments.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function editWallet(Request $request) {

        $customer = Customer::findOrNew($request->get('customer'));

        $data = compact(
            'customer'
        );

        return view('admin.gateway_payments.edit_wallet', $data)->render();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateWallet(Request $request) {

        $input = $request->all();

        $customer = Customer::findOrFail($input['customer_id']);

        if($input['sense'] == 'credit') {
            $customer->addWallet($input['amount']);
        } else {
            $customer->subWallet($input['amount']);
        }

        //create payment info
        $paymentGateway = new Base();
        $paymentGateway->source      = config('app.source');
        $paymentGateway->customer_id = $customer->id;
        $paymentGateway->target      = 'Wallet';
        $paymentGateway->method      = 'wallet';
        $paymentGateway->description = @$input['description'];
        $paymentGateway->sense       = @$input['sense'];
        $paymentGateway->value       = @$input['amount'];
        $paymentGateway->status      = Base::STATUS_SUCCESS;
        $paymentGateway->setCode();

        return Redirect::back()->with('success', 'Conta corrente atualizada.');
    }
}
