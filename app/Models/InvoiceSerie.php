<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceSerie extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'cache_invoices_series';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'invoices_series';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'doc_type', 'code', 'name', 'serie_id', 'api_key'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'doc_type'  => 'required',
        'code'      => 'required',
        'name'      => 'required'
    );

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function invoices()
    {
        return $this->hasMany('App\Models\Invoice', 'doc_serie_id', 'serie_id');
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
    public function setCodeAttribute($value) {
        $this->attributes['code'] = strtoupper($value);
    }

    public function setNameAttribute($value) {
        $this->attributes['name'] = trim($value);
    }
}
