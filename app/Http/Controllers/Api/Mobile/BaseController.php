<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Models\User;
use Auth, Validator, Setting;

class BaseController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Get user from api auth token
     *
     * @param $authToken
     * @return bool
     */
    public function getUser($authToken) {

        if(empty($authToken)) {
            return false;
        }

        $user = User::where('api_token', $authToken)->first();

        if(empty($user)) {
            return false;
        }

        return $user;
    }

    /**
     * Store shipment custom attributes
     * @return array
     */
    public function responseError($method, $code, $message = null) {

        $errors = trans('api.shipments.errors');

        $data = [
            'code'     => $code,
            'feedback' => $message ? $message : $errors[$method][$code],

            //só no login
            'error'    => $code,
            'message'  => $message ? $message : $errors[$method][$code]
        ];

        return response($data, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Store user location
     * @param $lat
     * @param $lng
     */
    public function storeUserLocation($operator, $lat, $lng) {
        if(!empty($lat) && !empty($lng) && $lat != 0.0 && $lng != 0.0) {
            $operator->location_last_update = date('Y-m-d H:i:s');
            $operator->location_enabled     = true;
            $operator->location_denied      = false;
            $operator->location_lat         = $lat;
            $operator->location_lng         = $lng;
            $operator->save();
        }
    }
}