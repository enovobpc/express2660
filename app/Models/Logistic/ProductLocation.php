<?php

namespace App\Models\Logistic;

use Auth;

class ProductLocation extends \App\Models\BaseModel
{

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_logistic';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'products_locations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id', 'location_id', 'stock', 'stock_available', 'stock_allocated'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'product_id'  => 'required',
        'location_id' => 'required',
    );

    /**
     * Set product location barcode
     * @param $stockValue
     */
    public function setBarcode() {

        if(!$this->barcode) {
            $this->save();

            $code = 'LOC';
            $code .= str_pad($this->product_id, 6, "0", STR_PAD_LEFT);
            $code .= str_pad($this->location_id, 5, "0", STR_PAD_LEFT);

            $this->barcode = $code;
        }

        $this->save();
    }

    /**
     * Update location stock
     * @param $stockValue
     */
    public function updateStock($stockValue) {

        if($stockValue > 0) {
            $this->stock = $stockValue;
            $this->setBarcode();
        } else {
            $this->delete();
        }
    }

    /**
     * @param $productId
     * @param $stockRequired
     * @param null $forceLocation //serve para retornar apenas da localização indicada
     * @return array
     */
    public static function getAutomaticLocation($productId, $stockRequired, $forceLocationId = null) {


        //ORDEM para remover stock
        //1. validade
        //2. menor stock
        //3. mais antigo


        if($forceLocationId) {
            $productLocations = ProductLocation::where('product_id', $productId)
                ->where('location_id', $forceLocationId)
                ->get();
        } else {
            $productLocations = ProductLocation::where('product_id', $productId)
                ->where('stock_available', '>', 0)
                ->orderBy('stock_available', 'asc')
                ->orderBy('updated_at', 'asc')
                ->get();
        }

        //dd($productLocations->toArray());

        $locations = [];
        foreach ($productLocations as $productLocation) {

            if($stockRequired > 0) {

                if($stockRequired > $productLocation->stock_available) {
                    $locations[] = [
                        'id'          => $productLocation->id,
                        'product_id'  => $productLocation->product_id,
                        'location_id' => $productLocation->location_id,
                        'code'        => @$productLocation->location->code,
                        'qty'         => $productLocation->stock_available
                    ];

                    $stockRequired-= $productLocation->stock_available;
                } else {

                    $locations[] = [
                        'id'          => $productLocation->id,
                        'product_id'  => $productLocation->product_id,
                        'location_id' => $productLocation->location_id,
                        'code'        => @$productLocation->location->code,
                        'qty'         => $stockRequired
                    ];

                    $stockRequired = 0;
                }
            }
        }

        return $locations;
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */

    public function product()
    {
        return $this->belongsTo('App\Models\Logistic\Product', 'product_id');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Logistic\Location', 'location_id');
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
    public function setStockAllocatedAttribute($value) {
        $this->attributes['stock_allocated'] = empty($value) ? 0 : $value;
    }

}
