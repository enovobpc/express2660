<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\FileTrait;
use Auth;

class Company extends BaseModel
{

    use SoftDeletes,
        FileTrait;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_companies';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'companies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'vat', 'name', 'display_name', 'address', 'zip_code', 'city', 'state', 'country', 'phone', 'mobile', 'email', 'website',
        'capital', 'charter', 'conservatory'
    ];

    /**
     * Default upload directory
     *
     * @const string
     */
    const DIRECTORY = 'uploads/agencies';

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'name' => 'required',
        'vat'  => 'required'
    );

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function agencies()
    {
        return $this->hasMany('App\Models\Agency', 'company_id');
    }

    public function users() {
        return $this->hasMany('App\Models\User', 'company_id');
    }

    public function customers() {
        return $this->hasMany('App\Models\Customer', 'company_id');
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

    public function setDisplayNameAttribute($value)
    {
        $this->attributes['display_name'] = empty($value) ? $this->attributes['name'] : trim($value);
    }

    public function setVatAttribute($value)
    {
        $this->attributes['vat'] = trim(str_replace(' ', '', $value));
    }
}
