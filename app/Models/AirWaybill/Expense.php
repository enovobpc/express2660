<?php

namespace App\Models\AirWaybill;

use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class Expense extends \App\Models\BaseModel
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
    protected $table = 'air_waybills_expenses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'name', 'type'
    ];


    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'code' => 'required',
        'name' => 'required',
    );


    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function awb()
    {
        return $this->belongsToMany('App\Models\AirWaybill\Waybill', 'air_waybills_expenses', '', 'provider_id');
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
    public function setPricesAttribute($value)
    {
        $this->attributes['prices'] = empty($value) ? null : json_encode($value);
    }
    public function getPricesAttribute($value)
    {
        return empty($value) ? [] : json_decode($value);
    }

}
