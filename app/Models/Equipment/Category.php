<?php

namespace App\Models\Equipment;

use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends \App\Models\BaseModel
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'equipments_categories';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'name', 'color'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'code'  => 'required',
        'name'  => 'required',
    );

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function equipments()
    {
        return $this->hasMany('App\Models\Equipment\Equipment', 'category_id');
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
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = trim($value);
    }

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper(trim($value));
    }

}