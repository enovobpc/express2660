<?php

namespace App\Http\Controllers\Account;

use App\Models\CustomerEcommerceGateway;
use App\Models\EcommerceGateway\Base;
use App\Models\Shipment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;


class EcommerceGatewayController extends \App\Http\Controllers\Controller
{
    /**
     * The layout that should be used for responses
     *
     * @var string
     */
    protected $layout = 'layouts.account';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Customer billing index controller
     *
     * @return string
     */
    public function index(Request $request) {
        return $this->setContent('account.ecommerce_gateway.index');
    }

    public function create(Request $request) {
        $webservice     = new CustomerEcommerceGateway;
        $action         = trans('account/ecommerce-gateway.create');
        $formOptions    = [
            'route'       => 'account.ecommerce-gateway.store',
            'method'      => 'POST',
            'data-toggle' => 'ajax-form'
        ];

        return view('account.ecommerce_gateway.edit', compact('webservice', 'action', 'formOptions'))->render();
    }

    public function edit(Request $request, int $id) {
        $webservice = CustomerEcommerceGateway::where('customer_id', Auth::guard('customer')->id())
            ->findOrFail($id);
        $action         = trans('account/ecommerce-gateway.edit');
        $formOptions    = [
            'route'       => ['account.ecommerce-gateway.update', $id],
            'method'      => 'PUT',
            'data-toggle' => 'ajax-form'
        ];

        return view('account.ecommerce_gateway.edit', compact('webservice', 'action', 'formOptions'))->render();
    }

    public function store(Request $request) {
        return $this->update($request, null);
    }

    public function update(Request $request, int $id = null) {
        $webservice = new CustomerEcommerceGateway;
        if ($id) {
            $webservice = CustomerEcommerceGateway::where('customer_id', Auth::guard('customer')->id())
                ->findOrFail($id);
        }

        $input = $request->all();
        $input['list_status_codes'] = $request->get('list_status_codes');
        $webservice->fill($input);
        $webservice->customer_id = Auth::guard('customer')->id();
        $webservice->save();

        return response()->json([
            'result'   => true,
            'feedback' => trans('account/ecommerce-gateway.feedback.edit.success'),
            'dtbs'     => ['oTable']
        ]);
    }

    public function destroy(Request $request, int $id) {
        $webservice = CustomerEcommerceGateway::where('customer_id', Auth::guard('customer')->id())
            ->findOrFail($id);

        if (!$webservice->delete()) {
            return Redirect::route('account.ecommerce-gateway.index')->with('error', trans('account/ecommerce-gateway.feedback.destroy.error'));
        }

        return Redirect::route('account.ecommerce-gateway.index')->with('success', trans('account/ecommerce-gateway.feedback.destroy.success'));
    }

    public function mapping(Request $request, int $id) {
        $webservice = CustomerEcommerceGateway::where('customer_id', Auth::guard('customer')->id())
            ->findOrFail($id);

        $action         = trans('account/global.word.mapping');
        $formOptions    = [
            'route'       => ['account.ecommerce-gateway.mapping.store', $id],
            'method'      => 'POST',
            'data-toggle' => 'ajax-form'
        ];

        $gateway = new \App\Models\EcommerceGateway\Base($webservice);

        $carriers = collect($gateway->listCarriers())->pluck('name', 'code')->toArray() ?? [];
        $status   = collect($gateway->listOrdersStatus())->pluck('name', 'code')->toArray() ?? [];

        return view('account.ecommerce_gateway.modals.mapping', compact('webservice', 'action', 'formOptions', 'carriers', 'status'));
    }

    public function mappingStore(Request $request, int $id) {
        $webservice = CustomerEcommerceGateway::where('customer_id', Auth::guard('customer')->id())
            ->findOrFail($id);

        $input = $request->all();
        $input['list_status_codes'] = $request->get('list_status_codes');
        $webservice->fill($input);
        $webservice->save();

        return response()->json([
            'result'   => true,
            'feedback' => trans('account/ecommerce-gateway.feedback.edit.success'),
            'dtbs'     => ['oTable']
        ]);
    }

    public function orders(Request $request, int $id = null) {
        if (!$id) {
            $gateways = CustomerEcommerceGateway::where('customer_id', Auth::guard('customer')->id())
                ->get()
                ->pluck('name', 'id')
                ->toArray() ?? [];

            return view('account.ecommerce_gateway.modals.orders', compact('gateways'))->render();
        }

        $webservice = CustomerEcommerceGateway::where('customer_id', Auth::guard('customer')->id())
            ->findOrFail($id);

        $gateway = new Base($webservice);
        $orders  = $gateway->listOrders();

        // Check submitted orders
        $ordersCodes = [];
        foreach (($orders ?? []) as $order) {
            $ordersCodes[] = $order['code'];
        }

        $submittedOrders = [];
        if (!empty($ordersCodes)) {
            $submittedOrders = Shipment::where('ecommerce_gateway_id', $id)
                ->whereIn('ecommerce_gateway_order_code', $ordersCodes)
                ->get()->pluck('id', 'ecommerce_gateway_order_code')->toArray() ?? [];
        }
        //--

        return view('account.ecommerce_gateway.partials.orders.table', compact('webservice', 'orders', 'submittedOrders'))->render();
    }

    public function datatable(Request $request) {
        $data = CustomerEcommerceGateway::where('customer_id', Auth::guard('customer')->id());

        return Datatables::of($data)
            ->addColumn('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->editColumn('method', function ($row) {
                return view('account.ecommerce_gateway.datatable.method', compact('row'))->render();
            })
            ->addColumn('actions', function ($row) {
                return view('account.ecommerce_gateway.datatable.actions', compact('row'))->render();
            })
            ->make(true);
    }
}