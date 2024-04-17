<?php

namespace App\Models\Budget;

use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetCourierHistory extends \App\Models\BaseModel
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
    protected $table = 'budgets_courier_history';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'budget_id', 'operator_id', 'status', 'message'
    ];

    /**
     * Validator rules
     * 
     * @var array 
     */
    protected $rules = array(
        'budget_id' => 'required',
    );
    
    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function budget()
    {
        return $this->belongsTo('App\Models\Budget\BudgetCourier');
    }

    public function operator()
    {
        return $this->belongsTo('App\Models\User');
    }
}
