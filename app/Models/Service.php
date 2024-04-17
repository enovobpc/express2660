<?php

namespace App\Models;

use App\Models\Traits\FileTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Auth, Setting;

class Service extends BaseModel implements Sortable
{

    use SoftDeletes,
        SortableTrait,
        FileTrait;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_services';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'services';

    /**
     * Default upload directory
     *
     * @const string
     */
    const DIRECTORY = 'uploads/services';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'agencies', 'sort', 'code', 'display_code', 'name', 'unity', 'group', 'zones', 'zones_transit', 'zones_transit_max', 'zones_provider',
        'provider_id', 'description', 'description2',

        'is_import', 'is_collection', 'is_return', 'is_internacional', 'is_maritime', 'is_air',
        'custom_prices','mapping_zones',
        'min_volumes', 'max_volumes', 'min_weight','max_weight', 'min_hour', 'max_hour',
        'max_dims', 'pickup_weekdays', 'delivery_weekdays', 'multiply_price', 'assigned_service_id', 'assigned_intercity_service_id',

        'transit_time', 'transit_time_max', 'delivery_hour', 'priority_level', 'priority_color', 'is_courier', 'is_regional', 'is_mail',
        'allow_kms', 'allow_cod', 'allow_saturday', 'allow_out_standard', 'allow_docs', 'allow_boxes', 'allow_pallets', 'allow_pudos', 'allow_return',
        'zip_codes', 'customers', 'webservice_mapping', 'dimensions_required', 'price_per_volume',
        'settings',

        'max_weight_docs', 'max_length_docs', 'max_width_docs', 'max_height_docs', 'max_dims_docs',
        'max_weight_boxes', 'max_length_boxes', 'max_width_boxes', 'max_height_boxes', 'max_dims_boxes',
        'max_weight_pallets', 'max_length_pallets', 'max_width_pallets', 'max_height_pallets', 'max_dims_pallets',
        'price_per_pack', 'transport_type_id', 'marker_icon', 'pickup_hour_difference'

