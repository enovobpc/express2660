<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\FileTrait;
use Auth;


class ShipmentHistoryAttachament extends \App\Models\BaseModel
{
    use SoftDeletes, FileTrait;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_shipments_history_attachament';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'shipments_history_attachaments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['shipment_id', 'shipment_history_id', 'name', 'filename', 'filepath'];


    /**
     * Default upload directory
     *
     * @const string
     */
    const DIRECTORY = 'uploads/shipment_history_attachament';

    /**
     * Validator rules
     *
     * @var array
     */
    public $rules = [
        'name'     => 'required',
        'filename' => 'required',
    ];

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    
    public function shipment()
    {
        return $this->belongsTo('App\Models\Shipment', 'shipment_id');
    }
    
    public function history()
    {
        return $this->belongsTo('App\Models\ShipmentHistory', 'shipment_history_id');
    }

}