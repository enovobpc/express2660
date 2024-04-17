<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class PickupPoint extends BaseModel
{
    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_pickup_points';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pickup_points';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'provider_id', 'provider_code', 'type', 'name', 'address', 'zip_code',
        'city', 'country', 'latitude', 'longitude', 'email', 'phone', 'mobile',
        'horary', 'is_active', 'delivery_saturday', 'delivery_sunday',
    ];


    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'provider_id'=> 'required',
        'name'       => 'required',
        'address'    => 'required',
        'zip_code'   => 'required',
        'city'       => 'required',
        'country'    => 'required'
    );

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Define current model relationships
    */
    public function provider()
    {
        return $this->belongsTo('App\Models\Provider', 'provider_id');
    }

    public function shipments()
    {
        return $this->hasMany('App\Models\Shipment', 'recipient_pudo_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    |
    | Define current Modal Scopes
    */
    public function scopeIsActive($query, $isActive = true){
        return $query->where('is_active', $isActive);
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

    public function setHoraryAttribute($value)
    {
        $this->attributes['horary'] = empty($value) ? null : json_encode($value);
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = trim($value);
    }

    public function setAddressAttribute($value)
    {
        $this->attributes['address'] = trim($value);
    }

    public function setZipCodeAttribute($value)
    {
        $this->attributes['zip_code'] = trim($value);
    }

    public function setCityAttribute($value)
    {
        $this->attributes['city'] = trim($value);
    }

    public function getHoraryAttribute()
    {
        return json_decode(@$this->attributes['horary'], true);
    }
}
