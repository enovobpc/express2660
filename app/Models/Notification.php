<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class Notification extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_notifications';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'notifications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'read', 'sender', 'recipient', 'message', 'alert_at', 'source_class', 'source_id'
    ];

    /**
     * The attributes that are dates.
     *
     * @var array
     */
    protected $dates = [
        'alert_at'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'recipient'   => 'required',
        'description' => 'required',
    );

    /**
     * Set pusher notification
     * @param $channel
     */
    public function setPusher($channel = null){

        $data['id']      = 0; //agrupa as notificações
        $data['title']   = 'Notificação';
        $data['message'] = $this->message;

        if(!$channel) {
            $channel = BroadcastPusher::getChannel();
        }

        $pusher = new BroadcastPusher();
        return $pusher->trigger($data, $channel);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Define current model relationships
    */
    public function recipient()
    {
        return $this->belongsTo('App\Models\User', 'recipient');
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

    public static function scopeFilterMyNotifications($query){
        return $query->where('recipient', Auth::user()->id);
    }

    public static function scopeExcludeScheduled($query){
        return $query->whereNotNull('alert_at')
                     ->whereRaw('alert_at <= NOW()');
    }

    public static function scopeUnread($query){
        return $query->where('read', 0)
             ->where(function($q){
                 $q->whereNull('alert_at')
                   ->orWhereRaw('alert_at <= NOW()');
             });
    }

    public static function scopeScheduledToday($query){
        return $query->where('recipient', Auth::user()->id)
                     ->whereNotNull('alert_at')
                     ->whereRaw('DATE(alert_at) = TODAY()');
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

    public function setSenderAttribute($value)
    {
        $this->attributes['sender'] = empty($value) ? null : $value;
    }
}
