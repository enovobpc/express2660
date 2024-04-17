<?php

namespace App\Models;

use App\Models\PaymentGateway\Base;
use Faker\Provider\Payment;
use Jenssegers\Date\Date;
use Mail, Setting;

class InvoiceSchedule extends BaseModel
{
    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_invoices_scheduled';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'invoices_scheduled';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'invoice_id', 'frequency', 'repeat_every', 'repeat', 'month_days', 'year_days',
        'weekdays', 'end_repetitions', 'start_date', 'end_date', 'count_repetitions', 'finished', 'is_draft',
        'send_email', 'mb_active', 'mbw_active',  'paypal_active'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['end_date'];

    /**
     * Validator rules
     *
     * @var array
     */
    public $rules = [
        'invoice_id'   => 'required',
        'frequency'    => 'required',
        'repeat_every' => 'required'
    ];


    /**
     * Run schedule invoices
     */
    public static function runSchedule($date = null) {

        $today = Date::today();

        if(!empty($date)) {
            $today = Date::parse($date)->startOfDay();
        }

        $schedules = InvoiceSchedule::filterSource()
            ->with('invoice')
            ->whereHas('invoice', function($q){
                $q->whereNull('deleted_at'); //mostra só os agendamentos sem fatura associada apagada
            })
            ->where(function ($q) use($today) {
                $q->whereNull('start_date');
                $q->orWhere('start_date', '<=', $today->format('Y-m-d'));
            })
            ->where('finished', 0)
            ->get();

        foreach ($schedules as $schedule) {

            if(empty($schedule->invoice)) {
                $schedule->delete();
            } else {
                $totalRepetitions = $schedule->count_repetitions;
                $invoice = $schedule->invoice;

                $data = [
                    'frequency'     => $schedule->frequency,
                    'repeat_every'  => $schedule->repeat_every,
                    'repeat'        => $schedule->repeat,
                    'month_days'    => $schedule->month_days,
                    'weekdays'      => $schedule->weekdays,
                    'year_days'     => $schedule->year_days,
                    'last_schedule' => $schedule->last_schedule
                ];

                $endSchedule = false;
                if(!empty($schedule->end_repetitions) && $totalRepetitions >= $schedule->end_repetitions) {
                    $endSchedule = true;
                } else if(!empty($schedule->end_date) && $schedule->end_date->lt($today)) {
                    $endSchedule = true;
                }

                if($endSchedule) {
                    $schedule->finished = 1;
                    $schedule->save();
                } else {

                    //REPETE TODOS OS DIAS
                    if ($schedule->frequency == 'day') {

                        if(empty($data['last_schedule'])) {
                            $scheduleDate = Date::today();
                        } else {
                            $scheduleDate = Date::parse($data['last_schedule'])->addDays($data['repeat_every']);
                        }

                        if ($scheduleDate->diffInDays($today) == 0) {
                            self::storeSchedule($invoice, $schedule, $today);
                        }


                    }

                    //REPETE TODAS AS SEMANAS
                    elseif ($schedule->frequency == 'week') {

                        $curWeekday = $today->dayOfWeek;

                        if (in_array($curWeekday, $data['weekdays'])) {
                            if($data['repeat_every'] == 1) {
                                $scheduleDate = $today;
                            } else {
                                //get first date of selected week
                                $week = Date::parse($data['last_schedule'])->addWeek($data['repeat_every']);
                                $week = $week->setISODate(date('Y'), $week->weekOfYear);
                                $scheduleDate = $week->startOfWeek()->addDays($curWeekday - 1);
                            }

                            if ($scheduleDate->diffInDays($today) == 0) {
                                self::storeSchedule($invoice, $schedule, $today);
                            }
                        }

                    }

                    //REPETE TODOS OS MESES
                    elseif ($schedule->frequency == 'month') {

                        if ($data['month_days'] && $data['repeat'] == 'day') {
                            $curDay = $today->day;

                            if(empty($data['last_schedule'])) {
                                $month = Date::now();
                            } else {
                                $month = Date::parse($data['last_schedule'])->addMonth($data['repeat_every']);
                            }

                            $scheduleDate = $month->startOfMonth();

                            if (in_array($curDay, $data['month_days'])) {
                                $scheduleDate = Date::parse($scheduleDate->year . '-' . $scheduleDate->month . '-' . $curDay);

                                if ($scheduleDate->diffInDays($today) == 0) {
                                    self::storeSchedule($invoice, $schedule, $today);
                                }
                            }

                        } else {

                            $curWeekday = $today->dayOfWeek;

                            if (in_array($curWeekday, $data['weekdays'])) {

                                $lastSchedule = Date::parse($data['last_schedule']);

                                if($lastSchedule->month == date('m')) {
                                    $lastSchedule = $lastSchedule->startOfMonth();
                                } else {
                                    $month = $lastSchedule->addMonth($data['repeat_every']);
                                    $scheduleDate = $month->startOfMonth();
                                }

                                $dtStr = $data['repeat'] . ' ' . jddayofweek($curWeekday - 1, 1) . ' of ' . $scheduleDate->format('F') . ' ' . $scheduleDate->year;
                                $scheduleDate = Date::parse($dtStr);

                                if ($scheduleDate->diffInDays($today) == 0) {

                                    self::storeSchedule($invoice, $schedule, $today);
                                }
                            }
                        }
                    }

                    //REPETE TODOS OS ANOS
                    elseif ($schedule->frequency == 'year') {

                        //$scheduleDate = Date::today();
                        $scheduleDate = $today;

                        $day   = $scheduleDate->day;
                        $month = $scheduleDate->month;

                        $key = $month.'-'.$day;

                        if(in_array($key, $schedule->year_days)) {
                            self::storeSchedule($invoice, $schedule, $today);
                        }
                    }
                }
            }
        }
    }

