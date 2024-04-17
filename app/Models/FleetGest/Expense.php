<?php

namespace App\Models\FleetGest;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\FileTrait;
use Auth;

class Expense extends \App\Models\BaseModel
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
    const CACHE_TAG = 'cache_fleet_expenses';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fleet_expenses';

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
        'vehicle_id', 'provider_id', 'operator_id', 'expense_id', 'type_id', 'title', 'description', 'km', 'total', 'date', 'obs'
    ];

    /**
     * Default upload directory
     *
     * @const string
     */
    const DIRECTORY = 'uploads/fleet/expenses';

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'vehicle_id'    => 'required',
        'expense_id'    => 'required',
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
        'expense_id'    => 'Despesa',
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

        $expense = Expense::where('vehicle_id', $vehicleId)
            ->orderBy('km', 'desc')
            ->first();

        if ($vehicle->km < $expense->km) {
            $vehicle->km = $expense->km;
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

    public function operator()
    {
        return $this->belongsTo('App\Models\User', 'operator_id');
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

    public function setProviderIdAttribute($value)
    {
        $this->attributes['provider_id'] = empty($value) ? null : $value;
    }

    public function setKmAttribute($value)
    {
        $this->attributes['km'] = empty($value) ? null : $value;
    }
}
