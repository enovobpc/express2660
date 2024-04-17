<?php

namespace App\Models\FleetGest;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\FileTrait;
use Auth;

class Cost extends \App\Models\BaseModel
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
    const CACHE_TAG = 'cache_fleet_costs';

    /**
     * Cost types
     */
    const TYPE_FUEL         = 'fuel';
    const TYPE_MAINTENANCE  = 'maintenance';
    const TYPE_TOLLS        = 'tolls';
    const TYPE_FIXED        = 'fixed';
    const TYPE_OTHERS       = 'others';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fleet_costs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'vehicle_id', 'source_id', 'source_type', 'type',
        'provider_id', 'description', 'date', 'total', 'obs', 'assigned_invoice_id'
    ];

    /**
     * The attributes that are dates
     *
     * @var array
     */
    protected $dates = ['date'];

    /**
     * Default upload directory
     *
     * @const string
     */
    const DIRECTORY = 'uploads/vehicles/costs';

    /**
     * Validator rules
     *
     * @var array
     */
    public $rules = [
        'vehicle_id'  => 'required',
        'type'        => 'required',
        'description' => 'required'
    ];

    /**
     * Set or update vehicle history
     *
     * @param $type
     * @param $sourceId
     * @param int $vehicleId
     */
    public static function setOrUpdate($targetType, $type, $sourceId, $vehicleId, $providerId, $assignedInvoice, $description, $date, $total, $typeId = null, $createdBy = null)
    {
        $cost = self::firstOrNew([
            'type'       => $type,
            'source_id'  => $sourceId,
            'vehicle_id' => $vehicleId
        ]);

        $cost->source_id            = $sourceId;
        $cost->source_type          = $targetType;
        $cost->vehicle_id           = $vehicleId;
        $cost->assigned_invoice_id  = $assignedInvoice ? $assignedInvoice : null;
        $cost->type                 = $type;
        $cost->type_id              = $typeId;
        $cost->source_id            = $sourceId;
        $cost->total                = $total;
        $cost->date                 = $date;
        $cost->description          = $description;
        $cost->created_by           = $createdBy ? $createdBy : @Auth::user()->id;
        $cost->provider_id          = $providerId;

        return $cost->save();
    }


    /**
     * Delete vehicle history
     *
     * @param $type
     * @param $sourceId
     * @param $vehicleId
     */
    public static function remove($sourceType, $sourceId, $vehicleId)
    {
        return self::where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->where('vehicle_id', $vehicleId)
            ->delete();
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
    public function scopeFilterSource($query)
    {
        return $query->whereHas('vehicle', function ($q) {
            $q->filterSource();
        });
    }

    /**
     * Filter Agency
     * @param $query
     * @param Array $agencies
     * @return mixed
     */
    public function scopeFilterAgencies($query, $agencies = null)
    {
        if (!empty($agencies)) {
            if (!is_array($agencies)) {
                // Cast to Array
                $agencies = [$agencies];
            }

            return $query->whereHas('vehicle', function ($q) use ($agencies) {
                return $q->whereIn('agency_id', $agencies);
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
    public function vehicle()
    {
        return $this->belongsTo('App\Models\FleetGest\Vehicle', 'vehicle_id');
    }

    public function provider()
    {
        return $this->belongsTo('App\Models\Provider', 'provider_id');
    }

    public function invoice()
    {
        return $this->belongsTo('App\Models\PurchaseInvoice', 'assigned_invoice_id');
    }

    public function type()
    {
        return $this->belongsTo('App\Models\PurchaseInvoiceType', 'type_id');
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
    public function setProviderIdAttribute($value)
    {
        $this->attributes['provider_id'] = empty($value) ? null : $value;
    }
}
