<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerCovenant extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_customers_covenants';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customers_covenants';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id', 'type', 'description', 'max_shipments', 'service_id', 'amount', 'start_date', 'end_date'
    ];
    
    /**
     * The attributes that are dates.
     * 
     * @var type 
     */
    protected $dates = [
         'start_date', 'end_date'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'type'          => 'required',
        'description'   => 'required',
        'start_date'    => 'required',
        'end_date'      => 'required',
    );

    /**
     * Filter covenants between two dates
     *
     * @param $query
     * @param $periodFirstDay
     * @param $periodLastDay
     * @return mixed
     */
    public function scopeFilterBetweenDates($query, $periodFirstDay, $periodLastDay) {

      
        return $query->where(function($q) use ($periodFirstDay, $periodLastDay) {
            $q->where(function ($q) use ($periodFirstDay, $periodLastDay){
                $q->where('start_date', '<=', $periodFirstDay);
                $q->orWhereBetween('start_date', [$periodFirstDay, $periodLastDay]);
            });
            $q->where('end_date', '>=', $periodLastDay);
        })
        ->orWhere(function ($q) use ($periodFirstDay, $periodLastDay){
            $q->where(function ($q) use ($periodFirstDay, $periodLastDay){
                $q->where('start_date', '<=', $periodFirstDay);
                $q->orWhereBetween('start_date', [$periodFirstDay, $periodLastDay]);
            });
            $q->whereBetween('end_date', [$periodFirstDay, $periodLastDay]);
        });
    }

    public function scopeFilterSource($query) {
        return $query->whereHas('customer', function($q){
            $q->filterSource();
        });
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

    public function service()
    {
        return $this->belongsTo('App\Models\Service');
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
    public function setServiceIdAttribute($value)
    {
        $this->attributes['service_id'] = empty($value) ? null : $value;
    }
}
