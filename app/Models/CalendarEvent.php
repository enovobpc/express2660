<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\FileTrait;
use Illuminate\Support\Facades\Auth;
use When\When;

class CalendarEvent extends BaseModel
{

    use SoftDeletes,
        FileTrait;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_calendar_events';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'calendar_events';

    /**
     * Default upload directory
     *
     * @const string
     */
    const DIRECTORY = 'uploads/calendar';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'title', 'description', 'start', 'end', 'color', 'type', 'customer_id',
        'repeat_hash', 'repeat_period', 'alert_period', 'alert_at', 'agencies',
        'source_class', 'source_id', 'concluded', 'is_public', 'created_by'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $dates = ['start', 'end', 'alert_at'];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'title' => 'required',
        'date'  => 'required',
    );

    /**
     * Return holidays list
     */
    public static function getHolidays() {

        $curYear = date('Y');

        $staticHolidays = [
            $curYear.'-01-01',
            $curYear.'-04-25',
            $curYear.'-05-01',
            $curYear.'-06-10',
            $curYear.'-10-05',
            $curYear.'-11-01',
            $curYear.'-12-01',
            $curYear.'-12-08',
        ];

        return $staticHolidays;
    }


    /**
     * Create repetion of dates by the given parameters
     *
     * @param $startDate
     * @param $repeatPeriod
     * @return array
     */
    public static function createRepetition($startDate, $repeatPeriod) {

        $endDate = new Carbon($startDate);
        $endDate->addYears(5);

        $repetions = new When();
        $repetions->startDate($startDate);

        if($repeatPeriod == 'yearly') {
            $repetions->freq("yearly");
        }

        elseif($repeatPeriod == 'monthly') {
            $repetions->freq("monthly");
        }

        elseif($repeatPeriod == 'weekly') {
            $repetions->freq("weekly");
        }

        elseif($repeatPeriod == 'daily') {
            $repetions->freq("daily");
        }

        elseif($repeatPeriod == 'quarterly') {
            $repetions->freq("monthly")->interval(3);
        }

        elseif($repeatPeriod == 'biennial') {
            $repetions->freq("monthly")->interval(6);
        }

        elseif($repeatPeriod == 'every_2_weeks') {
            $repetions->freq("weekly")->interval(2);
        }

        else {
            return [];
        }

        $repetions->until($endDate)
                  ->generateOccurrences();

        return $repetions->occurrences;
    }

    /**
     * Set notification of shipment
     *
     * @return int
     */
    public function setNotification()
    {
        $this->deleteNotification();

        $message = $this->title . ' (dentro '. $this->alert_period .' min)';

        $participants = $this->participants()->pluck('user_id')->toArray();

        $recipients[] = $this->created_by;
        if($participants) {
            $recipients = array_merge($recipients, $participants);
        }

        foreach($recipients as $userId) {
            $notification = Notification::firstOrNew([
                'source_class'  => 'CalendarEvent',
                'source_id'     => $this->id,
                'recipient'     => $userId
            ]);

            $notification->source_class = 'CalendarEvent';
            $notification->source_id    = $this->id;
            $notification->recipient    = $userId;
            $notification->message      = $message;
            $notification->alert_at     = $this->alert_at;
            $notification->read         = false;
            $notification->save();
        }

        if($this->alert_at <= date('Y-m-d H:i:s'))  {
            foreach($recipients as $userId) {
                $notification->setPusher(BroadcastPusher::getChannel($userId));
            }
        }

        return true;
    }


    /**
     * Set notification of shipment
     *
     * @return int
     */
    public function deleteNotification()
    {
        return Notification::where('source_class', 'CalendarEvent')
            ->where('source_id', $this->id)
            ->forceDelete();
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
        return $this->belongsTo('App\Models\User');
    }

    public function participants()
    {
        return $this->belongsToMany('App\Models\User', 'calendar_events_participants', 'calendar_event_id', 'user_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    public function owner()
    {
        return $this->belongsTo('App\Models\User', 'created_by');
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

    public function scopeFilterMyEvents($query){

        $user = Auth::user();
        if(!$user->hasRole([config('permissions.role.admin')]) || !empty($agencies)) {
            return $query->where('created_by', $user->id);
        }
    }

    public function scopeFilterEvents($query){

        $user = Auth::user();
        $agencies = $user->agencies ? $user->agencies : [];

        if(!$user->hasRole([config('permissions.role.admin')]) || !empty($agencies)) {
            return $query->where(function($q) use($user){
                $q->whereHas('participants', function($q) use($user) {
                    $q->where('users.id', $user->id);
                });
                $q->orWhere('created_by', $user->id);
            });
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

    public function setEndAttribute($value) {

        $this->attributes['end'] = empty($value) ? null : $value;
    }

    public function setAlertAtAttribute($value) {

        $this->attributes['alert_at'] = empty($value) ? null : $value;
    }

    public function setAgenciesAttribute($value) {

        $this->attributes['agencies'] = empty($value) ? null : json_encode($value);
    }

    public function getAgenciesAttribute()
    {
        return json_decode(@$this->attributes['agencies']);
    }
}
