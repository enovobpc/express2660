<?php

namespace App\Models\FleetGest;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\FileTrait;
use Auth;


class Accessory extends  \App\Models\BaseModel
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
    const CACHE_TAG = 'cache_fleet_accessories';


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fleet_accessories';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'vehicle_id', 'code', 'type', 'name', 'brand', 'model', 'buy_date', 'validity_date', 'obs'
    ];

    /**
     * The attributes that are dates.
     *
     * @var array
     */
    protected $dates = ['validity_date', 'buy_date'];

    /**
     * Default upload directory
     * 
     * @const string
     */
    const DIRECTORY = 'uploads/vehicles/acessories';
    
    /**
     * Validator rules
     * 
     * @var array 
     */
    public $rules = [
        'vehicle_id'  => 'required',
        'name'        => 'required',
    ];

    /**
     * Set code
     * @param bool $save
     */
    public function setCode($save = true) {

        $lastCode = Accessory::filterSource()
            ->orderBy('code', 'desc')
            ->first(['code']);

        $code = empty($lastCode->code) ? 1 : (int) $lastCode->code + 1;

        $code = str_pad($code, 6, '0', STR_PAD_LEFT);

        if($save) {
            $this->code = $code;
            $this->save();
        }

        return $code;
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function vehicle()
    {
        return $this->belongsTo('App\Models\FleetGest\Vehicle', 'vehicle_id');
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
}
