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
use App\Models\PurchaseInvoice;
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

class ProvidersController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Bindings
     *
     * @var array
     */
    protected $bindings = [
        'code',
        'type',
        'name',
        'vat',
        'company',
        'address',
        'zip_code',
        'city',
        'country',
        'attn',
        'email',
        'billing_email',
        'phone',
        'mobile',
        //'agency_id',
        'category_id',
        'obs',
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

        $dataList = Provider::filterSource()
            ->with(['category' => function($q){
                $q->select(['id', 'name']);
            }])
            ->with(['paymentCondition' => function($q){
                $q->select(['id', 'code', 'name']);
            }]);
            
        //filter code
        if($request->has('code')) {
            $dataList = $dataList->where('code', $request->get('code'));
        }

        //filter type
        if($request->has('type')) {
            $dataList = $dataList->where('type', $request->get('type'));
        }

        //payment condition
        if($request->has('payment_condition')) {
            $dataList = $dataList->where('payment_method', $request->get('payment_condition'));
        }

        //filter category
        if($request->has('category')) {
            $dataList = $dataList->where('category_id', $request->get('category'));
        }

        $dataList = $dataList->take(10000)
            ->orderBy('id', 'asc')
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

                $feedback = 'Provider updated sucessfuly';

                $provider = Provider::filterSource()
                    ->firstOrNew(['code' => $request->code]);

                if(!$provider->exists) {
                    return $this->responseError('update', '-001', 'Customer not found');
                }

                if(@$input['vat'] || @$input['country']) {
                    $countInvoices = PurchaseInvoice::filterSource()->where('provider_id', $provider->id)->count();
                    if($countInvoices) {
                        return $this->responseError('update', '-007', 'Cant update provider VAT details (vat or billing country). This user has invoices assigned.');
                    }
                }
            }

            //Cria novo provider
            else {
                $feedback = 'Provider created sucessfuly';
                $provider = new Provider();
            }

            if(empty(@$input['vat'])) {
                $input['vat'] = '999999990';
            } else {
                if(@$input['country'] == 'pt' && !validateVatPT(@$input['vat'])) {
                    return $this->responseError('update', '-006', 'Número de contríbuinte português inválido.');
                }
            }

            if($provider->validate($input)) {
                $provider->fill($input);
                $provider->source = config('app.source');
                $result = $provider->setCode();

                if($result) {
                    $response = [
                        'error'   => null,
                        'message' => $feedback,
                        'code'    => $provider->code
                    ];
                    return response($response, 200)->header('Content-Type', 'application/json');
                }
            }


            return $this->responseError('destroy', '-002', $provider->errors()->first());

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

        $result = Provider::filterSource()
            ->where('code', $code)
            ->delete();

        if(!$result) {
            return $this->responseError('destroy', '-001', 'Provider not found.');
        }

        $response = [
            'error'   => '',
            'message' => 'Provider deleted successfully.'
        ];
        return response($response, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Store shipment custom attributes
     * @return array
     */
    public function responseError($method, $code, $message = null, $returnArr = false) {

        $errors = trans('api.providers.errors');

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

        if($request->has('agency')) {
            $input['agency_id'] = $request->get('agency');
        }

        if($request->has('category')) {
            $input['category_id'] = $request->get('category');
        }

        if($request->has('country')) {
            $input['country'] = strtolower($request->get('country'));
        }
        
        return $input;
    }
}