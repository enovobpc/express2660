<?php

namespace App\Models\Equipment;

use Illuminate\Database\Eloquent\SoftDeletes;

class History extends \App\Models\BaseModel
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'equipments_history';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'equipment_id', 'action', 'location_id', 'operator_id', 'ot_code', 'obs', 'stock_low', 'stock'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'equipment_id' => 'required',
        'location_id'  => 'required',
        'action'       => 'required',
    );

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function equipment()
    {
        return $this->belongsTo('App\Models\Equipment\Equipment', 'equipment_id');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Equipment\Location', 'location_id');
    }

    public function operator()
    {
        return $this->belongsTo('App\Models\User', 'operator_id');
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
    public function setLocationIdAttribute($value)
    {
        $this->attributes['location_id'] = empty($value) ? null : $value;
    }

    public function setOperatorIdAttribute($value)
    {
        $this->attributes['operator_id'] = empty($value) ? null : $value;
    }
}
