<?php

namespace App\Models\Logistic;

use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class DevolutionItem extends \App\Models\BaseModel
{

    use SoftDeletes;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_logistic';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'devolutions_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'devolution_id', 'product_id', 'location_id', 'qty', 'status', 'obs'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'product_id'  => 'required',
        'location_id' => 'required'
    );

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */

    public function devolution()
    {
        return $this->belongsTo('App\Models\Logistic\Devolution', 'devolution_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Logistic\Product', 'product_id');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Logistic\Location', 'location_id');
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
}
