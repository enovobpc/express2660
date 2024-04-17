<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Setting, Mail, Auth;

class RefundControlRequest extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_refunds_control_requests';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'refunds_control_requests';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id', 'shipments', 'requested_method', 'payment_method', 'payment_date',
        'status', 'total', 'customer_obs', 'obs'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'customer_id' => 'required',
        'shipments'   => 'required',
        'requested_method'  => 'required',
    );


    /**
     * Limit query to user agencies
     * Atenção! Existe uma cópia desta função no modelo "Customers"
     *
     * @return type
     */
    public function scopeFilterCustomer($query, $customer = null){

        if(empty($customer)) {
            $customer = @Auth::guard('customer')->user();
        }

        if($customer) {
            if($customer->customer_id) {
                if(!empty($customer->view_parent_shipments)) {
                    return $query->where(function($q) use($customer){
                        $q->where('customer_id', $customer->customer_id);
                    });
                } else {
                    return $query->where('department_id', $customer->id);
                }
            } else {
                return $query->where(function($q) use($customer){
                    $q->where('customer_id', $customer->id);
                });
            }
        }
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
        return $this->hasOne('App\Models\Customer', 'id', 'customer_id');
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

    public function setRequestedMethodAttribute($value)
    {
        $this->attributes['requested_method'] = empty($value) ? null : $value;
    }

    public function setShipmentsAttribute($value)
    {
        $this->attributes['shipments'] = empty($value) ? null : json_encode($value);
    }

    public function getShipmentsAttribute()
    {
        return json_decode(@$this->attributes['shipments'], true);
    }
}
