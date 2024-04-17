<?php

namespace App\Models\Budget;

use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class Propose extends \App\Models\BaseModel
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
    protected $table = 'budgets_proposes';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'subject', 'message', 'from', 'from_name', 'to', 'to_name'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'provider_id' => 'required',
    );


    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */

    public function provider()
    {
        return $this->belongsTo('App\Models\Provider', 'provider_id');
    }

    public function budget()
    {
        return $this->belongsTo('App\Models\Budget\Budget');
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
    public function setAttachmentsAttribute($value)
    {
        $this->attributes['attachments'] = empty($value) ? null : json_encode($value);
    }
    public function getAttachmentsAttribute($value)
    {
        return json_decode($value);
    }
}
