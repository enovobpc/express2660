<?php

namespace App\Models\FleetGest;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\FileTrait;
use Auth, Date;

class Tyre extends  \App\Models\BaseModel
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
    const CACHE_TAG = 'cache_tyres';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fleet_tyres';

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
        'vehicle_id', 'provider_id', 'operator_id',  'position_id',
        'reference', 'kms', 'date', 'end_date', 'end_kms', 'size',
        'brand', 'model', 'position', 'total', 'measurements', 'depth',
        'total', 'invoice', 'obs', 'assigned_invoice_id', 'duration_km', 'duration_days'
    ];


    /**
     * Default upload directory
     * 
     * @const string
     */
    const DIRECTORY = 'uploads/vehicles/tyres';

    
    /**
     * Validator rules
     * 
     * @var array 
     */
    public $rules = [
        'vehicle_id'        => 'required',
        'position_id'       => 'required',
        'kms'               => 'required',
        'date'              => 'required|date'
    ];

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

    public function position()
    {
        return $this->belongsTo('App\Models\FleetGest\TyrePosition', 'position_id');
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

    public function setProviderIdAttribute($value)
    {
        $this->attributes['provider_id'] = empty($value) ? null : $value;
    }

    public function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = empty($value) ? null : $value;
    }

    public function setMeasurementsAttribute($value)
    {
        $this->attributes['measurements'] = json_encode($value);
    }

    public function getMeasurementsAttribute($value)
    {
        return @$this->attributes['measurements'] ? json_decode($this->attributes['measurements'], true) : [];
    }
}
