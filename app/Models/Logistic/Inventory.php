<?php

namespace App\Models\Logistic;

use Illuminate\Database\Eloquent\SoftDeletes;
use Mpdf\Mpdf;
use Setting;

class Inventory extends \App\Models\BaseModel
{

    use SoftDeletes;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_logistic';

    const STATUS_PROCESSING = '1';
    const STATUS_CONCLUDED  = '2';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'inventories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'customer_id', 'date', 'status_id', 'user_id', 'qty_existing', 'qty_real', 'items'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        //'customer_id'  => 'required',
    );

    /**
     * Set product location barcode
     * @param $stockValue
     */
    public function setCode($save = true)
    {
        if(!$this->code) {
            $this->save();

            $code = date('ym');
            $code .= str_pad($this->id, 5, "0", STR_PAD_LEFT);

            $this->code = $code;
        }

        if($save) {
            $this->save();
        }
    }

    /**
     * Update inventory totals
     * @param $inventoryId
     */
    public static function updateTotals($inventoryId) {

        $inventory = Inventory::where('id', $inventoryId)
            ->firstOrFail();

        $inventory->update([
            'qty_existing' => $inventory->lines->sum('qty_existing'),
            'qty_real'     => $inventory->lines->sum('qty_real'),
            'items'        => $inventory->lines->count()
        ]);
    }

    /**
     * Update all stock products
     */
    public function updateProductStocks() {

        $inventory = $this;

        if($inventory->status_id == Inventory::STATUS_CONCLUDED) {

            $lines = $inventory->lines;

            try {
                foreach ($lines as $line) {

                    $productLocation = ProductLocation::firstOrNew([
                        'product_id'  => $line->product_id,
                        'location_id' => $line->location_id
                    ]);

                    $productLocation->product_id      = $line->product_id;
                    $productLocation->location_id     = $line->location_id;
                    $productLocation->stock_allocated = $line->stock_allocated ? $line->stock_allocated : 0;
                    $productLocation->stock           = $line->qty_real;
                    $productLocation->stock_available = $line->stock - $line->stock_allocated;
                    $productLocation->setBarcode();

                    //save history
                    $productHistory = new ProductHistory();
                    $productHistory->action         = 'inventory';
                    $productHistory->product_id     = $line->product_id;
                    $productHistory->source_id      = $line->location_id;
                    $productHistory->destination_id = $line->location_id;
                    $productHistory->document_id    = $line->inventory_id;
                    $productHistory->qty            = $line->qty_real - $line->qty_existing;
                    $productHistory->obs            = 'Ajuste de ' . $line->qty_existing . ' para ' . $line->qty_real;
                    $productHistory->save();

                    $productLocation->product->updateStockTotal();
                }

            } catch (\Exception $e) {
                DD($e->getMessage(). ' line '. $e->getLine(). ' FILE '. $e->getFile());
            }
        }
    }


    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */

    public function lines()
    {
        return $this->hasMany('App\Models\Logistic\InventoryLine', 'inventory_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
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
    public function setCustomerIdAttribute($value)
    {
        $this->attributes['customer_id'] = empty($value) ? null : $value;
    }

    public function getAllowEditAttribute()
    {
        return $this->status_id == Self::STATUS_CONCLUDED ? false : true;
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
}
