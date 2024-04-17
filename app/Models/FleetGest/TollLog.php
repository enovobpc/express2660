<?php

namespace App\Models\FleetGest;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\FileTrait;
use Auth;

class TollLog extends \App\Models\BaseModel
{

    use SoftDeletes,
        FileTrait;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_fleet';

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_fleet_tolls_log';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fleet_tolls_log';


    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['entry_date', 'exit_date'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'vehicle_id', 'provider_id', 'entry_date', 'exit_date', 'entry_point',
        'exit_point', 'total', 'is_paid', 'payment_date', 'toll_provider', 'toll_service'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'vehicle_id' => 'required'
    );
    
    /**
     * Validator custom attributes
     * 
     * @var array 
     */
    protected $customAttributes = array(
        'vehicle_id'    => 'Viatura',
        'provider_id'   => 'Fornecedor',
        'entry_date'    => 'Data entrada',
        'exit_date'     => 'Data saída',
        'entry_point'   => 'Ponto de entrada',
        'exit_point'    => 'Ponto de saída',
        'is_paid'       => 'Pago',
        'payment_date'  => 'Data de pagamento',
    );

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */

    public function vehicle()
    {
        return $this->belongsTo('App\Models\FleetGest\Vehicle');
    }

    public function provider()
    {
        return $this->belongsTo('App\Models\Provider');
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
    public function scopeFilterSource($query) {
        return $query->whereHas('vehicle', function($q){
            $q->filterSource();
        });
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
    public function setProviderIdAttribute($value)
    {
        $this->attributes['provider_id'] = empty($value) ? null : $value;
    }
}
