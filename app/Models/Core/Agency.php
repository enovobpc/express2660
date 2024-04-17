<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\FileTrait;
use Auth;

class Agency extends \App\Models\BaseModel {

    use SoftDeletes,
        FileTrait;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_core';

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_agencies';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'agencies';

    /**
     * Default upload directory
     *
     * @const string
     */
    const DIRECTORY = 'uploads/agencies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'name', 'print_name', 'company', 'address', 'zip_code', 'city', 'country', 'vat', 'phone', 'mobile', 'email', 'web',
        'charter', 'default_car_registration', 'color', 'agencies',
        'billing_name', 'billing_address', 'billing_zip_code', 'billing_city', 'billing_country', 'billing_email'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'code' => 'required',
        'name' => 'required',
        'print_name' => 'required'
    );

    /**
     * Validator custom attributes
     *
     * @var array
     */
    protected $customAttributes = array(
        'code' => 'Código',
        'name' => 'Nome',
        'print_name' => 'Nome para Impressão'
    );

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function billing()
    {
        return $this->hasMany('App\Models\AgencyBilling', 'agency_id');
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

    public static function listsGrouped($allAgencies){

        $results = $allAgencies->groupBy('source');

        $list = [];
        foreach ($results as $source => $agencies) {
            $list[$source] = $agencies->pluck('name','id')->toArray();
        }

        return $list;
    }

    public function scopeFilterAgencies($query){

        $user = Auth::user();
        $agencies = $user->agencies;

        if(!$user->hasRole([config('permissions.role.admin')]) || !empty($agencies)) {
            return $query->where(function($q) use($agencies) {
                foreach ($agencies as $agency) {
                    $q->orWhere('agencies', 'like', '%"'.$agency.'"%');
                }
            });
        }
    }

    public function scopeFilterMyAgencies($query){

        $user = Auth::user();
        $agencies = $user->agencies;

        if(!$user->hasRole([config('permissions.role.admin')]) || !empty($agencies)) {
            return $query->whereIn('id', $agencies);
        }
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

    public function setAgenciesAttribute($value)
    {
        $this->attributes['agencies'] = empty($value) ? null : json_encode($value);
    }

    public function setBillingNameAttribute($value) {
        $this->attributes['billing_name'] = empty($value) ? null : $value;
    }

    public function setBillingAddressAttribute($value) {
        $this->attributes['billing_address'] = empty($value) ? null : $value;
    }

    public function setBillingZipCodeAttribute($value) {
        $this->attributes['billing_zip_code'] = empty($value) ? null : $value;
    }

    public function setBillingCityAttribute($value) {
        $this->attributes['billing_city'] = empty($value) ? null : $value;
    }

    public function setBillingCountryAttribute($value) {
        $this->attributes['billing_country'] = empty($value) ? null : $value;
    }

    public function getAgenciesAttribute()
    {
        return json_decode(@$this->attributes['agencies']);
    }

    public function getBillingNameAttribute($value) {
        return empty($value) ? $this->company : $value;
    }

    public function getBillingAddressAttribute($value) {
        return empty($value) ? $this->address : $value;
    }

    public function getBillingZipCodeAttribute($value) {
        return empty($value) ? $this->zip_code : $value;
    }

    public function getBillingCityAttribute($value) {
        return empty($value) ? $this->city : $value;
    }

    public function getBillingCountryAttribute($value) {
        return empty($value) ? $this->country : $value;
    }

    public function getBillingEmailAttribute($value) {
        return empty($value) ? $this->email : $value;
    }

}