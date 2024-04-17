<?php

namespace App\Models\Logistic;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\FileTrait;
use Mpdf\Mpdf;
use Setting;

class Product extends \App\Models\BaseModel
{

    use SoftDeletes,
        FileTrait;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_logistic';

    /**
     *
     */
    const STATUS_AVAILABLE  = 'available';
    const STATUS_LOWSTOCK   = 'lowstock';
    const STATUS_OUTSTOCK   = 'outstock';
    const STATUS_BLOCKED    = 'blocked';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'products';


    /**
     * Default upload directory
     *
     * @const string
     */
    const DIRECTORY = 'uploads/products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id', 'warehouse_id', 'barcode', 'name', 'sku', 'customer_ref',
        'category_id', 'subcategory_id', 'family_id',
        'brand_id', 'model_id', 'description', 'weight', 'width', 'height', 'length', 'price',
        'photo_url', 'obs', 'unity', 'unity_type', 'unities_by_pack', 'packs_by_box', 'boxes_by_pallete', 'stock_min',
        'stock_max', 'stock_total', 'stock_status', 'stock_allocated', 'is_active', 'is_obsolete', 'volumes',
        'serial_no', 'lote', 'expiration_date', 'production_date', 'description', 'vat', 'master_location', 'last_update', 'need_validation'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'sku'  => 'required',
        'name' => 'required',
    );

    /**
     * Update product stock total
     */
    public function updateStockTotal()
    {
        $totalStock = ProductLocation::where('product_id', $this->id)->sum('stock');

        $stockStatus = 'outstock';

        if ($this->stock_min) {
            if ($totalStock > $this->stock_min) {
                $stockStatus = 'outstock';
            } elseif ($totalStock > 0 && $totalStock <= $this->stock_min) {
                $stockStatus = 'lowstock';
            } else {
                $stockStatus = 'outstock';
            }
        }

        $this->stock_total  = $totalStock;
        $this->stock_status = $stockStatus;
        $this->save();
    }

    /**
     * Return stock status label
     */
    public function getStockLabel($stock = null)
    {
        $stock = is_null($stock) ? $this->stock_available : $stock;

        if (empty($stock)) {
            return 'red';
        } elseif ($stock > $this->stock_min) {
            return 'green';
        } elseif ($stock > 0 && $stock < $this->stock_min) {
            return 'yellow';
        }

        return 'red';
    }

    /**
     * Return next model ID
     * @param $query
     * @return mixed
     */
    public function nextId()
    {
        return Product::filterSource()
            ->where('id', '>', $this->id)
            ->min('id');
    }

    /**
     * Return previous model ID
     * @param $query
     * @return mixed
     */
    public function previousId()
    {
        return Product::filterSource()
            ->where('id', '<', $this->id)
            ->max('id');
    }

    /**
     * Auto select location id by given stock
     * @param $requiredQtd Quantity selected
     * @return int
     */
    public function autoselectLocationId($requiredQtd)
    {
        $location = @$this->locations->first()->id;

        $locations = [
            $location
        ];

        return $locations;
    }

    /**
     * Print labels
     *
     * @param $productId
     * @param $barcodes
     * @param $printQty
     * @return mixed
     * @throws \Throwable
     */
    public static function printLabels($productId)
    {

        $product = Product::filterSource()->findOrFail($productId);

        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'orientation'   => 'L',
            'format'        => [100, 145],
            'margin_left'   => 2,
            'margin_right'  => 2,
            'margin_top'    => 2,
            'margin_bottom' => 2,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);

        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;


        $data = [
            'product'  => $product
        ];

        $data['view'] = 'admin.printer.logistic.products.label';
        $mpdf->WriteHTML(view('admin.printer.shipments.layouts.adhesive_labels', $data)->render()); //write


        if (Setting::get('open_print_dialog_labels')) {
            $mpdf->SetJS('this.print();');
        }

        //output pdf in a single label
        $mpdf->debug = true;
        return $mpdf->Output('Etiquetas.pdf', 'I'); //output to screen

        exit;
    }

    /**
     * Print locations labels
     *
     * @param $productId
     * @param $barcodes
     * @param $printQty
     * @return mixed
     * @throws \Throwable
     */
    public static function printLabelsLocations($productId, $barcodes, $printQty)
    {

        $product = Product::filterSource()->findOrFail($productId);

        $productLocations = ProductLocation::with('location')->whereIn('barcode', $barcodes)->get();
        $productLocations = $productLocations->groupBy('barcode');


        //construct pdf
        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'orientation'   => 'L',
            'format'        => [100, 145],
            'margin_left'   => 2,
            'margin_right'  => 2,
            'margin_top'    => 2,
            'margin_bottom' => 2,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);

        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        foreach ($productLocations as $barcode => $productLocation) {
            foreach ($productLocation as $location) {

                $qty = $printQty[$barcode];

                $data = [
                    'product'  => $product,
                    'location' => $location->location,
                    'barcode'  => $barcode
                ];

                $data['view'] = 'admin.printer.logistic.products.label_locations';
                for ($i = 1; $i <= $qty; $i++) {
                    $mpdf->WriteHTML(view('admin.printer.shipments.layouts.adhesive_labels', $data)->render()); //write
                }
            }
        }

        if (Setting::get('open_print_dialog_labels')) {
            $mpdf->SetJS('this.print();');
        }

        //output pdf in a single label
        $mpdf->debug = true;
        return $mpdf->Output('Etiquetas.pdf', 'I'); //output to screen

        exit;
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    public function images()
    {
        return $this->hasMany('App\Models\Logistic\ProductImage');
    }

    public function brand()
    {
        return $this->belongsTo('App\Models\Logistic\Brand', 'brand_id');
    }

    public function brand_model()
    {
        return $this->belongsTo('App\Models\Logistic\Model', 'model_id');
    }

    public function locations()
    {
        return $this->belongsToMany('App\Models\Logistic\Location', 'products_locations', 'product_id')
            ->withPivot('stock', 'stock_available', 'stock_allocated', 'barcode');
    }

    public function warehouse()
    {
        return $this->belongsTo('App\Models\Logistic\Warehouse', 'warehouse_id');
    }


    public function product_location()
    {
        return $this->hasMany('App\Models\Logistic\ProductLocation', 'product_id');
    }

    public function history()
    {
        return $this->hasMany('App\Models\Logistic\ProductHistory', 'product_id');
    }

    public function stock_histories()
    {
        return $this->hasMany('App\Models\Logistic\ProductStockHistory', 'product_id');
    }

    public function family()
    {
        return $this->belongsTo('App\Models\Logistic\Family', 'family_id');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Logistic\Category', 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo('App\Models\Logistic\SubCategory', 'subcategory_id');
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
    public function setStockMinAttribute($value)
    {
        $this->attributes['stock_min'] = empty($value) ? null : $value;
    }

    public function setStockMaxAttribute($value)
    {
        $this->attributes['stock_max'] = empty($value) ? null : $value;
    }

    public function setCategoryIdAttribute($value)
    {
        $this->attributes['category_id'] = empty($value) ? null : $value;
    }

    public function setSubcategoryIdAttribute($value)
    {
        $this->attributes['subcategory_id'] = empty($value) ? null : $value;
    }

    public function setFamilyidAttribute($value)
    {
        $this->attributes['family_id'] = empty($value) ? null : $value;
    }

    public function setBrandIdAttribute($value)
    {
        $this->attributes['brand_id'] = empty($value) ? null : $value;
    }

    public function setModelIdAttribute($value)
    {
        $this->attributes['model_id'] = empty($value) ? null : $value;
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setWeightAttribute($value)
    {
        $this->attributes['weight'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setWidthAttribute($value)
    {
        $this->attributes['width'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setHeightAttribute($value)
    {
        $this->attributes['height'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setLengthAttribute($value)
    {
        $this->attributes['length'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setProductionDateAttribute($value)
    {
        $this->attributes['production_date'] = empty($value) || $value == '0000-00-00' ? null : $value;
    }

    public function setExpirationDateAttribute($value)
    {
        $this->attributes['expiration_date'] = empty($value) || $value == '0000-00-00' ? null : $value;
    }

    public function getAvailablePalletsAttribute($value)
    {
        if ($this->boxes_by_pallete) {
            return round($this->stock / $this->boxes_by_pallete);
        }
        return 0;
    }
}
