<?php

namespace App\Models\FleetGest;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\FileTrait;
use Auth;

class VehicleHistory extends \App\Models\BaseModel
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
    const CACHE_TAG = 'cache_fleet_vehicle_history';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fleet_vehicle_history';


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
        'vehicle_id', 'provider_id', 'type', 'source_id', 'km', 'total', 'date'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'vehicle_id' => 'required',
        'type'       => 'required',
        'source_id'  => 'required',
        'total'      => 'required',
        'date'       => 'required'
    );

    /**
     * Set or update vehicle history
     *
     * @param $type
     * @param $sourceId
     * @param int $vehicleId
     */
    public static function setOrUpdate($type, $sourceId, $vehicleId, $providerId, $date, $km, $total, $typeId, $createdBy = null)
    {

        $history = self::firstOrNew([
                    'type'       => $type,
                    'source_id'  => $sourceId,
                    'vehicle_id' => $vehicleId
                ]);

        $history->vehicle_id = $vehicleId;
        $history->provider_id = $providerId;
        $history->type = $type;
        //$history->type_id = $typeId;
        $history->source_id = $sourceId;
        $history->km = $km;
        $history->total = $total;
        $history->date = $date;
        $history = $history->save();

        return $history;
    }

    /**
     * Delete vehicle history
     *
     * @param $type
     * @param $sourceId
     * @param $vehicleId
     */
    public static function remove($type, $sourceId, $vehicleId)
    {
        return self::where('type', $type)
            ->where('source_id', $sourceId)
            ->where('vehicle_id', $vehicleId)
            ->delete();
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
    public function setProviderIdAttribute($value)
    {
        $this->attributes['provider_id'] = empty($value) ? null : $value;
    }
}
