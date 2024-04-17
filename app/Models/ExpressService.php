<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Auth, Mail, Date, Setting;

class ExpressService extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_express_services';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'express_services';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'agency_id', 'customer_id', 'operator_id', 'title', 'description', 'date', 'total_price', 'operator_price',
        'km', 'vehicle', 'is_paid', 'invoice_id', 'invoice_draft', 'status'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'agency_id' => 'required',
        'title'     => 'required',
        'date'      => 'required',
    );

    /**
     * Store or Update a meeting on calendar
     */
    public function setOnCalendar() {

        $calendar = CalendarEvent::firstOrNew([
            'source_class' => 'App\Models\ExpressService',
            'source_id'    => $this->id
        ]);

        $calendar->title        = $this->title . $this->operator_id ? '('. @$this->operator->name .')' : '';
        $calendar->start        = $this->date;
        $calendar->end          = $this->date;
        $calendar->user_id      = Auth::user()->id;
        $calendar->agencies     = Auth::user()->agencies;
        $calendar->description  = 'Motorista: ' . @$this->operator->name;
        $calendar->source_class = 'App\Models\ExpressService';
        $calendar->source_id = $this->id;
        $calendar->save();
    }

    /**
     * Delete a meeting from calendar
     */
    public function deleteFromCalendar() {

        $result = CalendarEvent::where('source_class', 'App\Models\ExpressService')
            ->where('source_id', $this->id)
            ->delete();

        return $result;
    }

    /**
     * Send email with billing info
     *
     * @param Request $request [customer_id, month, year, invoice, summary]
     * @param $id
     */
    public static function sendEmail($data) {

        $subject  = 'Fatura - Serviços Expresso';

        $expressServices = ExpressService::filterAgencies()
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

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */

    public function agency()
    {
        return $this->belongsTo('App\Models\Agency');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }

    public function operator()
    {
        return $this->belongsTo('App\Models\User');
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
    public function setOperatorIdAttribute($value)
    {
        $this->attributes['operator_id'] = empty($value) ? null : $value;
    }

    public function setCustomerIdAttribute($value)
    {
        $this->attributes['customer_id'] = empty($value) ? null : $value;
    }

    public function setKmAttribute($value)
    {
        $this->attributes['km'] = empty($value) || $value == 0.00 ? null : $value;
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
    
    
}
