<?php

namespace App\Models\Billing;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Item extends \App\Models\BaseModel implements Sortable
{
    use SoftDeletes, SortableTrait;


    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_billing_products';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'billing_products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reference', 'name', 'short_name', 'sell_price', 'price', 'unity', 'has_stock',
        'tax_rate', 'description', 'obs', 'provider_id', 'provider_reference',
        'brand_id', 'brand_model_id', 'is_service', 'is_fleet_part', 'is_active',
        'is_customer_customizable'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = [
        'reference' => 'required',
        'name'      => 'required',
        'tax_rate'  => 'required'
    ];

    /**
     * Default sort column
     *
     * @var array
     */
    public $sortable = [
        'order_column_name' => 'sort'
    ];

    /**
     * Constructor
     *
     * @param $agencia
     * @param $cliente
     * @param $password
     */
    public function __construct($apiKey = null)
    {
        if($apiKey) {
            $this->apiKey = $apiKey;
        } else {
            $this->apiKey = ApiKey::getDefaultKey();
        }

        parent::__construct();
    }

    /**
     * Return a namespace to a given Class
     *
     * @return string
     * @throws Exception
     */
    public function getNamespaceTo($class){

        $method = config('app.invoice_software');

        if(empty($method)) {
            throw new \Exception('Não está configurada nenhuma ligação ao software de faturação.');
        }

        return 'App\Models\InvoiceGateway\\' . $method . '\\'.$class;
    }

    /**
     * Sync products
     *
     * @param $id
     * @param $type
     * @return mixed
     */
    public function syncProducts(){

        $class = $this->getNamespaceTo('Product');

        try {
            $product = new $class();
            $data = $product->listsProducts();

            foreach ($data as $item) {

                $product = Item::firstOrNew([
                    'source'    => config('app.source'),
                    'reference' => $item['reference']
                ]);

                unset($product->apiKey, $product->api_key);
                $product->fill($item);
                $product->source = config('app.source');
                $product->save();
            }

            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Sync products
     *
     * @param $id
     * @param $type
     * @return mixed
     */
    public function insertOrUpdate($data){

        $ref            = @$data['ref'];
        $designation    = @$data['designation'];
        $taxValue       = @$data['tax_value'];
        $taxId          = @$data['tax_id'];
        $price          = @$data['price'];
        $isService      = @$data['is_service'];
        $shortName      = @$data['short_name'];

        if(empty($ref) && empty($designation)) {
            throw new \Exception('Referência e/ou Designação obrigatórias.');
        }

        $class = $this->getNamespaceTo('Product');
        try {
            $product = new $class();
            $product->insertOrUpdateProduct($ref, $designation, $taxValue, $price, $isService, 0, true, $shortName);

            if(empty($taxValue)) {
                $product->changeProductTax($ref, $taxId);
            }

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
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

    /**
     * Filter query by request
     * 
     * @param \Illuminate\Database\Query\Builder $query
     * @param \Request $request
     * @return void
     */
    public function scopeFilterRequest($query, $request) {
        $value = $request->get('type');
        if ($value) {
            $query->filterType($value);
        }

        $value = $request->get('is_active');
        if (is_numeric($value)) {
            $query->where('is_active', $value);
        }

        $value = $request->get('brand');
        if($value) {
            $query->whereIn('brand_id', $value);
        }

        $value = $request->get('model');
        if($value) {
            $query->whereIn('brand_model_id', $value);
        }
    }
    
    public function scopeIsActive($query, $value = true){
        $query->where('is_active', $value);
    }

    public function scopeIsService($query, $value = true){
        $query->where('is_service', $value);
    }

    public function scopeIsFleetPart($query, $value = true){
        $query->where('is_fleet_part', $value);
    }

    public function scopeIsCustomerCustomizable($query, $value = true) {
        $query->where('is_customer_customizable', $value);
    }

    public function scopeFilterType($query, $value = null) {
        if ($value == 'services') {
            $query = $query->isService();
        } else if ($value == 'products') {
            $query = $query->isService(false)
                ->where('has_stock', false);
        } else if ($value == 'stocks') {
            $query = $query->isService(false)
                ->where('has_stock', true);
        }

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Define current model relationships
    */

    public function brand() {
        return $this->belongsTo('App\Models\Brand');
    }

    public function brandModel() {
        return $this->belongsTo('App\Models\BrandModel');
    }

    public function history() {
        return $this->hasMany('App\Models\Billing\ItemStockHistory', 'billing_product_id', 'id');
    }

    public function provider() {
        return $this->belongsTo('App\Models\Provider');
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
    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setSellPriceAttribute($value)
    {
        $this->attributes['sell_price'] = empty($value) ? 0 : $value;
    }

    public function setApiKeyAttribute($value)
    {
        $this->attributes['api_key'] = empty($value) ? null : $value;
    }

    public function setBrandIdAttribute($value)
    {
        $this->attributes['brand_id'] = empty($value) ? null : $value;
    }

    public function setBrandModelIdAttribute($value)
    {
        $this->attributes['brand_model_id'] = empty($value) ? null : $value;
    }

    public function setProviderIdAttribute($value)
    {
        $this->attributes['provider_id'] = empty($value) ? null : $value;
    }

    public function setProviderReferenceAttribute($value)
    {
        $this->attributes['provider_reference'] = empty($value) ? null : $value;
    }

    public function setIsServiceAttribute($value) {
        $this->attributes['is_service'] = $value;
        if ($this->attributes['is_service']) {
            $this->attributes['has_stock'] = false;
            $this->attributes['is_fleet_part'] = false;
        }
    }

    public function setHasStockAttribute($value) {
        $this->attributes['has_stock'] = $value;
        if ($this->attributes['has_stock']) {
            $this->attributes['is_service'] = false;
        }
    }

    public function setUnityAttribute($value) {
        $this->attributes['unity'] = empty($value) ? null : $value;
    }
}
