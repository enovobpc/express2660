<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class CustomerRecipient extends BaseModel
{
    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_customers_recipients';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customers_recipients';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'name', 'email', 'phone', 'mobile', 'address', 'zip_code', 'city', 'state', 'country',
        'responsable', 'obs', 'customer_id', 'assigned_customer_id', 'vat', 'always_cod'
    ];
    
   /**
     * Validator rules
     * 
     * @var array 
     */
    public $rules = [
        'name' => 'required',
    ];
    
    /**
     * Validator custom attributes
     * 
     * @var array 
     */
    protected $customAttributes = [
        'code'     => 'Código',
        'name'     => 'Nome Destinatário',
        'email'    => 'E-mail',
        'phone'    => 'Telefone',
        'address'  => 'Morada',
        'zip_code' => 'Código Postal',
        'city'     => 'Localidade',
        'country'  => 'País',
        'responsable' => 'Responsável'
    ];


    /**
     * Get duplicate rows
     *
     * @param $query
     * @return mixed
     */
    public function scopeGetDuplicates($query) {
        return $query->groupBy('name','address', 'zip_code','city')
            ->havingRaw('COUNT(*) > 1')
            ->select('id','name','address','zip_code','city', DB::raw('COUNT(*) as `count`'))
            ->get();
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

    public function assigned_customer()
    {
        return $this->belongsTo('App\Models\Customer', 'assigned_customer_id');
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
        $this->attributes['code'] = empty($value) ? null : $value;
    }

    public function setAssignedCustomerIdAttribute($value)
    {
        $this->attributes['assigned_customer_id'] = empty($value) ? null : $value;
    }

    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = empty($value) ? null : $value;
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = empty($value) ? null : $value;
    }

    public function setVatAttribute($value)
    {
        $this->attributes['vat'] = empty($value) ? null : $value;
    }

    public function setResponsableAttribute($value)
    {
        $this->attributes['responsable'] = empty($value) ? null : $value;
    }

    public function setObsAttribute($value)
    {
        $this->attributes['obs'] = empty($value) ? null : $value;
    }
}
