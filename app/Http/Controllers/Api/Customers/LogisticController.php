<?php

namespace App\Http\Controllers\Api\Customers;

use App\Models\Api\OauthClient;
use App\Models\Logistic\Product;
use App\Models\Logistic\ShippingOrder;
use App\Models\Logistic\ShippingOrderStatus;
use App\Models\LogViewer;
use Illuminate\Http\Request;
use Auth, Validator, Setting, Mail, Log, DB, Date;

class LogisticController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Bindings
     *
     * @var array
     */
    protected $productBindings = [
        'sku',
        'barcode',
        'name',
        'description',
        'width',
        'height',
        'length',
        'weight',
        'unity',
        'stock_min',
        'stock_max',
        'stock_total',
        'stock_allocated',
        'stock_status',
        'price',
        'vat',
        'is_active',
        'filehost',
        'filepath'
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {

        try {
            $this->usage_exceed = false;
            $this->log_usage = Setting::get('api_debug_mode') ? true : false;

            $customer = Auth::guard('api')->user();
            if ($customer) {
                $oauth = OauthClient::where('user_id', $customer->id)->first();

                $lastCallDate = new Date($oauth->last_call);
                $lastCallDate = $lastCallDate->format('Y-m-d');

                if ($lastCallDate == date('Y-m-d')) {
                    $oauth->daily_counter += 1;
                    $oauth->last_call = date('Y-m-d H:i:s');

                    if ($oauth->daily_counter > $oauth->daily_limit) {
                        $this->usage_exceed = true;
                    }
                } else {
                    $oauth->daily_counter = 1;
                    $oauth->last_call = date('Y-m-d H:i:s');
                }
                $oauth->save();
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * Lists all shipments by given parameters
     *
     * @param Request $request
     * @return mixed
     */
    public function listsProducts(Request $request, $apiLevel) {

        $customer = Auth::guard('api')->user();
        $this->logUsage($customer, 'listsProducts');

        if($this->checkUsageLimit()) {
            return $this->responseUsageExceed();
        }

        $products = Product::where('customer_id', $customer->id);

        if($request->has('images')) {
            $products = $products->with(['images' => function($q){
                $q->select(['filehost', 'filepath', 'is_cover']);
            }]);
        }


        //filter sku
        if($request->has('sku')) {
            $products = $products->where('sku', $request->get('sku'));
        }

        //filter serial_no
        if($request->has('serial_no')) {
            $products = $products->where('serial_no', $request->get('serial_no'));
        }

        //filter lote
        if($request->has('lote')) {
            $products = $products->where('lote', $request->get('lote'));
        }

        //filter stock status
        if($request->has('status')) {
            $products = $products->where('stock_status', $request->get('status'));
        }

        //filter stock status
        if($request->has('active')) {
            $products = $products->where('is_active', $request->get('active'));
        }

        //filter last update
        if($request->has('last_update')) {
            $products = $products->whereRaw('DATE(updated_at)="' . $request->get('last_update').'"');
        }

        $bindings = $this->productBindings;
        $bindings[] = DB::raw('created_at as last_update');

        $products = $products->take(5000)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get($bindings);

        if(!$products) {
            return $this->responseError('lists', '-001') ;
        }

        return response($products, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Lists all shipments by given parameters
     *
     * @param Request $request
     * @return mixed
     */
    public function listsShippingOrders(Request $request, $apiLevel) {

        $partnersApi = $request->has('_partner_api_');
        if (!$partnersApi) {
            $customer = Auth::guard('api')->user();

            $this->logUsage($customer, 'listsShippingOrders');

            if ($this->checkUsageLimit()) {
                return $this->responseUsageExceed();
            }
        }

        $shippingOrders = ShippingOrder::with(['lines' => function($q){
                $q->join('products', function($join){
                    $join->on('shipping_orders_lines.product_id', '=', 'products.id');
                });
                $q->select(['shipping_order_id', 'qty', 'products.name', 'products.sku', 'products.serial_no', 'products.lote']);
            }]);

        if (!$partnersApi) {
            $shippingOrders = $shippingOrders->with(['status' => function ($q) {
                $q->select('id', 'name', 'color');
            }]);
            
            $shippingOrders = $shippingOrders->where('customer_id', $customer->id);
        }

        //filter order number
        if($request->has('order_no')) {
            $shippingOrders = $shippingOrders->where('code', $request->get('order_no'));
        }

        //filter document
        if($request->has('document')) {
            $shippingOrders = $shippingOrders->where('document', $request->get('document'));
        }

        //filter date
        if($request->has('date')) {
            $shippingOrders = $shippingOrders->where('date', $request->get('date'));
        }

        //filter stock status
        if($request->has('status')) {
            if($request->status == 'pending') {
                $statusId = ShippingOrderStatus::STATUS_PENDING;
            } elseif($request->status == 'processing') {
                $statusId = ShippingOrderStatus::STATUS_PROCESSING;
            } else {
                $statusId = ShippingOrderStatus::STATUS_CONCLUDED;
            }
            $shippingOrders = $shippingOrders->where('status_id', $statusId);
        }

        $shippingOrders = $shippingOrders->take(100)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get([
                'id',
                DB::raw('code as order_no'),
                'document',
                'date',
                'qty_total',
                'total_items',
                'status_id',
                'shipment_trk',
            ]);

        if(!$shippingOrders) {
            return $this->responseError('lists', '-001') ;
        }

        return response($shippingOrders, 200)->header('Content-Type', 'application/json');
    }


    public function storeRules() {
        return trans('api.logistic.rules');
    }

    /**
     * Check usage limit
     * @return array
     */
    public function checkUsageLimit() {
        return $this->usage_exceed;
    }

    /**
     * @return array
     */
    public function responseUsageExceed() {
        return $this->responseError('', '-996', 'Daily maximum API calls exceeded.');
    }

    /**
     * @param $customer
     */
    public function logUsage($customer, $method) {
        if($this->log_usage) {
            $trace = LogViewer::getTrace(null, 'API LOGISTIC - '.$method.' - CUSTOMER ' . $customer->name);
            Log::info(br2nl($trace));
        }
    }

    /**
     * Store shipment custom attributes
     * @return array
     */
    public function customAttributes() {
        return trans('api.logistic.attributes');
    }

    /**
     * Store shipment custom attributes
     * @return array
     */
    public function responseError($method, $code, $message = null) {

        $errors = trans('api.logistic.errors');

        $data = [
            'error'   => $code,
            'message' => $message ? $message : $errors[$method][$code]
        ];

        return response($data, 404)->header('Content-Type', 'application/json');
    }
}