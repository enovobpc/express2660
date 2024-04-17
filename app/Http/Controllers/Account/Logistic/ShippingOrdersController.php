<?php

namespace App\Http\Controllers\Account\Logistic;

use App\Models\Logistic\Location;
use App\Models\Logistic\Product;
use App\Models\Logistic\ProductHistory;
use App\Models\Logistic\ReceptionOrder;
use App\Models\Logistic\ShippingOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Datatables;
use App\Models\CustomerRecipient;
use Mpdf\Mpdf;
use DB, View;


class ShippingOrdersController extends \App\Http\Controllers\Controller
{
    /**
     * The layout that should be used for responses
     *
     * @var string
     */
    protected $layout = 'layouts.account';

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'logistic-shipping-orders';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Customer billing index controller
     *
     * @return type
     */
    public function index(Request $request) {

        //$customer = Auth::guard('customer')->user();

        return $this->setContent('account.logistic.shipping_orders.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {

        $productId = $request->get('product', null);

        $action = 'Criar Receção de Artigos';

        $product  = Product::findOrNew($productId);
        $customer = $product->customer;

        $formOptions = array('route' => array('account.logistic.shipping-orders.store'), 'method' => 'POST', 'class' => 'form-product-reception', 'autocomplete'=> 'nofill');

        $locations = Location::filterSource()->pluck('code', 'id')->toArray();

        $data = compact(
            'product',
            'action',
            'formOptions',
            'locations',
            'customer');

        return view('account.logistic.shipping_orders.edit', $data)->render();
    }

    /**
     * Show the form for consult the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($orderCode) {

        $customer = Auth::guard('customer')->user();

        $shippingOrder = ShippingOrder::filterSource()
            ->where(function ($q) use($customer) {
                $q->where('customer_id', $customer->id);
                $q->orWhere('customer_id', $customer->customer_id);
            })
            ->where('code', $orderCode)
            ->firstOrFail();

        return view('account.logistic.shipping_orders.show', compact('shippingOrder'))->render();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatable(Request $request) {

        $customer = Auth::guard('customer')->user();

        $data = ShippingOrder::filterSource()
            ->where(function ($q) use($customer) {
                $q->where('customer_id', $customer->id);
                $q->orWhere('customer_id', $customer->customer_id);
            })
            ->select();

        return Datatables::of($data)
            ->edit_column('code', function($row) {
                return view('account.logistic.shipping_orders.datatables.code', compact('row'))->render();
            })
            ->edit_column('shipment_id', function($row) {
                return view('account.logistic.shipping_orders.datatables.shipment', compact('row'))->render();
            })
            ->edit_column('status_id', function($row) {
                return view('account.logistic.shipping_orders.datatables.status', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('account.logistic.shipping_orders.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }
}