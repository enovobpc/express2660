<?php

namespace App\Models\FleetGest;

class MaintenancePart extends \App\Models\BaseModel
{

    public $timestamps = false;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_fleet';

    /**
     * Constant variables
     */
    const CACHE_TAG = 'fleet_maintenance_assigned_parts';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fleet_maintenance_assigned_parts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'part_id', 'maintenance_id', 'qty'
    ];

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */

    public function part()
    {
        return $this->belongsTo('App\Models\FleetGest\Part', 'part');
    }

    public function maintenance()
    {
        return $this->belongsTo('App\Models\Maintenance', 'maintenance_id');
    }
}
