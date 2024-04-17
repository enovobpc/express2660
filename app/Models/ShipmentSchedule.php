<?php

namespace App\Models;

use Jenssegers\Date\Date;
use Mail, Setting;
use App\Models\Traits\FileTrait;

class ShipmentSchedule extends BaseModel
{
    use FileTrait;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_shipments_scheduled';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'shipments_scheduled';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'shipment_id', 'frequency', 'repeat_every', 'repeat', 'month_days',
        'weekdays', 'end_repetitions', 'end_date', 'count_repetitions', 'finished', 'email'
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
        'shipment_id'   => 'required',
        'frequency'     => 'required',
        'repeat_every'  => 'required'
    ];

    /**
     * Run schedule shipments
     */
    public static function runSchedule($date = null) {

        $today = Date::today();

        if(!empty($date)) {
            $today = Date::parse($date)->startOfDay();
        }

        $schedules = ShipmentSchedule::filterSource()
                        ->with('shipment')
                        ->where('finished', 0)
                        ->get();

        foreach ($schedules as $schedule) {

            if(empty($schedule->shipment)) {
                $schedule->delete();
            } else {
                $totalRepetitions = $schedule->count_repetitions;
                $shipment = $schedule->shipment;

                $data = [
                    'frequency'     => $schedule->frequency,
                    'repeat_every'  => $schedule->repeat_every,
                    'repeat'        => $schedule->repeat,
                    'month_days'    => $schedule->month_days,
                    'weekdays'      => $schedule->weekdays,
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

                    if ($schedule->frequency == 'day') {

                        if(empty($data['last_schedule'])) {
                            $scheduleDate = Date::today();
                        } else {
                            $scheduleDate = Date::parse($data['last_schedule'])->addDays($data['repeat_every']);
                        }

                        if ($scheduleDate->diffInDays($today) == 0) {
                            self::storeSchedule($shipment, $schedule, $today);
                        }
                    } elseif ($schedule->frequency == 'week') {

                        $curWeekday = $today->dayOfWeek;

                        //o domingo é considerado dia 0 e não 7
                        if($curWeekday == 0){
                            $curWeekday = 7;
                        }

                        if (@$data['weekdays'] && in_array($curWeekday, $data['weekdays'])) {
                            if($data['repeat_every'] == 1) {
                                $scheduleDate = $today;
                            } else {
                                //get first date of selected week
                                $week = Date::parse($data['last_schedule'])->addWeek($data['repeat_every']);
                                $week = $week->setISODate(date('Y'), $week->weekOfYear);
                                $scheduleDate = $week->startOfWeek()->addDays($curWeekday - 1);
                            }

                            if ($scheduleDate->diffInDays($today) == 0) {
                                self::storeSchedule($shipment, $schedule, $today);
                            }
                        }

                    } elseif ($schedule->frequency == 'month') {

                        if ($data['month_days'] && $data['repeat'] == 'day') {
                            $curDay = date('d');

                            $month = Date::parse($data['last_schedule'])->addMonth($data['repeat_every']);
                            $scheduleDate = $month->startOfMonth();

                            if (in_array($curDay, $data['month_days'])) {
                                $scheduleDate = Date::parse($scheduleDate->year . '-' . $scheduleDate->month . '-' . $curDay);

                                if ($scheduleDate->diffInDays($today) == 0) {
                                    self::storeSchedule($shipment, $schedule, $today);
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

                                    self::storeSchedule($shipment, $schedule, $today);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public static function storeSchedule($shipment, $schedule, $date) {

        $defaultStatusId = empty(Setting::get('shipment_status_after_create')) ? ShippingStatus::ACCEPTED_ID : Setting::get('shipment_status_after_create');

        try {
            //store shipment
            $newShipment = $shipment->replicate();
            $newShipment->date          = $date->format('Y-m-d');
            $newShipment->billing_date  = $date->format('Y-m-d');
            $newShipment->delivery_date = null;
            $newShipment->tracking_code = null;
            $newShipment->status_id     = Setting::get('shipment_schedule_default_status') ? Setting::get('shipment_schedule_default_status') : ShippingStatus::PENDING_ID;
            $newShipment->invoice_id    = null;
            $newShipment->invoice_doc_id= null;
            $newShipment->invoice_type  = null;
            $newShipment->invoice_key   = null;
            $newShipment->invoice_draft = 0;
            $newShipment->is_closed     = 0;
            $newShipment->is_blocked    = 0;
            $newShipment->resetWebserviceError();
            $newShipment->setTrackingCode();

            //store expenses
            $expenses = $shipment->expenses;
            if($expenses) {
                foreach ($expenses as $expense) {
                    $newShipment->expenses()->attach($expense->pivot->expense_id, [
                        'qty'        => $expense->pivot->qty,
                        'cost_price' => $expense->pivot->cost_price,
                        'price'      => $expense->pivot->price,
                        'subtotal'   => $expense->pivot->subtotal,
                        'date'       => $date,
                    ]);
                }
            }

            //Store dimensions
            $dimensions = $shipment->dimensions;
            if($dimensions) {
                foreach ($dimensions as $dimension) {
                    $newDimension = $dimension->replicate();
                    $newDimension->shipment_id = $shipment->id;
                    $newDimension->save();
                }
            }

            //SUBMIT BY WEBSERVICE
            $submitWebservice = false;
            if(!empty(Setting::get('webservices_auto_submit')) && (empty($newShipment->webservice_method) || (!empty($newShipment->webservice_method) && empty($newShipment->submited_at)) || in_array($newShipment->webservice_method, ['envialia', 'tipsa', 'nacex']))) {
                $submitWebservice = true;
            }

            $debug = false;
            if($submitWebservice) {
                try {
                    $webservice = new Webservice\Base($debug);
                    $webservice->submitShipment($newShipment);
                } catch (\Exception $e) {}
            }

            $newShipment->notifyOperators();

            //Store history
            $history = new ShipmentHistory();
            $history->status_id   = $defaultStatusId;
            $history->agency_id   = $newShipment->agency_id;
            $history->shipment_id = $newShipment->id;
            $history->save();

            //update schedule task
            $schedule->last_schedule     = Date::today();
            $schedule->count_repetitions = ($schedule->count_repetitions + 1);
            $schedule->save();

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage() . ' FILE ' . $e->getFile(). ' LINE ' . $e->getLine());
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

    public function setRepeatAttribute($value)
    {
        if(in_array($this->attributes['frequency'], ['day', 'week'])) {
            $this->attributes['repeat'] = null;
        } else {
            $this->attributes['repeat'] = empty($value) ? null : $value;
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
}
