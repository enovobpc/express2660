<?php

namespace App\Models;

use App\Models\Billing\ItemStockHistory;
use App\Models\Billing\VatRate;
use Illuminate\Database\Eloquent\SoftDeletes;
use Setting;

class InvoiceLine extends BaseModel
{

    use SoftDeletes;

    /**
     * Constant variables
     */
    const CACHE_TAG = 'invoice_lines';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'invoices_lines';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoice_id', 'key', 'billing_product_id', 'reference', 'description', 'qty', 'total_price', 'subtotal', 'tax_rate',
        'discount', 'exemption_reason', 'exemption_reason_code', 'hidden', 'assigned_invoice_id', 'obs',
        'tax_rate_id', 'billing_code'
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
     * @param int $invoiceId
     * @param array $linesArr
     * @param \App\Models\Customer $customer
     * @return array
     */
    public static function storeLines($invoiceId, $linesArr, $customer) {
        $result = [
            'subtotal'  => 0,
            'vat'       => 0,
            'total'     => 0
        ];

        $invoice = Invoice::filterSource()
            ->where('id', $invoiceId)
            ->first();

        foreach ($linesArr as $key => $item) {

            $item['invoice_id'] = $invoiceId;
            $item['key'] = $key;

            if(!empty($item['description'])) {

                //obtem os dados da taxa a partir do codigo
                $vatRate = VatRate::getByCode($item['tax_rate']);

                /* if($customer->final_consumer) { //força cliente final a ter iva 23%. Desativado em 24/04. Ticket da KeyInvoice a dizer que é possivel.
                    $vatRate = VatRate::getDefaultRate();
                } */

                if($vatRate) {
                    $item['tax_rate']              = @$vatRate->value;
                    $item['exemption_reason']      = @$vatRate->exemption_reason;
                    $item['exemption_reason_code'] = @$vatRate->billing_code;

                /*  if(in_string('M', $item['tax_rate'])) {
                        $item['exemption_reason'] = $item['tax_rate'];
                        $item['exemption_reason_code'] = Setting::get('exemption_reason_' . strtolower(@$item['tax_rate']));
                        $item['tax_rate'] = '0';
                    } */
                    
                    $invoiceLine = new InvoiceLine();
                    $invoiceLine->fill($item);

                    //dd($invoiceLine->toArray());
                    $invoiceLine->save();

                    if ($invoice->doc_type == Invoice::DOC_TYPE_NC) {
                        ItemStockHistory::increaseByTarget(
                            $invoiceLine->billing_product_id,
                            $invoiceLine->qty,
                            $invoiceLine->total_price,
                            ItemStockHistory::TARGET_SALE,
                            $invoiceId,
                            $invoiceLine->id,
                            $invoice->created_at
                        );
                    } else {
                        ItemStockHistory::decreaseByTarget(
                            $invoiceLine->billing_product_id,
                            $invoiceLine->qty,
                            ItemStockHistory::TARGET_SALE,
                            $invoiceId,
                            $invoiceLine->id,
                            $invoice->created_at
                        );
                    }

                    $lineVat = $invoiceLine->subtotal * ($item['tax_rate'] / 100);

                    $result['subtotal'] += $invoiceLine->subtotal;
                    $result['vat']      += $lineVat;
                    $result['total']    += $invoiceLine->subtotal + $lineVat;
                }
            }
        }

        return $result;
    }

    /**
     * Store billing lines
     *
     * @param $billingId
     * @param $linesArr
     * @return bool
     */
    public static function storeReceiptLines($receiptId, $invoices, $invoicesTotals) {


        $result = [
            'total' => 0,
            'vat'   => 0,
            'subtotal' => 0
        ];

        foreach ($invoices as $invoice) {

            $value = @$invoicesTotals[$invoice->id];

            if(!empty($value) && $value > 0.00) {

                if($invoice->doc_series_id == 19) { //SALDO INICIAL
                    $documentReferenceCode = '19 ' . $invoice->doc_series_id . '/' . $invoice->doc_id;
                } elseif($invoice->doc_type == 'credit-note') {
                    $documentReferenceCode = '7 ' . $invoice->doc_series_id . '/' . $invoice->doc_id;
                    $value = -1 * $value; //força recibos a ficar com valor negativo, pois ao gravar os dados vem com sinal positivo
                } elseif($invoice->doc_type == 'debit-note') {
                    $documentReferenceCode = '8 ' . $invoice->doc_series_id . '/' . $invoice->doc_id;
                } else {
                    $documentReferenceCode = '4 ' . $invoice->doc_series_id . '/' . $invoice->doc_id;
                }

                $result['total']+= $value;

                if($value == $invoice->doc_total) {
                    //fatura liquidada na totalidade. Podemos guardar o valor de iva e subtotal
                    $result['vat']+= $invoice->doc_vat;
                    $result['subtotal']+= $invoice->doc_subtotal;
                } else {
                    //fatura parcial. Temos de ir buscar a percentagem de cada parcela, de acordo com o valor pago

                    $percentagemPaga = (($value * 100) / $invoice->doc_total) / 100;

                    //calcula o subtotal e iva com base na percentagem paga.
                    $valorParcialIva = $invoice->doc_vat * $percentagemPaga;
                    $valorParcialSubtotal = $invoice->doc_subtotal * $percentagemPaga;

                    $result['vat']+= $valorParcialIva;
                    $result['subtotal']+= $valorParcialSubtotal;
                }

                $item = [
                    'assigned_invoice_id' => $invoice->id,
                    'invoice_id'   => $receiptId,
                    'reference'    => $documentReferenceCode,
                    'description'  => $invoice->doc_series . ' ' . $invoice->doc_id,
                    'total_price'  => $value,
                ];

                $invoiceLine = new InvoiceLine();
                $invoiceLine->fill($item);
                $invoiceLine->save();
            }
        }

        //dd($result);
        return $result;
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
        return $this->belongsTo('App\Models\Invoice', 'invoice_id');
    }

    public function assigned_invoice()
    {
        return $this->belongsTo('App\Models\Invoice', 'assigned_invoice_id');
    }

    public function billingProduct() {
        return $this->belongsTo('App\Models\Billing\Item', 'billing_product_id', 'id');
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

    public function setAssignedInvoiceIdAttribute($value) {
        $this->attributes['assigned_invoice_id'] = empty($value) ? null : $value;
    }

    public function setBillingProductIdAttribute($value) {
        $this->attributes['billing_product_id'] = empty($value) ? null : $value;
    }
}
