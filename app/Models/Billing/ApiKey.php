<?php

namespace App\Models\Billing;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Setting;

class ApiKey extends \App\Models\BaseModel implements Sortable
{

    use SoftDeletes, SortableTrait;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_billing_api_keys';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'billing_api_keys';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id', 'name', 'username', 'password', 'token', 'is_active', 'is_default',
        'start_date', 'end_date'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = [
        'company_id' => 'required',
        'token'      => 'required'
    ];

    /**
     * Default sort column
     *
     * @var array
     */
    public $sortable = [
        'order_column_name' => 'sort'
    ];

    /**
     * Return default API Key
     * @return mixed
     */
    public static function getDefaultKey($companyId = null, $returnFull = false){
        
        $apiKey = ApiKey::remember(config('cache.query_ttl'))
            ->cacheTags(ApiKey::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->orderBy('is_default', 'desc'); //se existir default, assume esse, e não assume o proximo da lista

        if($companyId) {
            $apiKey = $apiKey->where('company_id', $companyId);
        }

        $apiKey = $apiKey->first();

        if(!$apiKey) {
            return null;
            //throw new \Exception('Não existe nenhuma ligação ao sistema de faturação para esta empresa.');
        }

        if ($returnFull) {
            return $apiKey;
        }

        return @$apiKey->token;

        //return Setting::get('invoice_apikey');
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Define current model relationships
    */
    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
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
    public function scopeIsActive($query, $value = true){
        $query->where('is_active', $value);
    }

    public function scopeIsDefault($query, $value = true){
        $query->where('is_default', $value);
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
    public function setTokenAttribute($value)
    {
        $this->attributes['token'] = trim($value);
    }

    public function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = empty($value) ? null : $value;
    }
}