    public static function storeSchedule($invoice, $schedule, $date) {

        /*try {*/
        $newInvoice = $invoice->replicate();
        $customer   = $newInvoice->customer;
        $newInvoicesLines = $invoice->lines;

        $today = Date::today();

        if($newInvoice->payment_condition == 'sft') {
            return false;
        }

        if(in_array($newInvoice->payment_condition, ['prt', 'wallet', 'dbt'])) {
            $days = PaymentCondition::getDays($newInvoice->payment_condition); //obtem dias de pagamento pela base de dados
        } else {
            $condition = str_replace('d', '', $newInvoice->payment_condition);
            $days = $condition;
        }

        $dueDate = $today->copy()->addDays($days);

        $reference = $invoice->reference;
        $reference = str_replace(':month', trans('datetime.list-month-tiny.' . $today->month), $reference);
        $reference = str_replace(':year', $today->year, $reference);

        $obs = $invoice->obs;
        $obs = str_replace(':month', trans('datetime.list-month.' . $today->month), $obs);
        $obs = str_replace(':year', $today->year, $obs);

        $newInvoice->obs          = $obs;
        $newInvoice->doc_date     = $today->format('Y-m-d');
        $newInvoice->due_date     = $dueDate->format('Y-m-d');
        $newInvoice->reference    = $reference;
        $newInvoice->is_settle    = in_array($newInvoice->doc_type, ['invoice-receipt', 'simplified-invoice']) ? '1' : '0';
        $newInvoice->is_scheduled = 0;
        $newInvoice->save();

        //dd($newInvoice->toArray());
        foreach ($newInvoicesLines as $newInvoicesLine) {

            $desc = $newInvoicesLine->description;
            $desc = str_replace(':month', trans('datetime.list-month.' . $today->month), $desc);
            $desc = str_replace(':year', $today->year, $desc);

            $line = new InvoiceLine();
            $line->fill($newInvoicesLine->toArray());
            $line->description = $desc;
            $line->invoice_id  = $newInvoice->id;
            $line->save();

            if($line->reference == 'SPRT') { //verifica se é para adicionar suporte

                $lastMonth = $today->copy()->subMonth(1); //Mês anterior
                $period = [$lastMonth->firstOfMonth()->format('Y-m-d H:i:s'), $lastMonth->lastOfMonth()->format('Y-m-d H:i:s')];

                $supportMonth = SupportHistory::with('address_book')
                    ->where('customer_id', $invoice->customer_id)
                    ->whereBetween('datetime', $period)
                    ->get();

                $taxable = SupportHistory::getTaxable($supportMonth, $invoice->customer);

                if($taxable['price'] > 0.00) {
                    $line->subtotal    = $taxable['price'];
                    $line->total_price = $taxable['price'];
                    $line->save();

                    $subtotal = $taxable['price'];
                    $total    = $taxable['price'] * (1+($line->tax_rate/100));
                    $vat      = ($total - $subtotal);

                    $newInvoice->doc_subtotal = $newInvoice->doc_subtotal + $subtotal;
                    $newInvoice->doc_vat      = $newInvoice->doc_vat + $vat;
                    $newInvoice->doc_total    = $newInvoice->doc_total + $total;
                    $newInvoice->total        = $newInvoice->doc_total;
                    $newInvoice->save();
                }
            }
        }

        $submitInvoice = in_array($newInvoice->doc_type, ['nodoc', 'internal-doc', 'proforma-invoice']) ? false : true;

        if($submitInvoice) {
            //submit invoice by webservice
            $input = [];
            $input['vat']               = $newInvoice->vat;
            $input['obs']               = $obs;
            $input['docdate']           = $newInvoice->doc_date;
            $input['duedate']           = $newInvoice->due_date;
            $input['docref']            = $newInvoice->reference;
            $input['billing_code']      = $newInvoice->billing_code;
            $input['billing_name']      = $newInvoice->billing_name;
            $input['billing_address']   = $newInvoice->billing_address;
            $input['billing_zip_code']  = $newInvoice->billing_zip_code;
            $input['billing_city']      = $newInvoice->billing_city;
            $input['irs_tax']           = $newInvoice->payment_method;
            $input['payment_method']    = $newInvoice->payment_method;
            $input['payment_condition'] = $newInvoice->payment_condition;

            $header = $newInvoice->prepareDocumentHeader($input, $customer);
            $lines  = $newInvoice->prepareDocumentLines();

            //create or update customer data by webservice
            $newInvoice->insertOrUpdateCustomer($customer);

            $documentId = $newInvoice->createDraft($newInvoice->doc_type, $header, $lines);

            if(!$schedule->is_draft) {
                $newInvoice->convertDraftToDoc($documentId, $newInvoice->doc_type);
            }

        } else {
            $documentId                = $newInvoice->setDocumentNo();
            $newInvoice->doc_id        = @$documentId['doc_id'];
            $newInvoice->doc_series    = @$documentId['doc_serie'];
            $newInvoice->doc_series_id = @$documentId['doc_serie_id'];
            $newInvoice->internal_code = @$documentId['internal_code'];
            $newInvoice->is_draft      = 0;
            $newInvoice->api_key       = null;
            $newInvoice->is_settle     = null;
        }

        //add MULTIBANCO
        if($schedule->mb_active && $newInvoice->doc_type == 'proforma-invoice') {

            $expirationDate = new Date($newInvoice->due_date);
            $expirationDate = $expirationDate->addDays(3)->format('Y-m-d');

            $data = [
                'payment_key'     => 'PRF' . ($newInvoice->internal_code ? $newInvoice->internal_code : str_pad($newInvoice->id, '9','0', STR_PAD_LEFT)),
                'value'           => $newInvoice->doc_total,
                'expiration_time' => $expirationDate,
                'description'     => 'Proforma ' . $newInvoice->name,
                'customer_name'   => @$newInvoice->billing_name,
                'customer_email'  => @$newInvoice->billing_email,
                'customer_phone'  => null, //@$newInvoice->customer->phone,
                'customer_country'=> @$newInvoice->billing_country,
                'customer_vat'    => @$newInvoice->vat
            ];

            if(env('APP_ENV') == 'local') {
                $paymentDetails = [
                    "result"    => true,
                    "feedback"  => "Dados de pagamento gerados com sucesso.",
                    "payment"   => PaymentNotification::orderBy('id', 'desc')->first()
                ];
            } else {
                $paymentDetails = new Base();
                $paymentDetails = $paymentDetails->createPayment('mb', $data);
            }

            if(@$paymentDetails['result'] && @$paymentDetails['payment']) {
                $paymentCollection        = @$paymentDetails['payment'];
                $newInvoice->mb_entity    = @$paymentCollection->entity;
                $newInvoice->mb_reference = @$paymentCollection->reference;
            }
        }

        //add MBWAY
        if($schedule->mbw_active && $newInvoice->doc_type == 'proforma-invoice' && Setting::get('mbw_phone')) {
            $newInvoice->mbw_phone = str_replace(' ', '', Setting::get('mbw_phone'));
        }

        //add PAYPAL
        if($schedule->paypal_active && $newInvoice->doc_type == 'proforma-invoice' && Setting::get('paypal_account')) {
            $newInvoice->paypal_account = Setting::get('paypal_account');
        }

        $newInvoice->save();


        //update schedule task
        $schedule->last_schedule     = Date::now();
        $schedule->count_repetitions = ($schedule->count_repetitions + 1);
        $schedule->save();

        try {
            //Send email
            if($newInvoice->doc_id && !$schedule->is_draft && $schedule->send_email && $customer->billing_email) {

                $emailResult = $newInvoice->sendEmail([
                    'email'       => trim($customer->billing_email),
                    'attachments' => ['invoice'],
                ]);

                if(!$emailResult) {
                    return response()->json([
                        'result'   => true,
                        'feedback' => 'Não foi possível enviar o e-mail ao cliente.'
                    ]);
                }
            }
        } catch (\Exception $e) {
            $result = [
                'result'   => true,
                'feedback' => $e->getMessage() . ' na linha '. $e->getLine() .' ficheiro ' . $e->getFile()
            ];
        }

        /* } catch (\Exception $e) {
             throw new \Exception($e->getMessage() . ' FILE ' . $e->getFile(). ' LINE ' . $e->getLine());
         }*/
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */

    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice', 'invoice_id');
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
    public function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = empty($value) ? null : $value;
    }

