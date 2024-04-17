<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Models\Customer;
use Illuminate\Http\Request;
use Auth, Validator, Setting, Mail, Date;

class CustomersController extends \App\Http\Controllers\Api\Mobile\BaseController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Bindings
     *
     * @var array
     */
    protected $bindings = [
        'code',
        'code_abbrv',
        'name',
        'display_name',
        'vat',
        'address',
        'zip_code',
        'city',
        'country',
        'map_lat',
        'map_lng',
        'filehost',
        'filepath',
        'route_id',
        'balance_total_unpaid',
        'balance_count_unpaid',
        'balance_count_expired',
        'balance_last_update',
    ];

    /**
     * Lists all customers
     *
     * @param Request $request
     * @return mixed
     */
    public function lists(Request $request) {

        $user = $this->getUser($request->get('user'));

        if(!$user) {
            return $this->responseError('login', '-002') ;
        }

        $operators = Customer::with(['route' => function($q){
                $q->select(['id', 'code', 'name', 'color']);
            }])
            ->filterAgencies()
            ->where('source', config('app.source'))
            ->where('is_active', 1);

        if($request->has('route')) {
            $operators = $operators->where('route_id', $request->get('route'));
        }

        $operators = $operators->orderBy('name', 'asc')
            ->get($this->bindings);

        if(!$operators) {
            return $this->responseError('lists', '-001') ;
        }

        return response($operators, 200)->header('Content-Type', 'application/json');
    }
}