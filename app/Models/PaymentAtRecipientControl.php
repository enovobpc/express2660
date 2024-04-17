<?php

namespace App\Models;

use Setting;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mpdf\Mpdf;

class PaymentAtRecipientControl extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_cod';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'refunds_cod';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shipment_id', 'obs', 'payment_method', 'payment_date'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'shipment_id'   => 'required',
    );

    /**
     * Print COD control summary
     *
     * @param $ids
     * @param string $outputFormat
     * @return mixed
     */
    public static function printSummary($ids, $outputFormat = 'I') {

        ini_set("memory_limit", "-1");

        $shipments = Shipment::filterAgencies()
            ->whereIn('id', $ids)
            ->get();

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 14,
            'margin_right'  => 5,
            'margin_top'    => 25,
            'margin_bottom' => 15,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("Paulo Costa");
        $mpdf->shrink_tables_to_fit = 0;

        $data = [
            'shipments'         => $shipments,
            'documentTitle'     => 'Resumo de Pagamentos no Destino',
            'documentSubtitle'  => '',
            'view'              => 'admin.printer.refunds.summary_cod'
        ];

        $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write

        if(Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Resumo de Pagamentos no Destino.pdf', $outputFormat); //output to screen

        exit;
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function shipment()
    {
        return $this->hasOne('App\Models\Shipment', 'shipment_id');
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
