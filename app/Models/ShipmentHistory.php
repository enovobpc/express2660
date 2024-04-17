<?php

namespace App\Models;

use App\Models\Sms\Sms;
use Mail, Setting;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\FileTrait;

class ShipmentHistory extends BaseModel
{
    use SoftDeletes,
        FileTrait;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_shipments_history';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'shipments_history';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shipment_id', 'status_id', 'agency_id','user_id',
        'city', 'latitude', 'longitude','receiver', 'provider_agency_code',
        'signature', 'operator_id', 'incidence_id', 'provider_code',
        'obs', 'resolved', 'api', 'vehicle', 'trailer', 'created_at'
    ];
    
   /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at'];
    
   /**
     * Validator rules
     * 
     * @var array 
     */
    public $rules = [
        'shipment_id'       => 'required',
        'status_id'         => 'required',
    ];
    
    /**
     * Validator custom attributes
     * 
     * @var array 
     */
    protected $customAttributes = [
    ];

    /**
     * Default upload directory
     * 
     * @const string
     */
    const DIRECTORY = 'uploads/shipments_attachments';

    /**
     * Set notification of shipment
     *
     * @return int
     */
    public function setNotification($channel, $data)
    {
        $data['id'] = time(); //ID para a aplicação.

        if(!$data) {
            $data['title']   = 'Novo Serviço de ' . @$this->shipment->sender_name;
            $data['message'] = 'Novo serviço';
        }

        if(!$channel) {
            $channel = BroadcastPusher::getChannel();
        }

        $pusher = new BroadcastPusher();
        return $pusher->trigger($data, $channel);
        return true;
    }

    /**
     * Send email with history update
     *
     * @param bool $forceSendCustomer force email to be sent to customer. Ignore default status
     * @param null $forceSendRecipient
     * @return bool
     */
    public function sendEmail($forceCustomerEmail = false, $forceRecipientEmail = false, $automatic = false) {

        $shipment = $this->shipment;
        $history  = $this;
        $customer = @$shipment->customer;

        if(!$customer) {
            return true;
        }

        //check if customer has active email notifications
        if(in_array(@$customer->shipping_status_notify_method, ['sms', 'both'])) {
            if($customer->shipping_status_notify_method == 'sms') {//only sms notifications
                return $this->sendSMS($forceCustomerEmail, $forceRecipientEmail, $automatic);
            }

            $this->sendSMS($forceCustomerEmail, $forceRecipientEmail, $automatic);
        }

        //check if history already has sended email
        $emailAlreadySended = ShipmentHistoryNotification::where('shipment_history_id', $history->id)
            ->where('type', 'email')
            ->first();

        if(empty($emailAlreadySended)) {

            $customerEmail   = @$customer->contact_email;
            $recipientEmail  = @$shipment->recipient_email;
            $customerLocale  = @$shipment->customer->locale;
            $recipientLocale = @$shipment->recipient_country;

            if(!$automatic && $recipientEmail && !$forceRecipientEmail) {
                //só envia email ao destinatário se tiver sido ativa manualmente a "checbox" para enviar e-mail.
                $recipientEmail = null;
            }

            $senderStatus    = empty(Setting::get('shipment_sender_notify_status')) ? [] : Setting::get('shipment_sender_notify_status');
            $recipientStatus = empty(Setting::get('shipment_recipient_notify_status')) ? [] : Setting::get('shipment_recipient_notify_status');

            if (@$customer->shipping_status_notify) {
                $senderStatus = @$customer->shipping_status_notify;

                if (in_array('-1', $customer->shipping_status_notify)) {
                    $senderStatus = [];
                }
            }

            if ($forceCustomerEmail) {
                $senderStatus = [$this->status_id];
            }

            if ($forceRecipientEmail) {
                $recipientStatus = [$this->status_id];
            }

            if ((!empty($recipientEmail) && in_array($shipment->status_id, $recipientStatus)) || (!empty($customerEmail) && in_array($shipment->status_id, $senderStatus))) {


                $status = ShippingStatus::remember(config('cache.query_ttl'))
                    ->cacheTags(ShippingStatus::CACHE_TAG)
                    ->filterSources()
                    ->pluck('name', 'id')
                    ->toArray();


                $incidences = IncidenceType::remember(config('cache.query_ttl'))
                    ->cacheTags(IncidenceType::CACHE_TAG)
                    ->filterSource()
                    ->isActive()
                    ->ordered()
                    ->get()
                    ->pluck('name', 'id')
                    ->toArray();

                $this->status_name = @$status[$this->status_id];
                $this->incidence_name = @$incidences[$this->incidence_id];
                $statusName = @$status[$this->status_id];
                $incidenceName = @$incidences[$this->incidence_id];
                $attachments = [];

                $subject = 'Envio ' . $shipment->tracking_code . ' - ' . $this->status_name;

                if ($shipment->is_collection) {
                    $subject = 'Recolha ' . $shipment->tracking_code . ' - ' . $this->status_name;
                }

                if(Setting::get('app_mode') == 'cargo') {
                    $subject = 'Carga ' . $shipment->tracking_code . ' - ' . $this->status_name;


                    if($history->status_id == ShippingStatus::SHIPMENT_PICKUPED || $history->status_id == ShippingStatus::DELIVERED_ID) {
                        
                        $attachments[] = [
                            'mime'      => null,
                            'filename'  => 'Prova Digital e-CMR ' . $shipment->tracking_code . '.pdf',
                            'content'   => Shipment::printECMR([$shipment->id], null, 'string')
                        ];

                        if($history->filepath) {
                            $attachments[] = [
                                'mime'      => null,
                                'filename'  => 'Prova Fotográfica ' . $shipment->tracking_code . '.png',
                                'content'   => file_get_contents($history->filepath)
                            ];
                        }
                    }
                }
                
                /**
                 * Send e-mail to recipient
                 */
                if (in_array($this->status_id, $recipientStatus)) {

                    $emails = validateNotificationEmails($recipientEmail);
                    $emails = $emails['valid'];

                    if (!empty($emails)) {

                        Mail::send(transEmail('emails.shipments.tracking_recipient', $recipientLocale), compact('shipment', 'history', 'statusName', 'incidenceName'), function ($message) use ($emails, $subject, $attachments) {
                            $message->to($emails);
                            $message->subject($subject);
                            
                            foreach ($attachments as $attachment) {
                                $message->attachData(
                                    $attachment['content'],
                                    $attachment['filename'],
                                    $attachment['mime'] ? ['mime' => $attachment['mime']] : []
                                );
                            }
                        });

                        if (count(Mail::failures()) > 0) {
                            if($automatic) {
                                $recipientEmail = $recipientEmail . ' [FAILED]';
                            } else {
                                throw new \Exception('Falhou o envio do e-mail para o destinatário. Tente de novo.');
                            }
                        }

                        $historyNotification = new ShipmentHistoryNotification();
                        $historyNotification->shipment_history_id = $history->id;
                        $historyNotification->email = $recipientEmail;
                        $historyNotification->target = 'D';
                        $historyNotification->type   = 'email';
                        $historyNotification->auto   = $automatic;
                        $historyNotification->save();
                    }
                }

                /**
                 * Send e-mail to customer
                 */
                if (in_array($this->status_id, $senderStatus)) {

                    $emails = validateNotificationEmails($customerEmail);
                    $emails = $emails['valid'];

                    if (!empty($emails)) {
                        Mail::send(transEmail('emails.shipments.tracking_sender', $customerLocale), compact('shipment', 'history', 'statusName', 'incidenceName'), function ($message) use ($emails, $subject, $attachments) {
                            $message->to($emails);
                            $message->subject($subject);

                            foreach ($attachments as $attachment) {
                                $message->attachData(
                                    $attachment['content'],
                                    $attachment['filename'],
                                    $attachment['mime'] ? ['mime' => $attachment['mime']] : []
                                );
                            }
                        });

                        if (count(Mail::failures()) > 0) {
                            if($automatic) {
                                $customerEmail = $customerEmail . ' [FAILED]';
                            } else {
                                throw new \Exception('Falhou o envio do e-mail para o cliente. Tente de novo.');
                            }
                        }

                        $historyNotification = new ShipmentHistoryNotification();
                        $historyNotification->shipment_history_id = $history->id;
                        $historyNotification->email  = $customerEmail;
                        $historyNotification->target = 'C';
                        $historyNotification->type   = 'email';
                        $historyNotification->auto   = $automatic;
                        $historyNotification->save();

                    }
                }
            }
        }

        return true;
    }


    /**
     * Send sms with history update
     *
     * @param bool $forceSendCustomer force email to be sent to customer. Ignore default status
     * @param bool $forceSendRecipient
     * @param bool $automatic só envia sms ao destinatário se tiver automatic estiver a false
     * @return bool
     */
    public function sendSMS($forceCustomerSMS = false, $forceRecipientSms = false, $automatic = false) {

        try {
            $shipment = @$this->shipment;
            $history = $this;
            $customer = @$shipment->customer;
            $customerMobile = @$shipment->customer->mobile;
            $recipientMobile = @$shipment->recipient_phone;
            $notifyServices = $customer->shipping_services_notify;

            //check if customer has active email notifications
            if ($customer->shipping_status_notify_method == 'email') {//only email notifications
                return $this->sendEmail($forceCustomerSMS, $forceRecipientSms, $automatic);
            }

            //check if history already has sended email
            $smsAlreadySended = ShipmentHistoryNotification::where('shipment_history_id', $history->id)
                ->where('type', 'sms')
                ->first();


            if ((empty($smsAlreadySended) && empty($notifyServices)) || (empty($smsAlreadySended) && !empty($notifyServices) && in_array($shipment->service_id, $notifyServices))) {

                if (!$automatic && $recipientMobile && !$forceRecipientSms && config('app.source') != 'corridadotempo') {
                    $recipientMobile = null; //só envia sms ao destinatário se tiver sido ativa manualmente a "checkbox" para enviar sms.
                }

                $senderStatus = empty(Setting::get('shipment_sender_notify_status')) ? [] : Setting::get('shipment_sender_notify_status');
                if (@$customer->shipping_status_notify) {
                    $senderStatus = @$customer->shipping_status_notify;

                    if (in_array('-1', $customer->shipping_status_notify)) {
                        $senderStatus = [];
                    }
                }

                $recipientStatus = empty(Setting::get('shipment_recipient_notify_status')) ? [] : Setting::get('shipment_recipient_notify_status');
                if (@$customer->shipping_status_notify_recipient) {
                    $recipientStatus = @$customer->shipping_status_notify_recipient;
                    $recipientMobile = @$shipment->recipient_phone; //se na ficha de cliente está definido para notificar automático o destinatario, volta a repor o valor da variavel

                    if (in_array('-1', $shipment->customer->shipping_status_notify_recipient)) {
                        $recipientStatus = [];
                    }
                }

                if ($forceCustomerSMS) {
                    $senderStatus = [$this->status_id];
                }

                if ($forceRecipientSms) {
                    $recipientStatus = [$this->status_id];
                }

                if ((!empty($recipientMobile) && in_array($this->status_id, $recipientStatus))
                    || (!empty($customerMobile) && in_array($this->status_id, $senderStatus))) {


                    $status = ShippingStatus::remember(config('cache.query_ttl'))
                        ->cacheTags(ShippingStatus::CACHE_TAG)
                        ->filterSources()
                        ->pluck('name', 'id')
                        ->toArray();

                    $incidences = IncidenceType::remember(config('cache.query_ttl'))
                        ->cacheTags(IncidenceType::CACHE_TAG)
                        ->filterSource()
                        ->isActive()
                        ->ordered()
                        ->get()
                        ->pluck('name', 'id')
                        ->toArray();

                    $this->status_name = @$status[$this->status_id];
                    $this->incidence_name = @$incidences[$this->incidence_id];
                    //$incidenceName        = @$incidences[$this->incidence_id];


                    //SEND SMS TO RECIPIENT
                    if (in_array($this->status_id, $recipientStatus)) {

                        $msgDefault = 'Envio #' . $shipment->tracking_code . ' - ' . $this->status_name;

                        if ($history->status_id == \App\Models\ShippingStatus::DELIVERED_ID) {
                            $message = $customer->getSmsText('delivered_recipient', $msgDefault);
                        } elseif ($history->status_id == \App\Models\ShippingStatus::INCIDENCE_ID) {
                            $message = $customer->getSmsText('incidence_recipient', $msgDefault);
                        } else {
                            $message = $customer->getSmsText('tracking_recipient', $msgDefault);
                        }

                        $messageRecipient = $this->replaceMsgVars($message, $shipment, $this);

                        $mobiles = validateNotificationMobiles($recipientMobile);
                        $mobiles = $mobiles['valid'];

                        if (!empty($mobiles) && !empty($messageRecipient)) {

                            try {
                                $sms = new Sms();
                                $sms->to = implode(';', $mobiles);
                                $sms->message = $messageRecipient;
                                $sms->source_id = $history->id;
                                $sms->source_type = 'ShipmentHistory';
                                $sms->send();
                            } catch (\Exception $e) {
                                throw new \Exception('Falhou o envio da SMS para o destinatário. Tente de novo.');
                            }

                            $historyNotification = new ShipmentHistoryNotification();
                            $historyNotification->shipment_history_id = $history->id;
                            $historyNotification->mobile = $recipientMobile;
                            $historyNotification->target = 'D';
                            $historyNotification->type = 'sms';
                            $historyNotification->auto = $automatic;
                            $historyNotification->save();
                        }
                    }

                    /**
                     * Send SMS to customer
                     */
                    if (in_array($this->status_id, $senderStatus)) {

                        $msgDefault = 'Envio #' . $shipment->tracking_code . ' - ' . $this->status_name;

                        if ($history->status_id == \App\Models\ShippingStatus::DELIVERED_ID) {
                            $message = $customer->getSmsText('delivered_sender', $msgDefault);
                        } elseif ($history->status_id == \App\Models\ShippingStatus::INCIDENCE_ID) {
                            $message = $customer->getSmsText('incidence_sender', $msgDefault);
                        } else {
                            $message = $customer->getSmsText('tracking_sender', $msgDefault);
                        }

                        $messageSender = $this->replaceMsgVars($message, $shipment, $this);


                        $mobiles = validateNotificationMobiles($customerMobile);
                        $mobiles = $mobiles['valid'];

                        if (!empty($mobiles)) {

                            try {
                                $sms = new Sms();
                                $sms->to = implode(';', $mobiles);
                                $sms->message = $messageSender;
                                $sms->source_id = $history->id;
                                $sms->source_type = 'ShipmentHistory';
                                $sms->send();
                            } catch (\Exception $e) {
                                throw new \Exception('Falhou o envio da SMS para o cliente. Tente de novo.');
                            }

                            $historyNotification = new ShipmentHistoryNotification();
                            $historyNotification->shipment_history_id = $history->id;
                            $historyNotification->mobile = $customerMobile;
                            $historyNotification->target = 'C';
                            $historyNotification->type = 'sms';
                            $historyNotification->auto = $automatic;
                            $historyNotification->save();
                        }
                    }
                }
            }
        } catch (\Exception $e) {}

        return true;
    }

    /**
     * Replace message variables
     * @param $message
     * @param $shipment
     * @param $incidenceName
     * @return mixed
     */
    public function replaceMsgVars($message, $shipment, $history = null) {

        $url = request()->getHttpHost() . '/trk/'.$shipment->tracking_code;

        if(str_contains($message, ':price')) {
            if($shipment->charge_price > 0.00) {
                $message = str_replace(':price', ' Cobranca ' . $shipment->charge_price . 'EUR', $message);
            } else {
                $message = str_replace(':price', '', $message);
            }
        }

        if(str_contains($message, ':ptrk')) {
            $ptrk = $shipment->tracking_code;
            if($shipment->provider_tracking_code) {
                $ptrk = explode(',', $shipment->provider_tracking_code);
                $ptrk = @$ptrk[0];
            }
            $message = str_replace(':ptrk', $ptrk, $message);
        }

        if($history) {
            $message = str_replace(':status', $history->status_name, $message);
            $message = str_replace(':incidence', $history->incidence_name, $message);
            $message = str_replace(':receiver', $history->receiver, $message);
        }

        if($shipment->delivery_date) {
            $message = str_replace(':ddate', $shipment->delivery_date->format('d/m'), $message);
            $message = str_replace(':dhour', $shipment->delivery_date->format('H').'h', $message);
        }

        if ($shipment->start_hour) {
            $message = str_replace(':dshour', $shipment->start_hour, $message);
        }

        if ($shipment->end_hour) {
            $message = str_replace(':dehour', $shipment->end_hour, $message);
        }

        $message = str_replace(':trk', $shipment->tracking_code, $message);
        $message = str_replace(':sender', substr(removeAccents($shipment->sender_name), 0, 20), $message);
        $message = str_replace(':recipient', substr(removeAccents($shipment->recipient_name), 0, 20), $message);
        $message = str_replace(':date', date('d/m H:i'), $message);
        $message = str_replace(':url', $url, $message);

        return $message;
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
    
    public function status()
    {
        return $this->belongsTo('App\Models\ShippingStatus');
    }
    
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function deleted_user()
    {
        return $this->belongsTo('App\Models\User', 'deleted_by');
    }
    
    public function operator()
    {
        return $this->belongsTo('App\Models\User', 'operator_id');
    }
    
    public function incidence()
    {
        return $this->belongsTo('App\Models\IncidenceType', 'incidence_id');
    }

    public function agency()
    {
        return $this->belongsTo('App\Models\Agency', 'agency_id');
    }

    public function provider_agency()
    {
        return $this->belongsTo('App\Models\Core\ProviderAgency', 'provider_agency_code', 'code');
    }

    public function resolutions()
    {
        return $this->hasMany('App\Models\ShipmentIncidenceResolution', 'shipment_history_id', 'id');
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
    public function setIncidenceIdAttribute($value)
    {
        $this->attributes['incidence_id'] = empty($value) ? null : $value;
    }

    public function setReceiverAttribute($value)
    {
        $this->attributes['receiver'] = empty($value) ? null : $value;
    }

    public function setOperatorIdAttribute($value)
    {
        $this->attributes['operator_id'] = empty($value) ? null : $value;
    }

    public function setAgencyIdAttribute($value)
    {
        $this->attributes['agency_id'] = empty($value) ? null : $value;
    }

    public function setCityAttribute($value)
    {
        $this->attributes['city'] = empty($value) ? null : $value;
    }

    public function setResolvedAttribute($value)
    {
        $this->attributes['resolved'] = empty($value) ? null : $value;
    }
    public function setFilepathAttribute($value)
    {
        $this->attributes['filepath'] = empty($value) ? null : $value;
    }

    public function setFilenameAttribute($value)
    {
        $this->attributes['filename'] = empty($value) ? null : $value;
    }
}
