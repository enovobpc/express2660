<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Setting;

class BankInstitution extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_banks_institutions';

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_core';


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'banks_institutions';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'country', 'country_code', 'bank_code', 'bank_name', 'bank_swift', 'is_active'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'country'  => 'required',
        'name'     => 'required',
        'code'     => 'required'
    );



    /**
     * List banks width data
     *
     * @return array
     */
    public static function listWithData($country = null){

        $banks = BankInstitution::isActive();

        if($country) {
            $banks = $banks->where('country', $country);
        }

        $banks = $banks->orderByRaw('FIELD(country,"'.Setting::get('app_country').'") desc') //Coloca o pais da App em primeiro
            ->orderBy('bank_name', 'asc')
            ->get(['code', 'bank_name', 'bank_iban', 'bank_swift', 'bank_code', 'country']);

        $arr = [[
            'value'      => '',
            'display'    => '',
        ]];
        foreach ($banks as $bank) {

            $namePrefix = $country ? '' : '['.strtoupper($bank->country).'] ';

            $arr[$bank->code] = [
                'value'      => $bank->code,
                'display'    => $namePrefix.$bank->bank_name,
                'data-code'  => $bank->bank_code,
                'data-iban'  => $bank->bank_iban,
                'data-swift' => $bank->bank_swift,
                'data-name'  => $bank->bank_name,
            ];
        }
      
        return $arr;
    }


    /**
     * List banks width data
     *
     * @return array
     */
    public static function listBanks($country = null){

        $banks = BankInstitution::isActive();

        if($country) {
            $banks = $banks->where('country', $country);
        }

        $banks = $banks->orderByRaw('FIELD(country,"'.Setting::get('app_country').'") desc') //Coloca o pais da App em primeiro
            ->orderBy('bank_name', 'asc')
            ->get(['code', 'bank_name', 'bank_iban', 'bank_swift', 'bank_code', 'country']);

      
        if($country) {
            $banksArr = $banks->pluck('bank_name', 'code')->toArray();
        } else {
            
            $banksCountry = $banks->groupBy('country');

            $banksArr = [];
            foreach($banksCountry as $country => $banks) {
                $banksArr[trans('country.'.$country)] = $banks->pluck('bank_name', 'code')->toArray();
            }
        }

        return $banksArr;
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
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    |
    | Eloquent provides a convenient way to transform your model attributes when 
    | getting or setting them. Simply define a 'getFooAttribute' method on your model 
    | to declare an accessor. Keep in mind that the methods should follow camel-casing, 
    | even though your database columns are snake-case.
    |
    */
    public function setCountryCodeAttribute($value)
    {
        $this->attributes['country_code'] = strtoupper(trim($value));
    }

    public function setBankSwiftAttribute($value)
    {
        $this->attributes['bank_swift'] = strtoupper(trim($value));
    }

    public function setBankCodeAttribute($value)
    {
        $this->attributes['bank_code'] = strtoupper(trim($value));
    }
}
