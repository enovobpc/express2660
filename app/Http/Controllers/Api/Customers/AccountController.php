<?php

namespace App\Http\Controllers\Api\Customers;

use App\Models\Api\OauthClient;
use App\Models\LogViewer;
use Illuminate\Http\Request;
use Auth, Validator, Setting, Mail, Log, DB, Date;

class AccountController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {

        $this->usage_exceed = false;
        $this->log_usage    = Setting::get('api_debug_mode') ? true : false;

        $customer = Auth::guard('api')->user();
        $oauth    = OauthClient::where('user_id', $customer->id)->first();

        $lastCallDate = new Date($oauth->last_call);
        $lastCallDate = $lastCallDate->format('Y-m-d');

        if($lastCallDate == date('Y-m-d')) {
            $oauth->daily_counter+= 1;
            $oauth->last_call     = date('Y-m-d H:i:s');

            if($oauth->daily_counter > $oauth->daily_limit) {
                $this->usage_exceed   = true;
            }
        } else {
            $oauth->daily_counter = 1;
            $oauth->last_call     = date('Y-m-d H:i:s');
        }
        $oauth->save();
    }

    /**
     * Lists all shipments by given parameters
     *
     * @param Request $request
     * @return mixed
     */
    public function details(Request $request, $apiLevel) {

        $customer = Auth::guard('api')->user();
        $this->logUsage($customer, 'listsProducts');

        if($this->checkUsageLimit()) {
            return $this->responseUsageExceed();
        }

        $customer = $customer->first([
            'code',
            'name',
            'address',
            'zip_code',
            'city',
            'country',
            'phone',
            'mobile',
            'contact_email',
            'vat',
            'billing_name',
            'billing_address',
            'billing_zip_code',
            'billing_city',
            'billing_country',
            'billing_email',
        ]);

        return response($customer, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Return RGPD status
     *
     * @param Request $request
     * @return mixed
     */
    public function rgpdStatus(Request $request, $apiLevel, $action) {

        $status = [
            'status' => 'approved'
        ];

        return response($status, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Request RGPD changes
     *
     * @param Request $request
     * @return mixed
     */
    public function rgpdRequest(Request $request, $apiLevel) {

        $status = [
            'status' => 'accepted'
        ];

        return response($status, 200)->header('Content-Type', 'application/json');
    }

    /**
     * @return string|\Symfony\Component\Translation\TranslatorInterface
     */
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
            $trace = LogViewer::getTrace(null, 'API ACCOUNT - '.$method.' - CUSTOMER ' . $customer->name);
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