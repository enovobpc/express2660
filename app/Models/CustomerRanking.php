<?php

namespace App\Models;

use DB;

class CustomerRanking extends BaseModel
{

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_customers_ranking';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customers_ranking';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id', 'year', 'month', 'shipments', 'billing'
    ];


    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'customer_id' => 'required',
        'year'        => 'required',
        'month'       => 'required',
    );

    /**
     * Get ranking by customer id
     * @param $id
     */
    public static function getByCustomer($id) {

        $allRanking = CustomerRanking::remember(8)
            ->groupBy('customer_id')
            ->get([
                'customer_id',
                DB::raw('avg(shipments) as shipments'),
                DB::raw('avg(volumes) as volumes'),
                DB::raw('avg(billing) as billing'),
            ])
            ->toArray();

        aasort($allRanking, 'shipments', SORT_DESC);
        $orderedShipments = $allRanking;

        aasort($allRanking, 'volumes', SORT_DESC);
        $orderedVolumes = $allRanking;

        aasort($allRanking, 'billing', SORT_DESC);
        $orderedBilling = $allRanking;

        //find user on all ranking
        $shipmentsPos = array_search($id, array_column($orderedShipments, 'customer_id'));
        $volumesPos   = array_search($id, array_column($orderedVolumes, 'customer_id'));
        $billingPos   = array_search($id, array_column($orderedBilling, 'customer_id'));

        $customerRanking = [];
        if($shipmentsPos !== false) {
            $customerRanking = @$orderedShipments[$shipmentsPos];
        }

        $totalCustomers  = count($allRanking);

        $data = [
            'customers' => $totalCustomers,
            'shipments' => [
                'pos_n' => @$customerRanking['shipments'] ? (int) ($shipmentsPos + 1) : 0,
                'pos_p' => @$customerRanking['shipments'] ? number($totalCustomers ? (100 - (($shipmentsPos * 100) / $totalCustomers)) : 0.00) : 0,
                'avg'   => number(@$customerRanking['shipments'])
            ],
            'volumes' => [
                'pos_n' => @$customerRanking['volumes'] ? (int) ($volumesPos + 1) : 0,
                'pos_p' => @$customerRanking['volumes'] ? number($totalCustomers ? (100 - (($volumesPos * 100) / $totalCustomers)) : 0.00) : 0,
                'avg'   => number(@$customerRanking['volumes'])
            ],
            'billing' => [
                'pos_n' => @$customerRanking['billing'] ? (int) ($billingPos + 1) : 0,
                'pos_p' => @$customerRanking['billing'] ? number($totalCustomers ? (100 - (($billingPos * 100) / $totalCustomers)) : 0.00) : 0,
                'avg'   => number(@$customerRanking['billing'])
            ]
        ];

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Define current model relationships
    */
    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    |
    | Scopes allow you to easily re-use query logic in your models.
    | To define a scope, simply prefix a model method with scope.
    |
    */
    public function scopeFilterSource($query) {
        return $query->where(function ($q){
            $q->where('source', config('app.source'));
            $q->orWhereNull('source');
        });
    }

   /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    |
    | Eloquent provides a convenient way to transform your model attributes when 
    | getting or setting them. Simply define a 'getFooAttribute' method on your model 
    | to declare an accessor. Keep in mind that the methods should follow camel-casing, 
    | even though your database columns are snake-case.
    |
    */

    public function setShipmentsAttribute($value) {
        $this->attributes['shipments'] = empty($value) ? null : $value;
    }

    public function setBillingAttribute($value) {
        $this->attributes['billing'] = empty($value) ? null : $value;
    }
}
