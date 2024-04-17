<?php
namespace App\Models\Sms;

use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class Pack extends \App\Models\BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_sms';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sms_packs';

    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $gateway;

    /**
     * @var string
     */
    public $token;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'total_sms', 'remaining_sms', 'buy_by', 'is_active', 'reference', 'price_un'
    ];

    /**
     * Return total of available SMS
     * @return mixed
     */
    public static function countAvailableSms() {
        $availableSMS = Pack::filterSource()
            //->where('remaining_sms', '>', 0)
            ->where('is_active', 1)
            ->sum('remaining_sms');

        return $availableSMS;
    }


    public function scopeCountRemaining() {

        $counter = $this->where('remaining_sms', '>', 0)
            ->where('is_active', 1);

        return $counter;
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
        return $this->belongsTo('App\Models\User', 'buy_by');
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
}