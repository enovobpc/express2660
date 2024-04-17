<?php

namespace App\Models\Billing;

use App\Models\InvoiceLine;
use App\Models\PurchaseInvoiceLine;
use Illuminate\Database\Eloquent\SoftDeletes;
use Setting;

class ItemStockHistory extends \App\Models\BaseModel
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'billing_products_stocks_history';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'billing_product_id', 'target', 'target_id',
        'line_id', 'qty', 'price'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = [];

    public const TARGET_INITIAL     = 'initial';
    public const TARGET_PURCHASE    = 'purchase';
    public const TARGET_SALE        = 'sale';
    public const TARGET_MAINTENANCE = 'maintenance';

    public function __construct(array $attributes = [])
    {
        /**
         * @author Daniel Almeida
         * --
         * Como existem relacionamentos em base de dados diferentes,
         * temos que dizer qual o nome da base de dados desta tabela porque senão
         * o mysql não consegue encontrá-la
         */
        $this->table = env('DB_DATABASE') . ".{$this->table}";
        parent::__construct($attributes);
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
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Define current model relationships
    */

    public function product() {
        return $this->belongsTo('App\Models\Billing\Item', 'billing_product_id', 'id');
    }

    /*
     |--------------------------------------------------------------------------
     | Functions
     |--------------------------------------------------------------------------
    */

    /**
     * Delete stock history by purchase invoice id
     * 
     * @param int $invoiceId
     * @return bool
     */
    public static function deleteByPurchaseInvoiceId($invoiceId) {
        $lines = PurchaseInvoiceLine::where('invoice_id', $invoiceId)
            ->whereNotNull('billing_product_id')
            ->get();

        if ($lines->isEmpty()) {
            return false;
        }

        foreach ($lines as $line) {
            self::deleteByTarget(self::TARGET_PURCHASE, $line->invoice_id, $line->id);
        }

        return true;
    }

    /**
     * Delete stock history by salve invoice id
     * 
     * @param int $invoiceId
     * @return bool
     */
    public static function deleteBySaleInvoiceId($invoiceId) {
        $lines = InvoiceLine::where('invoice_id', $invoiceId)
            ->whereNotNull('billing_product_id')
            ->get();

        if ($lines->isEmpty()) {
            return false;
        }

        foreach ($lines as $line) {
            self::deleteByTarget(self::TARGET_SALE, $line->invoice_id, $line->id);
        }

        return true;
    }

    /**
     * Set initial stock
     * 
     * @param \App\Models\Billing\Item $billingProduct
     * @param float $qty
     * @param float $price
     * @return bool
     */
    public static function setInitial($billingProduct, $qty, $price) {
        if ($qty == 0.00) {
            return true;
        }

        $itemStockHistory = ItemStockHistory::firstOrNew([
            'billing_product_id' => $billingProduct->id,
            'target' => self::TARGET_INITIAL
        ]);

        if ($itemStockHistory->exists) {
            return false;
        }

        $itemStockHistory->qty   = $qty;
        $itemStockHistory->price = $price;
        $itemStockHistory->save();

        $billingProduct->stock_total = $qty;
        $billingProduct->save();

        return true;
    }

    /**
     * Increase billing product stock by target
     * 
     * @param int $billingProductId
     * @param int $qty
     * @param float $price
     * @param string $target
     * @param int $targetId
     * @param int $lineId
     * @param string $date
     * @return bool
     */
    public static function increaseByTarget($billingProductId, $qty, $price, $target, $targetId, $lineId = null, $date = null) {
        if ($qty < 0.00) {
            $qty = -1 * $qty; // Convert negative signal to positive
        }

        // Get billing product
        $product = Item::filterSource()
            ->where('id', $billingProductId)
            ->first();

        if (!$product || !$product->has_stock) {
            return false;
        }

        // Create product stock history
        $createHistory = [
            'billing_product_id' => $billingProductId,
            'target' => $target,
            'target_id' => $targetId
        ];

        if ($lineId) {
            $createHistory['line_id'] = $lineId;
        }
        
        $itemStockHistory = ItemStockHistory::firstOrNew($createHistory);
        //--

        if ($itemStockHistory->exists) {
            $product->stock_total -= $itemStockHistory->qty; // Remove old qty
        }

        $product->stock_total += $qty; // Add new qty
        $product->price        = $price;
        $product->save();

        $itemStockHistory->qty   = $qty;
        $itemStockHistory->price = $price;

        if ($date) {
            $itemStockHistory->created_at = $date;
        }

        $itemStockHistory->save();

        return true;
    }

    /**
     * Decrease product stock by target
     * 
     * @param int $billingProductId
     * @param int $qty
     * @param string $target
     * @param int $targetId
     * @param int $lineId
     * @param string $date
     * @return bool
     */
    public static function decreaseByTarget($billingProductId, $qty, $target, $targetId, $lineId = null, $date = null) {
        if ($qty >= 0.00) {
            $qty = -($qty); // Convert positive signal to negative
        }

        // Get billing product
        $product = Item::filterSource()
            ->where('id', $billingProductId)
            ->first();

        if (!$product || !$product->has_stock) {
            return false;
        }

        // Create product stock history
        $createHistory = [
            'billing_product_id' => $billingProductId,
            'target' => $target,
            'target_id' => $targetId
        ];

        if ($lineId) {
            $createHistory['line_id'] = $lineId;
        }
        
        $itemStockHistory = ItemStockHistory::firstOrNew($createHistory);
        //--

        if ($itemStockHistory->exists) {
            $product->stock_total -= $itemStockHistory->qty; // Remove old qty
        }

        // If the future stock is less than 0 don't decrease stock
        if (!Setting::get('billing_allow_negative_stock') && ($product->stock_total + $qty) < 0.00) {
            return false;
        }

        $product->stock_total += $qty; // Add new qty
        $product->save();

        $itemStockHistory->qty = $qty;
        $itemStockHistory->price = $product->price;

        if ($date) {
            $itemStockHistory->created_at = $date;
        }

        $itemStockHistory->save();

        return true;
    }

    /**
     * Delete stock and history and update product(s) stock_total
     * 
     * @param string $target
     * @param int $targetId
     * @param int $lineId
     * @param int $billingProductId
     * @return bool
     */
    public static function deleteByTarget($target, $targetId, $lineId = null, $billingProductId = null) {
        if ($billingProductId) {
            $product = Item::filterSource()
                ->where('id', $billingProductId)
                ->first();

            if (!$product || !$product->has_stock) {
                return false;
            }

            $itemStockHistory = ItemStockHistory::firstOrNew([
                'billing_product_id' => $billingProductId,
                'target' => $target,
                'target_id' => $targetId
            ]);

            // Remove old qty
            $product->stock_total -= $itemStockHistory->qty;
            $product->save();

            // Delete stock history
            $itemStockHistory->forceDelete();
        } else {
            $targetStockHistory = ItemStockHistory::with('product')
                ->where('target', $target)
                ->where('target_id', $targetId)
                ->get();

            foreach ($targetStockHistory as $history) {
                // Remove old qty
                $history->product->stock_total -= $history->qty;
                $history->product->save();

                // Delete stock history
                $history->forceDelete();
            }
        }

        return true;
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
