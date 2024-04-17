<?php

namespace App\Models;

use App\Models\Traits\FileTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mockery\Exception;
use Mpdf\Mpdf;
use Setting, Mail;

class RefundControl extends BaseModel
{

    use SoftDeletes,
        FileTrait;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_refunds_control';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    //protected $table = 'refunds_customers';
    protected $table = 'refunds_control';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shipment_id', 'request_id', 'received_method', 'payment_method',
        'received_date', 'payment_date', 'requested_method', 'requested_date', 'submited_at',
        'obs', 'customer_obs', 'confirmed', 'canceled', 'received_user_id', 'payment_user_id'
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
     * Default upload directory
     *
     * @const string
     */
    const DIRECTORY = 'uploads/proofs';

    /**
     * Print proof os selected shipments
     * @param $shipmentIds
     * @return mixed
     */
    public static function printProof($shipmentIds) {

        ini_set("memory_limit", "-1");
        ini_set("pcre.backtrack_limit", "5000000");

        $shipments = Shipment::filterAgencies()
            ->with('refund_control')
            ->whereIn('id', $shipmentIds)
            ->get();

        $customers = $shipments->groupBy('customer_id');

        $mpdf = new Mpdf([
            'format'        => 'A4-L',
            'margin_left'   => 0,
            'margin_right'  => 0,
            'margin_top'    => 5,
            'margin_bottom' => 3,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);

        $mpdf->SetAuthor("Paulo Costa");
        $mpdf->showImageErrors = true;
        $mpdf->shrink_tables_to_fit = 1;

        foreach ($customers as $customer => $shipments) {
            $data = [
                'documentTitle' => 'Comprovativo de Pagamento',
                'customer'      => $shipments->first()->customer,
                'shipments'     => $shipments,
                'view'          => 'admin.printer.refunds.proof'
            ];

            $mpdf->WriteHTML(view('admin.layouts.pdf_blank', $data)->render()); //write
        }

        if(Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Comprovativo de Pagamento de Reembolso.pdf', 'I'); //output to screen
        exit;
    }

    /**
     * Print refunds history
     *
     * @param $shipmentIds
     * @param string $outputFormat [I = output to screen]
     * @return string
     */
    public static function printSummary($shipmentIds, $agencies = false, $shipments = null, $outputFormat = 'I'){

        ini_set("memory_limit", "-1");
        ini_set("pcre.backtrack_limit", "5000000");


        if(empty($shipments)) {
            $shipments = Shipment::filterAgencies();

            if($agencies) {
                $shipments = $shipments->with('refund_agencies');
            } else {
                $shipments = $shipments->with('refund_control');
            }

            $shipments = $shipments->whereIn('id', $shipmentIds)
                        ->get();
        }

        $customer = null;
        if(count(array_unique($shipments->pluck('customer_id', 'customer_id')->toArray())) == 1) {
            $customer = $shipments->first()->customer;
        }

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 10,
            'margin_right'  => 10,
            'margin_top'    => 30,
            'margin_bottom' => 20,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);
        $mpdf->SetAuthor("ENOVO");
        $mpdf->showImageErrors      = true;
        $mpdf->shrink_tables_to_fit = 0;

        $data = [
            'customer'          => $customer,
            'shipments'         => $shipments,
            'documentTitle'     => $agencies ? 'Reembolso a Agências' : 'Resumo de Reembolsos',
            'documentSubtitle'  => '',
            'view'              => $agencies ? 'admin.printer.refunds.summary_agencies' : 'admin.printer.refunds.summary_customers'
        ];

        $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render());

        if(Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;

        $filename = self::createFilename('summary');

        if($outputFormat == 'S') {
            return [
                'mime'     => 'application/pdf',
                'filename' => $filename,
                'content'  => $mpdf->Output($filename, 'S')
            ];
        }

        return $mpdf->Output($filename, $outputFormat); //output to screen
        exit;
    }

    /**
     * Return filename
     *
     * @param $shipmentIds
     * @param string $outputFormat
     * @return string
     */
    public static function createFilename($method){
        if($method == 'summary') {
            return 'Resumo de Reembolsos.pdf';
        }
    }

    /**
     * Send refund email notification
     * @return mixed
     */
    public static function sendEmail($emails, $shipments = null) {

        $attachFiles = [
            RefundControl::printSummary(null, false, $shipments, 'S')
        ];


        Mail::send('emails.refunds.notify_customer', compact('shipments'), function ($message) use ($emails, $attachFiles) {
            $message->to($emails);

            if(Setting::get('refunds_email_cc')) {
                $emails = explode(';', Setting::get('refunds_email_cc'));
                $message->cc($emails);
            }

            $message->subject('Notificação de Reembolso');

            if($attachFiles) {
                foreach ($attachFiles as $file) {
                    $message->attachData($file['content'], $file['filename'], ['mime' => $file['mime']]);
                }
            }
        });

        if (count(Mail::failures()) > 0) {
            throw new Exception('Falhou o envio do e-mail. Tente de novo.');
        }
    }

    /**
     * Get customer available refunds
     *
     * @param $customerId
     * @return mixed
     */
    public static function getCustomerAvailableRefunds($customerId) {

            $shipments = Shipment::where(function($q) use($customerId) {
                $q->where('customer_id', $customerId);
                $q->orWhere('requested_by', $customerId);
            })
            ->with(['status' => function($q){
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(ShippingStatus::CACHE_TAG);
                $q->select(['id', 'name', 'color', 'is_final']);
            }])
            ->with('last_history')
            ->with('refund_control')
            ->where('is_collection', 0)
            ->whereNotNull('charge_price')
            ->whereNotIn('status_id', [ShippingStatus::DEVOLVED_ID, ShippingStatus::CANCELED_ID])
            ->whereHas('refund_control', function($q) {
                $q->whereNull('payment_method');
                $q->whereNull('payment_date');
                $q->whereNotNull('received_method');
                $q->whereNotNull('received_date');
                $q->where('received_method', '<>', 'claimed');
                $q->where('canceled', 0);
            })
            ->pluck('id')
            ->toArray();

        return $shipments;
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function received_user()
    {
        return $this->belongsTo('App\Models\User', 'received_user_id');
    }

    public function payment_user()
    {
        return $this->belongsTo('App\Models\User', 'payment_user_id');
    }

    public function shipment()
    {
        return $this->hasOne('App\Models\Shipment', 'id', 'shipment_id');
    }

    public function request()
    {
        return $this->hasOne('App\Models\RefundControlRequest', 'id', 'request_id');
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
    public function setReceivedMethodAttribute($value)
    {
        $this->attributes['received_method'] = empty($value) ? null : $value;
    }

    public function setReceivedDateAttribute($value)
    {
        $this->attributes['received_date'] = empty($value) ? null : $value;
    }

    public function setPaymentMethodAttribute($value)
    {
        $this->attributes['payment_method'] = empty($value) ? null : $value;
    }

    public function setSubmitedAtAttribute($value)
    {
        $this->attributes['submited_at'] = empty($value) ? null : $value;
    }

    public function setPaymentDateAttribute($value)
    {
        $this->attributes['payment_date'] = empty($value) ? null : $value;
    }

    public function setRequestedMethodAttribute($value)
    {
        $this->attributes['requested_method'] = empty($value) ? null : $value;
    }

    public function setRequestedDateAttribute($value)
    {
        $this->attributes['requested_date'] = empty($value) ? null : $value;
    }

    public function setRequestIdAttribute($value)
    {
        $this->attributes['request_id'] = empty($value) ? null : $value;
    }
}
