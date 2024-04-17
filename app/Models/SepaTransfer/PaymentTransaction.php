<?php

namespace App\Models\SepaTransfer;

use Illuminate\Database\Eloquent\SoftDeletes;
use Setting, Mail;

class PaymentTransaction extends \App\Models\BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_sepa_payments_transactions';
    const STATUS_PENDING  = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sepa_payments_transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payment_id', 'group_id', 'invoice_id', 'purchase_invoice_id', 'customer_id', 'provider_id',
        'reference', 'amount', 'mandate_date', 'mandate_code', 'transaction_code', 'company_code', 'company_vat',
        'company_name', 'bank_name', 'bank_iban', 'bank_swift', 'obs', 'status', 'error_code', 'error_msg'
    ];

    /**
     * The attributes that are dates.
     *
     * @var array
     */
    protected  $dates = ['mandate_date'];
    
    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'payment_id'      => 'required',
        'group_id'        => 'required',
        'invoice_id'      => 'required',
        'customer_id'     => 'required',
        'processing_date' => 'required'
    );

    /**
     * Notify sepa error
     */
    public function notifyTransactionError() {

        $transaction = $this;
        $customer = @$transaction->customer;
        $email    = @$customer->billing_email;
        $emails   = validateNotificationEmails($email);
        $emails   = $emails['valid'];

        if(empty($emails)) {
            return false;
        }

        try {

            //add emails in CC
            $emailsCC = null;
            if (!empty(Setting::get('billing_email_cc'))) {
                $emailsCC = validateNotificationEmails(Setting::get('billing_email_cc'));
                $emailsCC = $emailsCC['valid'];
            }

            $subject = "Suspensão de Licença - Falha na cobrança por débito direto.";

            Mail::send('emails.billing.sepa_transfer_error', compact('transaction', 'customer'), function ($message) use ($emails, $emailsCC, $transaction, $subject) {

                $message->from(config('mail.from.address'), config('mail.from.name'))
                    ->to($emails);

                if ($emailsCC) {
                    $message = $message->cc($emailsCC);
                }

                $message->subject($subject);
            });


            if (count(Mail::failures()) > 0) {
                return false;
            }

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage() . ' line ' . $e->getLine() . ' file '. $e->getFile());
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
    public function payment()
    {
        return $this->belongsTo('App\Models\SepaTransfer\Payment', 'payment_id');
    }

    public function group()
    {
        return $this->belongsTo('App\Models\SepaTransfer\PaymentGroup', 'payment_id');
    }

    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice', 'invoice_id');
    }

    public function purchase_invoice()
    {
        return $this->belongsTo('App\Models\PurchaseInvoice', 'purchase_invoice_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    public function provider()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
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
    public function setCustomerIdAttribute($value) {
        $this->attributes['customer_id'] = empty($value) ? null : $value;
    }

    public function setProviderIdAttribute($value) {
        $this->attributes['provider_id'] = empty($value) ? null : $value;
    }

    public function setInvoiceIdAttribute($value) {
        $this->attributes['invoice_id'] = empty($value) ? null : $value;
    }

    public function setPurchaseInvoiceIdAttribute($value) {
        $this->attributes['purchase_invoice_id'] = empty($value) ? null : $value;
    }

    public function setBankIbanAttribute($value) {
        $this->attributes['bank_iban'] = strtoupper(trim(str_replace(' ', '', $value)));
    }

    public function setErrorCodeAttribute($value) {
        $this->attributes['error_code'] = empty($value) || $value == '0000' ? null : $value;
    }

    public function setErrorMsgAttribute($value) {
        $this->attributes['error_msg'] = empty($value) ? null : $value;
    }
}
