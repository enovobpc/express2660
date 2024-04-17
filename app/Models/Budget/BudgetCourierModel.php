<?php

namespace App\Models\Budget;

use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetCourierModel extends \App\Models\BaseModel
{

    use SoftDeletes;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_budgets';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'budgets_courier_models';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'intro', 'transport_info', 'payment_conditions', 'geral_conditions', 'type'
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
        'name' => 'Nome',
    );
}
