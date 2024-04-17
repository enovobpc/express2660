<?php

namespace App\Models\Logistic;

use App\Models\Customer;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mpdf\Mpdf;
use Setting;

class ReceptionOrder extends \App\Models\BaseModel
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

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'reception_orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'customer_id', 'shipment_id', 'requested_date', 'received_date', 'obs', 'document',
        'user_id', 'status_id', 'total_items', 'total_qty', 'total_qty_received', 'total_price', 'pallets', 'boxs', 'price'
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
     * @var array
     */
    protected $dates = ['received_date'];

    /**
     * Set product location barcode
     * @param $stockValue
     */
    public function setCode() {

        $this->save();

        $code = date('ym');
        $code.= str_pad($this->id, 5, "0", STR_PAD_LEFT);

        $this->code = $code;
        $this->save();
    }

    /**
     * Update reception order price
     * @param $receptionOrderId
     * @return mixed
     */
    public static function updatePrice($receptionOrderId){
        $price = ReceptionOrderLine::where('reception_order_id', $receptionOrderId)->sum('price');
        ReceptionOrder::whereId($receptionOrderId)->update(['total_price' => $price]);
        return $price;
    }

    /**
     * Atualiza a informação da reception order
     * @param null $receptionConfirmation
     * @return bool
     */
    public function updateReceptionOrderProducts()
    {

        $receptionOrder = $this;
        $receptionConfirmation = $receptionOrder->confirmation;

        $receptionConfirmationProducts = $receptionConfirmation->groupBy('product_id');

        //cria um array contendo produto => total recebido a partir da tabela de confirmações
        $updateLines = [];
        foreach ($receptionConfirmationProducts as $productId => $receptionOrderItem) {
            $qty = $receptionOrderItem->sum('qty_received');
            $updateLines[$productId] = $qty;
        }


        //atualiza as linhas da ordem de recepção
        $receptionOrderLines = ReceptionOrderLine::where('reception_order_id', $receptionOrder->id)->get();
        foreach ($receptionOrderLines as $line) {

            $qtyReceived = @$updateLines[$line->product_id];

            $line->qty_received = $qtyReceived;
            $line->save();
        }

        $receptionOrder->update([
            'total_items'          => $receptionOrder->lines->count(),
            'total_qty'            => $receptionOrder->lines->sum('qty'),
            'total_qty_received'   => $receptionOrder->lines->sum('qty_received'),
            'total_price'          => $receptionOrder->lines->sum('price'),
        ]);

        return $receptionOrder;
    }
    
    /**
     * Print reception order summary
     *
     * @param $ids
     * @return mixed
     * @throws \Throwable
     */
    public static function printSummary($ids) {

        $receptionOrders = ReceptionOrder::with('customer', 'lines')
            ->filterSource()
            ->whereIn('id', $ids)
            ->get();

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
        $mpdf->SetAuthor("Paulo Costa");
        $mpdf->shrink_tables_to_fit = 0;

        foreach ($receptionOrders as $receptionOrder) {
            $data = [
                'receptionOrder'    => $receptionOrder,
                'lines'             => $receptionOrder->lines,
                'documentTitle'     => 'Ordem de Recepção',
                'documentSubtitle'  => '',
                'view'              => 'admin.printer.logistic.reception_orders.summary'
            ];

            $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write
        }

        if(Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Ordem Recepção ' . $receptionOrder->code . '.pdf', 'I'); //output to screen

        exit;
    }


    public function storeOnS3Document() {

        $receptionOrder = $this;
        if (config('app.source') == 'activos24') {

            try {

                if (@$receptionOrder->customer->customer_id) {
                    $receptionOrder->customer = Customer::find(@$receptionOrder->customer->customer_id);
                }

                $onS3 = new \App\Models\InvoiceGateway\OnSearch\Document();
                $onS3->insertOrUpdateDocument($receptionOrder, 'reception');
            } catch (\Exception $e) {
                return [
                    'result'   => false,
                    'feedback' => 'Erro submissão OnS3: ' . $e->getMessage()
                ];
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
        return $this->hasMany('App\Models\Logistic\ReceptionOrderLine', 'reception_order_id');
    }

    public function confirmation()
    {
        return $this->hasMany('App\Models\Logistic\ReceptionOrderConfirmation', 'reception_order_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\Logistic\ReceptionOrderStatus', 'status_id');
    }

    public function shipment()
    {
        return $this->belongsTo('App\Models\Shipment', 'shipment_id');
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
    public function getAllowEditAttribute() {
        if($this->status_id != ReceptionOrderStatus::STATUS_CONCLUDED) {
            return true;
        }
        return false;
    }
}
