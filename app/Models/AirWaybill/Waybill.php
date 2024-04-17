<?php

namespace App\Models\AirWaybill;

use App\Models\Agency;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mpdf\Mpdf;
use Auth, Setting, Date, Mail;

class Waybill extends \App\Models\BaseModel
{

    use SoftDeletes;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_awb';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'air_waybills';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'main_waybill_id', 'awb_no', 'date', 'reference', 'currency', 'customs_status', 'charge_code', 'value_for_carriage',
        'value_for_customs', 'value_insurance', 'agent_id',
        'customer_id', 'sender_vat', 'sender_name', 'sender_address',
        'consignee_id', 'consignee_vat', 'consignee_name', 'consignee_address',
        'provider_id', 'issuer_name', 'issuer_address',
        'source_airport', 'recipient_airport', 'flight_no_1', 'flight_no_2', 'flight_no_3', 'flight_no_4', 'flight_scales',
        'handling_info', 'nature_quantity_info', 'adicional_info', 'accounting_info', 'obs', 'goods',
        'volumes', 'weight', 'chargable_weight', 'total_goods_price', 'has_hawb', 'goods_type_id', 'hawb_hash'
    ];

    /**
     * The attributes that are dates
     *
     * @var array
     */
    protected $dates = ['date'];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'title' => 'required',
        'awb_no' => 'required',
        'date'   => 'required',
        'sender_name' => 'required',
        'sender_address' => 'required',
        'consignee_name' => 'required',
        'consignee_address' => 'required',
        'issuer_name' => 'required',
        'issuer_address' => 'required',
    );

    /**
     * Create adhesive labels
     *
     * @param \App\Models\type $shipmentsIds
     * @param \App\Models\type $useAgenciesLogo
     * @param type $source [admin|customer]
     * @return type
     */
    public static function printAwb($awbIds){

        $waybills = self::filterSource()
            ->with('sourceAirport', 'recipientAirport', 'agent')
            ->whereIn('id', $awbIds)
            ->get();

        $providers = Provider::get(['name', 'iata_code', 'id']);

        //construct pdf
        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 11,
            'margin_right'  => 10,
            'margin_top'    => 18,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("Paulo Costa");
        $mpdf->shrink_tables_to_fit = 0;

        foreach ($waybills as $waybill) {

            $providerCode = explode('-', $waybill->awb_no);
            $awbNo = explode(' ', @$providerCode[1]);
            $waybillParts = [
                '1' => @$providerCode[0],
                '2' => @$awbNo[0],
                '3' => @$awbNo[1],
            ];


            $totalAgentPrice = $waybill->expenses->filter(function ($item) {
                return $item->type == 'agent';
            })->sum('pivot.price');

            $totalCarrierPrice = $waybill->expenses->filter(function ($item) {
                return $item->type == 'carrier';
            })->sum('pivot.price');

            if($waybill->flight_scales) {
                $scales = [];

                foreach ($waybill->flight_scales as $scale) {

                    $name = @$providers->filter(function ($item) use($scale) { return $item->id == $scale->provider; })->first()->name;
                    $code = @$providers->filter(function ($item) use($scale) { return $item->id == $scale->provider; })->first()->iata_code;

                    $scales[] = [
                        'airport'       => @$scale->airport,
                        'provider'      => $name,
                        'provider_code' => $code,
                    ];
                }

                $scales[] = [
                    'airport'  => @$waybill->recipientAirport->code,
                    'provider' => @$waybill->provider->name,
                    'provider_code' => $code,
                ];

            } else {
                $scales = [[
                    'airport'  => @$waybill->recipientAirport->code,
                    'provider' => @$waybill->provider->name
                ]];
            }

            //dd($scales);

            $data = [
                'is_hawb'           => false,
                'waybill'           => $waybill,
                'waybillParts'      => $waybillParts,
                'totalAgentPrice'   => $totalAgentPrice,
                'totalCarrierPrice' => $totalCarrierPrice,
                'documentTitle'     => 'AWB ' . $waybill->awb_no,
                'scales'            => $scales
            ];

            $blankPage = false;
            $totalCopies = 11;
            for ($i = 1; $i <= $totalCopies; $i++) {
                $data['view'] = 'admin.awb.air_waybills.pdf.awb';

                if ($i == 1) {
                    $blankPage = false;
                    $data['copyNumber'] = 'ORIGINAL 3 (FOR SHIPPER) / (PARA O EXPEDIDOR)';
                } elseif ($i == 2) {
                    $blankPage = true;
                    $data['copyNumber'] = 'COPY 8 (FOR AGENT) / (PARA O AGENTE)';
                } elseif ($i == 3) {
                    $blankPage = false;
                    $data['copyNumber'] = 'ORIGINAL 1 (FOR ISSUING CARRIER) / (PARA O TRANSPORTADOR EMISSOR)';
                } elseif ($i == 4) {
                    $blankPage = true;
                    $data['copyNumber'] = 'COPY 10 (EXTRA COPY) / (CÓPIA EXTRA)';
                } elseif ($i == 5) {
                    $blankPage = true;
                    $data['copyNumber'] = 'COPY 11 (EXTRA COPY) / (CÓPIA EXTRA)';
                } elseif ($i == 6) {
                    $blankPage = true;
                    $data['copyNumber'] = 'COPY 12 (EXTRA COPY) / (CÓPIA EXTRA)';
                } elseif ($i == 7) {
                    $blankPage = false;
                    $data['copyNumber'] = 'ORIGINAL 2 (FOR CONSIGNEE) / (PARA O DESTINATÁRIO)';
                } elseif ($i == 8) {
                    $blankPage = true;
                    $data['copyNumber'] = 'COPY 4 (DELIVERY RECEIPT) / (RECIBO DE ENTREGA)';
                } elseif ($i == 9) {
                    $blankPage = true;
                    $data['copyNumber'] = 'COPY 5 (EXTRA COPY) / (CÓPIA EXTRA)';
                } elseif ($i == 10) {
                    $blankPage = true;
                    $data['copyNumber'] = 'COPY 6 (EXTRA COPY) / (CÓPIA EXTRA)';
                } elseif ($i == 11) {
                    $blankPage = true;
                    $data['copyNumber'] = 'COPY 7 (EXTRA COPY) / (CÓPIA EXTRA)';
                }


                $mpdf->WriteHTML(view('admin.awb.air_waybills.pdf.layouts.awb', $data)->render());

                if ($blankPage) {
                    $mpdf->WriteHTML(view('admin.awb.air_waybills.pdf.layouts.blank', $data)->render());
                } else {
                    if(config('app.env') != 'local') {
                        $mpdf->WriteHTML(view('admin.awb.air_waybills.pdf.layouts.conditions', $data)->render());
                    }
                }
            }
        }

        if(Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('AWB_'.$waybill->awb_no.'.pdf', 'I'); //output to screen

        exit;
    }

    /**
     * Create adhesive labels
     *
     * @param \App\Models\type $shipmentsIds
     * @param \App\Models\type $useAgenciesLogo
     * @param type $source [admin|customer]
     * @return type
     */
    public static function printHawbs($awbIds){

        $waybills = self::filterSource()
                    ->whereIn('id', $awbIds)
                    ->get();

        //construct pdf
        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 11,
            'margin_right'  => 10,
            'margin_top'    => 18,
            'margin_bottom' => 5,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("Paulo Costa");
        $mpdf->shrink_tables_to_fit = 0;


        foreach ($waybills as $waybill) {

            $houseWaybills = self::with('sourceAirport', 'recipientAirport', 'agent')
                                ->where('main_waybill_id', $waybill->id)
                                ->get();

            $providerCode = explode('-', $waybill->awb_no);
            $awbNo = explode(' ', @$providerCode[1]);
            $waybillParts = [
                '1' => @$providerCode[0],
                '2' => @$awbNo[0],
                '3' => @$awbNo[1],
            ];

            foreach ($houseWaybills as $waybill) {

                $data = [
                    'is_hawb'           => true,
                    'waybill'           => $waybill,
                    'waybillParts'      => $waybillParts,
                    'documentTitle'     => '',
                    'totalAgentPrice'   => '',
                    'totalCarrierPrice' => '',
                    'copyNumber'        => 'ORIGINAL 3 (FOR SHIPPER) / (PARA O EXPEDIDOR)'
                ];

                for ($i = 1; $i <= 11; $i++) {
                    $data['view'] = 'admin.awb.air_waybills.pdf.awb';

                    if ($i == 1) {
                        $blankPage = false;
                        $data['copyNumber'] = 'ORIGINAL 3 (FOR SHIPPER) / (PARA O EXPEDIDOR)';
                    } elseif ($i == 2) {
                        $blankPage = true;
                        $data['copyNumber'] = 'COPY 8 (FOR AGENT) / (PARA O AGENTE)';
                    } elseif ($i == 3) {
                        $blankPage = false;
                        $data['copyNumber'] = 'ORIGINAL 1 (FOR ISSUING CARRIER) / (PARA O TRANSPORTADOR EMISSOR)';
                    } elseif ($i == 4) {
                        $blankPage = true;
                        $data['copyNumber'] = 'COPY 10 (EXTRA COPY) / (CÓPIA EXTRA)';
                    } elseif ($i == 5) {
                        $blankPage = true;
                        $data['copyNumber'] = 'COPY 11 (EXTRA COPY) / (CÓPIA EXTRA)';
                    } elseif ($i == 6) {
                        $blankPage = true;
                        $data['copyNumber'] = 'COPY 12 (EXTRA COPY) / (CÓPIA EXTRA)';
                    } elseif ($i == 7) {
                        $blankPage = false;
                        $data['copyNumber'] = 'ORIGINAL 2 (FOR CONSIGNEE) / (PARA O DESTINATÁRIO)';
                    } elseif ($i == 8) {
                        $blankPage = true;
                        $data['copyNumber'] = 'COPY 4 (DELIVERY RECEIPT) / (RECIBO DE ENTREGA)';
                    } elseif ($i == 9) {
                        $blankPage = true;
                        $data['copyNumber'] = 'COPY 5 (EXTRA COPY) / (CÓPIA EXTRA)';
                    } elseif ($i == 10) {
                        $blankPage = true;
                        $data['copyNumber'] = 'COPY 6 (EXTRA COPY) / (CÓPIA EXTRA)';
                    } elseif ($i == 11) {
                        $blankPage = true;
                        $data['copyNumber'] = 'COPY 7 (EXTRA COPY) / (CÓPIA EXTRA)';
                    }

                    $mpdf->WriteHTML(view('admin.awb.air_waybills.pdf.layouts.awb', $data)->render());

                    if ($blankPage) {
                        $mpdf->WriteHTML(view('admin.awb.air_waybills.pdf.layouts.blank', $data)->render());
                    } else {
                        $mpdf->WriteHTML(view('admin.awb.air_waybills.pdf.layouts.conditions', $data)->render());
                    }

                }
            }
        }

        if(Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        //output pdf in a single label
        $mpdf->debug = true;
        return $mpdf->Output('HAWB_'.$waybill->awb_no.'.pdf', 'I'); //output to screen

        exit;
    }

    /**
     * Create adhesive labels
     *
     * @param \App\Models\type $shipmentsIds
     * @param \App\Models\type $useAgenciesLogo
     * @param type $source [admin|customer]
     * @return type
     */
    public static function printLabels($waybillIds){

        $waybills = self::filterSource()
            ->whereIn('id', $waybillIds)
            ->get();


        if($waybills->isEmpty()) {
            return App::abort(404);
        }

        //construct pdf
        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'orientation'   => 'L',
            'format'        => [145,100],
            'margin_left'   => 5,
            'margin_right'  => 5,
            'margin_top'    => 5,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("Paulo Costa");
        $mpdf->shrink_tables_to_fit = 0;

        foreach($waybills as $waybill) {

            $agency = Agency::where('source', $waybill->source)->first();

            $data = [
                'waybill' => $waybill,
                'website' => $agency->web
            ];

            $data['view'] =  'admin.awb.air_waybills.pdf.label';
            for($i = 1 ; $i <= $waybill->volumes ; $i++) {
                $data['count'] = $i;
                $mpdf->WriteHTML(view('admin.awb.air_waybills.pdf.layouts.adhesive_labels', $data)->render()); //write
            }
        }

        if(Setting::get('open_print_dialog_labels')) {
            $mpdf->SetJS('this.print();');
        }

        //output pdf in a single label
        $mpdf->debug = true;
        return $mpdf->Output('AWB_label_'.$waybill->awb_no.'.pdf', 'I'); //output to screen
    }

    /**
     * Send email with billing info
     *
     * @param Request $request [customer_id, month, year, invoice, summary]
     * @param $id
     */
    public static function sendBillingEmail($data) {

        $subject  = 'Fatura - Expedição Aérea ';

        $expressServices = Waybill::filterSource()
            ->where('invoice_id', $data['invoice_id'])
            ->get();

        $service = $expressServices->first();

        if($service->invoice_id) {
            $data['invoiceId'] = $service->invoice_id;
            $date = new Date($service->date);
            $data['year'] = $date->year;

            $invoice = new Invoice($service->api_key);
            $content = $invoice->getDocumentPdf($service->invoice_id, $service->invoice_type);
            $invoiceFile = base64_decode($content);
        }

        Mail::send('emails.invoice_express_service', compact('data', 'expressServices'), function($message) use($data, $invoiceFile, $subject) {

            $message->to($data['email']);

            if(!empty(Setting::get('billing_email_cc'))) {
                $emails = validateNotificationEmails(Setting::get('billing_email_cc'));
                $message = $message->cc($emails['valid']);
            }

            $message = $message->from(config('mail.from.address'), config('mail.from.name'))
                ->subject($subject);

            //attach invoice file
            if($invoiceFile) {
                $filename = 'Fatura '.$data['invoiceId'] . '/'.$data['year'].' - Serviços expresso.pdf';
                $message->attachData($invoiceFile, $filename, [ 'mime' => 'application/pdf']);
            }
        });

        if(count(Mail::failures()) > 0) {
            return false;
        }

        return true;
    }

    /**
     * Print air waybills summary
     *
     * @return string
     */
    public static function printManifest($id){

        $ids = [$id];

        $waybills = self::filterSource()
            ->whereIn('id', $ids)
            ->get();

        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'format'        => 'A4-L',
            'margin_left'   => 0,
            'margin_right'  => 0,
            'margin_top'    => 28,
            'margin_bottom' => 20,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);

        $layout = 'pdf';

        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO TMS");
        $mpdf->shrink_tables_to_fit = 0;

        foreach ($waybills as $waybill) {

            $houseWaybills = self::with('sourceAirport', 'recipientAirport', 'agent')
                ->where('main_waybill_id', $waybill->id)
                ->get();

            $data = [
                'waybill'          => $waybill,
                'houseWaybills'    => $houseWaybills,
                'documentTitle'    => 'Cargo Manifest',
                'documentSubtitle' => 'AWB ' . $waybill->awb_no,
                'view'             => 'admin.awb.air_waybills.pdf.cargo_manifest'
            ];
        }

        $mpdf->WriteHTML(view('admin.layouts.pdf_h', $data)->render()); //write

        if(Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        //output pdf
        $mpdf->debug = true;
        return $mpdf->Output('Cargo Manifest.pdf', 'I'); //output to screen

        exit;

    }

    /**
     * Print air waybills summary
     *
     * @return string
     */
    public static function printSummary($ids){

        $ids = [1,2];
        $waybills = Waybill::with('expenses', 'customer')
            ->filterSource()
            ->whereIn('id', $ids)
            ->get();

        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 11,
            'margin_right'  => 5,
            'margin_top'    => 28,
            'margin_bottom' => 20,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);

        $layout = 'pdf';

        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("Paulo Costa");
        $mpdf->shrink_tables_to_fit = 0;

        $data = [
            'waybills'         => $waybills,
            'documentTitle'    => 'Resumo Expedições Aéreas',
            'documentSubtitle' => '12',
            'view'             => 'admin.awb.air_waybills.pdf.summary'
        ];

        $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write

        if(Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        //output pdf
        $mpdf->debug = true;
        return $mpdf->Output('Resumo de Expedições Aéreas.pdf', 'I'); //output to screen

        exit;

    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function provider()
    {
        return $this->belongsTo('App\Models\AirWaybill\Provider', 'provider_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    public function agent()
    {
        return $this->belongsTo('App\Models\AirWaybill\Agent', 'agent_id');
    }

    public function sourceAirport()
    {
        return $this->belongsTo('App\Models\AirWaybill\IataAirport', 'source_airport', 'code');
    }

    public function recipientAirport()
    {
        return $this->belongsTo('App\Models\AirWaybill\IataAirport', 'recipient_airport', 'code');
    }

    public function expenses()
    {
        return $this->belongsToMany('App\Models\AirWaybill\Expense', 'air_waybills_assigned_expenses', 'waybill_id', 'waybill_expense_id')
            ->withPivot('price');
    }

    public function goodType()
    {
        return $this->belongsTo('App\Models\AirWaybill\GoodType', 'goods_type_id');
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

    public function scopeIsHAWB($query, $isHawb = true){
        if($isHawb) {
            return $query->whereNotNull('main_waybill_id');
        } else {
            return $query->whereNull('main_waybill_id');
        }
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
    public function setUserIdAttribute($value)
    {
        $this->attributes['user_id'] = empty($value) ? null : $value;
    }
    public function setCustomerIdAttribute($value)
    {
        $this->attributes['customer_id'] = empty($value) ? null : $value;
    }
    public function setProviderIdAttribute($value)
    {
        $this->attributes['provider_id'] = empty($value) ? null : $value;
    }
    public function setConsigneeIdAttribute($value)
    {
        $this->attributes['consignee_id'] = empty($value) ? null : $value;
    }
    public function setIssuerIdAttribute($value)
    {
        $this->attributes['issuer_id'] = empty($value) ? null : $value;
    }
    public function setAgentIdAttribute($value)
    {
        $this->attributes['agent_id'] = empty($value) ? null : $value;
    }
    public function setFlightScalesAttribute($value)
    {
        $this->attributes['flight_scales'] = empty($value) ? null : json_encode($value);
    }
    public function setGoodsAttribute($value)
    {
        $this->attributes['goods'] = empty($value) ? null : json_encode($value);
    }
    public function setValueForCarriageAttribute($value)
    {
        $this->attributes['value_for_carriage'] = empty($value) ? null : $value;
    }
    public function setValueForCustomsAttribute($value)
    {
        $this->attributes['value_for_customs'] = empty($value) ? null : $value;
    }
    public function setValueInsuranceAttribute($value)
    {
        $this->attributes['value_insurance'] = empty($value) ? null : $value;
    }
    public function getFlightScalesAttribute($value)
    {
        return json_decode($value);
    }
    public function getGoodsAttribute($value)
    {
        return json_decode($value);
    }

}
