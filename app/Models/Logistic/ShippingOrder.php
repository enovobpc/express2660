<?php

namespace App\Models\Logistic;

use Illuminate\Database\Eloquent\SoftDeletes;
use Mpdf\Mpdf;
use Setting, DB;

class ShippingOrder extends \App\Models\BaseModel
{

    use SoftDeletes;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_logistic';

    /**
     * Status
     */
    const STATUS_PENDING    = '1';
    const STATUS_PROCESSING = '2';
    const STATUS_CONCLUDED  = '3';
    const STATUS_CANCELED   = '4';
    const STATUS_PICKUP     = '5';


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'shipping_orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'customer_id', 'shipment_id', 'date','obs', 'document', 'user_id', 'status_id',
        'total_items', 'qty_total', 'qty_satisfied', 'total_price'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'customer_id'  => 'required',
    );

    /**
     * Set product location barcode
     * @param $stockValue
     */
    public function setCode() {

        $this->save();
        if(!$this->code) {

            $code = date('ym');
            $code.= str_pad($this->id, 5, "0", STR_PAD_LEFT);

            $this->code = $code;
            $this->save();
        }
    }

    /**
     * Update reception order price
     * @param $receptionOrderId
     * @return mixed
     */
    public static function updatePrice($shippingOrderId){
        $shippingOrderLine = ShippingOrderLine::where('shipping_order_id', $shippingOrderId)->get();
        $price = $shippingOrderLine->sum('price');
        $qty   = $shippingOrderLine->sum('qty');

        ShippingOrder::whereId($shippingOrderId)->update([
            'total_price' => $price,
            'qty_total' => $qty
        ]);

        return $price;
    }

    /**
     * @param $ids
     * @param string $outputMode
     * @return mixed
     * @throws \Throwable
     */
    public static function printSummary($ids, $outputMode = 'I') {

        $shippingOrders = ShippingOrder::with('customer')
            ->with(['lines' => function($q) {
                $q->with('product', 'location');
            }])
            ->filterSource()
            ->whereIn('id', $ids)
            ->get();

        //construct pdf
        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'orientation'   => 'L',
            'format'        => 'A4',
            'margin_left'   => 11,
            'margin_right'  => 5,
            'margin_top'    => 18,
            'margin_bottom' => 20,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);

        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        foreach ($shippingOrders as $shippingOrder) {
            $data = [
                'shippingOrder'     => $shippingOrder,
                'documentTitle'     => 'Ordem de Saída',
                'documentSubtitle'  => '',
                'view'              => 'admin.printer.logistic.shipping_orders.summary'
            ];

            $mpdf->WriteHTML(view('admin.layouts.pdf_h', $data)->render()); //write
        }

        if(Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Ordem de Saída.pdf', $outputMode); //output to screen

        exit;
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
    public static function printLabels($shippingOrdersIds) {

        $shippingOrders = ShippingOrder::filterSource()
                ->whereIn('id', $shippingOrdersIds)
                ->get();

        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'orientation'   => 'L',
            'format'        => [145,100],
            'margin_left'   => 5,
            'margin_right'  => 5,
            'margin_top'    => 2,
            'margin_bottom' => 2,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);

        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;


        foreach ($shippingOrders as $shippingOrder) {

            $qrCode = new \Mpdf\QrCode\QrCode($shippingOrder->code);
            $qrCode->disableBorder();
            $output = new \Mpdf\QrCode\Output\Png();
            $qrCode = 'data:image/png;base64,' . base64_encode($output->output($qrCode, 120));

            $data = [
                'shippingOrder'  => $shippingOrder,
                'qrCode' => $qrCode,
            ];

            $data['view'] = 'admin.printer.logistic.shipping_orders.label';
            $mpdf->WriteHTML(view('admin.printer.shipments.layouts.adhesive_labels', $data)->render()); //write
        }


        /*if(Setting::get('open_print_dialog_labels')) {
            $mpdf->SetJS('this.print();');
        }*/

        //output pdf in a single label
        $mpdf->debug = true;
        return $mpdf->Output('Etiquetas.pdf', 'I'); //output to screen

        exit;
    }

    /**
     * @param $ids
     * @param string $outputMode
     * @return mixed
     * @throws \Throwable
     */
    public static function printWavePicking($ids, $outputMode = 'I') {

        //https://blog.longa.com.br/sistema-de-picking/

        $shippingOrders = ShippingOrder::with('customer')
            ->with(['lines' => function($q) {
                $q->with('product', 'location');
            }])
            ->filterSource()
            ->whereIn('id', $ids)
            ->get();

        //ShippingOrder::whereIn('id', $ids)->update(['status_id' => ShippingOrderStatus::STATUS_PENDING]);

        //LIMITAR APENAS A ORDENS NO ESTADO PENDENTE
        $shippingOrdersLines = ShippingOrderLine::with('product', 'location')
            ->whereHas('shipping_order', function($q){
                $q->whereIn('status_id', [
                    ShippingOrderStatus::STATUS_PENDING,
                    ShippingOrderStatus::STATUS_PROCESSING,
                    ShippingOrderStatus::STATUS_PICKUP
                ]);
            })
            ->whereIn('shipping_order_id', $ids)
            ->groupBy(DB::raw('concat(product_id,location_id,COALESCE(serial_no,\'\'),COALESCE(lote,\'\'))'))
            ->get([
                'shipping_orders_lines.*',
                DB::raw('sum(qty) as total_qty'),
                DB::raw('count(*) as total_count'),
                DB::raw('concat(product_id,location_id,COALESCE(serial_no,\'\'),COALESCE(lote,\'\')) as uniqid')
            ]);

        //dd($shippingOrdersLines->toArray());
        $shippingOrdersLines = $shippingOrdersLines->sortBy('location.code');

        //construct pdf
        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'orientation'   => 'L',
            'format'        => 'A4',
            'margin_left'   => 11,
            'margin_right'  => 5,
            'margin_top'    => 18,
            'margin_bottom' => 20,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);

        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;


        $data = [
            'shippingOrders'      => $shippingOrders,
            'shippingOrdersLines' => $shippingOrdersLines,
            'documentTitle'       => 'Wave Picking',
            'documentSubtitle'    => '',
            'view'                => 'admin.printer.logistic.shipping_orders.wave_picking'
        ];

        $mpdf->WriteHTML(view('admin.layouts.pdf_h', $data)->render()); //write


        if(Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Ordem de Saída.pdf', $outputMode); //output to screen

        exit;
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */

    public function lines() {
        return $this->hasMany('App\Models\Logistic\ShippingOrderLine', 'shipping_order_id');
    }

    public function customer() {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    public function shipment() {
        return $this->belongsTo('App\Models\Shipment', 'shipment_id');
    }

    public function status() {
        return $this->belongsTo('App\Models\Logistic\ShippingOrderStatus', 'status_id');
    }

    public function user() {
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
    public function scopeFilterAgencies($query) {
        return $query->whereHas('customer', function($q){
            $q->filterAgencies();
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

}
