<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class PurchasePaymentNoteInvoice extends BaseModel
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'purchase_payment_note_invoices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payment_id', 'invoice_id', 'total', 'total_pending', 'invoice_total', 'invoice_unpaid'
    ];

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function payment_note()
    {
        return $this->belongsTo('App\Models\PurchasePaymentNote', 'payment_note_id', 'id');
    }

    public function invoice()
    {
        return $this->belongsTo('App\Models\PurchaseInvoice', 'invoice_id', 'id');
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

}
