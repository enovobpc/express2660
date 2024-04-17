<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class LoginLog extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_login_logs';

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_logs';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'login_logs';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'target', 'user_id', 'success', 'ip', 'remember'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Define current model relationships
    */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'user_id');
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
   /* public function setIpAttribute($value)
    {
        $this->attributes['ip'] = ip2long($value);
    }

    public function getIpAttribute($value)
    {
        return long2ip($value);
    }*/
    
   /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    |
    | Scopes allow you to easily re-use query logic in your models.
    | To define a scope, simply prefix a model method with scope.
    |
    */
    
    public function scopeFilterSource($query) {
        return $query->where('source', env('APP_SOURCE'));
    }
}
