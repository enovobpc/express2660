<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Auth;

class AddressBook extends BaseModel
{

    use SoftDeletes;


    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_contacts';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'address_book';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'name', 'email', 'phone'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'source' => 'required',
        'email'  => 'required',
        'phone'  => 'required'
    );

    /**
     *
     * @param $emails
     */
    public static function storeEmails($emails) {

    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Define current model relationships
    */


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

    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = trim($value);
    }

    public function setEmailAttribute($value) {
        $this->attributes['email'] = strtolower(trim($value));
    }
}
