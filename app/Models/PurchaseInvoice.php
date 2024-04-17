<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\FileTrait;
use App\Models\InvoiceGateway\Base;
use Auth, Setting, Mail, DB;
use Jenssegers\Date\Date;
use Mpdf\Mpdf;

class PurchaseInvoice extends BaseModel
{

    use SoftDeletes,
        FileTrait;

    /**
     * @var null
     */
    public $apiKey = null;

    /**
     * Default target values
     */
    const TARGET_PURCHASE_INVOICE = 'PurchaseInvoice';
    const TARGET_INVOICE          = 'Invoice';

    const DOC_TYPE_FT             = 'purchase-invoice';
    const DOC_TYPE_FR             = 'purchase-invoice-receipt';
    const DOC_TYPE_SIND           = 'sind';
    const DOC_TYPE_SINC           = 'sinc';

    /**
     * Default upload directory
     *
     * @const string
     */
    const DIRECTORY = 'uploads/proofs';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'purchase_invoices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'gateway', 'type_id', 'target', 'target_id', 'provider_id', 'description',
        'doc_id', 'doc_type', 'doc_series', 'doc_series_id', 'doc_date', 'due_date', 'reference',
        'total', 'vat_total', 'subtotal', 'total_discount', 'irs_tax', 'rounding_value', 'currency',
        'payment_condition', 'payment_date', 'payment_method', 'payment_method_id', 'vat', 'billing_code', 
        'billing_name', 'billing_address', 'billing_zip_code', 'billing_city', 'billing_country', 'billing_email',
        'obs', 'is_deleted', 'assigned_targets', 'payment_until', 'received_date', 'bank_id',
        'delete_reason', 'api_key', 'created_by', 'ignore_stats', 'is_scheduled', 'sense',
    ];

    /**
     * Constructor
     *
     * @param $agencia
     * @param $cliente
     * @param $password
     */
    public function __construct(array $attributes = [], $apiKey = null)
    {
        if (is_array($attributes)) {
            parent::__construct($attributes);
        } else {
            $this->apiKey = $apiKey;
        }
    }

    /**
     * Set code
     * @param bool $save
     */
    public function setCode($save = true)
    {

        if ($this->code) {
            return $this->code;
        } else {


            $docSerieType = trans('admin/billing.types_code.'. $this->doc_type);

            if($this->doc_date) {
                $docDate = new Date($this->doc_date);
                $docSerie = $docSerieType . $docDate->format('y');
            } else {
                $docSerie = $docSerieType . date('y');
            }

            $totalInvoices = PurchaseInvoice::filterSource()
                ->withTrashed()
                ->where('doc_type', $this->doc_type)
                ->where('code', 'like', $docSerie.'/%')
                ->count();

            $totalInvoices++;
            $docId = $totalInvoices;

            //$code = $docSerie.'/'.str_pad($docId, 4, "0", STR_PAD_LEFT);
            $code = $docSerie.'/'.$docId;

            if ($save) {
                $this->code = $code;
                $this->doc_id = $docId;
                $this->doc_series = $docSerie;
                $this->save();
            }

            return $code;
        }
    }


    /**
     * Update provider counters
     *
     * @param $providerId
     * @return mixed
     */
    public static function updateProviderCounters($providerId)
    {

        $provider = Provider::find($providerId);

        if ($provider) {
            $totalPurchaseInvoices = PurchaseInvoice::filterSource()
                ->where('provider_id', $provider->id)
                ->whereNull('is_scheduled')
                ->where('is_deleted', 0)
                ->where('is_settle', 0)
                ->get(['total', 'total_unpaid', 'due_date', 'doc_type', 'sense']);


            $totalCredit = $totalPurchaseInvoices->filter(function ($item) {
                return $item->sense == 'credit';
            })->sum('total_unpaid');

            $totalDebit = $totalPurchaseInvoices->filter(function ($item) {
                return $item->sense == 'debit';
            })->sum('total_unpaid');
            //$totalDebit= $totalDebit * -1;

            $totalUnpaid = $totalDebit + $totalCredit;
            $countUnpaid = $totalPurchaseInvoices->count('total_unpaid');
            $countExpired = $totalPurchaseInvoices->filter(function ($item) {
                return $item->due_date < date('Y-m-d');
            })->count();

            $provider->balance_total_unpaid = $totalUnpaid;
            $provider->balance_count_unpaid = $countUnpaid;
            $provider->balance_count_expired = $countExpired;

            return $provider->save();
        }

        return false;
    }

    /**
     * Return summary of vat balance
     *
     * @param $startDate
     * @param $endDate
     * @return array
     */
    public static function getVatBalance($startDate, $endDate)
    {

/* 
        $paymentNotes = PurchasePaymentNote::whereBetween('doc_date', [$startDate, $endDate])
            ->get();

        $groupedData[''] = [
            'vat_rate'    => 0,
            'rate_name'   => 'Taxa não especificada',
            'count'       => @$paymentNotes->count(),
            'subtotal'    => $paymentNotes->sum('subtotal'),
            'incidence'   => $paymentNotes->sum('subtotal'),
            'vat'         => $paymentNotes->sum('vat_total'),
            'total'       => $paymentNotes->sum('total'),
        ];

        return $groupedData; */


        $docTypes = ['receipt', 'proforma-invoice', 'nodoc'];
        $invoicesLines = PurchaseInvoiceLine::with(['invoice' => function ($q) {
                $q->select(['id', 'doc_type', 'doc_id']);
            }])
            ->whereHas('invoice', function ($q) use ($startDate, $endDate, $docTypes) {
                $q->filterSource();
                $q->filterAgencies();
                $q->whereNotNull('doc_id');
                /* $q->where(function ($q) {
                    $q->where('is_hidden', 0);
                    $q->orWhereNull('is_hidden');
                }); */
                $q->whereNotIn('doc_type', $docTypes);
                $q->whereBetween('doc_date', [$startDate, $endDate]);
                $q->where(function ($q) {
                    $q->where('is_deleted', 0);
                    $q->orWhereNull('is_deleted');
                });
            })
            ->whereNotNull('tax_rate')
            ->get([
                'invoice_id',
                'subtotal',
                'tax_rate',
                'exemption_reason',
                'reference',
            ]);

   
        $vatData = $invoicesLines->groupBy('tax_rate');
        $vatRateExempt = $invoicesLines->filter(function ($item) {
            return $item->exemption_reason;
        })->groupBy('exemption_reason');

        $groupedData = [];
        foreach ($vatData as $vatRate => $invoices) {

    
            if (empty($vatRate) || $vatRate == 0.00) {

                foreach ($vatRateExempt as $exemptionReason => $invoices) {

                    $invoices     = $invoices->filter(function ($item) {
                        return $item->invoice->doc_type != 'credit-note';
                    });
                    $paymentNotes = $invoices->filter(function ($item) {
                        return $item->invoice->doc_type == 'credit-note';
                    });

                    $rateName = trans('admin/billing.exemption-reasons.' . Setting::get('app_country') . '.' . $exemptionReason);
                    $vatRate = 0;

                    $incidence    = @$invoices->sum('subtotal');
                    $total        = @$invoices->sum('subtotal') * (1 + (@$vatRate / 100));
                    $vatToReceive = $total - $incidence;
                    $vatToPay     = 0;

                    $groupedData[$exemptionReason] = [
                        'vat_rate'    => 0,
                        'rate_name'   => $rateName,
                        'total'       => $total,
                        'incidence'   => $incidence,
                        'vat_receive' => $vatToReceive,
                        'vat_pay'     => $vatToPay,
                        'vat'         => $vatToReceive,
                        'count'       => @$invoices->count(),
                    ];
                }
            } else {

                $invoices     = $invoices->filter(function ($item) {
                    return $item->invoice->doc_type != 'credit-note';
                });
                $paymentNotes = $invoices->filter(function ($item) {
                    return $item->invoice->doc_type == 'credit-note';
                });

                $rateName = 'Taxa IVA ' . money($vatRate, '%');

                $incidence    = @$invoices->sum('subtotal');
                $total        = @$invoices->sum('subtotal') * (1 + (@$vatRate / 100));
                $vatToReceive = $total - $incidence; 
                $vatToPay     = 0;
                $vat          = $total - $incidence;

                //futuramente é preciso ver se o tipo de despesa deduz o IVA. Se não deduzir o IVA ter forma de apresentar ou não abater
     
                $groupedData[$vatRate] = [
                    'vat_rate'    => $vatRate,
                    'rate_name'   => $rateName,
                    'total'       => $total,
                    'incidence'   => $incidence,
                    'vat_receive' => $vatToReceive,
                    'vat_pay'     => $vatToPay,
                    'vat'         => $vatToReceive,
                    'count'       => @$invoices->count(),
                ];
            }
        }

        return $groupedData;
    }


    /**
     * Prepare invoice header data array
     *
     * @return \Illuminate\Http\Response
     */
    public function prepareDocumentHeader($input, $provider, $target = null)
    {

        $input['vat'] = $input['vat'] == '999999990' ? null : $input['vat'];

        $data = [
            'nif'           => $input['vat'],
            'obs'           => $input['obs'],
            'docdate'       => $input['docdate'],
            'duedate'       => $input['duedate'],
            'docref'        => $input['docref'],
            'code'          => $input['billing_code'],
            'name'          => $input['billing_name'],
            'address'       => $input['billing_address'],
            'zip_code'      => $input['billing_zip_code'],
            'city'          => $input['billing_city'],
            'printComment'  => "",
            'taxRetention'  => !empty(@$input['irs_tax']) ? intval($input['irs_tax']) : null,
            'provider_id'   => $provider->id,
            'target_id'     => @$target['id'],
            'target'        => @$target['target'] ? @$target['target'] : 'Invoice',
        ];

        return $data;
    }

    /**
     * Prepare invoice lines data array
     *
     * @return \Illuminate\Http\Response
     */
    public function prepareDocumentLines()
    {

        $arr = [];
        foreach ($this->lines as $line) {
            $arr[] = [
                'ref'       => $line->reference,
                'qt'        => $line->qty,
                'price'     => $line->total_price + $line->total_expenses,
                'tax'       => $line->tax_rate,
                'prodDesc'  => $line->description,
                'discount'  => $line->discount,
                'exemption' => $line->exemption_reason_code,
            ];
        }

        return $arr;
    }

    public function insertOrUpdateProvider($data)
    {

        if (!empty($data['vat']) && !in_array($data['vat'], ['999999990', '999999999'])) {
            $class = Base::getNamespaceTo('Provider');
            $customerKeyinvoice = new $class();
            $customerKeyinvoice->insertOrUpdateProvider(
                $data['vat'],
                $data['code'],
                $data['name'],
                $data['address'],
                $data['zip_code'],
                $data['city'],
                $data['phone'],
                null,
                $data['email'],
                $data['obs'],
                $data['country']
            );
        }
    }

    /**
     * Create a draft
     *
     * @param $docType
     * @param $header
     * @param $lines
     * @return mixed
     */
    public function createDraft($docType, $header, $lines, $apiKey = null)
    {

        $this->apiKey = $this->api_key;
        if (!empty($apiKey)) {
            $this->apiKey = $apiKey;
        }

        $class = $this->getNamespaceTo('Document');

        $invoice = new $class($this->apiKey);
        $draftId = $invoice->createDraft($docType, $header);

        $docTotal = 0;
        foreach ($lines as $line) {
            $docTotal += $line['price'];
            $invoice->insertDraftLine($draftId, $docType, $line);
        }

        //store invoice
        $this->source      = config('app.source');
        $this->gateway     = Setting::get('invoice_software') ? Setting::get('invoice_software') : 'KeyInvoice';
        $this->provider_id = $header['provider_id'];
        $this->target      = $this->target ? $this->target : 'invoice';
        $this->doc_id      = $draftId;
        $this->doc_type    = $docType;
        $this->is_draft    = 1;
        //$this->total       = $docTotal;
        $this->due_date    = $header['duedate'];
        $this->doc_date    = $header['docdate'];
        $this->reference   = $header['docref'];
        $this->api_key     = $this->apiKey;
        $this->save();

        return $draftId;
    }

    /**
     * Destroy a draft
     *
     * @param $type
     * @param $data - obrigatório indicar o campo NIF e Código do sistema de faturação
     * @return mixed
     */
    public function destroyDraft($id, $type)
    {

        $class = $this->getNamespaceTo('Document');

        $invoice = new $class($this->apiKey);

        return $invoice->deleteDraft($id, $type);
    }

    /**
     * Convert a draft document to a final document
     *
     * @param $draftId
     * @param $docType
     * @return mixed
     */
    public function convertDraftToDoc($draftId, $docType)
    {

        $class = $this->getNamespaceTo('Document');

        $invoice = new $class($this->apiKey);

        $invoiceId = $invoice->convertDraftToDoc($draftId, $docType);

        $this->update([
            'is_draft' => 0,
            'doc_id'   => $invoiceId
        ]);

        return $invoiceId;
    }


    /**
     * Return a namespace to a given Class
     *
     * @return string
     * @throws Exception
     */
    public function getNamespaceTo($class)
    {
        return Base::getNamespaceTo($class);
    }

    /**
     * Print invoice
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public static function printInvoices($invoicesIds, $returnMode = 'pdf')
    {

        try {
            $invoices = self::filterSource()
                ->whereIn('id', $invoicesIds)
                ->get();

            if ($invoices->isEmpty()) {
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


            $data['view']          = 'admin.printer.invoices.purchase.invoice';
            $data['documentTitle'] = '';

            foreach ($invoices as $key => $invoice) {

                $data['invoice'] = $invoice;

                for ($i = 0; $i < 3; $i++) {
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
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Print orders
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public static function printOrders($invoicesIds, $returnMode = 'pdf')
    {

        try {
            $invoices = self::filterSource()
                ->whereIn('id', $invoicesIds)
                ->get();

            if ($invoices->isEmpty()) {
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
            
            $data['view']          = 'admin.printer.invoices.purchase.invoice';
            $data['documentTitle'] = '';

            foreach ($invoices as $key => $invoice) {

                $data['invoice'] = $invoice;
                $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write
            }

            if (Setting::get('open_print_dialog_docs')) {
                $mpdf->SetJS('this.print();');
            }

            $mpdf->debug = true;

            return $mpdf->Output('Encomenda.pdf', 'I'); //output to screen

            exit;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Print balance summary for provider
     * @param $customerId
     */
    public static function printBalanceResume($request, $returnMode = 'pdf')
    {


        /*$data = PurchaseInvoice::filterSource()
                    ->with('provider');

        //filter category
        $value = $request->category;
        if($request->has('category')) {
            $data = $data->where('category_id', $value);
        }

        //filter payment method
        $value = $request->payment_method;
        if($request->has('payment_method')) {
            $data = $data->where('payment_method', $value);
        }

        //filter unpaid
        $value = $request->unpaid;
        if($request->has('unpaid')) {
            if($value == '1') {
                $data = $data->where(function($q){
                    $q->where('balance_total_unpaid', '=', '0.00');
                    $q->orWhere('balance_total_unpaid', '');
                    $q->orWhereNull('balance_total_unpaid');
                });
            } else {
                $data = $data->where('balance_total_unpaid', '>', '0.00');
            }
        }

        //filter is expired
        $value = $request->expired;
        if($request->has('expired')) {
            if($value == '1') {
                $data = $data->where(function($q){
                    $q->where('balance_count_expired', '>', '0');
                    $q->where('balance_count_expired', '<>', '0');
                });
            } else {
                $data = $data->where(function($q){
                    $q->where('balance_count_expired', '=', '0');
                    $q->orWhere('balance_count_expired', '');
                    $q->orWhereNull('balance_count_expired');
                });
            }
        }

        $providers = $data->groupBy('provider_id')
            ->get([
            'purchase_invoices.*',
            DB::raw('count(total_unpaid) as count'),
            DB::raw('sum(total_unpaid) as total_unpaid'),
            DB::raw('sum(vat_total) as vat_total'),
            DB::raw('sum(subtotal) as subtotal'),
            DB::raw('sum(total) as total'),
            DB::raw('max(doc_date) as last_invoice'),
        ]);*/

        $data = Provider::filterSource()
            ->select([
                'providers.*',
                DB::raw('(select sum(total) from purchase_invoices where sense="debit" and purchase_invoices.provider_id = providers.id and deleted_at is null limit 0,1) as debit'),
                DB::raw('(select sum(total) from purchase_invoices where sense="credit" and purchase_invoices.provider_id = providers.id and deleted_at is null limit 0,1) as credit'),
                DB::raw('(select max(doc_date) from purchase_invoices where purchase_invoices.provider_id = providers.id and deleted_at is null limit 0,1) as last_invoice'),
            ]);

        //filter sense
        $value = $request->sense;
        if ($request->has('sense')) {
            $data = $data->where('sense', $value);
        }

        //filter category
        $value = $request->category;
        if ($request->has('category')) {
            $data = $data->where('category_id', $value);
        }

        //filter payment method
        $value = $request->payment_method;
        if ($request->has('payment_method')) {
            $data = $data->where('payment_method', $value);
        }

        //filter unpaid
        $value = $request->unpaid;
        if ($request->has('unpaid')) {
            if ($value == '1') {
                $data = $data->where(function ($q) {
                    $q->where('balance_total_unpaid', '=', '0.00');
                    $q->orWhere('balance_total_unpaid', '');
                    $q->orWhereNull('balance_total_unpaid');
                });
            } else {
                $data = $data->where('balance_total_unpaid', '>', '0.00');
            }
        }

        //filter is expired
        $value = $request->expired;
        if ($request->has('expired')) {
            if ($value == '1') {
                $data = $data->where(function ($q) {
                    $q->where('balance_count_expired', '>', '0');
                    $q->where('balance_count_expired', '<>', '0');
                });
            } else {
                $data = $data->where(function ($q) {
                    $q->where('balance_count_expired', '=', '0');
                    $q->orWhere('balance_count_expired', '');
                    $q->orWhereNull('balance_count_expired');
                });
            }
        }

        $providers = $data->orderBy('balance_total_unpaid', 'desc')
            ->get();


        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 10,
            'margin_right'  => 10,
            'margin_top'    => 30,
            'margin_bottom' => 20,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);

        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        $data = [
            'providers'         => $providers,
            'documentTitle'     => 'Conta Corrente Fornecedores',
            'documentSubtitle'  => 'Emissão em ' . date('Y-m-d'),
            'view'              => 'admin.printer.billing.balance.summary_provider'
        ];

        $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write

        if ($returnMode == 'string') {
            return $mpdf->Output('Conta Corrente Fornecedores - ' . date('Y-m-d') . '.pdf', 'S'); //string
        }

        if (Setting::get('open_print_dialog_docs') && $returnMode != 'string') {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Conta Corrente Fornecedores - ' . date('Y-m-d') . '.pdf', 'I'); //output to screen

        exit;
    }

    /**
     * Filter Source
     * @param $query
     * @param bool $isScheduled
     * @return mixed
     */
    public function scopeFilterSource($query, $isScheduled = false)
    {
        return $query->where(function ($q) use ($isScheduled) {
            if (!$isScheduled) {
                $q->whereNull('is_scheduled');
            } else {
                $q->whereNotNull('is_scheduled');
            }
            $q->whereNull('source');
            $q->orWhere('source', config('app.source'));
        });
    }

    /**
     * Filter Agency
     * @param $query
     * @param Array $agencies
     * @return mixed
     */
    public function scopeFilterAgencies($query, $agencies = null)
    {
        if (!empty($agencies)) {
            if (!is_array($agencies)) {
                // Cast to Array
                $agencies = [$agencies];
            }

            return $query->whereHas('provider', function ($q) use ($agencies) {
                return $q->whereIn('agency_id', $agencies);
            });
        }
    }

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function lines()
    {
        return $this->hasMany('App\Models\PurchaseInvoiceLine', 'invoice_id', 'id');
    }

    public function payment_notes()
    {
        return $this->belongsToMany('App\Models\PurchasePaymentNote', 'purchase_payment_note_invoices', 'invoice_id', 'payment_note_id');
        //return $this->hasMany('App\Models\PurchasePaymentNoteInvoice', 'invoice_id', 'id');
    }

    public function provider()
    {
        return $this->belongsTo('App\Models\Provider', 'provider_id');
    }

    public function bank()
    {
        return $this->belongsTo('App\Models\Bank', 'bank_id');
    }

    public function payment()
    {
        return $this->belongsTo('App\Models\PaymentMethod', 'payment_method_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function type()
    {
        return $this->belongsTo('App\Models\PurchaseInvoiceType', 'type_id', 'id');
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
    public function setProviderIdAttribute($value)
    {
        $this->attributes['provider_id'] = empty($value) ? null : $value;
    }

    public function setPaymentDateAttribute($value)
    {
        $this->attributes['payment_date'] = empty($value) ? null : $value;
    }

    public function setReceivedDateAttribute($value)
    {
        $this->attributes['received_date'] = empty($value) ? null : $value;
    }

    public function setPaymentUntilAttribute($value)
    {
        $this->attributes['payment_until'] = empty($value) ? null : $value;
    }

    public function setPaymentMethodAttribute($value)
    {
        $this->attributes['payment_method'] = empty($value) ? null : $value;
    }

    public function setAssignedTargetsAttribute($value)
    {
        $this->attributes['assigned_targets'] = empty($value) ? null : json_encode($value);
    }

    public function getAssignedTargetsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getNameAttribute()
    {
        if ($this->attributes['doc_type']) {
            return trans('admin/billing.types_code.' . $this->attributes['doc_type']) . ' ' . $this->attributes['doc_id'];
        }

        return $this->attributes['doc_id'];
    }
}
