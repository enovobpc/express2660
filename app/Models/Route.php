<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Auth;

class Route extends BaseModel implements Sortable
{

    use SoftDeletes, SortableTrait;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_routes';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'routes';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'route_group_id', 'code', 'name', 'agencies', 'color', 'zip_codes',
        'services', 'schedules', 'operator_id', 'provider_id', 'type'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'name' => 'required',
    );
    
    /**
     * Validator custom attributes
     * 
     * @var array 
     */
    protected $customAttributes = array(
        'code' => 'Código',
        'name' => 'Nome',
    );

    /**
     * Default sort column
     *
     * @var array
     */
    public $sortable = [
        'order_column_name' => 'sort'
    ];

    
     /*
      |--------------------------------------------------------------------------
      | Scopes
      |--------------------------------------------------------------------------
      |
      | Scopes allow you to easily re-use query logic in your models.
      | To define a scope, simply prefix a model method with scope.
      |
     */
    
    public function scopeFilterAgencies($query){
    
        $user = Auth::user();
        $agencies = $user->agencies;
        
        if(!$user->hasRole([config('permissions.role.admin')]) || !empty($agencies)) {
            return $query->where(function($q) use($agencies) {
                foreach ($agencies as $agency) {
                    $q->orWhere('agencies', 'like', '%'.$agency.'%');
                }
            });
        }
    }

    private function queryHelperFilterSchedules($query, $field, $value) {
        if (is_array($value)) {
            $first = true;
            foreach ($value as $id) {
                if (!$first) {
                    $query = $query->orWhere('schedules', 'LIKE', '%"'. $field .'":"'. $id .'"%');
                    continue;
                }

                $first = false;
                $query = $query->where('schedules', 'LIKE', '%"'. $field .'":"'. $id .'"%');
            }
        } else {
            $query = $query->where('schedules', 'LIKE', '%"'. $field .'":"'. $value .'"%');
        }

        return $query;
    }

    public function scopeFilterOperator($query, $value) {
        return $this->queryHelperFilterSchedules($query, 'operator', $value);
    }

    public function scopeFilterProvider($query, $value) {
        return $this->queryHelperFilterSchedules($query, 'provider', $value);
    }


    public static function listsWithCode($allRoutes){

        if($allRoutes->isEmpty()) {
            return [];
        }

        $list = [];
        foreach ($allRoutes as $route) {
            $list+=[$route->id => $route->code.' - '. $route->name];
        }

        return $list;

    }

    /**
     * Get route from zip code
     * 
     * @param string|Shipment $zipCode
     * @param string $source
     * @param string $type
     * @return Route
     */
    public static function getRouteFromZipCode($zipCode, $serviceId = null, $source = null, $type = null)
    {
        $source = empty($source) ? config('app.source') : $source;

        if(!empty($zipCode)) {
            $fullZipCode  = $zipCode;
            $zipCodeParts = explode('-', $fullZipCode);
            $zipCode4     = trim($zipCodeParts[0]);

            $routes = Route::whereSource($source)
                ->where(function($q) use($type, $serviceId) {
                    if($type == 'delivery' || $type == 'pickup') {
                        $q->where(function ($q) use($type) {
                            $q->where('type', $type);
                            $q->orWhereNull('type');
                        });
                    } else {
                        $q->whereNull('type');
                    }

                    // Filter route by service
                    if (!empty($serviceId)) {
                        $q->where(function ($q) use ($serviceId) {
                            $q->where('services', 'LIKE', '%"'. $serviceId .'"%');
                            $q->orWhereNull('services');
                        });
                    } else {
                        $q->whereNull('services');
                    }
                    //--

                })->get();

            //encontra se existe o codigo postal completo 0000-000 em alguma rota.
            $route = $routes->filter(function($item) use($fullZipCode) {
                return in_array($fullZipCode, $item->zip_codes);
            })->first();

            if(empty($route)) {
                //não encontrou codigo postal completo, tenta localizar rotas com o codigo postal de 4 digitos apenas
                $route = $routes->filter(function($item) use($zipCode4) {
                    return in_array($zipCode4, $item->zip_codes);
                })->first();
            }

            return $route;
        }

        return new Route();
    }

    /**
     * Get schedule based on hour
     * 
     * @param string $hour
     * @return array|null
     */
    public function getSchedule($startHour = null, $endHour = null) {
        if (empty($this->schedules)) {
            return null;
        }

        if (empty($startHour)) {
            $startHour = date('H:i');
        }

        foreach ($this->schedules as $schedule) {
            if ($startHour >= $schedule['min_hour'] && $startHour <= $schedule['max_hour']) {
                if (!empty($schedule['operator'])) {
                    $schedule['operator'] = User::select(['id', 'name', 'vehicle'])
                        ->find($schedule['operator'])
                        ->toArray();
                }

                if (!empty($schedule['provider'])) {
                    $schedule['provider'] = Provider::select(['id', 'code', 'name', 'color'])
                        ->find($schedule['provider'])
                        ->toArray();
                }

                return $schedule;
            }
        }

        return null;
    }

    /**
     * Schedule has operator or provider
     * 
     * @param string|int $operatorId
     * @param string|int $providerId
     * @return bool
     */
    public function scheduleHas($operatorId = null, $providerId = null) {
        if (empty($operatorId) && empty($providerId)) {
            return false;
        }

        foreach ($this->schedules as $schedule) {
            if (!empty($operatorId) && $schedule['operator'] == $operatorId) {
                return true;
            }

            if (!empty($providerId) && $schedule['provider'] == $providerId) {
                return true;
            }
        }

        return false;
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */

    public function customers()
    {
        return $this->hasMany('App\Models\Customer', 'route_id');
    }

    public function provider()
    {
        return $this->belongsTo('App\Models\Provider', 'provider_id');
    }

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

    public function setProviderIdAttribute($value)
    {
        $this->attributes['provider_id'] = empty($value) ? null : $value;
    }

    public function setOperatorIdAttribute($value)
    {
        $this->attributes['operator_id'] = empty($value) ? null : $value;
    }

    public function setTypeAttribute($value)
    {
        $this->attributes['type'] = empty($value) ? null : $value;
    }

    public function setZipCodesAttribute($value)
    {
        $this->attributes['zip_codes'] = empty($value) ? null : json_encode($value);
    }

    public function setServicesAttribute($value)
    {
        $this->attributes['services'] = empty($value) ? null : json_encode($value);
    }

    public function setSchedulesAttribute($value)
    {
        $this->attributes['schedules'] = empty($value) ? null : json_encode($value);
    }

    public function getAgenciesAttribute()
    {
        return json_decode(@$this->attributes['agencies']);
    }

    public function getZipCodesAttribute($value)
    {
        return empty($value) ? [] : json_decode($value);
    }

    public function getZipCodesArrAttribute()
    {
        return explode(',', @$this->attributes['zip_codes']);
    }

    public function getServicesAttribute($value)
    {
        return empty($value) ? [] : json_decode($value);
    }

    public function getSchedulesAttribute($value)
    {
        return empty($value) ? [] : json_decode($value, true);
    }
}
