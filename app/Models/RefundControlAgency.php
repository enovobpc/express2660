<?php

namespace App\Models;

use App\Models\Traits\FileTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Setting, Mail;

class RefundControlAgency extends BaseModel
{

    use SoftDeletes,
        FileTrait;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_refunds_control_agencies';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'refunds_control_agencies';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shipment_id', 'received_method', 'received_date', 'payment_method', 'payment_date',
        'obs', 'obs_internal', 'confirmed', 'canceled'
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
     * Send refund email notification
     * @return mixed
     */
    public static function sendEmail($emails, $shipments = null) {

        $attachFiles = [
            RefundControl::printSummary(null, true, $shipments, 'S')
        ];

        Mail::send('emails.refunds.notify_agency', compact('shipments'), function ($message) use ($emails, $attachFiles) {
            $message->to($emails);
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

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function shipment()
    {
        return $this->belongsTo('App\Models\Shipment', 'shipment_id');
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

    public function setPaymentDateAttribute($value)
    {
        $this->attributes['payment_date'] = empty($value) ? null : $value;
    }
    
}
