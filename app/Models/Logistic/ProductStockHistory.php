<?php

namespace App\Models\Logistic;

use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class ProductStockHistory extends \App\Models\BaseModel
{

    use SoftDeletes;

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
    protected $table = 'products_stocks_history';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id', 'product_id', 'warehouse_id', 'location_id', 'product_history_id',
        'date', 'stock_total', 'stock_available', 'stock_allocated', 'unique_hash'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'product_id'  => 'required',
        'stock_total' => 'required',
    );

    /**
     * Memoriza o historico de quantidades para um artigo sempre que existe uma alteração de movimentos
     * @param $id
     */
    public static function insertFromProductHistory($productHistory = null) {

        $date = date('Y-m-d');
        $now  = date('Y-m-d H:i:s');

        //stock per location
        $product = Product::with('locations')
                    ->where('id', $productHistory->product_id)
                    ->first();

        if($product) {

            //apaga todos os registos do produto para a data de hoje
            ProductStockHistory::where('product_id', $product->id)
                ->where('date', $date)
                ->forceDelete();


            $arr = [];
            if ($product->locations->isEmpty()) {
                $arr[] = [
                    'unique_hash'     => $product->id,
                    'history_action'  => $productHistory->action,
                    'history_id'      => $productHistory->id,
                    'warehouse_id'    => null,
                    'customer_id'     => $product->customer_id,
                    'product_id'      => $product->id,
                    'location_id'     => null,
                    'date'            => $date,
                    'stock_total'     => 0,
                    'stock_allocated' => 0,
                    'stock_available' => 0,
                    'created_at'      => $now
                ];
            } else {

                foreach ($product->locations as $location) {

                    $arr[] = [
                        'unique_hash'     => $product->id.$location->id,
                        'history_action'  => $productHistory->action,
                        'history_id'      => $productHistory->id,
                        'warehouse_id'    => $location->warehouse_id,
                        'customer_id'     => $product->customer_id,
                        'product_id'      => $product->id,
                        'location_id'     => $location->id,
                        'date'            => $date,
                        'stock_total'     => $location->pivot->stock,
                        'stock_allocated' => $location->pivot->stock_allocated,
                        'stock_available' => $location->pivot->stock_available,
                        'created_at'      => $now
                    ];
                }
            }

            ProductStockHistory::insert($arr);
        }
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

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    public function warehouse()
    {
        return $this->belongsTo('App\Models\Logistic\Warehouse', 'warehouse_id');
    }

    public function history()
    {
        return $this->belongsTo('App\Models\Logistic\ProductHistory', 'history_id');
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
    public function setLocationIdAttribute($value)
    {
        $this->attributes['location_id'] = empty($value) ? null : $value;
    }
}
