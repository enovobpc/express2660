<?php

namespace App\Http\Controllers\Api\Partners;

use App\Models\Agency;
use App\Models\Api\OauthClient;
use App\Models\BroadcastPusher;
use App\Models\Customer;
use App\Models\CustomerService;
use App\Models\FileRepository;
use App\Models\IncidenceResolutionType;
use App\Models\Invoice;
use App\Models\Logistic\Product;
use App\Models\LogViewer;
use App\Models\OperatorTask;
use App\Models\PickupPoint;
use App\Models\Provider;
use App\Models\RefundControl;
use App\Models\Route;
use App\Models\Service;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use App\Models\ShipmentIncidenceResolution;
use App\Models\ShipmentPackDimension;
use App\Models\ShippingExpense;
use App\Models\ShippingStatus;
use App\Models\User;
use App\Models\Webservice\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Jenssegers\Date\Date;
use Auth, Validator, Setting, Mail, Log, DB;

class CustomersController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Bindings
     *
     * @var array
     */
    protected $bindings = [
        'code',
        'name',
        'address',
        'zip_code',
        'city',
        'country',
        'map_lat',
        'map_lng',
        'contact_email',
        'phone',
        'mobile',
        'responsable',
        'vat',
        'billing_name',
        'billing_address',
        'billing_zip_code',
        'billing_city',
        'billing_country',
        'payment_method',
        'billing_email',
        'is_particular',
        'unpaid_invoices_credit',
        'unpaid_invoices_limit',
        'balance_count_expired',
        'balance_count_unpaid',
        'balance_total_unpaid',
        'balance_last_update',
        'wallet_balance',
        'currency',
        'obs',
        'obs_shipments',
        'filepath',
        'filename',
        'other_name',
        'agency_id',
        'type_id',
        'route_id',
        'seller_id',
        'is_active',
        'last_login'
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
    }

    /**
     * Lists all shipments by given parameters
     *
     * @param Request $request
     * @return mixed
     */
    public function lists(Request $request) {

        $dataList = Customer::filterSource()
            ->with(['agency' => function($q){
                $q->select(['id', 'name']);
            }])
            ->with(['type' => function($q){
                $q->select(['id', 'name']);
            }])
            ->with(['paymentCondition' => function($q){
                $q->select(['id', 'code', 'name']);
            }])
            ->with(['route' => function($q){
                $q->select(['id', 'code', 'name']);
            }])
            ->with(['seller' => function($q){
                $q->select(['id', 'code', 'name']);
            }])
            ->whereNull('customer_id');
            
        //filter code
        if($request->has('code')) {
            $dataList = $dataList->where('code', $request->get('code'));
        }

        //payment condition
        if($request->has('payment_condition')) {
            $dataList = $dataList->where('payment_condition', $request->get('payment_condition'));
        }

        //agency
        if($request->has('agency')) {
            $dataList = $dataList->where('agency_id', $request->get('agency'));
        }

        $dataList = $dataList->take(10000)
            ->orderBy('id', 'desc')
            ->get($this->bindings);

        if(!$dataList) {
            return $this->responseError('lists', '-001') ;
        }

        return response($dataList, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Obtem o token de autenticação para um utilizador.
     *
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request) {
        return $this->update($request);
    }

    /**
     * Obtem o token de autenticação para um utilizador.
     *
     * @param Request $request
     * @return mixed
     */
    public function update(Request $request) {

        try {
            $input = $this->filterInput($request);

            //atualiza cliente
            if($request->get('code')) {

                $feedback = 'Customer updated sucessfuly';

                $customer = Customer::filterSource()
                    ->firstOrNew(['code' => $request->code]);

                if(!$customer->exists) {
                    return $this->responseError('update', '-001', 'Customer not found');
                }

                if(@$input['vat'] || @$input['billing_country']) {
                    $countInvoices = Invoice::filterSource()->where('customer_id', $customer->id)->count();
                    if($countInvoices) {
                        return $this->responseError('update', '-007', 'Cant update user VAT details (vat or billing country). This user has invoices assigned.');
                    }
                }
            }

            //Cria novo cliente
            else {
                $feedback = 'Customer created sucessfuly';
                $customer = new Customer();
            }

            if(empty(@$input['vat'])) {
                $input['vat'] = '999999990';
            } else {
                if(@$input['billing_country'] == 'pt' && !validateVatPT(@$input['vat'])) {
                    return $this->responseError('update', '-006', 'Número de contríbuinte português inválido.');
                }
            }

            if($customer->validate($input)) {
                $customer->fill($input);
                $customer->source = config('app.source');
                $result = $customer->setCode();

                if($result) {

                    //grava na base de dados central
                    $customer->storeOnCoreDB();

                    try {
                        if(!empty($customer->vat) && !in_array($customer->vat, ['999999990', '999999999'])) {

                            $class = \App\Models\InvoiceGateway\Base::getNamespaceTo('Customer');
                            $customerKeyinvoice = new $class();
                            $customerKeyinvoice->insertOrUpdateCustomer(
                                $customer->vat,
                                $customer->code,
                                $customer->billing_name,
                                $customer->billing_address,
                                $customer->billing_zip_code,
                                $customer->billing_city,
                                $customer->phone,
                                null,
                                $customer->billing_email ? $customer->billing_email : $customer->email,
                                $customer->obs,
                                $customer->billing_country,
                                $customer->payment_method,
                                $customer
                            );
                        }

                    } catch (\Exception $e) {}

                    $response = [
                        'error'   => null,
                        'message' => $feedback,
                        'code'    => $customer->code
                    ];
                    return response($response, 200)->header('Content-Type', 'application/json');
                }
            }


            return $this->responseError('destroy', '-002', $customer->errors()->first());

        } catch (\Exception $e) {
            return $this->responseError('destroy', '-999', $e->getMessage());
        }
    }

    /**
     * Delete
     *
     * @param Request $request
     * @return mixed
     */
    public function destroy(Request $request, $code) {

        $result = Customer::filterSource()
            ->where('code', $code)
            ->delete();

        if(!$result) {
            return $this->responseError('destroy', '-001', 'Customer not found.');
        }

        $response = [
            'error'   => '',
            'message' => 'Customer deleted successfully.'
        ];
        return response($response, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Store shipment custom attributes
     * @return array
     */
    public function responseError($method, $code, $message = null, $returnArr = false) {

        $errors = trans('api.customers.errors');

        $data = [
            'error'   => $code,
            'message' => $message ? $message : $errors[$method][$code]
        ];

        if($returnArr) {
            return $data;
        }

        return response($data, 404)->header('Content-Type', 'application/json');
    }

    /**
     * Filter inputs
     * @param $request
     * @return mixed
     */
    public function filterInput($request) {

        $input = $request->only($this->bindings);

        if($request->has('country')) {
            $input['country'] = strtolower($request->get('country'));
        }

        if($request->has('billing_country')) {
            $input['billing_country'] = strtolower($request->get('billing_country'));
        }


        if($request->has('agency')) {
            $input['agency_id'] = $request->get('agency');
        }

        if($request->has('type')) {
            $input['type_id'] = $request->get('type');
        }

        if($request->has('route')) {
            $input['route_id'] = $request->get('route');
        }

        if($request->has('seller')) {
            $input['seller_id'] = $request->get('seller');
        }

        $input['currency'] = $request->get('currency') ? $request->get('currency') : Setting::get('app_currency');

        return $input;
    }
}