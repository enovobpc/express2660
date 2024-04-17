<?php

namespace App\Models\DeliveryManifest;

use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class DeliveryManifest extends \App\Models\BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_delivery_management';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'delivery_manifests';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'code', 'pickup_date', 'delivery_date', 'agency_id', 'operator_id', 'auxiliar_id', 'provider_id',
        'pickup_route_id', 'delivery_route_id', 'vehicle', 'trailer', 'created_by'
    ];
    
    /**
     * Date attributes 
     * 
     * @var type 
     */
    protected $dates = [
        'pickup_date',
        'delivery_date'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'operator_id' => 'required',
        'pickup_date' => 'required'
    );

    /**
     * Create movement code
     * 
     * @return int
     */
    public function setCode($save = true)
    {
        if(!$this->exists) {
            if ($save) {
                $this->save();
            }

            $totalManifests = DeliveryManifest::filterSource()
                ->with('trashed')
                ->where(DB::raw('YEAR(pickup_date)'), date('Y'))
                ->count();

            $code = date('ym') . str_pad($totalManifests, 5, '0', STR_PAD_LEFT);

            if ($save) {
                $this->code = $code;
                $this->save();
            }

            return $code;
        }

        $this->save();
        return $this->code;
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

    public function auxiliar()
    {
        return $this->belongsTo('App\Models\User', 'auxiliar_id');
    }
    
    public function agency()
    {
        return $this->belongsTo('App\Models\Agency', 'agency_id');
    }

    public function provider()
    {
        return $this->belongsTo('App\Models\Provider', 'provider_id');
    }

    public function pickup_route()
    {
        return $this->belongsTo('App\Models\Route', 'pickup_route_id');
    }

    public function delivery_route()
    {
        return $this->belongsTo('App\Models\Route', 'delivery_route_id');
    }

    public function creator()
    {
        return $this->belongsTo('App\Models\User', 'created_by');
    }

    public function shipments()
    {
        return $this->belongsToMany('App\Models\Shipment', 'delivery_manifests_shipments', 'delivery_manifest_id')->withPivot('sort');
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

    public function setAuxiliarIdAttribute($value)
    {
        $this->attributes['auxiliar_id'] = empty($value) ? null : $value;
    }

    public function setProviderIdAttribute($value)
    {
        $this->attributes['provider_id'] = empty($value) ? null : $value;
    }

    public function setCreatedByAttribute($value)
    {
        $this->attributes['created_by'] = empty($value) ? null : $value;
    }

    public function setDeliveryRouteIdAttribute($value)
    {
        $this->attributes['delivery_route_id'] = empty($value) ? null : $value;
    }

    public function setPickupRouteIdAttribute($value)
    {
        $this->attributes['pickup_route_id'] = empty($value) ? null : $value;
    }

    public function setAgencyIdAttribute($value)
    {
        $this->attributes['agency_id'] = empty($value) ? null : $value;
    }

    public function setPickupDateAttribute($value)
    {
        $this->attributes['pickup_date'] = empty($value) ? null : $value;
    }

    public function setDeliveryDateAttribute($value)
    {
        $this->attributes['delivery_date'] = empty($value) ? null : $value;
    }
}
