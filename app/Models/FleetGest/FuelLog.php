<?php

namespace App\Models\FleetGest;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\FileTrait;
use Auth, Date;

class FuelLog extends  \App\Models\BaseModel
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
    const CACHE_TAG = 'cache_fleet_fuel_log';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fleet_fuel_log';

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
        'vehicle_id', 'provider_id', 'operator_id', 'km', 'liters', 'price_per_liter',
        'total', 'invoice', 'obs', 'date', 'assigned_invoice_id', 'product'
    ];


    /**
     * Default upload directory
     * 
     * @const string
     */
    const DIRECTORY = 'uploads/vehicles/fuel';

    
    /**
     * Validator rules
     * 
     * @var array 
     */
    public $rules = [
        'vehicle_id'        => 'required',
        'provider_id'       => 'required',
        'liters'            => 'required',
        'price_per_liter'   => 'required',
        'total'             => 'required',
        'date'              => 'required|date'
    ];

    /**
     * Update vehicle km and consumption average counters
     *
     * @param $vehicleId
     */
    public static function updateVehicleCounters($vehicleId)
    {
        $iterations = 500;

        $vehicle = Vehicle::filterSource()->find($vehicleId);

        $fuelLogs = FuelLog::where('vehicle_id', $vehicleId)
            ->orderBy('date', 'asc')
            ->orderBy('km', 'asc')
            ->where('product', 'fuel')
            ->take($iterations)
            ->get(['vehicle_id', 'km', 'liters', 'date', 'total', 'id', 'provider_id', 'operator_id']);

        $avgConsumption = 0;
        $iterations = 0;
        $maxKm = 0;
        foreach ($fuelLogs->values() as $key => $value){
            $currentRow = $value;
            $nextRow    = $fuelLogs->get(++$key);

            if(!empty($nextRow->km)) {
                $iterations++;

                $startDate = new Date($currentRow->date);
                $endDate  = new Date($nextRow->date);

                $diffKm  = $nextRow->km - $currentRow->km;
                $literKm = 0;
                if($diffKm) {
                    $literKm = (100 * $currentRow->liters) / $diffKm;
                }
                $avgConsumption+= $literKm;

                $value->balance_km       = $diffKm;
                $value->balance_time     = $endDate->diffInSeconds($startDate);
                $value->balance_liter_km = $literKm;
                $value->save();
            }

            $maxKm = $value->km > 0 ? $value->km : $maxKm; //ultimo registo será sempre o maxKm
        }

        if($iterations) {
            $avgConsumption = $avgConsumption / $iterations;
        }

        $vehicle->counter_consumption = $avgConsumption;
        $vehicle->counter_km = $maxKm;
        return $vehicle->save();
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

    public function operator()
    {
        return $this->belongsTo('App\Models\User', 'operator_id');
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

    public function getIsAdblueAttribute() {
        return ($this->product == 'adblue');
    }
}