    public function setEndRepetitionsAttribute($value)
    {
        $this->attributes['end_repetitions'] = empty($value) ? null : $value;
    }

    public function setWeekdaysAttribute($value)
    {
        if(in_array($this->attributes['frequency'], ['day']) || in_array($this->attributes['repeat'], ['day'])) {
            $this->attributes['weekdays'] = null;
        } else {
            $this->attributes['weekdays'] = empty($value) ? null : json_encode($value);
        }
    }

    public function setMonthDaysAttribute($value)
    {
        if($this->attributes['repeat'] != 'day') {
            $this->attributes['month_days'] = null;
        } else {
            $this->attributes['month_days'] = empty($value) ? null : json_encode($value);
        }
    }

    public function setYearDaysAttribute($value)
    {
        if($this->attributes['frequency'] != 'year') {
            $this->attributes['year_days'] = null;
        } else {
            $this->attributes['year_days'] = empty($value) ? null : json_encode($value);
        }
    }

    public function setRepeatAttribute($value)
    {
        if(in_array(@$this->attributes['frequency'], ['day', 'week', 'year'])) {
            @$this->attributes['repeat'] = null;
        } else {
            @$this->attributes['repeat'] = empty($value) ? null : $value;
        }
    }

    public function getWeekdaysAttribute($value) {
        return  json_decode($value, true);
    }

    public function getMonthDaysAttribute($value) {
        if(!empty($value)) {
            return  array_map('intval', json_decode($value, true));
        }
    }

    public function getYearDaysAttribute($value) {
        return  json_decode($value, true);
    }
}
