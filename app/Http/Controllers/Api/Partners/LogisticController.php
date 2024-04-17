<?php

namespace App\Http\Controllers\Api\Partners;

use Illuminate\Http\Request;
use Auth, Validator, Setting, Mail, Log, DB;

class LogisticController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
    }

    public function listsShippingOrders(Request $request) {
        $request = new Request(['_partner_api_' => true] + $request->toArray());
        $logisticController = new \App\Http\Controllers\Api\Customers\LogisticController();
        return $logisticController->listsShippingOrders($request, 'partners');
    }
}