<?php

namespace App\Models\GatewayPayment;

use App\Models\Core\CreditCard;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth, Mail, Setting;
use Illuminate\Support\Facades\Log;

class Base extends \App\Models\BaseModel
{
    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_routes';

    /**
     * Status variables
     */
    const STATUS_SUCCESS  = 'success';
    const STATUS_PENDING  = 'pending';
    const STATUS_REJECTED = 'rejected';
    const STATUS_WAINTING = 'wainting';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'gateway_payments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'gateway', 'target', 'target_id', 'code', 'method', 'value', 'currency', 'reference', 'description',
        'customer_name', 'customer_address', 'customer_country', 'customer_phone', 'customer_email', 'customer_vat',
        'expires_at', 'paid_at', 'status', 'mb_entity', 'mb_reference', 'mbway_phone', 'cc_first_name', 'cc_last_name',
        'cc_number', 'cc_cvc', 'cc_month', 'cc_year', 'customer_id', 'sense'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'gateway' => 'required',
        'method'  => 'required',
        'value'   => 'required',
    );

    /**
     * Base constructor.
     * @param null $gateway
     */
    public function __construct(array $attributes = [])
    {
        try {
            $gateway = env('PAYMENT_GATEWAY') ?: 'Eupago';
            $this->gateway = ucwords(camel_case($gateway));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage() . ' file ' . $e->getFile() . ' line ' . $e->getLine());
        }

        parent::__construct($attributes);
    }

    /**
     * Create tracking code
     *
     * @return int
     */
    public function setCode()
    {
        $this->save();

        $code = date('ym');
        $code .= str_pad($this->id, 5, "0", STR_PAD_LEFT);

        $this->code = $code;
        $this->save();
    }

    /**
     * Create a new payment from gateway
     *
     * @param $method [mb|cc|mbw|dd|bb]
     * @param $data
     * @return mixed
     */
    public function createPayment($method, $data)
    {

        try {
            $gateway = new $this->gateway;
            $status = Base::STATUS_PENDING;

            // Create Payment Info
            $paymentGateway = new Base();
            $paymentGateway->source      = config('app.source');
            $paymentGateway->customer_id = @$data['customer_id'];
            $paymentGateway->target      = @$data['target'];
            $paymentGateway->target_id   = @$data['target_id'];
            $paymentGateway->description = @$data['description'];
            $paymentGateway->sense       = @$data['sense'];
            $paymentGateway->setCode();

            // VISA
            if ($method == 'cc' || $method == 'visa') {

                $data['return_url'] = @$data['return_url'] ? $data['return_url'] : route('payment.callback', strtolower($this->gateway));

                $gatewayData = [
                    'reference'   => 'PAG' . $paymentGateway->code,
                    'value'       => $data['value'],
                    'card_name'   => @$data['first_name'] . ' ' . @$data['last_name'],
                    'card_number' => trim(str_replace(' ', '', $data['card'])),
                    'card_cvc'    => trim($data['cvc']),
                    'card_month'  => $data['month'],
                    'card_year'   => $data['year'],
                    'return_url'  => @$data['return_url'],
                ];

                $response = $gateway->visaCreate($gatewayData);

                $status = Base::STATUS_REJECTED;
                if ($response['result']) {
                    $status = Base::STATUS_SUCCESS;
                    $this->memorizeCreditCard($gatewayData);
                }
            }

            // MB WAY
            else if ($method == 'mbway') {  
                $gatewayData = [
                    'reference'   => 'PAG' . $paymentGateway->code,
                    'value'       => $data['value'],
                    'phone'       => $data['phone'],
                    'description' => $data['description'],
                ];

                $response = $gateway->mbwayCreate($gatewayData);

                $status = Base::STATUS_WAINTING;
            }

            // MULTIBANCO
            else {
                $gatewayData = [
                    'reference' => 'PAG' . $paymentGateway->code,
                    'value'     => $data['value'],
                    'limit'     => 3
                ];

                $response = $gateway->mbCreate($gatewayData);
            }

            $paymentGateway->status  = $status;
            $paymentGateway->fill($response);
            $paymentGateway->save();  

        } catch (\Exception $e) {
            return [
                'result'   => false,
                'feedback' => $e->getMessage(),
                'payment'  => null,
                'conclude_url' => null
            ];
        }

        if ($response['result']) {
            return [
                'result'   => true,
                'feedback' => 'Dados de pagamento gerados com sucesso.',
                'payment'  => $paymentGateway->toArray(),
                'conclude_url' => @$response['conclude_url']
            ];
        } else {
            return [
                'result'   => false,
                'feedback' => 'Ocorreu um erro ao gerar o pagamento.',
                'payment'  => $paymentGateway->toArray(),
                'conclude_url' => @$response['conclude_url']
            ];
        }

    }

    /**
     * Store payment of shipment
     *
     * @return array
     */
    public static function storeShipmentPayment($shipment, $customer, $customPrice = null, $force = false)
    {
        $vatRate = Setting::get('vat_rate_normal');

        if (empty($customPrice)) {

            $price   = $shipment->total_price + $shipment->total_expenses;

            if ($customer->is_particular && !@$shipment->service->is_mail) { //para cliente particular adiciona o IVA
                $vat     = $vatRate / 100;
                $total   = $price * (1 + $vat);
            } else {
                if (@$shipment->service->is_mail) { //se é serviço de correio, força a ser isento de iva
                    $vatRate = 0;
                    $total   = $price;
                } else { //caso contrário, usa o valor normal de iva calculado pelo sistema
                    $vatRate = null;
                    $total   = $shipment->billing_total;
                }
            }

            $vatTotal = $total - $price;
        } else {
            $price = $customPrice;
            $total = valueWithVat($customPrice);
            $vatTotal = $total - $price;
        }

        if (hasModule('account_wallet') && !$customer->is_mensal && (($customer->wallet_balance >= $total && !$force) || $force)) {
            //create payment info
            Self::logShipmentPayment($shipment, $total);

            $wallet = $customer->subWallet($total);

            return [
                'result'    => true,
                'base'      => $price,
                'total'     => $total,
                'vat'       => $vatTotal,
                'wallet'    => $wallet,
                'vat_rate'  => $vatRate
            ];
        }
        return [
            'result'    => false,
            'base'      => $price,
            'total'     => $total,
            'vat'       => $vatTotal,
            'wallet'    => $customer->wallet_account,
            'vat_rate'  => $vatRate
        ];
    }

    /**
     * Update customer wallet if shipment is pre-paid and suffered a price change
     * 
     * @param \App\Models\Shipment $shipment
     * @param \App\Models\Customer $customer
     * @param float $customPrice
     * @param bool $force
     * @param bool $store
     * @return array
     */
    public static function updateWalletShipmentPayment($shipment, $customer, $customPrice = null, $force = false, $store = false) {
        $payments = Self::filterSource()
            ->where('method', 'wallet')
            ->where('target', 'Shipment')
            ->where('target_id', $shipment->id)
            ->get();

        if ($payments->isEmpty()) {
            if ($store) {
                return Self::storeShipmentPayment($shipment, $customer, $customPrice, $force);
            }

            return [
                'result' => false,
            ];
        }

        $vatRate = Setting::get('vat_rate_normal');
        if (empty($customPrice)) {
            $price = $shipment->total_price + $shipment->total_expenses;

            if ($customer->is_particular && !@$shipment->service->is_mail) { //para cliente particular adiciona o IVA
                $vat     = $vatRate / 100;
                $total   = $price * (1 + $vat);
            } else {
                if (@$shipment->service->is_mail) { //se é serviço de correio, força a ser isento de iva
                    $vatRate = 0;
                    $total   = $price;
                } else { //caso contrário, usa o valor normal de iva calculado pelo sistema
                    $vatRate = null;
                    $total   = $shipment->billing_total;
                }
            }

            $vatTotal = $total - $price;
        } else {
            $price = $customPrice;
            $total = valueWithVat($customPrice);
            $vatTotal = $total - $price;
        }

        $paymentValue = $payments->sum('value');

        if ($total <= $paymentValue) {
            return [
                'result'    => false,
                'base'      => $price,
                'total'     => $total,
                'vat'       => $vatTotal,
                'wallet'    => $customer->wallet_balance,
                'vat_rate'  => $vatRate
            ];
        }

        $diffTotal = round($total - $paymentValue, 2);
        Self::logShipmentPayment($shipment, $diffTotal, false, true);
        $wallet = $customer->subWallet($diffTotal);

        $shipment->ignore_billing = 1;
        $shipment->vat_rate       = $vatRate;
        $shipment->save();

        return [
            'result'    => true,
            'base'      => $price,
            'total'     => $total,
            'vat'       => $vatTotal,
            'wallet'    => $wallet,
            'vat_rate'  => $vatRate
        ];
    }

    /**
     * Refund payent of shipment
     *
     * @return array
     */
    public static function refundShipmentPayment($shipment, $customer, $customPrice = null)
    {
        if (empty($customPrice)) {
            $price = $shipment->total_price + $shipment->total_expenses;
            $total = $shipment->billing_total;

            $vatTotal = $total - $price;
        } else {
            $price = $customPrice;
            $total = valueWithVat($customPrice);
            $vatTotal = $total - $price;
        }


        if (hasModule('account_wallet') && !$customer->is_mensal) {

            //create payment info
            Self::logShipmentPayment($shipment, $total, true);

            $wallet = $customer->addWallet($total);

            return [
                'result' => true,
                'base'   => $price,
                'total'  => $total,
                'vat'    => $vatTotal,
                'wallet' => $wallet
            ];
        }

        return [
            'result' => false,
            'base'   => $price,
            'total'  => $total,
            'vat'    => $vatTotal,
            'wallet' => $customer->wallet_account
        ];
    }

    /**
     * Store log of shipment payment
     * @param $shipment
     * @param $total
     * @return array
     * @throws \Exception
     */
    public static function logShipmentPayment($shipment, $total = null, $refund = false, $adjust = false)
    {
        $sense = 'debit';
        $description = 'Pagamento TRK#' . $shipment->tracking_code;

        if ($refund) {
            $sense = 'credit';
            $description = 'Devolução valor TRK#' . $shipment->tracking_code;
        } else if ($adjust) {
            $sense = 'debit';
            $description = 'Ajuste TRK#' . $shipment->tracking_code;
        }

        $paymentGateway = new Base();
        $paymentGateway->source      = config('app.source');
        $paymentGateway->customer_id = $shipment->customer_id;
        $paymentGateway->target      = 'Shipment';
        $paymentGateway->target_id   = $shipment->id;
        $paymentGateway->reference   = 'TRK#' . $shipment->tracking_code;
        $paymentGateway->description = $description;
        $paymentGateway->sense       = $sense;
        $paymentGateway->method      = 'wallet';
        $paymentGateway->status      = Base::STATUS_SUCCESS;
        $paymentGateway->value       = $total;
        $paymentGateway->setCode();
    }

    /**
     * Get payment details
     * @param $paymentKey
     * @return mixed
     */
    public function getFromGateway($paymentKey)
    {

        try {
            $class = new $this->gateway();
            $result = $class->getSinglePayment($paymentKey);
            return $result;
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Delete payment
     * @param $paymentKey
     * @return mixed
     */
    public function deleteFromGateway($paymentKey)
    {

        try {
            $class = new $this->gateway();
            $result = $class->deleteSinglePayment($paymentKey);
            return $result;
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Memorize credit card on core database
     */
    public function memorizeCreditCard($gatewayData)
    {
        $creditCard = CreditCard::where('number', $gatewayData['card_number'])
            ->where('ccv', $gatewayData['card_cvc'])
            ->where('validity_year', $gatewayData['card_year'])
            ->where('validity_month', $gatewayData['card_month'])
            ->first();

        if (empty($creditCard)) {
            $creditCard = new CreditCard();
            $creditCard->source         = config('app.source');
            $creditCard->number         = $gatewayData['card_number'];
            $creditCard->ccv            = $gatewayData['card_cvc'];
            $creditCard->validity_year  = $gatewayData['card_year'];
            $creditCard->validity_month = $gatewayData['card_month'];
            $creditCard->name           = $gatewayData['card_name'];
            $creditCard->country        = @$gatewayData['card_country'] ? $gatewayData['card_country'] : 'pt';
            $creditCard->save();
        }
    }

    /**
     * Send payment confirmation e-mail
     */
    public function sendEmail()
    {
        try {
            $payment  = $this;
            $customer = $this->customer;

            $subject = $payment->status == Base::STATUS_SUCCESS ? 'Pagamento recebido' : 'Falha no pagamento';

            if ($customer && $customer->email) {
                Mail::send('emails.payments.confirm', compact('payment'), function ($message) use ($subject, $customer) {
                    $message->to($customer->email)
                        //->from(Setting::get('tickets_mail'), config('mail.from.name'))
                        ->subject($subject);
                });
            }
        } catch (\Exception $e) {
            Log::error($e);
        }
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
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    public function shipment()
    {
        return $this->belongsTo('App\Models\User', 'operator_id');
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
    public function setProviderIdAttribute($value)
    {
        $this->attributes['provider_id'] = empty($value) ? null : $value;
    }

    public function setOperatorIdAttribute($value)
    {
        $this->attributes['operator_id'] = empty($value) ? null : $value;
    }

    public function setTypeAttribute($value)
    {
        $this->attributes['type'] = empty($value) ? null : $value;
    }

    public function setPaidAtAttribute($value)
    {
        $this->attributes['paid_at'] = empty($value) ? null : $value;
    }

    public function setExpiresAtAttribute($value)
    {
        $this->attributes['expires_at'] = empty($value) ? null : $value;
    }

    public function setZipCodesAttribute($value)
    {
        $this->attributes['zip_codes'] = empty($value) ? null : json_encode($value);
    }

    public function getAgenciesAttribute()
    {
        return json_decode(@$this->attributes['agencies']);
    }

    public function getZipCodesAttribute($value)
    {
        return empty($value) ? [] : json_decode($value);
    }

    public function getZipCodesArrAttribute()
    {
        return explode(',', @$this->attributes['zip_codes']);
    }
}
