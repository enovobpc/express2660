<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Auth;

class PaymentCondition extends BaseModel implements Sortable
{

    use SoftDeletes, SortableTrait;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_payment_conditions';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payment_conditions';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'name', 'days', 'sales_visible', 'purchases_visible', 'software_code', 'is_active'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'code' => 'required',
        'name' => 'required',
    );
    
    /**
     * Validator custom attributes
     * 
     * @var array 
     */
    protected $customAttributes = array(
        'code' => 'Código',
        'name' => 'Nome',
    );

    /**
     * Default sort column
     *
     * @var array
     */
    public $sortable = [
        'order_column_name' => 'sort'
    ];

    /**
     * Get payment condition days
     * @param $paymentConditionCode
     * @return int
     */
    public static function getDays($paymentConditionCode) {
        $condition = PaymentCondition::filterSource()
            ->where('code', $paymentConditionCode)
            ->first(['days']);

        //obtem condição por defeito
        if(empty($condition)) {
            $condition = PaymentCondition::filterSource()
                ->where('code', '30d')
                ->first(['days']);
        }

        return @$condition->days;
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
    
    public function scopeFilterAgencies($query){
    
        $user = Auth::user();
        $agencies = $user->agencies;
        
        if(!$user->hasRole([config('permissions.role.admin')]) || !empty($agencies)) {
            return $query->where(function($q) use($agencies) {
                foreach ($agencies as $agency) {
                    $q->orWhere('agencies', 'like', '%'.$agency.'%');
                }
            });
        }
    }

    public function scopeIsSalesVisible($query, $visible = true){
        return $query->where('sales_visible', $visible);
    }

    public function scopeIsPurchasesVisible($query, $visible = true){
        return $query->where('purchases_visible', $visible);
    }

    public function scopeIsActive($query, $visible = true){
        return $query->where('is_active', $visible);
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
    
    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = empty($value) ? null : strtolower($value);
    }
}
