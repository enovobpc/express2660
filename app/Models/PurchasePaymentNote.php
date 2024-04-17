<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Jenssegers\Date\Date;
use Mpdf\Mpdf;
use App, Setting, Mail;

class PurchasePaymentNote extends BaseModel
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'purchase_payment_notes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'reference', 'doc_id', 'doc_date', 'discount', 'discount_unity', 'total', 'user_id'
    ];

    /**
     * Create document number
     *
     * @return int
     */
    public function setCode($save = false)
    {
        if($this->code) {
            $code = $this->code;
            if($save){
                $this->save();
            }
        } else {
            if (config('app.source') == 'corridexcelente') {

                //$date = date('ymd');
                $date = new Date($this->doc_date);
                $date = $date->format('ymd');

                $totalPayments = PurchasePaymentNote::filterSource()
                    ->withTrashed()
                    ->where('doc_date', $this->doc_date)
                    ->count();

                $totalPayments++;

                $docId = $totalPayments;
                $docSerie = date('y');
                $code = $date . '.' . str_pad($docId, 2, "0", STR_PAD_LEFT);

                if ($save) {
                    $this->code = $code;
                    $this->doc_id = $docId;
                    $this->doc_series = $docSerie;
                    $this->save();
                }
            } else {
                $totalPayments = PurchasePaymentNote::filterSource()
                    ->withTrashed()
                    ->where('code', 'like', '%/' . date('y'))
                    ->count();

                $totalPayments++;

                $docId = $totalPayments;
                $docSerie = date('y');
                $code = str_pad($docId, 4, "0", STR_PAD_LEFT) . '/' . $docSerie;

                if ($save) {
                    $this->code = $code;
                    $this->doc_id = $docId;
                    $this->doc_series = $docSerie;
                    $this->save();
                }
            }
        }

        return $code;
    }

    /**
     * Print invoice
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public static function printPaymentNote($invoicesIds, $returnMode = 'pdf') {

        /*try {*/
            $paymentNotes = self::with('invoices.invoice', 'payment_methods')
                ->withTrashed()
                ->filterSource()
                ->whereIn('id', $invoicesIds)
                ->get();

            if ($paymentNotes->isEmpty()) {
                return App::abort(404);
            }

            ini_set("memory_limit", "-1");

            $mpdf = new Mpdf([
                'format'        => 'A4',
                'margin_top'    => 20,
                'margin_bottom' => 10,
                'margin_left'   => 20,
                'margin_right'  => 20,
            ]);

            $mpdf->showImageErrors = true;
            $mpdf->SetAuthor("ENOVO");
            $mpdf->shrink_tables_to_fit = 0;


            $data['view']          = 'admin.printer.invoices.purchase.payment_note';
            $data['documentTitle'] = '';

            foreach ($paymentNotes as $key => $paymentNote) {

                $data['paymentNote'] = $paymentNote;

                $copies = 1; //numero de copias

                for ($i = 0; $i < $copies; $i++) {
                    $data['copy'] = $i + 1;

                    if ($i == 0) {
                        $data['copyId'] = 1;
                        $data['copyNumber'] = 'ORIGINAL';
                    } else if ($i == 1) {
                        $data['copyId'] = 2;
                        $data['copyNumber'] = ' DUPLICADO';
                    } else if ($i == 2) {
                        $data['copyId'] = 3;
                        $data['copyNumber'] = 'TRIPLICADO';
                    }

                    $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write
                }
            }

            if ($returnMode == 'string') {
                return $mpdf->Output('Fatura Compra.pdf', 'S'); //string
            }

            if (Setting::get('open_print_dialog_docs')) {
                $mpdf->SetJS('this.print();');
            }

            $mpdf->debug = true;

            return $mpdf->Output('Fatura de Compra.pdf', 'I'); //output to screen

            exit;
       /* } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }*/
    }

    /**
     * Send E-mail
     * @param $data
     * @return bool
     */
    public function sendEmail($data) {

        $email = @$data['email'];
        $paymentNote = $this;
        //$selectedAttachments = $data['attachments'];

        try {

            $data['subject'] = 'Envio de Nota Pagamento NDL' . $this->code;

            $content = self::printPaymentNote([$this->id], 'string');

            $attachments[] = [
                'mime'      => 'application/pdf',
                'filename'  => 'Nota de Pagamento ' . $this->code . '.pdf',
                'content'   => $content
            ];

            //validate emails
            $emails = null;
            if (!empty($email)) {
                $emails = validateNotificationEmails($email);
                $emails = $emails['valid'];

                if(!$emails) {
                    throw new \Exception('O e-mail indicado é inválido.');
                }
            }

            //add emails in CC
            $emailsCC = null;
            if (!empty(Setting::get('billing_email_cc'))) {
                $emailsCC = validateNotificationEmails(Setting::get('billing_email_cc'));
                $emailsCC = $emailsCC['valid'];
            }

            Mail::send('emails.billing.payment_note', compact('data', 'paymentNote'), function ($message) use ($data, $emails, $emailsCC, $attachments) {

                $message->to($emails);

                if ($emailsCC) {
                    $message = $message->cc($emailsCC);
                }

                $message = $message->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject($data['subject']);

                if($attachments) {
                    foreach ($attachments as $attachment) {

                        if(isset($attachment['content'])) {
                            $message->attachData(
                                $attachment['content'],
                                $attachment['filename'],
                                $attachment['mime'] ? ['mime' => $attachment['mime']] : []
                            );
                        }
                    }
                }
            });

            if (count(Mail::failures()) > 0) {
                return false;
            }



        } catch (\Exception $e) {
            throw new \Exception($e->getMessage() . ' on file ' . $e->getFile(). ' line ' . $e->getLine());
        }

        return true;
    }

    /**
     * Store or update a purchase invoice on provider balance account
     */
    public function storeOrUpdatePurchaseInvoice() {

        $invoice = \App\Models\PurchaseInvoice::firstOrNew([
            'doc_type'      => 'payment-note',
            'doc_id'        => $this->code,
            'provider_id'   => $this->provider_id
        ]);

        $provider = $this->provider;

        $code     = explode('/', @$this->code);
        $docId    = (int) @$code[0];
        $docSerie = @$code[1];

        $invoice->source            = config('app.source');
        $invoice->doc_type          = 'payment-note';
        $invoice->provider_id       = $this->provider_id;
        $invoice->sense             = 'debit';
        $invoice->doc_id            = $docId;
        $invoice->doc_series        = $docSerie;
        $invoice->reference         = $this->code;
        $invoice->doc_date          = $this->doc_date;
        $invoice->due_date          = $this->doc_date;
        $invoice->subtotal          = null;
        $invoice->vat_total         = null;
        $invoice->total             = $this->total * -1;
        $invoice->currency          = Setting::get('app_currency');
        $invoice->billing_code      = @$provider->code;
        $invoice->billing_name      = @$provider->company;
        $invoice->billing_address   = @$provider->billing_address;
        $invoice->billing_zip_code  = @$provider->billing_zip_code;
        $invoice->billing_city      = @$provider->billing_city;
        $invoice->billing_country   = @$provider->billing_country;
        $invoice->vat               = @$provider->vat;
        $invoice->is_settle         = 1;
        $invoice->created_by        = $this->user_id;
        $invoice->save();
    }

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

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function deletedBy()
    {
        return $this->belongsTo('App\Models\User', 'deleted_by');
    }

    public function invoices()
    {
        return $this->hasMany('App\Models\PurchasePaymentNoteInvoice', 'payment_note_id')->withTrashed();
    }

    public function payment_methods()
    {
        return $this->hasMany('App\Models\PurchasePaymentNoteMethod', 'payment_note_id')->withTrashed();
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
    public function setReferenceAttribute($value)
    {
        $this->attributes['reference'] = empty($value) ? null : $value;
    }

}
