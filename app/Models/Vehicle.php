<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Auth;

class Vehicle extends BaseModel implements Sortable
{

    use SoftDeletes, SortableTrait;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_vehicles';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'vehicles';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'license_plate', 'name', 'agencies', 'gross_weight', 'usefull_weight', 'type', 'is_default', 'operator_id', 'is_active'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'license_plate' => 'required',
        'name' => 'required',
    );
    
    /**
     * Validator custom attributes
     * 
     * @var array 
     */
    protected $customAttributes = array(
        'license_plate' => 'Matrícula',
        'name'          => 'Designação',
    );

    /**
     * Default sort column
     *
     * @var array
     */
    public $sortable = [
        'order_column_name' => 'sort'
    ];

    /**
     * Return list of vehicles
     *
     * @return mixed
     */
    public static function listVehicles($isTrailer = false, $textField = 'name', $idField = 'license_plate') {

        if(hasModule('fleet')) {
            $vehicles = \App\Models\FleetGest\Vehicle::remember(config('cache.query_ttl'))
                ->cacheTags(\App\Models\FleetGest\Vehicle::CACHE_TAG)
                ->filterSource()
                ->isActive();

                if($isTrailer) {
                    $vehicles = $vehicles->where('type', 'trailer');
                } else {
                    $vehicles = $vehicles->where(function($q){
                       $q->where('type', '<>', 'trailer');
                       $q->orWhereNull('type');
                    });
                }

            $vehicles = $vehicles->pluck($textField, $idField)
                ->toArray();

        } else {
            $vehicles = Vehicle::remember(config('cache.query_ttl'))
                ->cacheTags(Vehicle::CACHE_TAG)
                ->filterSource()
                ->filterAgencies()
                ->isActive();

            if($isTrailer) {
                $vehicles = $vehicles->where('type', 'trailer');
            } else {
                $vehicles = $vehicles = $vehicles->where(function($q){
                    $q->where('type', '<>', 'trailer');
                    $q->orWhereNull('type');
                });
            }

            $vehicles = $vehicles->ordered()
                        ->pluck($textField, $idField)
                        ->toArray();
        }

        return $vehicles;
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
    public function scopeIsActive($query, $isActive = true)
    {
        return $query->where('is_active', $isActive);
    }


    public function scopeFilterAgencies($query){
    
        $user = Auth::user();

        if($user) {
            $agencies = $user->agencies;

            if(!$user->hasRole([config('permissions.role.admin')]) || !empty($agencies)) {
                return $query->where(function($q) use($agencies) {
                    foreach ($agencies as $agency) {
                        $q->orWhere('agencies', 'like', '%'.$agency.'%');
                    }
                });
            }

        } else {
            $customer = Auth::guard('customer')->user();

            return $query->where(function($q) use($customer) {
                $q->where('agencies', 'like', '%'.@$customer->agency_id.'%');
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
    public function operator()
    {
        return $this->belongsTo('App\Models\User', 'operator_id');
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

    public function setLicensePlateAttribute($value)
    {
        $this->attributes['license_plate'] = strtoupper($value);
    }

    public function setOperatorIdAttribute($value)
    {
        $this->attributes['operator_id'] = empty($value) ? null : $value;
    }

    public function getAgenciesAttribute()
    {
        $values = @$this->attributes['agencies'];
        return json_decode($values);
    }
}
