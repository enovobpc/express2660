<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class PriceTable extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_prices_tables';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'prices_tables';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'source', 'active', 'agencies', 'color'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'name'      => 'required',
    );


    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function customers()
    {
        return $this->hasMany('App\Models\Customer', 'price_table_id');
    }

    public function services()
    {
        return $this->belongsToMany('App\Models\Service', 'customers_assigned_services', 'price_table_id', 'service_id')
            ->whereNull('deleted_at')
            ->withPivot('min', 'max', 'price', 'zone', 'is_adicional', 'adicional_unity');
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

    public function scopeIsActive($query, $active = true){
        return $query->where('active', $active);
    }

    public function scopeFilterAgencies($query, $agencies = null){

        $user = Auth::user();

        if(!$agencies) {
            $agencies = $user->agencies;
        }

        if(!$user->hasRole([config('permissions.role.admin')]) || !empty($agencies)) {

             $query->where(function($q) use($agencies) {
                foreach ($agencies as $agency) {
                    $q->orWhere('agencies', 'like', '%"'.$agency.'"%');
                }
            });
        }

        return $query;
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

    public function setCustomerIdAttribute($value)
    {
        $this->attributes['customer_id'] = empty($value) ? null : $value;
    }

/*    public function setSourceAttribute($value)
    {
        $this->attributes['source'] = empty($value) ? null : $value;
    }*/
    
    public function getAgenciesAttribute()
    {
        return json_decode(@$this->attributes['agencies']);
    }
}
