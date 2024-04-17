<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class UserCard extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_users_cards';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users_cards';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'card_no', 'name', 'type', 'issue_date', 'validity_date', 'notification_date', 'notification_days', 'obs'
    ];

    /**
     * The attributes that are dates.
     *
     * @var array
     */
    protected $dates = [
        'issue_date', 'validity_date', 'notification_date'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'card_no'       => 'required',
        'validity_date' => 'required',
        'name'          => 'required',
    );

    
    /**
     * 
     * Relashionships
     * 
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
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
    public function setDocNoAttribute($value)
    {
        $this->attributes['doc_no'] = empty($value) ? null : $value;
    }

    public function setIssueDateAttribute($value)
    {
        $this->attributes['issue_date'] = empty($value) ? null : $value;
    }

    public function setValidityDateAttribute($value)
    {
        $this->attributes['validity_date'] = empty($value) ? null : $value;
    }

    public function setNotificationDaysAttribute($value)
    {
        $this->attributes['notification_days'] = empty($value) ? null : $value;
    }

    public function getNameAttribute()
    {
        if(empty($this->attributes['name']) && !empty($this->attributes['type'])) {
            return trans('admin/users.default-cards.' . $this->attributes['type']);
        }

        return $this->attributes['name'];
    }
}
