<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class Meeting extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_meetings';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'meetings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id', 'seller_id', 'created_by', 'date', 'duration', 'objectives', 'occurrences', 'charges', 'local',
        'status', 'obs', 'interlocutor', 'is_prospect'
    ];

    /**
     * The attributes that are dates.
     *
     * @var array
     */
    protected $dates = [
        'date'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'customer_id' => 'required',
        'date'        => 'required',
    );

    /**
     * Validator custom attributes
     *
     * @var array
     */
    protected $customAttributes = array(
        'name' => 'Nome',
    );

    /**
     * Store or Update a meeting on calendar
     */
    public function setOnCalendar() {

        $end = new Carbon($this->date);
        $end->addSecond($this->duration);

        $calendar = CalendarEvent::firstOrNew([
            'source_class' => 'App\Models\Meeting',
            'source_id'    => $this->id
        ]);

        $calendar->title = 'Visita a ' . $this->customer->name;
        $calendar->start = $this->date;
        $calendar->end   = $end;
        $calendar->user_id  = Auth::user()->id;
        $calendar->agencies = Auth::user()->agencies;
        $calendar->source_class = 'App\Models\Meeting';
        $calendar->source_id = $this->id;
        $calendar->save();
    }

    /**
     * Delete a meeting from calendar
     */
    public function deleteFromCalendar() {

         $result = CalendarEvent::where('source_class', 'App\Models\Meeting')
                        ->where('source_id', $this->id)
                        ->delete();
        return $result;
    }

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

    public function seller()
    {
        return $this->belongsTo('App\Models\User', 'seller_id');
    }

    public function owner()
    {
        return $this->belongsTo('App\Models\User', 'created_by');
    }


    /**
     * Limit query to seller
     *
     * @return type
     */
    public function scopeFilterSeller($query){

        $user = Auth::user();

        if($user->hasRole([config('permissions.role.seller')]) && !$user->hasRole([config('permissions.role.admin')])) {

            return $query->where(function($q){
                $q->whereNull('seller_id');
                $q->orWhere('seller_id', Auth::user()->id);
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
    public function setSellerIdAttribute($value)
    {
        $this->attributes['seller_id'] = empty($value) ? null : $value;
    }

    public function setObjectivesAttribute($value)
    {
        $this->attributes['objectives'] = empty($value) ? null : json_encode($value);
    }

    public function setOccurrencesAttribute($value)
    {
        $this->attributes['occurrences'] = empty($value) ? null : json_encode($value);
    }

    public function setChargesAttribute($value)
    {
        $this->attributes['charges'] = empty($value) ? null : json_encode($value);
    }

    public function getObjectivesAttribute($value)
    {
        return  json_decode($value, true);
    }

    public function getOccurrencesAttribute($value)
    {
        return  json_decode($value, true);
    }

    public function getChargesAttribute($value)
    {
        return  json_decode($value, true);
    }
}
