<?php

namespace App\Models;

use App\Models\Traits\FileTrait;

class ShipmentIncidenceResolution extends BaseModel
{
    use FileTrait;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_shipments_incidences_resolutions';
    const AVAILABLE_PROVIDERS = ['envialia', 'tipsa', 'gls_zeta', 'enovo_tms'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'shipments_incidences_resolutions';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'soluction_code', 'shipment_id', 'shipment_history_id', 'resolution_type_id', 'obs', 'operator_id'
    ];
    
   /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at'];
    
   /**
     * Validator rules
     * 
     * @var array 
     */
    public $rules = [
        'shipment_id'        => 'required',
        'resolution_type_id' => 'required',
    ];

    /**
     * Create soluction code
     *
     * @return int
     */
    public function setCode()
    {
        $this->save();

        $code = str_pad($this->shipment_id, 7, "0", STR_PAD_LEFT);
        $code.= str_pad($this->id, 5, "0", STR_PAD_LEFT);

        $this->solution_code = $code;
        $this->save();

        return $code;
    }

    /**
     * Set notification of shipment
     *
     * @return int
     */
    public function setNotification($channel, $message, $fromApi = false)
    {
        $sourceClass = 'ShipmentIncidenceResolution';
        $sourceId    = $this->shipment_id;

        $agencies = Agency::filterSource()->pluck('id')->toArray();

        $shipment = Shipment::findOrFail($this->shipment_id);


        if($shipment->agency_id != $shipment->recipient_agency_id || $fromApi) {
            if (in_array($shipment->agency_id, $agencies) && $shipment->agency_id != $shipment->recipient_agency_id) { //resposta da agencia proprieatario do envio
                $agencies = [$shipment->recipient_agency_id];
                $channel = Agency::find($shipment->recipient_agency_id);
                $channel = $channel->source;
            } else {
                $agencies = [$shipment->agency_id];
                $channel = Agency::find($shipment->agency_id);
                $channel = $channel->source;
            }


            //get notification recipients
            $recipients = \App\Models\User::where(function ($q) use ($agencies) {
                    $q->where(function ($q) use ($agencies) {
                        foreach ($agencies as $agency) {
                            $q->orWhere('agencies', 'like', '%"' . $agency . '"%');
                        }
                    });
                    $q->orWhereNull('agencies');
                })
                ->where(function($q){
                    $q->whereHas('roles.perms', function ($query) {
                        $query->whereName('shipments');
                    });
                    $q->orWhereHas('roles', function ($query) {
                        $query->whereName('administrator');
                    });
                })
                ->get(['id']);

            foreach ($recipients as $user) {
                $notification = Notification::firstOrNew([
                    'source_class' => $sourceClass,
                    'source_id' => $sourceId,
                    'recipient' => $user->id
                ]);

                $notification->source_class = $sourceClass;
                $notification->source_id = $sourceId;
                $notification->recipient = $user->id;
                $notification->message = $message;
                $notification->alert_at = date('Y-m-d H:i:s');
                $notification->read = false;
                $notification->save();
            }

            if ($notification) {
                $notification->setPusher($channel ? $channel : BroadcastPusher::getGlobalChannel());
            }
        }
        return true;
    }


    /**
     * Set notification of shipment
     *
     * @return int
     */
    public function deleteNotification()
    {
        return Notification::where('source_class', 'BudgetMessage')
            ->where('source_id', $this->id)
            ->delete();
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function resolution()
    {
        return $this->belongsTo('App\Models\IncidenceResolutionType', 'resolution_type_id');
    }

    public function type()
    {
        return $this->belongsTo('App\Models\IncidenceResolutionType', 'resolution_type_id');
    }

    public function history()
    {
        return $this->belongsTo('App\Models\ShipmentHistory', 'shipment_history_id');
    }

    public function shipment()
    {
        return $this->belongsTo('App\Models\Shipment', 'shipment_id');
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
    public function setIncidenceIdAttribute($value)
    {
        $this->attributes['incidence_id'] = empty($value) ? null : $value;
    }

}