        //'matrix_from', 'matrix_to', 'matrix_zones', 'matrix_arr','mapping_provider_services',
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'zones'        => 'required',
        'display_code' => 'required',
        'name'         => 'required',
    );
    
    /**
     * Validator custom attributes
     * 
     * @var array 
     */
    protected $customAttributes = array(
        'zones'     => 'Zonas',
        'agencies'  => 'Agências',
        'code'      => 'Código',
        'display_code' => 'Código',
        'name'      => 'Nome',
    );

    /**
     * Default sort column
     *
     * @var array
     */
    public $sortable = [
        'order_column_name' => 'sort'
    ];

    protected $appends = [
        'display_code_alt'
    ];

    /**
     * Devolve uma lista de códigos postais autorizados no serviço
     * @param $billingZones
     * @param $existingZipCodes
     * @return array|string
     */
    public static function getZipCodesFromZones($billingZones, $existingZipCodes) {

        $unities = $billingZones->pluck('unity', 'unity')->toArray();

        $zipCodes = [];
        if(count($unities) == 1 && isset($unities['zip_code'])) { //só adiciona codigos se o serviço tiver codigos postais

            $serviceZipCodes = explode(',', $existingZipCodes);

            $zonesZipCodes = [];
            foreach ($billingZones as $billingZone) {
                $zonesZipCodes = array_merge($zonesZipCodes, array_map('trim', ($billingZone->mapping ? $billingZone->mapping : [])));
            }

            //separa os codigos postais das zonas daqueles que foram inseridos manualmente
            $customZipCodes = array_diff($serviceZipCodes, $zonesZipCodes);

            $zipCodes = array_merge($zonesZipCodes, $customZipCodes);
        }

        return $zipCodes;
    }

    /**
     * Get allowed pack types
     * 
     * @return array
     */
    public function getAllowedPackTypes()
    {
        $allowedPackTypes = [];
        if ($this->allow_docs) {
            $allowedPackTypes[] = 'docs';
        }

        if ($this->allow_boxes) {
            $allowedPackTypes[] = 'boxes';
        }

        if ($this->allow_pallets) {
            $allowedPackTypes[] = 'pallets';
        }

        return PackType::filterSource()
            ->whereIn('type', $allowedPackTypes)
            ->get();
    }

    /**
     * Get default pickup hours values
     * 
     * @param bool $isPickup
     * @return array
     */
    public function getDefaultPickupHours($isPickup = false, $customerId = null)
    {
        $service = !$isPickup ? $this : $this->assignedService;

        $minHour = $service->min_hour ?? '00:00';
        $maxHour = Setting::get('shipments_daily_limit_hour', ($service->max_hour ?? '23:55'));

        $hourFromTask = false;

        $defaultMinHour = $service->min_hour;
        $defaultMaxHour = Setting::get('shipments_daily_limit_hour', '23:55');
        if ($service->max_hour < $defaultMaxHour) {
            $defaultMaxHour = $service->max_hour;
        }

        if (!$isPickup && $customerId) {

            /**
             * @author Daniel Almeida
             * 
             * If it's not a pickup and the customer id was specified
             * we can get the default hours based on the existing operator task
             */

            $task = OperatorTask::filterSource()
                ->where('concluded', 0)
                ->where('customer_id', $customerId)
                ->where('date', date('Y-m-d'))
                ->where('transport_type_id', @$service->transport_type_id)
                ->first();

            if ($task) {
                $defaultMinHour = $task->start_hour;
                if ($task->end_hour <= $defaultMaxHour) {
                    $defaultMaxHour = $task->end_hour;
                }

                $hourFromTask = true;
            }
        }

        if (!$hourFromTask) {
            $defaultMinHour = getNextTimePossible(date('H:i'), $defaultMinHour);
            if ($defaultMinHour > $defaultMaxHour) {
                $defaultMinHour = $service->min_hour;
            }
        }

        return [
            'min_hour'         => $minHour,
            'max_hour'         => $maxHour,
            'default_min_hour' => $defaultMinHour,
            'default_max_hour' => $defaultMaxHour,
            'hour_from_task'   => $hourFromTask,
        ];
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */

    public function volumetricFactors()
    {
        return $this->hasMany('App\Models\ServiceVolumetricFactor', 'service_id');
    }

    public function serviceGroup()
    {
        return $this->belongsTo('App\Models\ServiceGroup', 'group', 'code');
    }

    public function provider()
    {
        return $this->belongsTo('App\Models\Provider', 'provider_id');
    }

    public function assignedService()
    {
        return $this->belongsTo('App\Models\Service', 'assigned_service_id');
    }

    public function transport_type() {
        return $this->belongsTo('App\Models\TransportType', 'transport_type_id');
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
    
    public function scopeIsReturn($query, $value = true){
        $query->where('is_return', $value);
    }
    
    public function scopeIsCollection($query, $value = true){
        $query->where('is_collection', $value);
    }

    public function scopeIsPickup($query, $value = true){
        $query->where('is_collection', $value);
    }

    public function scopePickupAssigned($query){
        $query->whereNotNull('assigned_service_id');
    }
    
    public function scopeIsShipment($query){
        $query->where('is_collection', 0);
    }
    
    public function scopeIsInternacional($query, $value = true){
        $query->where('is_internacional', $value);
    }
    
    public function scopeIsAir($query, $value = true){
        $query->where('is_air', $value);
    }
    
    public function scopeIsMaritime($query, $value = true){
        $query->where('is_maritime', $value);
    }

    public function scopeShowOnPricesTable($query, $value = true){
        $query->where('custom_prices', $value);
    }

    public function scopeFilterHorary($query, $curHour = null){

        return $query; 
        
        /* $curHour = $curHour ? $curHour : date('H:m');
        $curWeekDay = date('w');

        return $query->where(function($q) use($curHour) {
                $q->where(function($q) use($curHour){
                    $q->where('min_hour', '<=', $curHour);
                    $q->where('max_hour', '>=', $curHour);
                });
                $q->orWhereNull('min_hour');
            })->where(function($q) use($curWeekDay) {
                $q->where('pickup_weekdays', 'like', '%"' .$curWeekDay . '"%')
                ->orWhereNull('pickup_weekdays');
            }); */

    }


    public function scopeFilterCustomer($query, $customerId){
        return $query->where(function($q) use($customerId) {
           $q->whereNull('customers');
           $q->orWhere('customers', 'LIKE', '%"'.$customerId.'"%');
        });
    }

    public function scopeFilterSource($query){
        return $query->where(function ($q){
            $q->where('source', config('app.source'));
            $q->orWhereNull('source');
        });

    }

    public function scopeFilterAgencies($query, $agencies = null) {

        $user = Auth::user();

        if($user && is_null($agencies)) {
            $agencies = $user->agencies;
        }

        $query->filterSource();

        if(($user && !$user->hasRole([config('permissions.role.admin')])) || !empty($agencies)) {
            $query->where(function($q) use($agencies) {
                foreach ($agencies as $agency) {
                    $q->orWhere('agencies', 'like', '%"'.$agency.'"%');
                }
            });
        }

        return $query;
    }

    public function scopeWithVolumetricFactor($query, $providerId = null, $zone = null){
        return $query->leftjoin('services_volumetric_factor', function($join) use($providerId, $zone) {
                $join->on('services.id', '=', 'services_volumetric_factor.service_id');
                    if(!is_null($providerId)) {
                        $join->where('services_volumetric_factor.provider_id', '=', $providerId);
                    }

                    if(!is_null($zone)) {
                        $join->where('services_volumetric_factor.zone', '=', $zone);
                    }
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
    public function setAgenciesAttribute($value)
    {
        $this->attributes['agencies'] = empty($value) ? null : json_encode($value);
    }
    
    public function getAgenciesAttribute()
    {
        return json_decode(@$this->attributes['agencies']);
    }

    public function getPickupWeekdaysAttribute()
    {
        return json_decode(@$this->attributes['pickup_weekdays'], true);
    }

    public function getDeliveryWeekdaysAttribute()
    {
        return json_decode(@$this->attributes['delivery_weekdays'], true);
    }

    public function setZonesAttribute($value)
    {
        $this->attributes['zones'] = empty($value) ? null : json_encode($value);
    }

    public function setZonesProviderAttribute($value)
    {
        $this->attributes['zones_provider'] = empty($value) ? null : json_encode($value);
    }

    public function setZonesTransitAttribute($value)
    {
        $this->attributes['zones_transit'] = empty($value) ? null : json_encode($value);
    }

    public function setZonesTransitMaxAttribute($value)
    {
        $this->attributes['zones_transit_max'] = empty($value) ? null : json_encode($value);
    }

    public function setWebserviceMappingAttribute($value)
    {
        $this->attributes['webservice_mapping'] = empty($value) ? null : json_encode($value);
    }

    public function setPickupWeekdaysAttribute($value)
    {
        $this->attributes['pickup_weekdays'] = empty($value) ? null : json_encode($value);
    }

    public function setDeliveryWeekdaysAttribute($value)
    {
        $this->attributes['delivery_weekdays'] = empty($value) ? null : json_encode($value);
    }

    public function setCustomersAttribute($value)
    {
        $this->attributes['customers'] = empty($value) ? null : json_encode($value);
    }

/*    public function setMatrixFromAttribute($value)
    {
        $this->attributes['matrix_from'] = empty($value) ? null : json_encode($value);
    }

    public function setMatrixToAttribute($value)
    {
        $this->attributes['matrix_to'] = empty($value) ? null : json_encode($value);
    }

    public function setMatrixZonesAttribute($value)
    {
        $this->attributes['matrix_zones'] = empty($value) ? null : json_encode($value);
    }

    public function setMatrixArrAttribute($value)
    {
        $this->attributes['matrix_arr'] = empty($value) ? null : json_encode($value);
    }

    public function setMappingProviderServicesAttribute($value)
    {
        $this->attributes['matrix_zones'] = empty($value) ? null : json_encode($value);
    }
*/

    public function setProviderIdAttribute($value)
    {
        $this->attributes['provider_id'] = empty($value) ? null : $value;
    }

    public function setTransportTypeIdAttribute($value)
    {
        $this->attributes['transport_type_id'] = empty($value) ? null : $value;
    }

    public function setAssignedServiceIdAttribute($value)
    {
        $this->attributes['assigned_service_id'] = empty($value) ? null : $value;
    }

    public function setAssignedIntercityServiceIdAttribute($value)
    {
        $this->attributes['assigned_intercity_service_id'] = empty($value) ? null : $value;
    }

    public function setMinVolumesAttribute($value)
    {
        $this->attributes['min_volumes'] = empty($value) ? null : $value;
    }

    public function setMaxVolumesAttribute($value)
    {
        $this->attributes['max_volumes'] = empty($value) ? null : $value;
    }

    public function setMinWeightAttribute($value)
    {
        $this->attributes['min_weight'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setMaxWeightAttribute($value)
    {
        $this->attributes['max_weight'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setMinWeightVolumeAttribute($value)
    {
        $this->attributes['min_weight_volume'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setMaxWeightBoxesAttribute($value)
    {
        $this->attributes['max_weight_boxes'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setMaxWeightPalletsAttribute($value)
    {
        $this->attributes['max_weight_pallets'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setMaxWeightDocsAttribute($value)
    {
        $this->attributes['max_weight_docs'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setMaxDimsAttribute($value)
    {
        $this->attributes['max_dims'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setTransitTimeAttribute($value)
    {
        $this->attributes['transit_time'] = empty($value) ? null : $value;
    }

    public function setTransitTimeMaxAttribute($value)
    {
        $this->attributes['transit_time_max'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setDeliveryHourAttribute($value)
    {
        $this->attributes['delivery_hour'] = empty($value) ? null : $value;
    }

    public function setMaxLengthDocsAttribute($value)
    {
        $this->attributes['max_length_docs'] = empty($value) ? null : $value;
    }

    public function setMaxWidthDocsAttribute($value)
    {
        $this->attributes['max_width_docs'] = empty($value) ? null : $value;
    }

    public function setMaxHeightDocsAttribute($value)
    {
        $this->attributes['max_height_docs'] = empty($value) ? null : $value;
    }

    public function setMaxDimsDocsAttribute($value)
    {
        $this->attributes['max_dims_docs'] = empty($value) ? null : $value;
    }

    public function setMaxLengthBoxesAttribute($value)
    {
        $this->attributes['max_length_boxes'] = empty($value) ? null : $value;
    }

    public function setMaxWidthBoxesAttribute($value)
    {
        $this->attributes['max_width_boxes'] = empty($value) ? null : $value;
    }

    public function setMaxHeightBoxesAttribute($value)
    {
        $this->attributes['max_height_boxes'] = empty($value) ? null : $value;
    }

    public function setMaxDimsBoxesAttribute($value)
    {
        $this->attributes['max_dims_boxes'] = empty($value) ? null : $value;
    }

    public function setMaxLengthPalletAttribute($value)
    {
        $this->attributes['max_length_pallet'] = empty($value) ? null : $value;
    }

    public function setMaxWidthPalletAttribute($value)
    {
        $this->attributes['max_width_pallet'] = empty($value) ? null : $value;
    }

    public function setMaxHeightPalletAttribute($value)
    {
        $this->attributes['max_height_pallet'] = empty($value) ? null : $value;
    }

    public function setMaxDimsPalletAttribute($value)
    {
        $this->attributes['max_dims_pallet'] = empty($value) ? null : $value;
    }

    public function setPriorityLevelAttribute($value)
    {
        if(empty($value)) {
            $this->attributes['priority_level'] = null;
            $this->attributes['priority_color'] = null;
        } else {
            $this->attributes['priority_level'] = $value;
        }
    }

    public function setSettingsAttribute($value) {
        $this->attributes['settings'] = empty($value) ? null : json_encode($value);
    }

    public function setPickupHourDifferenceAttribute($value) {
        $this->attributes['pickup_hour_difference'] = empty($value) ? null : $value;
    }

    public function getZonesAttribute()
    {
        return json_decode(@$this->attributes['zones']);
    }

    public function getZonesProviderAttribute()
    {
        return json_decode(@$this->attributes['zones_provider'], true);
    }

    public function getZonesTransitAttribute()
    {
        return json_decode(@$this->attributes['zones_transit'], true);
    }

    public function getZonesTransitMaxAttribute()
    {
        return json_decode(@$this->attributes['zones_transit_max'], true);
    }

    public function getWebserviceMappingAttribute()
    {
        return json_decode(@$this->attributes['webservice_mapping'], true);
    }

    public function getCustomersAttribute()
    {
        return json_decode(@$this->attributes['customers']);
    }
    public function getMarkerIconAttribute()
    {
        return @$this->attributes['marker_icon'] ? $this->attributes['marker_icon'] : 'assets/img/default/map/marker_red.svg';
    }
   
    public function getSettingsAttribute() {
        return json_decode((@$this->attributes['settings'] ?? '{}'), true);
    }

    public function getDisplayCodeAltAttribute() {
        if (!in_array(config('app.source'), ['baltrans'])) {
            return @$this->attributes['display_code'];
        }

        $aux = substr(@$this->name, 0, 20);
        if (strlen(@$this->name) > 20) {
            $aux .= '...';
        }

        return $aux;
    }

/*    public function getMatrixFromAttribute()
    {
        return json_decode(@$this->attributes['matrix_from'], true);
    }

    public function getMatrixToAttribute()
    {
        return json_decode(@$this->attributes['matrix_to'], true);
    }

    public function getMatrixZonesAttribute()
    {
        return json_decode(@$this->attributes['matrix_zones'], true);
    }

    public function getMatrixArrAttribute()
    {
        return json_decode(@$this->attributes['matrix_arr'], true);
    }

    public function getMappingProviderServicesAttribute()
    {
        return json_decode(@$this->attributes['mapping_provider_services'], true);
    }
    */
}
