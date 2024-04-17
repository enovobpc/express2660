<?php

namespace App\Models\Cashier;

use Illuminate\Database\Eloquent\SoftDeletes;
use DB, Setting;
use Mpdf\Mpdf;

class Movement extends \App\Models\BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_cashier_movements';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cashier_movements';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'operator_id', 'customer_id', 'provider_id', 'operator_id', 'type_id',
        'code', 'description', 'amount', 'payment_method', 'sense', 'date', 'obs', 'is_paid'
    ];
    
    /**
     * Date attributes 
     * 
     * @var type 
     */
    protected $dates = [
        'date'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'created_by'   => 'required',
        'description'  => 'required',
        'amount'       => 'required'
    );
    
    /**
     * Validator custom attributes
     * 
     * @var array 
     */
    protected $customAttributes = array(
        'user_id'     => 'Utilizador',
        'description' => 'Descrição',
        'amount'      => 'Valor'
    );
    
    /**
     * Create movement code
     * 
     * @return int
     */
    public function setMovementCode($save = true)
    {

        if(!$this->code) {

            $totalMovements = Movement::filterSource()
                ->withTrashed()
                ->where(DB::raw('YEAR(date)'), date('Y'))
                ->count();

            $totalMovements++;

            $code = date('ym') . str_pad($totalMovements, 4, '0', STR_PAD_LEFT);

            if ($save) {
                $this->code = $code;
                $this->save();
            }

            return $code;
        }

        $this->save();
        return $this->code;
    }

    /**
     * Print COD control summary
     *
     * @param $ids
     * @param string $outputFormat
     * @return mixed
     */
    public static function printSummary($ids, $groupedBy=null, $outputFormat = 'I') {

        ini_set("memory_limit", "-1");

        $movements = Movement::with('customer', 'provider')
            ->with(['operator' => function($q){
                $q->withTrashed();
            }])
            ->filterSource()
            ->whereIn('id', $ids)
            ->get();

        if($groupedBy == 'operator') {
            $movements = $movements->groupBy('operator.name');
        }

        $mpdf = new Mpdf([
            'format'        => 'A4-L',
            'margin_left'   => 14,
            'margin_right'  => 5,
            'margin_top'    => 25,
            'margin_bottom' => 15,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        $data = [
            'movements'         => $movements,
            'grouped'           => $groupedBy,
            'documentTitle'     => 'Resumo - Valores de Caixa',
            'documentSubtitle'  => '',
            'view'              => 'admin.printer.cashier.movements'
        ];

        $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write

        if(Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Resumo de Valores de Caixa.pdf', $outputFormat); //output to screen

        exit;
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by');
    }
    
    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    public function provider()
    {
        return $this->belongsTo('App\Models\Provider', 'provider_id');
    }

    public function operator()
    {
        return $this->belongsTo('App\Models\User', 'operator_id');
    }

    public function type()
    {
        return $this->belongsTo('App\Models\PurchaseInvoiceType', 'type_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo('App\Models\PaymentMethod', 'payment_method', 'code');
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
    
    public function setCustomerIdAttribute($value)
    {
        $this->attributes['customer_id'] = empty($value) ? null : $value;
    }

    public function setProviderIdAttribute($value)
    {
        $this->attributes['provider_id'] = empty($value) ? null : $value;
    }

    public function setOperatorIdAttribute($value)
    {
        $this->attributes['operator_id'] = empty($value) ? null : $value;
    }

    public function setTypeIdAttribute($value)
    {
        $this->attributes['type_id'] = empty($value) ? null : $value;
    }
}
