<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Auth;

class ShippingStatus extends BaseModel implements Sortable
{

    use SoftDeletes,
        Sluggable,
        SortableTrait;

    /**
     * Constant variables
     */
    const CACHE_TAG     = 'cache_shipping_status';

    const PENDING               = 'pendente';
    const ACCEPTED              = 'aceite';
    const IN_TRANSPORTATION     = 'em-transporte';
    const IN_DISTRIBUTION       = 'em-distribuicao';
    const DELIVERED             = 'entregue';
    const INCIDENCE             = 'incidencia';
    const DEVOLVED              = 'devolvido';

    const PENDING_ID            = 1;
    const ACCEPTED_ID           = 2;
    const IN_TRANSPORTATION_ID  = 3;
    const IN_DISTRIBUTION_ID    = 4;
    const DELIVERED_ID          = 5;
    const DELIVERED_PARTIAL_ID  = 12;
    const CANCELED_ID           = 8;
    const INCIDENCE_ID          = 9;
    const DEVOLVED_ID           = 7;
    const SCHEDULED_ID          = 27;
    const PAYMENT_PENDING_ID    = 40;

    const IN_PICKUP_ID          = 10;
    const RECEIVED_BY_API_ID    = 28;
    const PICKUP_REQUESTED_ID   = 24;
    const PICKUP_ACCEPTED_ID    = 21;
    const PICKUP_DONE_ID        = 14;
    const PICKUP_CONCLUDED_ID   = 19;
    const PICKUP_FAILED_ID      = 18;

    const PENDING_OPERATOR          = 37;
    const WAINTING_REALIZATION      = 22;
    const READ_BY_COURIER_OPERATOR  = 38;
    const WAINTING_SYNC_ID          = 15;
    const DOCUMENTED_SYNC_ID        = 16;
    const SHIPMENT_PICKUPED         = 36;

    const SHIPMENT_PROCESSING       = 44;
    const SHIPMENT_WAINT_EXPEDITION = 31;
    const INBOUND_ID                = 17;
    const OUTBOUND_ID               = 29;

    const ACCEPTED_DELIVERY         = 20;
    const DOCS_RECEIVED_ID          = 44; //CMR recebido
    const TRAILER_CHANGED_ID        = 45; //CMR recebido
    const BILLED_ID                 = 42; //Faturado

    const OPERATORS_DELIVERY_DEFAULT_STATUS = [38,37,31,20,21,22,20,3,4];
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'shipping_status';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'color', 'alias',  'sources',
        'is_shipment', 'is_collection', 'is_final', 'is_visible', 'is_static', 'is_public', 'is_traceability',
        'name', 'name_es', 'name_fr', 'name_en', 'tracking_step',
        'description',  'description_es',  'description_fr', 'description_en'
    ];
    
    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'name' => 'required',
    );
    
    /**
     * Validator custom attributes
     * 
     * @var array 
     */
    protected $customAttributes = array(
        'name' => 'Nome',
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
    
    public function scopeFilterSources($query){
    
        $user = Auth::user();
        if($user) {
            $sources = [$user->source];

            if(!$user->isAdmin() || !empty($sources)) {
                return $query->where(function($q) use($sources) {
                    foreach ($sources as $source) {
                        $q->orWhere('sources', 'like', '%'.$source.'%');
                    }
                });
            }
        } else {
            return $query->where('sources', 'like', '%'.config('app.source').'%');
        }
    }
    
    public function scopeIsShipment($query, $value = true){
        $query->where('is_shipment', $value);
    }
    
    public function scopeIsCollection($query, $value = true){
        $query->where('is_collection', $value);
    }

    public function scopeIsPickup($query, $value = true){
        $query->where('is_collection', $value);
    }
    
    public function scopeIsFinal($query, $value = true){
        $query->where('is_final', $value);
    }

    public function scopeIsPublic($query, $value = true){
        $query->where('is_public', $value);
    }

    public function scopeIsVisible($query, $value = true){
        $query->where('is_visible', $value);
    }

    public function hasSource($source = null){
        if(empty($source)) {
            $source = config('app.source');
        }

        $sources = $this->sources ? $this->sources : [];

        if (is_array($sources)) {
            return in_array($source, $sources);
        }

        return false;
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

    public function setSourcesAttribute($value)
    {
        $this->attributes['sources'] = empty($value) ? null : json_encode($value, true);
    }

    public function setAliasAttribute($value)
    {
        $this->attributes['alias'] = empty($value) ? null : json_encode($value);
    }

    public function getSourcesAttribute()
    {
        return json_decode(@$this->attributes['sources']);
    }

    public function getCustomAttrsAttribute()
    {
        return json_decode(@$this->attributes['alias'], true);
    }

    public function getOriginalNameAttribute()
    {
        return @$this->attributes['name'];
    }

    public function getOriginalColorAttribute()
    {
        return @$this->attributes['color'];
    }

    public function getOriginalDescriptionAttribute()
    {
        return @$this->attributes['description'];
    }

    public function getNameAttribute()
    {
        $source  = config('app.source');
        $dataArr = json_decode(@$this->attributes['alias'], true);
        return isset($dataArr[$source]['name']) ? $dataArr[$source]['name'] : @$this->attributes['name'];
    }

    public function getDescriptionAttribute()
    {
        $source  = config('app.source');
        $dataArr = json_decode(@$this->attributes['alias'], true);
        return isset($dataArr[$source]['description']) ? $dataArr[$source]['description'] : @$this->attributes['description'];
    }

    public function getColorAttribute()
    {
        $source  = config('app.source');
        $dataArr = json_decode(@$this->attributes['alias'], true);
        return isset($dataArr[$source]['color']) ? $dataArr[$source]['color'] : @$this->attributes['color'];
    }
}
