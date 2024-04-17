<?php

namespace App\Models\AirWaybill;

use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class Model extends \App\Models\BaseModel
{

    use SoftDeletes;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_awb';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'air_waybills_models';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'reference', 'currency', 'customs_status', 'charge_code', 'value_for_carriage',
        'value_for_customs', 'value_insurance', 'agent_id',
        'customer_id', 'sender_vat', 'sender_name', 'sender_address',
        'consignee_id', 'consignee_vat', 'consignee_name', 'consignee_address',
        'provider_id', 'issuer_name', 'issuer_address',
        'source_airport', 'recipient_airport', 'flight_scales',
        'handling_info', 'nature_quantity_info', 'adicional_info', 'accounting_info', 'obs', 'goods_type_id', 'expenses'
    ];


    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'name'    => 'required',
    );


    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function sourceAirport()
    {
        return $this->belongsTo('App\Models\AirWaybill\IataAirport', 'source_airport', 'code');
    }

    public function recipientAirport()
    {
        return $this->belongsTo('App\Models\AirWaybill\IataAirport', 'recipient_airport', 'code');
    }

    public function provider()
    {
        return $this->belongsTo('App\Models\AirWaybill\Provider', 'provider_id');
    }

    public function goodType()
    {
        return $this->belongsTo('App\Models\AirWaybill\GoodType', 'goods_type_id');
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
    public function setCustomerIdAttribute($value)
    {
        $this->attributes['customer_id'] = empty($value) ? null : $value;
    }
    public function setProviderIdAttribute($value)
    {
        $this->attributes['provider_id'] = empty($value) ? null : $value;
    }
    public function setConsigneeIdAttribute($value)
    {
        $this->attributes['consignee_id'] = empty($value) ? null : $value;
    }
    public function setIssuerIdAttribute($value)
    {
        $this->attributes['issuer_id'] = empty($value) ? null : $value;
    }
    public function setFlightScalesAttribute($value)
    {
        $this->attributes['flight_scales'] = empty($value) ? null : json_encode($value);
    }
    public function setGoodsAttribute($value)
    {
        $this->attributes['goods'] = empty($value) ? null : json_encode($value);
    }
    public function setExpensesAttribute($value)
    {
        $this->attributes['expenses'] = empty($value) ? null : json_encode($value);
    }
    public function setOtherExpensesAttribute($value)
    {
        $this->attributes['other_expenses'] = empty($value) ? null : json_encode($value);
    }
    public function setValueForCarriageAttribute($value)
    {
        $this->attributes['value_for_carriage'] = empty($value) ? null : $value;
    }
    public function setValueForCustomsAttribute($value)
    {
        $this->attributes['value_for_customs'] = empty($value) ? null : $value;
    }
    public function setValueInsuranceAttribute($value)
    {
        $this->attributes['value_insurance'] = empty($value) ? null : $value;
    }
    public function getFlightScalesAttribute($value)
    {
        return json_decode($value);
    }
    public function getGoodsAttribute($value)
    {
        return json_decode($value);
    }
    public function getExpensesAttribute($value)
    {
        return empty($value) ? [] : json_decode($value);
    }
    public function getOtherExpensesAttribute($value)
    {
        return empty($value) ? [] : json_decode($value);
    }
}
