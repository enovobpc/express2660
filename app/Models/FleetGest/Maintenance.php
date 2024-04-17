<?php

namespace App\Models\FleetGest;

use App\Models\Billing\ItemStockHistory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\FileTrait;
use Auth;

class Maintenance extends \App\Models\BaseModel
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
    const CACHE_TAG = 'fleet_maintenances';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fleet_maintenances';


    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['date'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'vehicle_id', 'provider_id', 'operator_id', 'service_id',
        'incidence_id', 'title', 'description', 'km', 'total', 'date',
        'created_by', 'assigned_invoice_id'
    ];

    /**
     * Default upload directory
     *
     * @const string
     */
    const DIRECTORY = 'uploads/fleet/maintenance';

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'vehicle_id'    => 'required',
        'provider_id'   => 'required',
        'title'         => 'required',
        'date'          => 'required',
    );
    
    /**
     * Validator custom attributes
     * 
     * @var array 
     */
    protected $customAttributes = array(
        'vehicle_id'    => 'Viatura',
        'title'         => 'Título',
        'date'          => 'Data'
    );


    /**
     * Update vehicle km and consumption average counters
     *
     * @param $vehicleId
     */
    public static function updateVehicleCounters($vehicleId)
    {
        $vehicle = Vehicle::filterSource()->find($vehicleId);

        $maintenance = Maintenance::where('vehicle_id', $vehicleId)
            ->orderBy('km', 'desc')
            ->first();

        if ($vehicle->counter_km < $maintenance->km) {
            $vehicle->counter_km = $maintenance->km;
            return $vehicle->save();
        }

        return true;
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */

    public function vehicle()
    {
        return $this->belongsTo('App\Models\FleetGest\Vehicle', 'vehicle_id');
    }

    public function provider()
    {
        return $this->belongsTo('App\Models\Provider', 'provider_id');
    }

    public function service()
    {
        return $this->belongsTo('App\Models\FleetGest\Service', 'service_id');
    }

    public function operator()
    {
        return $this->belongsTo('App\Models\User', 'operator_id');
    }

    // public function parts()
    // {
    //     return $this->belongsToMany('App\Models\FleetGest\Part', 'fleet_maintenance_assigned_parts', 'maintenance_id', 'part_id')
    //         ->withPivot('qty');
    // }

    public function parts() {
        return $this->hasMany('App\Models\Billing\ItemStockHistory', 'target_id', 'id')
            ->where('target', ItemStockHistory::TARGET_MAINTENANCE);
    }

    public function creator()
    {
        return $this->belongsTo('App\Models\User', 'created_by');
    }

    public function invoice()
    {
        return $this->belongsTo('App\Models\PurchaseInvoice', 'assigned_invoice_id');
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
    public function scopeFilterSource($query) {
        return $query->whereHas('vehicle', function($q){
            $q->filterSource();
            $q->filterAgencies();
        });
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
    public function setOperatorIdAttribute($value)
    {
        $this->attributes['operator_id'] = empty($value) ? null : $value;
    }
}
