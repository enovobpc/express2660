<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class CreditCard extends \App\Models\BaseModel {

    use SoftDeletes;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_enovo';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'credit_cards';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'name', 'number', 'ccv', 'month', 'year', 'country'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'number' => 'required',
        'ccv'    => 'required',
        'month'  => 'required',
        'year'   => 'required'
    );

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

    public function setNumberAttribute($value)
    {
        $this->attributes['number'] = str_replace(' ', '', $value);
    }

    public function setCcvAttribute($value)
    {
        $this->attributes['ccv'] = str_replace(' ', '', $value);
    }

}
