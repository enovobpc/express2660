<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Bank extends BaseModel implements Sortable
{
    use SoftDeletes, SortableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'banks';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id', 'name', 'titular_name', 'titular_vat', 'bank_institution_id','bank_code', 'bank_name', 'bank_iban', 'bank_swift',
        'credor_code', 'is_active', 'obs'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    public $rules = [
        'name'       => 'required',
        'bank_code'  => 'required',
        'bank_name'  => 'required',
        'bank_iban'  => 'required',
        'bank_swift' => 'required',
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
     * List banks
     *
     * @return array
     */
    public static function listBanks()
    {
        $banks = Bank::filterSource()
            ->isActive()
            ->pluck('name', 'id')
            ->toArray();

        return $banks;
    }


    /**
     * Get bank code from iban
     *
     * @return array
     */
    public static function getBankCodeFromIban($iban)
    {
        $iban    = str_replace(' ', '', $iban);
        $country = substr($iban, 0, 2);
        $code    = substr($iban, 4, 4);
        return $country.$code;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    public function scopeIsActive($query, $isActive = true){
        return $this->where('is_active', $isActive);
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
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    |
    | Eloquent provides a convenient way to transform your model attributes when
    | getting or setting them. Simply define a 'getFooAttribute' method on your model
    | to declare an accessor. Keep in mind that the methods should follow camel-casing,
    | even though your database columns are snake-case.
    |
    */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = trim($value);
    }

    public function setBankIbanAttribute($value)
    {
        $this->attributes['bank_iban'] = strtoupper(trim(str_replace(' ', '', $value)));
    }

    public function setBankSwiftAttribute($value)
    {
        $this->attributes['bank_swift'] = strtoupper(trim($value));
    }
    
}
