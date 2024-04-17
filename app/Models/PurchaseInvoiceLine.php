<?php

namespace App\Models;

use App\Models\Billing\ItemStockHistory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Setting;

class PurchaseInvoiceLine extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'purchase_invoice_lines';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'purchase_invoices_lines';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoice_id', 'key', 'billing_product_id', 'reference', 'description', 'qty', 'total_price', 'subtotal',
        'tax_rate', 'discount', 'exemption_reason', 'exemption_reason_code', 'hidden'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = array(
        'invoice_id' => 'required',
        'reference'  => 'required',
    );

    /**
     * Store billing lines
     *
     * @param $billingId
     * @param $linesArr
     * @return bool
     */
    public static function storeLines($invoiceId, $linesArr, $provider) {
        $invoice = PurchaseInvoice::filterSource()
            ->where('id', $invoiceId)
            ->first();

        foreach ($linesArr as $key => $item) {

            $item['invoice_id'] = $invoiceId;
            $item['key'] = $key;

            if(!empty($item['description'])) {
                if(in_string('M', $item['tax_rate'])) {
                    $item['exemption_reason'] = $item['tax_rate'];
                    $item['exemption_reason_code'] = Setting::get('exemption_reason_' . strtolower(@$item['tax_rate']));
                    $item['tax_rate'] = '0';
                }

                $invoiceLine = new PurchaseInvoiceLine();
                $invoiceLine->fill($item);
                $invoiceLine->save();

                if ($invoice->doc_type == 'provider-credit-note') {
                    ItemStockHistory::decreaseByTarget(
                        $invoiceLine->billing_product_id,
                        $invoiceLine->qty,
                        ItemStockHistory::TARGET_PURCHASE,
                        $invoiceId,
                        $invoiceLine->id,
                        $invoice->created_at
                    );
                } else {
                    ItemStockHistory::increaseByTarget(
                        $invoiceLine->billing_product_id,
                        $invoiceLine->qty,
                        $invoiceLine->total_price,
                        ItemStockHistory::TARGET_PURCHASE,
                        $invoiceId,
                        $invoiceLine->id,
                        $invoice->created_at
                    );
                }
            }
        }

        return true;
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function invoice()
    {
        return $this->belongsTo('App\Models\PurchaseInvoice', 'invoice_id');
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
    public function setDiscountAttribute($value) {
        $this->attributes['discount'] = empty($value) || $value == 0.00 ? null : $value;
    }

    public function setBillingProductIdAttribute($value) {
        $this->attributes['billing_product_id'] = empty($value) ? null : $value;
    }
}
