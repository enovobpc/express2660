<?php

namespace App\Models\FleetGest;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\FileTrait;
use Auth;

class Brand extends \App\Models\BaseModel
{

    use SoftDeletes,
        FileTrait;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_fleet';

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_fleet_brands';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fleet_brands';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'name'
    ];

    /**
     * Default upload directory
     *
     * @const string
     */
    const DIRECTORY = 'uploads/fleet_brands';

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'name'          => 'required',
    );


    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */

    public function vehicles()
    {
        return $this->hasMany('App\Models\FleetGest\Veichle', 'brand_id');
    }

    public function models()
    {
        return $this->hasMany('App\Models\FleetGest\BrandModel', 'brand_id');
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
}
