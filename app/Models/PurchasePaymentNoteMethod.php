<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class PurchasePaymentNoteMethod extends BaseModel
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'purchase_payment_note_methods';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payment_id', 'payment_method_id', 'bank_id', 'method', 'bank', 'date', 'total', 'obs'
    ];

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function bankInfo()
    {
        return $this->belongsTo('App\Models\Bank', 'bank_id', 'id')->withTrashed();
    }

    public function payment_note()
    {
        return $this->belongsTo('App\Models\PurchasePaymentNote', 'payment_note_id', 'id');
    }

    public function payment_method()
    {
        return $this->belongsTo('App\Models\PaymentMethod', 'payment_method_id', 'id');
    }

    /* public function paymentMethod()
    {
        return $this->belongsTo('App\Models\PaymentMethod', 'method', 'code')->withTrashed();
    } */

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

}
