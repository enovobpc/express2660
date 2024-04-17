<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Auth;

class OperatorTask extends BaseModel implements Sortable
{

    use SoftDeletes,
        SortableTrait;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_operators_tasks';

    const STATUS_PENDING    = 'pending';
    const STATUS_ACCEPTED   = 'accepted';
    const STATUS_CONCLUDED  = 'concluded';
    const STATUS_INCIDENCE  = 'incidence';
    const STATUS_OPERATOR   = 'operator';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'operators_tasks';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'details', 'operators', 'operator_id', 'shipment_id', 'customer_id',
        'readed', 'concluded', 'last_update', 'created_by', 'shipments', 'transport_type_id', 'is_pickup'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'name' => 'required',
    );

    /**
     * Default sort column
     *
     * @var array
     */
    public $sortable = [
        'order_column_name' => 'sort'
    ];
    
     /*
      |--------------------------------------------------------------------------
      | Scopes
      |--------------------------------------------------------------------------
      |
      | Scopes allow you to easily re-use query logic in your models.
      | To define a scope, simply prefix a model method with scope.
      |
     */

    /**
     * Set notification of shipment
     *
     * @return int
     */
    public function setNotification($channel)
    {
        $data['id']      = time(); //ID para a aplicação.
        $data['title']   = $this->name . time();
        $data['message'] = $this->description ? $this->description : 'Novo serviço';

        if(!$channel) {
            $channel = BroadcastPusher::getChannel();
        }

        $pusher = new BroadcastPusher();
        return $pusher->trigger($data, $channel);
        return true;
    }

    /**
     * Set notification of shipment
     *
     * @return int
     */
    public function notifyAllOperators()
    {
        $data['id']      = 'all'; //ID para a aplicação.
        $data['title']   = $this->name;
        $data['message'] = $this->description ? $this->description : 'Nova recolha';

        $channel = BroadcastPusher::getOperatorsChannel();

        $pusher = new BroadcastPusher();
        return $pusher->trigger($data, $channel);
        return true;
    }

    /**
     * Set address
     * 
     * @param string $senderAddress
     * @param string $senderZipCode
     * @param string $senderCity
     * @param string $senderPhone
     * @return void
     */
    public function setAddress($senderAddress = null, $senderZipCode = null, $senderCity = null, $senderPhone = null) {
        $this->details = $this->details ?? ' ';
        $this->address = br2nl($senderAddress . ' ' . $senderZipCode . ' ' . $senderCity . '<br/>Contacto: ' . $senderPhone);
    }

    /**
     * Removes a shipment from the Operator Task
     * 
     * @param int $shipmentId
     * @return void
     */
    public function removeShipment($shipmentId) {
        $shipmentsArr = $this->shipments;
        if (empty($shipmentsArr)) {
            return;
        }

        if (($key = array_search($shipmentId, $shipmentsArr)) !== false) {
            unset($shipmentsArr[$key]);
            $shipmentsArr = array_values($shipmentsArr);
        }

        if (empty($shipmentsArr)) {
            if (!$this->readed) {
                $this->delete();
                return;
            }

            $this->deleted   = 1;
            $this->shipments = null;
            $this->save();
            return;
        }

        $details = '';
        $volumes = $weight = 0;
        foreach ($shipmentsArr as $shipmentId) {
            $shp = Shipment::find($shipmentId);
            if (!$shp) {
                continue;
            }

            $volumes += $shp->volumes;
            $weight  += $shp->weight;
            $details .= '<br/>' . $shp->volumes . ' Vol. - ' . $shp->recipient_city;
        }

        $this->details   = br2nl($details);
        $this->volumes   = $volumes;
        $this->weight    = $weight;
        $this->shipments = $shipmentsArr;
        $this->save();
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function operator()
    {
        return $this->belongsTo('App\Models\User', 'operator_id');
    }

    public function owner()
    {
        return $this->belongsTo('App\Models\User', 'created_by');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    public function transport_type() {
        return $this->belongsTo('App\Models\TransportType', 'transport_type_id');
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
    
    public function setOperatorsAttribute($value)
    {
        $this->attributes['operators'] = empty($value) ? null : json_encode($value);
    }

    public function setShipmentsAttribute($value)
    {
        $this->attributes['shipments'] = empty($value) ? null : json_encode($value);
    }

    public function setShipmentIdAttribute($value)
    {
        $this->attributes['shipment_id'] = empty($value) ? null : $value;
    }

    public function setOperatorIdAttribute($value)
    {
        $this->attributes['operator_id'] = empty($value) ? null : $value;
    }

    public function setCreatedByAttribute($value)
    {
        $this->attributes['created_by'] = empty($value) ? null : $value;
    }

    public function setStartHourAttribute($value) {
        $this->attributes['start_hour'] = empty($value) ? null : $value;
    }

    public function setEndHourAttribute($value) {
        $this->attributes['end_hour'] = empty($value) ? null : $value;
    }

    public function setTransportTypeIdAttribute($value) {
        $this->attributes['transport_type_id'] = empty($value) ? null : $value;
    }

    public function getOperatorsAttribute()
    {
        return json_decode(@$this->attributes['operators']);
    }

    public function getShipmentsAttribute()
    {
        return json_decode(@$this->attributes['shipments']);
    }

    public function getStatusAttribute()
    {
        if(!$this->attributes['readed'] && !$this->attributes['concluded']) {
            return 'pending';
        } elseif($this->attributes['readed'] && !$this->attributes['concluded']) {
            return 'accepted';
        } else {
           return 'concluded';
        }
    }

    public function getFullAddressAttribute() {
        if (empty($this->attributes['full_address'])) {
            return br2nl($this->address . ' ' . $this->zip_code . ' ' . $this->city . '<br/>Contacto: ' . $this->phone);
        }

        return $this->attributes['full_address'];
    }
}


