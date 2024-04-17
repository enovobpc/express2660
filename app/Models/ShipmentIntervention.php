<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\FileTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShipmentIntervention extends Model
{
    //
    use SoftDeletes,
        FileTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'shipments_customer_supports';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shipment_id', 'user_id', 'subject', 'action_taken', 'created_at'
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

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
