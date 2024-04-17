<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class UserContract extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_users_contracts';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users_contracts';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'contract_type', 'start_date', 'end_date', 'notification_date', 'notification_days', 'obs'
    ];

    /**
     * The attributes that are dates.
     *
     * @var array
     */
    protected $dates = [
        'start_date', 'end_date', 'notification_date'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'contract_type' => 'required',
        'start_date'    => 'required',
        'end_date'      => 'required',
    );
    
    /**
     * Validator custom attributes
     * 
     * @var array 
     */
    protected $customAttributes = array(
        'type'       => 'Tipo Contrato',
        'start_date' => 'Data Início',
        'end_date'   => 'Data Fim',
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
}
