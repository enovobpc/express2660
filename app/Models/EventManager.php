<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class EventManager extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_event_manager';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'events_manager';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'name', 'customer_id', 'horary', 'observation', 'is_active'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'name' => 'required|max:255',
        'type' => 'max:25',
    );

    /*
      |--------------------------------------------------------------------------
      | Scopes
      |--------------------------------------------------------------------------
      |
      | Scopes allow you to easily re-use query logic in your models.
      | To define a scope, simply prefix a model method with scope.
      |
     */

    public function scopeFilterAgencies($query)
    {

        $user = Auth::user();

        if ($user) {
            $agencies = $user->agencies;

            if (!$user->hasRole([config('permissions.role.admin')]) || !empty($agencies)) {
                return $query->where(function ($q) use ($agencies) {
                    foreach ($agencies as $agency) {
                        $q->orWhere('agencies', 'like', '%' . $agency . '%');
                    }
                });
            }
        } else {
            $customer = Auth::guard('customer')->user();

            return $query->where(function ($q) use ($customer) {
                $q->where('agencies', 'like', '%' . @$customer->agency_id . '%');
            });
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Define current model relationships
    */
    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    /**
     * Get all of the EventProductLine for the EventManager
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function lines()
    {
        return $this->hasMany('App\Models\EventProductLine', 'event_manager_id', 'id');
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

    public function setCustomerIdAttribute($value)
    {
        $this->attributes['customer_id'] = empty($value) ? null : $value;
    }

    public function setHoraryAttribute($value)
    {
        $this->attributes['horary'] = empty($value) ? null : json_encode($value);
    }

    public function getHoraryAttribute()
    {
        $values = @$this->attributes['horary'];
        return json_decode($values);
    }
}
