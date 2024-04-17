<?php

namespace App\Http\Controllers\Account\Logistic;

use App\Models\Customer;
use App\Models\Logistic\Location;

use App\Models\Logistic\ReceptionOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Datatables;
use App\Models\Logistic\CartProduct;
use App\Models\Shipment;
use App\Models\Agency;
use App\Models\Service;
use Carbon\Carbon;
use DB, View, Excel, Mail;


class CartController extends \App\Http\Controllers\Controller
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
    protected $sidebarActiveOption = 'logistic-cart-orders';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Customer billing index controller
     *
     * @return type
     */
    public function index(Request $request)
    {
        $status = [
            'pending' => 'Pendente',
            'accept'  => 'Aceite',
            'refused'  => 'Recusado',
        ];

        $customer = Auth::guard('customer')->user();

        $departments = Customer::where('customer_id', $customer->customer_id)
            ->where('is_commercial', 0)
            ->pluck('name', 'id')
            ->toArray();

        $departments[$customer->id] = $customer->name;

        $data = compact(
            'status',
            'departments'
        );
        return $this->setContent('account.logistic.cart.index', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
    }

    /**
     * Show the form for consult the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $order = CartProduct::where('id', $id)->first();

        $products = CartProduct::with('product')
            ->where('reference', $order->reference)
            ->get();

        $reference = $order->reference;
        return view('account.logistic.cart.show', compact('products', 'reference'))->render();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatable(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        if ($customer->is_commercial) {
            $customersIds = Customer::where('customer_id', $customer->customer_id)->pluck('id')->toArray();
            array_push($customersIds, $customer->customer_id);

            $orders = CartProduct::selectRaw('*, sum(qty) as total_qty')
                ->with('product')
                ->with('shipment')
                ->with('customer')
                ->whereIn('customer_id', $customersIds)
                ->whereNotNull('reference')
                ->where('closed', 1)
                ->groupBy('reference')
                ->orderBy('status');
        } else {
            $orders = CartProduct::selectRaw('*, sum(qty) as total_qty')
                ->with('product')
                ->with('shipment')
                ->with('customer')
                ->where('customer_id', $customer->id)
                ->whereNotNull('reference')
                ->where('closed', 1)
                ->groupBy('reference')
                ->orderBy('status');
        }

        $value = $request->department;
        if ($request->has('department')) {
            $orders = $orders->where('customer_id', $value);
        }

        $value = $request->status;
        if ($request->has('status')) {
            $orders = $orders->where('status', $value);
        }


        $datatables = Datatables::of($orders)
            ->edit_column('reference', function ($row) {
                return view('account.logistic.cart.datatables.reference', compact('row'))->render();
            })
            ->edit_column('qty', function ($row) {
                return view('account.logistic.cart.datatables.qty', compact('row'))->render();
            })
            ->edit_column('status', function ($row) {
                return view('account.logistic.cart.datatables.status', compact('row'))->render();
            })
            ->edit_column('shipment', function ($row) {
                return view('account.logistic.cart.datatables.shipment', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('created_at', function ($row) {
                return view('account.logistic.cart.datatables.created_at', compact('row'))->render();
            })
            ->add_column('submitted_by', function ($row) {
                return view('account.logistic.cart.datatables.created_by', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('account.logistic.cart.datatables.actions', compact('row'))->render();
            });

        return $datatables->make(true);
    }

    /**
     * Destroy shopping order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $order = CartProduct::where('id', $id)->first();

        CartProduct::where('reference', $order->reference)
            ->delete();

        return Redirect::back()->with('success', 'Pedido eliminado com sucesso..');
    }

    /**
     * Commercial refuse a order
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function refuse(Request $request, $reference)
    {
        $customer = Auth::guard('customer')->user();
        CartProduct::where('reference', $reference)
            ->update([
                'status'        => 'refused',
                'closed'        => 1,
                'refused_by'    => $customer->id
            ]);

        $cartProduct    = CartProduct::where('reference', $reference)->first();
        $customerCart   = Customer::where('id', $cartProduct->customer_id)->first();

        $products = CartProduct::with('product')
            ->with('product')
            ->where('reference', $reference)
            ->get();

        Mail::send('emails.logistic.cartOrderRefuse', compact('products', 'customer', 'reference', 'customerCart'), function ($message) use ($customerCart, $reference) {
            $message->to($customerCart->email);
            $message->subject('Encomenda ' . $reference . ' recusada.');
        });


        $result = [
            'result' => true,
            'feedback' => 'Pedido recusado com sucesso'
        ];

        return response()->json($result);
    }

    /**
     * Commercial refuse a order
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function accept(Request $request, $reference)
    {
        $customer = Auth::guard('customer')->user();
        // CartProduct::where('reference', $reference)
        //     ->update([
        //         'status'        => 'accept',
        //         'accepted_by'    => $customer->id
        //     ]);

        $cartProduct    = CartProduct::where('reference', $reference)->first();
        $customerCart   = Customer::where('id', $cartProduct->customer_id)->first();

        $products = CartProduct::with('product')
            ->with('product')
            ->where('reference', $reference)
            ->get();

        $mainCustomer = Customer::where('id', $customerCart->customer_id)->first();
        if (!isset($mainCustomer)) {
            $mainCustomer = $customer;
        }

        $agency = Agency::filterSource()->first();

        $service = Service::filterSource()->where('id', 1)->first();

        $shipment = new Shipment();

        $shipment->date                 = Carbon::today()->format('Y-m-d');
        $shipment->service_id           = @$service->id;
        $shipment->service              = @$service->display_code;
        $shipment->agency_id            = @$agency->id;
        $shipment->recipient_agency_id  = @$agency->id;
        $shipment->sender_agency_id     = @$agency->id;
        $shipment->customer_id          = $mainCustomer->id;
        $shipment->provider_id          = '1';
        $shipment->reference            = $reference;

        $shipment->sender_name          = $cartProduct->origin_name ?? $mainCustomer->name;
        $shipment->sender_address       = $cartProduct->origin_address ?? $mainCustomer->address;
        $shipment->sender_zip_code      = $cartProduct->origin_zip_code ?? $mainCustomer->zip_code;
        $shipment->sender_city          = $cartProduct->origin_city ?? $mainCustomer->city;
        $shipment->sender_country       = $cartProduct->origin_country ?? $mainCustomer->country;
        $shipment->sender_phone         = $cartProduct->origin_phone_number ?? $mainCustomer->phone;

        $shipment->recipient_name       = $cartProduct->destination_name ?? $customerCart->name;
        $shipment->recipient_address    = $cartProduct->destination_address ?? $customerCart->address;
        $shipment->recipient_zip_code   = $cartProduct->destination_zip_code ?? $customerCart->zip_code;
        $shipment->recipient_city       = $cartProduct->destination_city ?? $customerCart->city;
        $shipment->recipient_country    = $cartProduct->destination_country ?? $customerCart->country;
        $shipment->recipient_phone      = $cartProduct->destination_phone_number ?? $customerCart->phone;
        $shipment->recipient_email      = $customerCart->email;

        $shipment->obs                  = $cartProduct->obs ?? '';

        $qty = $sku = $serialNo = $lote = $stock = $product =
            $boxType = $boxDescription = $length = $width =
            $height = $boxWeight = $fatorM3Row = [];
        $totalWeight = $count = 0;

        foreach ($products as $productInfo) {
            array_push($qty, $productInfo->qty);
            array_push($sku, $productInfo->product->sku ?? '');
            array_push($serialNo, $productInfo->product->serial_no ?? '');
            array_push($lote, $productInfo->product->lote ?? '');
            array_push($stock, $productInfo->product->stock_available ?? '');
            array_push($product, $productInfo->product->id ?? '');
            array_push($boxType, 'box');
            array_push($boxDescription, $productInfo->product->name ?? '');
            array_push($length, $productInfo->product->length ?? '');
            array_push($width, $productInfo->product->width ?? '');
            array_push($height, $productInfo->product->height ?? '');
            array_push($boxWeight, $productInfo->product->weight ?? '');
            array_push($fatorM3Row, '');
            $totalWeight .= $productInfo->product->weight;
            $count++;
        }

        $shipment->volumes          = $count;
        $shipment->weight           = $totalWeight;
        $shipment->qty              = $qty;
        $shipment->sku              = $sku;
        $shipment->serial_no        = $serialNo;
        $shipment->lote             = $lote;
        $shipment->stock            = $stock;
        $shipment->product          = $product;
        $shipment->box_type         = $boxType;
        $shipment->box_description  = $boxDescription;
        $shipment->length           = $length;
        $shipment->width            = $width;
        $shipment->height           = $height;
        $shipment->box_weight       = $boxWeight;
        $shipment->fator_m3_row     = $fatorM3Row;


        $shipment->pack_dimensions = '';

        // $requestData = new Request($shipment->toArray());
        $request = new Request([
            'shipment' => $shipment,
            'cart'     => true,
            'source'   => 'cart'
        ]);

        $controller  = new \App\Http\Controllers\Account\ShipmentsController;
        return $controller->create($request);
    }


    /**
     * Delete a product from cart
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteProduct(Request $request)
    {
        $request = $request->all();
        $productId = $request['id'];
        $customer = Auth::guard('customer')->user();


        CartProduct::where('product_id', $productId)
            ->where('customer_id', $customer->id)->where(function ($q) {
                $q->where('reference', NULL)->orWhere('closed', 0);
            })
            ->forceDelete();


        $totalItems = CartProduct::where('customer_id', $customer->id)->where(function ($q) {
            $q->where('reference', NULL)->orWhere('closed', 0);
        })->sum('qty');

        $result = [
            'result' => true,
            'cart_total' => $totalItems,
            'feedback' => 'Produto removido com sucesso'
        ];

        return response()->json($result);
    }
}
