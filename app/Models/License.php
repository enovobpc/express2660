<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Jenssegers\Date\Date;

class License extends BaseModel
{

    use SoftDeletes;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'licenses';

    /**
     * The attributes that are dates
     *
     * @var array
     */
    protected $dates = ['renew_date', 'regist_date'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description', 'status', 'renew_date', 'regist_date', 'obs', 'source', 'email', 'unpaid_limit'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'source'      => 'required',
        'name'        => 'required',
        'status'      => 'required',
        'renew_date'  => 'required',
        'regist_date' => 'required',
    );

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function payments()
    {
        return $this->hasMany('App\Models\LicensePayment', 'license_id');
    }

    /**
     * Set notification of shipment
     *
     * @return int
     */
    public function setNotification($channel, $message)
    {
        $sourceClass = 'License';
        $sourceId    = $this->id;

        $agencies = Agency::filterSource()->pluck('id')->toArray();

        //get notification recipients
        $recipients = \App\Models\User::where(function($q) use($agencies) {
            $q->whereNull('agencies')
                ->orWhere(function($q) use ($agencies){
                    foreach($agencies as $agency) {
                        $q->orWhere('agencies', 'like', '%"'.$agency.'"%');
                    }
                });
            })
            ->where(function($q){
                $q->whereHas('roles', function($query) {
                    $query->whereName('administrator');
                })
                ->orWhereHas('roles.perms', function($query) {
                    $query->whereName('license');
                });
            })
            ->get(['id']);

        foreach($recipients as $user) {
            $notification = Notification::firstOrNew([
                'source_class'  => $sourceClass,
                'source_id'     => $sourceId,
                'recipient'     => $user->id
            ]);

            $notification->source_class = $sourceClass;
            $notification->source_id    = $sourceId;
            $notification->recipient    = $user->id;
            $notification->message      = $message;
            $notification->alert_at     = date('Y-m-d H:i:s');
            $notification->read         = false;
            $notification->save();
        }

        if($notification)  {
            $notification->setPusher($channel ? $channel : BroadcastPusher::getGlobalChannel());
        }

        return true;
    }


}
