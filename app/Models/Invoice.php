<?php

namespace App\Models;

use App\Models\Billing\Item;
use App\Models\Email\Email;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\Exports\BillingController;
use App\Models\InvoiceGateway\Base;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth, Setting, Mail, App, File, DB;
use Jenssegers\Date\Date;
use Mpdf\Mpdf;


class Invoice extends BaseModel
{

    use SoftDeletes;

    /**
     * @var null
     */
    public $apiKey = null;

    /**
     * Default target values
     */
    const TARGET_CUSTOMER_BILLING = 'CustomerBilling';
    const TARGET_INVOICE          = 'Invoice';
    
    const DOC_TYPE_FT             = 'invoice';
    const DOC_TYPE_FR             = 'invoice-receipt';
    const DOC_TYPE_FS             = 'simplified-invoice';
    const DOC_TYPE_FP             = 'proforma-invoice';
    const DOC_TYPE_NC             = 'credit-note';
    const DOC_TYPE_ND             = 'debit-note';
    const DOC_TYPE_RC             = 'receipt';
    const DOC_TYPE_RG             = 'regularization';
    const DOC_TYPE_GT             = 'transport-guide';
    const DOC_TYPE_INTERNAL_DOC   = 'internal-doc';
    const DOC_TYPE_NODOC          = 'nodoc';
    const DOC_TYPE_SIND           = 'sind';
    const DOC_TYPE_SINC           = 'sinc';

    const SOFTWARE_KEYINVOICE     = 'KeyInvoice';
    const SOFTWARE_SAGEX3         = 'SageX3';
    const SOFTWARE_ENOVO          = 'EnovoTms';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'invoices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source', 'gateway', 'target', 'target_id', 'customer_id', 'assigned_invoice_id', 'internal_code',
        'doc_id', 'doc_type', 'doc_series', 'doc_series_id', 'doc_date', 'due_date', 'reference', 'doc_after_payment',
        'total', 'total_vat', 'total_no_vat', 'total_discount', 'fuel_tax', 'irs_tax',
        'vat', 'billing_code', 'billing_name', 'billing_address', 'billing_zip_code', 'billing_city',
        'billing_country', 'billing_email', 'obs', 'is_draft', 'is_settle', 'is_particular', 'is_deleted',
        'delete_reason', 'delete_user', 'delete_date', 'credit_note_id', 'api_key', 'created_by', 'payment_condition',
        'payment_method', 'payment_date', 'payment_bank_id', 'doc_total', 'doc_vat', 'doc_subtotal', 'doc_total_pending', 'settle_method',
        'settle_date', 'settle_obs', 'mb_entity', 'mb_reference',  'mbw_phone', 'paypal_account', 'sepa_payment_id',
        'doc_total_credit', 'doc_total_debit', 'doc_total_balance', 'customer_balance', 'sort'
    ];

    /**
     * The attributes that are dates.
     *
     * @var array
     */
    protected $dates = ['date'];

    /**
     * Constructor
     *
     * @param $agencia
     * @param $cliente
     * @param $password
     */
    public function __construct($apiKey = null)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Return a namespace to a given Class
     *
     * @return string
     * @throws Exception
     */
    public function getNamespaceTo($class)
    {
        return InvoiceGateway\Base::getNamespaceTo($class);
    }


    /**
     * Set document number
     * @return string
     */
    public function setDocumentNo($save = false)
    {

        $invoice = $this;
        $year = date('y');

        $lastCode = Invoice::filterSource()
            ->where('doc_type', $invoice->doc_type)
            ->whereRaw('YEAR(created_at) = 20' . $year)
            ->orderByRaw('CAST(doc_id as unsigned) desc')
            ->first(['doc_id']);

        $docId = (int) @$lastCode->doc_id + 1;
        $internalCode = $year . str_pad($docId, 7, '0', STR_PAD_LEFT);

        $docSerie = trans('admin/billing.types_code.' . $invoice->doc_type) . '/' . $year;

        if ($invoice->doc_type == 'nodoc') {
            $docId = str_random(6);
        }

        if ($save) {
            $invoice->doc_id        = $docId;
            $invoice->doc_serie     = $docSerie;
            $invoice->internal_code = $internalCode;
            $invoice->save();
        }

        return [
            'doc_id'        => $docId,
            'doc_serie'     => $docSerie,
            'doc_serie_id'  => null,
            'internal_code' => $internalCode
        ];
    }

    /**
     * Import invoice documents from gateway
     *
     * @param [type] $customerId
     * @return void
     */
    public static function importFromGateway($customerId = null) {
        return Base::importDocuments($customerId);
    } 

   /**
     * Set document QR Code
     * @return string
     */
    public function getQRcode() {

        $invoice = $this;

        'A:516546333*B:507751892*C:PT*D:FR*E:N*F:20230719*G:34 40/788*H:JF5ZK7WF-788*I1:PT*I7:275.00*I8:63.25*N:63.25*O:338.25*Q:T+Vz*R:2864';

        $emissorVat    = @$invoice->company->vat ? @$invoice->company->vat : Setting::get('company_vat');
        $atLicenseCode = '2864';
        $date          = str_replace('-', '', $invoice->date);
        $fiscalSpace   = 'PT';

        //HASH deve ser preenchido com 0 se não existe

        $data = [];
        $data['A'] = $emissorVat;
    
        $data = [
            'A:'.$invoice->vat, //nif emissor
            'B:'.$invoice->vat, //nif adquirente
            'C:'.strtoupper($invoice->country), //espaço fiscal
            'D:'.$invoice->doc_type, //tipo documento
            'E:'.'N', //estado fatura
            'F:'.$date, //data AAAAMMDD
            'G:'.$invoice->doc_id, //numero fatura
            'H:'.$invoice->atcud, //codigo ATCUD
            'I1:'.$fiscalSpace, //espaço fiscal
            'I2:', //[opcional] base tributavel isenta de iva
            'I3:', //[opcional] base tributavel à taxa reduzida
            'I4:', //[opcional] iva tributavel à taxa reduzida
            'I3:', //[opcional] base tributavel à taxa intermédia
            'I4:', //[opcional] iva tributavel à taxa intermédia
            'I7:'.$invoice->doc_subtotal, //[opcional] base tributavel à taxa normal
            'I8:'.$invoice->doc_vat, //[opcional] iva tributavel à taxa normal
           // 'J1:'
            'N:'.$invoice->doc_vat,
            'O:'.$invoice->doc_total,
            'P:'.$invoice->irs_tax, //retencao na fonte
            'Q:'.$invoice->doc_hash,
            'R:'.$atLicenseCode
        ];


        $content = implode('*', $data);

        return $content;
    }

    /**
     * Preenche campo Balance da fatura
     *
     * @return void
     */
    public function updateBalanceFields() {
        
        $invoice = $this;


        //notas credito / recibos / regularizações - força a ter sinal negativo na base de dados
        if(($invoice->doc_type == Invoice::DOC_TYPE_NC 
            || $invoice->doc_type == Invoice::DOC_TYPE_RC 
            || $invoice->doc_type == Invoice::DOC_TYPE_RG
            || $invoice->doc_type == Invoice::DOC_TYPE_SINC)) {

            if($invoice->doc_total > 0.00) {
                $invoice->doc_total = $invoice->doc_total * -1;
            }

            if($invoice->doc_vat > 0.00) {
                $invoice->doc_vat = $invoice->doc_vat * -1;
            }

            if($invoice->doc_subtotal > 0.00) {
                $invoice->doc_subtotal = $invoice->doc_subtotal * -1;
            }

            if($invoice->doc_total_pending > 0.00) {
                $invoice->doc_total_pending = $invoice->doc_total_pending * -1;
            }

            if($invoice->total > 0.00) {
                $invoice->total = $invoice->total * -1;
            }

            if($invoice->total_vat > 0.00) {
                $invoice->total_vat = $invoice->total_vat * -1;
            }

            if($invoice->total_no_vat > 0.00) {
                $invoice->total_no_vat = $invoice->total_no_vat * -1;
            }
        }

        if(in_array($invoice->doc_type, [Invoice::DOC_TYPE_NC, Invoice::DOC_TYPE_RC, Invoice::DOC_TYPE_RG, Invoice::DOC_TYPE_SINC])) {
            $invoice->doc_total_debit   = null;
            $invoice->doc_total_credit  = $invoice->doc_total;
            $invoice->doc_total_balance = $invoice->doc_total_credit;
        } elseif(in_array($invoice->doc_type, [Invoice::DOC_TYPE_FR, Invoice::DOC_TYPE_FS])) { //são simultaneamente fatura e recibo
            $invoice->doc_total_debit   = $invoice->doc_total;
            $invoice->doc_total_credit  = $invoice->doc_total * -1;
            $invoice->doc_total_balance = 0;
        } else {
            $invoice->doc_total_debit   = $invoice->doc_total;
            $invoice->doc_total_credit  = null;
            $invoice->doc_total_balance = $invoice->doc_total_debit;
        }

        if($invoice->doc_type == Invoice::DOC_TYPE_RC 
            || $invoice->doc_type == Invoice::DOC_TYPE_RG
            || $invoice->doc_type == Invoice::DOC_TYPE_FR) {
            $invoice->is_settle = true;
        }

        return $invoice;
    }

    /**
     * Atualiza campos de conta corrente na ficha do cliente
     *
     * @param [type] $customerId
     * @return void
     */
    /* public static function updateCustomerBalance($customerId) {

        $ignoredDocs = [Invoice::DOC_TYPE_FR, Invoice::DOC_TYPE_FS, Invoice::DOC_TYPE_NODOC, Invoice::DOC_TYPE_GT];

        $balanceTotal = Invoice::where('customer_id', $customerId)
            ->whereNotIn('doc_type', $ignoredDocs) //para calculo da conta corrente, ignora documentos que se anulam mutuamente e documentos sem relevancia (nodoc)
            ->isDeleted(false)
            ->sum('doc_total');

        $docsExpired = Invoice::where('customer_id', $customerId)
            ->whereNotIn('doc_type', $ignoredDocs) //para calculo da conta corrente, ignora documentos que se anulam mutuamente e documentos sem relevancia (nodoc)
            ->where('due_date', '<', date('Y-m-d'))
            ->isDeleted(false)
            ->isSettle(false)
            ->count();

        $allDocs = Invoice::where('customer_id', $customerId)
            ->whereNotIn('doc_type', $ignoredDocs) //para calculo da conta corrente, ignora documentos que se anulam mutuamente e documentos sem relevancia (nodoc)
            ->isDeleted(false)
            ->first([
                DB::raw('count(doc_total) as count'),
                DB::raw('sum(doc_total) as balance'),
                DB::raw('sum(doc_total_debit) as debit'),
                DB::raw('sum(doc_total_credit) as credit')
            ]);
        
        $updateArr = [
            'total_unpaid'  => $allDocs->balance,
            'total_credit'  => $allDocs->credit,
            'total_debit'   => $allDocs->debit,
            'count_unpaid'  => $allDocs->count,
            'count_expired' => $docsExpired
        ];

        //Customer::where('id', $customerId)->update($updateArr);

        return $updateArr;
    } */

    /**
     * Identify and remove duplicate invoices
     * This ocorres on oldest versions by syncBalanceAccount
     *
     * @return void
     */
    public static function identifyRemovesDuplicates($debug = false){

       //OBTEM DUPLICADOS
       $duplicates = Invoice::where('is_draft', 0)
            ->groupBy('doc_date')
            ->groupBy('doc_id')
            ->groupBy('customer_id')
            ->having('count', '>', 1)
            ->get([
                'id', 'doc_date', 'doc_id', 'customer_id', DB::raw('count(*) as count')
            ]);

        if($duplicates->isEmpty()) {
            return false;
        }

        $deleteIds = [];
        foreach($duplicates as $duplicate) {
            
            //NORMALIZAR INFORMAÇÃO DA SÉRIE
            //primeiro tem de normalizar todas as faturas, garantindo que para os duplicados o campo doc_serie e doc_serie_id é igual em todos
            //isto ocorre porque anteriormente não era registado nas faturas estes 2 campos e devido a isso comecou a duplicar as faturas sempre que são importadas
            //pois o importador faz consulta se existe na base de dados a fatura com o doc_id e doc_serie_id indicado.
                $invoices = Invoice::where('doc_date', $duplicate->doc_date)
                ->where('doc_id', $duplicate->doc_id)
                ->where('customer_id', $duplicate->customer_id)
                ->orderBy('doc_series', 'desc') //fica em 1º faturas com doc_serie preenchido
                ->get(['id', 'doc_series', 'doc_series_id']);
            

                $docSerie = $docSerieId = null;
                foreach($invoices as $invoice) {
                    if(!empty($invoice->doc_series)) {
                        $docSerie   = $invoice->doc_series;
                        $docSerieId = $invoice->doc_series_id;
                    } else {
                        $invoice->update([
                            'doc_series'    => $docSerie,
                            'doc_series_id' => $docSerieId
                        ]);
                    }
                }
            
                if($debug) {
                    echo '<h4>['.$duplicate->count.'] Cliente '.$duplicate->customer_id.'  = '.$duplicate->doc_id .' - '.$duplicate->doc_date.'</h4>';
                    echo '<table>';
                }

            //OBTEM FATURAS DUPLICADAS
            //exclui as faturas de CustomerBilling
            $invoices = Invoice::with('lines')
                ->where('doc_date', $duplicate->doc_date)
                ->where('doc_id', $duplicate->doc_id)
                ->where('customer_id', $duplicate->customer_id)
                ->where('target', '<>', 'CustomerBilling') //se é CustomerBilling é porque foi criada diretamente pelo programa. Isto vai excluir o registo verdadeiro das lista de faturas
                ->orderBy('target', 'asc') //fica em 1º faturas com subtotal 0
                ->orderBy('doc_subtotal', 'asc') //fica em 1º faturas com subtotal 0
                ->get();
                
                
                if($debug) {
                    echo '<tr style="text-align: left">';
                    echo '<th style="width: 100px">ID</td>';
                    echo '<th style="width: 100px">target</td>';
                    echo '<th style="width: 100px">Cliente</td>';
                    echo '<th style="width: 100px">Serie</td>';
                    echo '<th style="width: 100px">DocID</td>';
                    echo '<th style="width: 100px">Subtotal</td>';
                    echo '<th style="width: 100px">API</td>';
                    echo '<th style="width: 100px">Linhas</td>';
                    echo '<tr>';
                }
                
            if($invoices->count() == $duplicate->count ) { 
                //se depois de excluir o customerBilling o total de faturas é 
                //igual ao total de duplicados, então deixa a 1ª fatura e apaga as outras.
                    
                foreach($invoices as $key => $invoice) {

                    if($debug) {
                        echo '<tr style="color: green">';
                        echo '<td>'.$invoice->id.'</td>';
                        echo '<td>'.$invoice->target.'</td>';
                        echo '<td>'.@$invoice->customer_id.'</td>';
                        echo '<td>'.$invoice->doc_series.'</td>';
                        echo '<td>'.$invoice->doc_id.'</td>';
                        echo '<td>'.$invoice->doc_subtotal.'</td>';
                        echo '<td>'.$invoice->apikey.'</td>';
                        echo '<td>'.@$invoice->lines->count().'</td>';
                        echo '<tr>'; 
                    }

                    if($key) { //ignora a key 0 (1ª fatura das 2 duplicadas iguais)
                        $deleteIds[] = $invoice->id;
                    }
                }
                
                } else {
                    foreach($invoices as $key => $invoice) {

                        if($debug) {
                            echo '<tr style="color: red">';
                            echo '<td>'.$invoice->id.'</td>';
                            echo '<td>'.$invoice->target.'</td>';
                            echo '<td>'.@$invoice->customer_id.'</td>';
                            echo '<td>'.$invoice->doc_series.'</td>';
                            echo '<td>'.$invoice->doc_id.'</td>';
                            echo '<td>'.$invoice->doc_subtotal.'</td>';
                            echo '<td>'.$invoice->apikey.'</td>';
                            echo '<td>'.@$invoice->lines->count().'</td>';
                            echo '<tr>'; 
                        }

                        $deleteIds[] = $invoice->id;
                    }
                }

                if($debug) {
                    echo '</table>';
                }
        }
        
        if(!empty($deleteIds)) {
            InvoiceLine::whereIn('invoice_id', $deleteIds)->forceDelete();
            Invoice::whereIn('id', $deleteIds)->forceDelete();
            
            return $deleteIds;
        }
   
        return false;
    }

    /**
     * Return document
     *
     * @param $id
     * @param $type
     * @return  mixed
     */
    public function getDocument($id, $type, $serie = null)
    {

        $class = $this->getNamespaceTo('Document');

        $invoice = new $class($this->apiKey);

        return $invoice->getDocument($id, $type, $serie);
    }

    /**
     * Return document PDF in base64
     *
     * @param $id
     * @param $type
     * @param null $docSerie
     * @param bool $acceptStorage [true = get from storage, false = get from API]
     * @return mixed
     */
    public function getDocumentPdf($id, $type, $docSerie = null, $acceptStorage = true)
    {

        $class = $this->getNamespaceTo('Document');

        $invoice = new $class($this->apiKey);

        return $invoice->getDocumentPdf($id, $type, $docSerie, $acceptStorage);
    }

    /**
     * Add Payment MB
     *
     * @param $id
     * @param $type
     * @param null $docSerie
     * @param bool $acceptStorage [true = get from storage, false = get from API]
     * @return mixed
     */
    public function addPaymentMb($docId, $docSerie = null)
    {

        $class = $this->getNamespaceTo('Document');

        $invoice = new $class($this->apiKey);

        return $invoice->addPaymentMB($docId, $docSerie);
    }

    /**
     * Return document
     *
     * @param $id
     * @param $type
     * @return mixed
     */
    public function getZipDocuments($docType, $month, $year, $mode = 'pdf', $startDate = null, $endDate = null)
    {

        $class = $this->getNamespaceTo('Document');

        $invoice = new $class($this->apiKey);

        return $invoice->getZipDocuments($docType, $month, $year, $mode, $startDate, $endDate);
    }

    /**
     * Delete a saved document
     *
     * @param $type
     * @param $data - obrigatório indicar o campo NIF e Código do sistema de faturação
     * @return mixed
     */
    public function destroyDocument($id, $type, $data, $returnFullData = false)
    {

        $class = $this->getNamespaceTo('Document');

        $invoice = new $class($this->apiKey);

        return $invoice->deleteDocument($id, $type, $data, $returnFullData);
    }


    /**
     * Create debit note from gven 
     *
     * @param [type] $data
     * @return void
     */
    public function autocreateDebitNote($data)
    {
        return $this->autocreateCreditNote($data);
    }

    /**
     * Create credit note from a given invoice
     *
     * @param $type
     * @param $data - obrigatório indicar o campo NIF e Código do sistema de faturação
     * @return mixed
     */
    public function autocreateCreditNote($data)
    {

        if (!hasModule('invoices')) {
            throw new \Exception('Módulo de faturação não contratado.');
        }

        $invoice  = $this;
        $customer = $invoice->customer;
        $receipts = $invoice->receipts;
        $docType  = $invoice->doc_type == Invoice::DOC_TYPE_NC ? 'debit-note' : 'credit-note'; //se anula nota credito, cria nota debito

        if ($invoice->is_reversed || $invoice->is_deleted  || !empty($invoice->credit_note_id)) {
            throw new \Exception('Este documento já foi estornado.');
        } elseif (in_array($invoice->doc_type, ['receipt', 'proforma-invoice', 'internal-doc'])) {
            throw new \Exception('Não pode estornar este tipo de documento.');
        } elseif ($receipts && !$receipts->isEmpty()) {
            throw new \Exception('Este já não pode ser estornado porque tem recibos associados.');
        } elseif(empty(@$data['doc_serie'])) {
            if($docType == 'credit-note') {
                throw new \Exception('Obrigatório indicar a série da nota de débito.');
            }
            throw new \Exception('Obrigatório indicar a série da nota de crédito.');
        }

        $draft      = @$data['draft'];
        $reference  = trans('admin/billing.types_code.'.$this->doc_type). ' '. $this->doc_series_id . '/' . $this->doc_id;
        $docDate    = @$data['credit_date'] ? $data['credit_date'] : date('Y-m-d');

        $paymentCondition = $this->payment_condition;
        if (in_array($paymentCondition, ['prt', 'wallet', 'dbt'])) {
            $dueDays = PaymentCondition::getDays($paymentCondition); //obtem dias de pagamento pela base de dados
        } else {
            $dueDays = str_replace('d', '', $paymentCondition);
        }

        $dt = new Date($docDate);
        $dueDate = $dt->addDays($dueDays)->format('Y-m-d');
 
        $creditNote = $this->replicate();
        $creditNote->api_key            = @$data['apiKey'];
        $creditNote->reference          = $reference;
        $creditNote->target             = 'Invoice';
        $creditNote->target_id          = $this->id;
        $creditNote->doc_type           = $docType;
        $creditNote->doc_id             = null;
        $creditNote->doc_series         = null;
        $creditNote->doc_series_id      = null;
        $creditNote->doc_date           = $docDate;
        $creditNote->due_date           = $dueDate;
        $creditNote->is_draft           = 1;
        $creditNote->is_settle          = 0;
        $creditNote->is_deleted         = 0;
        $creditNote->is_reversed        = 0;
        $creditNote->is_scheduled       = 0;
        $creditNote->is_particular      = 0;
        $creditNote->delete_date        = null;
        $creditNote->delete_user        = null;
        $creditNote->delete_reason      = null;
        $creditNote->settle_method      = null;
        $creditNote->settle_date        = null;
        $creditNote->doc_after_payment  = null;
        $creditNote->mb_entity          = null;
        $creditNote->mb_reference       = null;
        $creditNote->mbw_phone          = null;
        $creditNote->sort               = null;
        $creditNote->obs                = 'Motivo estorno: '.$data['credit_reason'];

        //inverte o sinal da linha caso estejamos a anular uma nota credito
        if($docType == 'debit-note' && $invoice->doc_type == 'credit-note') {
            if($creditNote->total < 0.00) {
                $creditNote->total = $creditNote->total * -1;
            }

            if($creditNote->total_discount < 0.00) {
                $creditNote->total_discount = $creditNote->total_discount * -1;
            }

            if($creditNote->doc_subtotal < 0.00) {
                $creditNote->doc_subtotal = $creditNote->doc_subtotal * -1;
            }

            if($creditNote->doc_vat < 0.00) {
                $creditNote->doc_vat = $creditNote->doc_vat * -1;
            }

            if($creditNote->doc_total < 0.00) {
                $creditNote->doc_total = $creditNote->doc_total * -1;
            }

            if($creditNote->doc_total_debit < 0.00) {
                $creditNote->doc_total_debit = $creditNote->doc_total_debit * -1;
            }

            if($creditNote->doc_total_credit < 0.00) {
                $creditNote->doc_total_credit = $creditNote->doc_total_credit * -1;
            }

            if($creditNote->doc_total_balance < 0.00) {
                $creditNote->doc_total_balance = $creditNote->doc_total_balance * -1;
            }
        }

        $creditNote->save();
        
        //adiciona linhas
        $docLines = $this->lines;
        foreach($docLines as $line) {
            $creditLine = new InvoiceLine();
            $creditLine->fill($line->toArray());
            $creditLine->invoice_id = $creditNote->id;

            //inverte o sinal da linha
            if($docType == 'debit-note' && $invoice->doc_type == 'credit-note') {
                if($creditLine->total_price < 0.00) {
                    $creditLine->total_price = $creditLine->total_price * -1;
                }
                if($creditLine->subtotal < 0.00) {
                    $creditLine->subtotal = $creditLine->subtotal * -1;
                }
            }

            $creditLine->save();
        }

        //Finaliza e submete documento
        if (Invoice::getInvoiceSoftware() == Invoice::SOFTWARE_ENOVO) {
            $documentId                = $invoice->setDocumentNo();
            $creditNote->doc_id        = @$documentId['doc_id'];
            $creditNote->doc_series    = @$documentId['doc_serie'];
            $creditNote->doc_series_id = @$documentId['doc_serie_id'];
            $creditNote->internal_code = @$documentId['internal_code'];
            $creditNote->is_draft      = 0;
            $creditNote->api_key       = null;
            $creditNote->is_settle     = null;
        } else {

            try {
                $input = [
                    'vat'               => $creditNote->vat,
                    'docdate'           => $creditNote->doc_date,
                    'duedate'           => $creditNote->due_date,
                    'docref'            => $creditNote->reference,
                    'billing_code'      => $creditNote->billing_code,
                    'billing_name'      => $creditNote->billing_name,
                    'billing_address'   => $creditNote->billing_address,
                    'billing_zip_code'  => $creditNote->billing_zip_code,
                    'billing_city'      => $creditNote->billing_city,
                    'irs_tax'           => $creditNote->irs_tax,
                    'total_discount'    => $creditNote->total_discount,
                    'id'                => $creditNote->id,
                    'target'            => $creditNote->target,
                    'payment_method'    => $creditNote->payment_method,
                    'payment_condition' => $creditNote->payment_condition,
                    'obs'               => $creditNote->obs,
                ];

                $header = $creditNote->prepareDocumentHeader($input, $customer);
                $lines  = $creditNote->prepareDocumentLines();

                $documentId = $creditNote->createDraft($docType, $header, $lines);

                $creditNoteDocId = null;
                if (!$draft) {
                    $creditNoteDocId = $creditNote->convertDraftToDoc($documentId, $docType);
                } 

            } catch(\Exception $e) {
                $creditNote->lines->forceDelete();
                $creditNote->forceDelete();

                throw new \Exception($e->getMessage()); //força para que no salescontroller apareça o erro
            }
        }

        //atualiza informação da fatura inicial
        if(@$creditNote->exists) {
            $isSettle = 0;

            if(in_array($invoice->doc_type, ['invoice-receipt', 'simplified-invoice'])) {
                $isSettle = 1; //se é fatura-recibo ou fat. simplificada tem de ser forçado a que esteja marcada como paga (uma FR/FS está sempre paga). 
            }

            $invoice->delete_date    = date('Y-m-d H:i:s');
            $invoice->delete_reason  = @$data['credit_reason'];
            $invoice->delete_user    = Auth::check() ? Auth::user()->id : null;
            $invoice->credit_note_id = $creditNote->id;
            $invoice->is_deleted     = 0;
            $invoice->is_reversed    = 1;
            $invoice->is_settle      = $isSettle; //marca como não paga a fatura inicial
            $invoice->save();
        }
        
        return $creditNote;
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

        //Get document serie
        $invoiceSerie = InvoiceSerie::remember(config('cache.query_ttl'))
            ->cacheTags(InvoiceSerie::CACHE_TAG)
            ->filterSource()
            ->where('doc_type', $docType)
            ->where('api_key', $this->apiKey)
            ->first();

        if (empty($invoiceSerie)) { //get serie from api if serie not found
            $serieDetails = $invoice->getCurrentSerie($docType);

            if ($serieDetails) {
                $invoiceSerie = new InvoiceSerie();
                $invoiceSerie->source   = config('app.source');
                $invoiceSerie->doc_type = $docType;
                $invoiceSerie->code     = $serieDetails['code'];
                $invoiceSerie->name     = $serieDetails['name'];
                $invoiceSerie->serie_id = $serieDetails['id'];
                $invoiceSerie->api_key  = $serieDetails['api_key'];
                $invoiceSerie->save();
            }
        }

        if (config('app.source') == 'girocarga') {
            //imprime nº cliente na referencia
            $header['docref'] = @$header['code'];
        }

        $draftId = $invoice->createDraft($docType, $header);

        $docTotal = 0;
        foreach ($lines as $line) {
            $docTotal += ($line['qt'] * $line['price']);
            $invoice->insertDraftLine($draftId, $docType, $line);
        }

        //store invoice
        $this->source        = config('app.source');
        $this->gateway       = Setting::get('invoice_software') ? Setting::get('invoice_software') : 'KeyInvoice';
        $this->customer_id   = $header['customer_id'];
        $this->target        = $this->target ? $this->target : 'invoice';
        $this->doc_id        = $draftId;
        $this->doc_type      = $docType;
        $this->is_draft      = 1;
        $this->total         = $docTotal;
        $this->due_date      = $header['duedate'];
        $this->doc_date      = $header['docdate'];
        $this->reference     = $header['docref'];
        $this->doc_series    = @$invoiceSerie->code;
        $this->doc_series_id = @$invoiceSerie->serie_id;
        $this->api_key       = $this->apiKey;


        $this->save();

        return $draftId;
    }

    /**
     * Create a draft for receipt
     *
     * @param $docType
     * @param $header
     * @param $lines
     * @return mixed
     */
    public function createReceiptDraft($header, $lines, $apiKey = null)
    {
        $this->apiKey = $this->apiKey ?: $this->api_key;
        if (!empty($apiKey)) {
            $this->apiKey = $apiKey;
        }

        $class = $this->getNamespaceTo('Document');

        $invoice = new $class($this->apiKey);

        //Get document serie
        $invoiceSerie = InvoiceSerie::remember(config('cache.query_ttl'))
            ->cacheTags(InvoiceSerie::CACHE_TAG)
            ->filterSource()
            ->where('doc_type', 'receipt')
            ->where('api_key', $this->apiKey)
            ->first();

        if (empty($invoiceSerie)) { //get serie from api if serie not found
            $serieDetails = $invoice->getCurrentSerie('receipt');

            if ($serieDetails) {
                $invoiceSerie = new InvoiceSerie();
                $invoiceSerie->source   = config('app.source');
                $invoiceSerie->doc_type = 'receipt';
                $invoiceSerie->code     = $serieDetails['code'];
                $invoiceSerie->name     = $serieDetails['name'];
                $invoiceSerie->serie_id = $serieDetails['id'];
                $invoiceSerie->api_key  = $serieDetails['api_key'];
                $invoiceSerie->save();
            }
        }

        $draftId = $invoice->createReceiptDraft($header, @$invoiceSerie->serie_id);

        $docTotal = 0;
        foreach ($lines as $line) {
            $invoice->insertReceiptLine($draftId, $line, $invoiceSerie->serie_id);
        }

        //store invoice
        $this->source        = config('app.source');
        $this->gateway       = Setting::get('invoice_software') ? Setting::get('invoice_software') : 'KeyInvoice';
        $this->target        = $this->target ? $this->target : 'invoice';
        $this->doc_id        = $draftId;
        $this->doc_type      = 'receipt';
        $this->is_draft      = 1;
        $this->total         = $docTotal;
        $this->due_date      = @$header['docdate'];
        $this->doc_date      = @$header['docdate'];
        $this->reference     = @$header['reference'];
        $this->doc_series    = @$invoiceSerie->code;
        $this->doc_series_id = @$invoiceSerie->serie_id;
        $this->api_key       = $this->apiKey;

        /*if (config('app.source') == 'girocarga') {
            //imprime nº cliente na referencia
            $this->reference     = @$header['code'] . ($header['docref'] ? '/Ref: ' . $header['docref'] : '');
        }*/

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
    public function convertDraftToDoc($draftId, $docType, $docSerie = null)
    {

        $class = $this->getNamespaceTo('Document');

        $invoice = new $class($this->apiKey);

        if ($docType == 'receipt') {
            $invoiceId = $invoice->convertDraftToReceipt($draftId, $docSerie);
        } else {
            $invoiceId = $invoice->convertDraftToDoc($draftId, $docType, $docSerie);
        }

        $this->update([
            'is_draft' => 0,
            'doc_id'   => $invoiceId
        ]);

        return $invoiceId;
    }

    /**
     * Convert receipt from draft
     * @param $customer
     * @param $invoices
     * @param $data
     * @param null $apiKey
     * @return int
     */
    public function convertDraftToReceipt($draftId, $docSerie)
    {
        $class = $this->getNamespaceTo('Document');

        $receipt = new $class($this->apiKey);

        $receiptId = $receipt->convertDraftToReceipt($draftId, $docSerie);

        $this->update([
            'is_draft' => 0,
            'doc_id'   => $receiptId
        ]);

        return $receiptId;
    }

    /**
     * communicate AT
     *
     * @param $type
     * @param $data - obrigatório indicar o campo NIF e Código do sistema de faturação
     * @return mixed
     */
    public function communicateAT($docId, $docType, $docSerie = null)
    {

        $class = $this->getNamespaceTo('Document');

        $invoice = new $class($this->apiKey);

        return $invoice->communicateAT($docId, $docType, $docSerie);
    }

    /**
     * Create a draft
     *
     * @param $docType
     * @param $header
     * @param $lines
     * @return mixed
     */
    public static function createTransportGuideFromShipment($shipment, $data = null)
    {

        $docType = 'transport-guide';

        $customer = $shipment->customer;
        $customer->vat = $customer->vat == '999999990' || empty($customer->vat) ? null : $customer->vat;

        $shippingDate = @$data['shipping_date'] ? $data['shipping_date'] : $shipment->shipping_date->format('Y-m-d');
        $deliveryDate = new Date($shipment->delivery_date);
        $deliveryDate = $deliveryDate->format('Y-m-d');

        $docDate = @$data['docdate'] ? $data['docdate'] : date('Y-m-d');
        $dueDate = @$data['duedate'] ? $data['duedate'] : $deliveryDate;


        if ($dueDate < $docDate) {
            throw new \Exception('Impossível emitir guia: Data de descarga ultrapassada.');
        }


        $transportDetails = [
            'vehicle'            => $shipment->vehicle,
            'delivery_date'      => $deliveryDate,
            'shipping_date'      => $shippingDate,
            'sender_address'     => $shipment->sender_address,
            'sender_zip_code'    => $shipment->sender_zip_code,
            'sender_city'        => $shipment->sender_city,
            'sender_country'     => $shipment->sender_country,
            'recipient_address'  => $shipment->recipient_address,
            'recipient_zip_code' => $shipment->recipient_zip_code,
            'recipient_city'     => $shipment->recipient_city,
            'recipient_country'  => $shipment->recipient_country,
        ];

        $header = [
            'nif'           => $customer->vat,
            'obs'           => @$data['obs'],
            'docdate'       => $docDate,
            'duedate'       => $dueDate,
            'docref'        => @$data['reference'],
            'code'          => $customer->code,
            'name'          => $customer->billing_name,
            'address'       => $customer->billing_address,
            'zip_code'      => $customer->billing_zip_code,
            'city'          => $customer->billing_city,
            'printComment'  => '',
            'taxRetention'  => null,
            'totalDiscount' => null,
            'customer_id'   => $customer->id,
            'target_id'     => $shipment->id,
            'target'        => 'Shipment',
            'transport'     => $transportDetails
        ];


        $lines = [];
        $taxRate = Setting::get('vat_rate_normal');
        if (!empty($data['line'])) {
            foreach ($data['line'] as $line) {

                $exemption = '';
                if (in_string('M', $line['tax_rate'])) {
                    $exemption = $line['tax_rate'];
                    $taxRate = '0';
                }


                $lines[] = [
                    'ref'       => '',
                    'qt'        => $line['qty'],
                    'price'     => ($line['price'] / $line['qty']),
                    'tax'       => $taxRate,
                    'prodDesc'  => $line['description'],
                    'discount'  => 0,
                    'exemption' => $exemption,
                    'obs'       => ''
                ];
            }
        } else {

            if (!$shipment->pack_dimensions->isEmpty()) {
                foreach ($shipment->pack_dimensions as $line) {

                    $exemption = '';
                    if (in_string('M', $line['tax_rate'])) {
                        $exemption = $line['tax_rate'];
                        $taxRate = '0';
                    }

                    $lines[] = [
                        'ref'       => '', //$line->reference,
                        'qt'        => $line->qty,
                        'price'     => $line->total_price,
                        'tax'       => $taxRate,
                        'prodDesc'  => $line->description,
                        'discount'  => 0,
                        'exemption' => $exemption,
                        'obs'       => ''
                    ];
                }
            } else {
                $lines[] = [
                    'ref'       => '', //$line->reference,
                    'qt'        => $shipment->volumes,
                    'price'     => $shipment->total_price,
                    'tax'       => $shipment->getTaxRate(),
                    'prodDesc'  => 'Bens diversos',
                    'discount'  => 0,
                    'exemption' => '',
                    'obs'       => ''
                ];
            }
        }

        $invoiceModel = new Invoice();
        $draftId = $invoiceModel->createDraft($docType, $header, $lines);

        if ($draftId) {
            $invoiceId = $invoiceModel->convertDraftToDoc($draftId, $docType, null);
        }

        return $invoiceId;
    }

    /**
     * Sotore invoice on sage X3
     *
     * @param $type
     * @param $data - obrigatório indicar o campo NIF e Código do sistema de faturação
     * @return mixed
     */
    public function storeSageX3($input, $customer)
    {

        $class = $this->getNamespaceTo('Document');
        $invoice = new $class();
        $docId = $invoice->createInvoice($input, $customer);

        return $docId;
    }

    /**
     * Check if invoice can be submitted by webservice
     *
     * @param [type] $docType
     * @return boolean
     */
    public static function canSubmitWebservice($docType) {

        if(!hasModule('invoices')
         || Invoice::getInvoiceSoftware() == Invoice::SOFTWARE_ENOVO
         || in_array($docType, [Invoice::DOC_TYPE_NODOC, Invoice::DOC_TYPE_INTERNAL_DOC, Invoice::DOC_TYPE_FP])) {
            return false;
        }

        return true;
    }

    /**
     * Get API active keys
     *
     * @param $draftId
     * @param $docType
     * @return mixed
     */
    public static function getApiKeys($companyId = null)
    {
        $apiKeys = App\Models\Billing\ApiKey::remember(config('cache.query_ttl'))
            ->cacheTags(App\Models\Billing\ApiKey::CACHE_TAG)
            ->filterSource()
            ->isActive();

        if ($companyId) {
            $apiKeys = $apiKeys->where('company_id', $companyId);
        }

        $apiKeys = $apiKeys->ordered()
            ->pluck('name', 'token')
            ->toArray();

        return $apiKeys;
    }

    /**
     * List vat rates
     * @param bool $onlySales
     * @return array
     */
    public static function getVatTaxes($onlySales = true, $idsAsKeys = false)
    {
        $vatRates = App\Models\Billing\VatRate::remember(config('cache.query_ttl'))
            ->cacheTags(App\Models\Billing\VatRate::CACHE_TAG)
            ->filterSource();

        if ($onlySales) { //so faturas de compra
            $vatRates = $vatRates->where('is_sales', true)->isActive();
        } else {
            $vatRates = $vatRates->where('is_active', true); //as taxas de compra nao tem webservice
        }

        $vatRates = $vatRates->ordered()
            ->get();


        $arr = [];
        foreach ($vatRates as $vatRate) {
            if ($idsAsKeys) {
                $arr[$vatRate->id] = $vatRate->name_abrv;
            } else {
                if (in_array($vatRate->subclass, ['ise', 'na'])) {
                    $arr[$vatRate->exemption_reason] = $vatRate->name_abrv;
                } else {
                    $arr[$vatRate->code] = $vatRate->name_abrv;
                }
            }
        }

        return $arr;
    }

    /**
     * List exemption reasons
     * @return array
     */
    public static function getExemptionReasons($zone = 'pt')
    {
        $zone = substr($zone, 0, 2);
        $reasons = trans('admin/billing.exemption-reasons.' . $zone);

        $exemptions = [];
        foreach ($reasons as $code => $reason) {
            $exemptions[$code] = $code . ' - ' . $reason;
        }

        return $exemptions;
    }

    public static function prefillObs($obsText, $invoice = null)
    {
        if(!empty($invoice)) {
            $docDate = new Date($invoice->doc_date);
            $obsText = str_replace(':year', $docDate->year, $obsText);
            $obsText = str_replace(':month', trans('datetime.month.' . $docDate->month), $obsText);

            if(@$invoice->reference_period) {
                $obsText = str_replace(':period', @$invoice->reference_period, $obsText);
            }

            if(Setting::get('invoice_obs_allowance_percent') && @$invoice->doc_subtotal) {
                $allowancePercent = Setting::get('invoice_obs_allowance_percent');
                $allowancePrice   = $invoice->doc_subtotal * ($allowancePercent / 100);

                $obsText = str_replace(':allowanceprice', number($allowancePrice), $obsText);
                $obsText = str_replace(':allowancepercent', $allowancePercent, $obsText);
            }

            unset($invoice->reference_period);
        }

        return $obsText;
    }

    /**
     * Prepare invoice header data array
     *
     * @return \Illuminate\Http\Response
     */
    public function prepareDocumentHeader($input, $customer, $target = null)
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
            'printComment'  => Setting::get('invoice_footer_obs'),
            'taxRetention'  => !empty(@$input['irs_tax']) ? floatval($input['irs_tax']) : null,
            'totalDiscount' => !empty(@$input['total_discount']) ? floatval($input['total_discount']) : null,
            'customer_id'   => $customer->id,
            'target_id'     => @$target['id'],
            'target'        => @$target['target'] ? @$target['target'] : 'Invoice',
            'paymentMethod'     => @$input['payment_method'],
            'paymentCondition'  => @$input['payment_condition'],
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

            $description = $line->description;
            /*$description = str_replace(':month', trans('datetime.list-month.' . $dueDate->month), $description);
            $description = str_replace(':year', $dueDate->year, $description);*/

            $arr[] = [
                'ref'       => '', //$line->reference,
                'qt'        => $line->qty,
                'price'     => $line->total_price + $line->total_expenses,
                'tax'       => $line->tax_rate, //assume o codigo do sistema de faturação
                'tax_id'    => $line->billing_code,
                'prodDesc'  => $description,
                'discount'  => $line->discount,
                'exemption' => $line->exemption_reason_code,
                'obs'       => $line->obs
            ];
        }

        return $arr;
    }

    /**
     * Prepare invoice header data array
     *
     * @return \Illuminate\Http\Response
     */
    public function prepareReceiptHeader($input, $customer)
    {

        $input['vat'] == '999999990' || $input['vat'] == '999999999' ? null : $input['vat'];

        $data = [
            'customer_id'   => $customer->id,
            'nif'           => $customer->code == 'CFINAL' ? '' : (@$input['vat'] ? $input['vat'] : $customer->vat),
            'code'          => @$input['billing_code'] ? $input['billing_code'] : $customer->billing_code,
            'name'          => @$input['billing_name'] ? $input['billing_name'] : $customer->billing_name,
            'address'       => @$input['billing_address'] ? $input['billing_address'] : $customer->billing_address,
            'zip_code'      => @$input['billing_zip_code'] ? $input['billing_zip_code'] : $customer->billing_zip_code,
            'city'          => @$input['billing_city'] ? $input['billing_city'] : $customer->billing_city,
            'docdate'       => @$input['docdate'],
            'reference'     => @$input['docref'],
            'obs'           => @$input['obs'],
            'paymentMethod' => @$input['payment_method']
        ];

        return $data;
    }

    /**
     * Prepare invoice lines data array
     *
     * @return \Illuminate\Http\Response
     */
    public function prepareReceiptLines()
    {

        $arr = [];
        foreach ($this->lines as $line) {
            $arr[] = [
                'invoice_ref' => $line->reference,
                'value'       => $line->total_price < 0.00 ? (-1 * $line->total_price) : $line->total_price,
            ];
        }

        return $arr;
    }

    public static function downloadGuideAt($data, $returnMode = 'pdf')
    {

        $apiKey     = @$data['api_key'];
        $docSerie   = @$data['serie'];
        $invoiceId  = @$data['doc_id'];
        $customerId = @$data['customer_id'];


        //imprime documentos da API
        $webservice = new Invoice($apiKey);
        $doc = $webservice->getDocumentPdf($invoiceId, 'transport-guide', $docSerie);

        if ($returnMode == 'string') {
            return $doc;
        } else {
            $doc = base64_decode($doc);
            header('Content-Type: application/pdf');
            echo $doc;
            exit;
        }
    }

    /**
     * Download invoice PDF file
     *
     * @param $data [id] or [customer_id, doc_id, doc_type, serie, api_key]
     * @param string $returnMode
     * @return bool|\Illuminate\Http\Response|mixed|string
     * @throws \Exception
     */
    public static function downloadPdf($data, $returnMode = 'pdf')
    {

        $storeFiles = Setting::get('billing_store_invoices');
        $id = @$data['id'];
        $forceRefreshCache =  @$data['refresh_cache'];

        if (!empty($id)) {
            $invoice = Invoice::filterSource()
                ->whereId($id)
                ->first();
        } else {

            $apiKey     = @$data['api_key'];
            $docSerie   = @$data['serie'];
            $docType    = @$data['doc_type'];
            $invoiceId  = @$data['doc_id'];
            $customerId = @$data['customer_id'];

            if ($docType == 'transport-guide') {
                return self::downloadGuideAt($data, $returnMode);
            }

            if (in_array($docType, ['internal-doc', 'proforma-invoice', 'regularization'])) {
                $apiKey = null;
            }

            if (!empty($id)) {
            } elseif ($apiKey) {
                $invoice = Invoice::filterSource()
                    ->where('api_key', $apiKey)
                    ->where('doc_id', $invoiceId);
                if ($customerId) {
                    $invoice = $invoice->where('customer_id', $customerId);
                }
                $invoice = $invoice->orderBy('id', 'desc')
                    ->first();
            } else {
                $invoice = Invoice::filterSource()
                    ->where('customer_id', $customerId)
                    ->where('doc_id', $invoiceId);
                if ($docSerie) {
                    $invoice = $invoice->where('doc_series_id', $docSerie);
                }
                if ($docType) {
                    $invoice = $invoice->where('doc_type', $docType);
                }
                $invoice = $invoice->first();
            }
        }

        if (!$invoice) {
            throw new \Exception('Nenhuma fatura encontrada.');
        }

        $filename   = $invoice->doc_type. '_'. $invoice->id . '.txt';
        $folderpath = storage_path('invoices/');
        $filepath   = storage_path('invoices/' . $filename);

        if (!$forceRefreshCache && $storeFiles && File::exists($filepath) && !$invoice->is_deleted) { //VERIFICA SE O FICHEIRO EXISTE EM ARQUIVO DESDE QUE NAO ESTEJA APAGADO
            $doc = file_get_contents($filepath);
        } else {
            if (in_array($invoice->doc_type, [Invoice::DOC_TYPE_FP, Invoice::DOC_TYPE_RG, Invoice::DOC_TYPE_INTERNAL_DOC]) || Setting::get('invoice_software') == 'EnovoTms') {
                //imprime documentos internos
                $doc = Invoice::printInvoices([$invoice->id], 'string');
            } else {

                $acceptStorage = $forceRefreshCache ? false : true;

                //imprime documentos da API
                $webservice = new Invoice($invoice->api_key);
                $doc = $webservice->getDocumentPdf($invoice->doc_id, $invoice->doc_type, $invoice->doc_series_id, $acceptStorage);
            }

            if ($storeFiles) {
                if (!File::exists($folderpath)) {
                    File::makeDirectory($folderpath);
                }

                File::put($filepath, $doc);
            }
        }

        if ($returnMode == 'string') {
            return $doc;
        } else {
            $doc = base64_decode($doc);
            header('Content-Type: application/pdf');
            echo $doc;
            exit;
        }
    }


    /**
     * Print invoice
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public static function printInvoices($invoicesIds, $returnMode = 'pdf', $invoicesCollection = null)
    {

        try {

            $locale = Setting::get('app_country');

            if ($invoicesCollection) {
                $invoices = $invoicesCollection;
            } else {
                $invoices = self::filterSource()
                    ->filterAgencies()
                    ->with(['lines', 'customer.agency'])
                    ->where(function ($q) {
                        $q->where('is_hidden', 0);
                        $q->orWhereNull('is_hidden');
                    })
                    ->whereIn('id', $invoicesIds)
                    ->get();
            }

            if ($invoices->isEmpty()) {
                throw new \Exception('Nenhuma fatura para impressão.');
            }

            ini_set("memory_limit", "-1");

            $mpdf = new Mpdf([
                'format'        => 'A4',
                'margin_top'    => 8,
                'margin_bottom' => 0,
                'margin_left'   => 20,
                'margin_right'  => 20,
            ]);

            $mpdf->showImageErrors = true;
            $mpdf->SetAuthor("ENOVO");
            $mpdf->shrink_tables_to_fit = 0;

            $data['view'] = 'admin.printer.invoices.docs.invoice';
            $data['documentTitle'] = '';

            foreach ($invoices as $key => $invoice) {

                $data['locale']      = $locale;
                $data['invoice']     = $invoice;
                $data['agency']      = $invoice->customer->agency;
                $data['taxesNormal'] = @$invoice->lines->filter(function ($item) {
                    return $item->tax_rate > 0.00;
                })
                    ->groupBy('tax_rate')
                    ->sortByDesc('tax_rate');

                $data['taxesExempt'] = @$invoice->lines->filter(function ($item) {
                    return $item->tax_rate == 0.00;
                })
                    ->groupBy('exemption_reason')
                    ->sortByDesc('exemption_reason');

                $totalCopies = 3;
                if ($invoice->doc_type == 'proforma-invoice') {
                    $data['view'] = 'admin.printer.invoices.docs.proforma';
                    $totalCopies  = 1;
                } else if ($invoice->doc_type == 'internal-doc') {
                    $data['view'] = 'admin.printer.invoices.docs.internal_doc';
                } else if ($invoice->doc_type == 'invoice') {
                    $data['view'] = 'admin.printer.invoices.docs.invoice';
                } else if ($invoice->doc_type == 'receipt') {
                    $data['view'] = 'admin.printer.invoices.docs.receipt';
                } else if ($invoice->doc_type == 'regularization') {
                    $data['view'] = 'admin.printer.invoices.docs.regularization';
                } else if ($invoice->doc_type == 'credit-note') {
                    $data['view'] = 'admin.printer.invoices.docs.credit_note';
                }

                for ($i = 0; $i < $totalCopies; $i++) {
                    $data['copy'] = $i + 1;

                    if ($i == 0) {
                        $data['copyId'] = 1;
                        $data['copyNumber'] = transLocale('admin/global.word.original', $locale);
                    } else if ($i == 1) {
                        $data['copyId'] = 2;
                        $data['copyNumber'] = transLocale('admin/global.word.double', $locale);
                    } else if ($i == 2) {
                        $data['copyId'] = 3;
                        $data['copyNumber'] = transLocale('admin/global.word.triplicate', $locale);
                    }

                    $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write
                }
            }

            if ($returnMode == 'string') {
                $doc = $mpdf->Output('Fatura.pdf', 'S');
                $doc = base64_encode($doc);
                return $doc; //string
            }

            if (Setting::get('open_print_dialog_docs')) {
                $mpdf->SetJS('this.print();');
            }

            $mpdf->debug = true;

            return $mpdf->Output('Fatura.pdf', 'I'); //output to screen

            exit;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Return Invoices summary grouped by customer
     *
     * @param $startDate
     * @param $endDate
     * @param bool $docDetails
     * @return array
     */
    public static function getInvoicesSummaryByCustomer($startDate, $endDate, $docDetails = true, $nodocs = false)
    {

        $period = [$startDate, $endDate];

        $invoices = Invoice::with(['customer' => function ($q) {
            $q->select(['id', 'name', 'code']);
        }])
            ->filterAgencies()
            ->filterSource()
            ->where(function ($q) {
                $q->where('is_hidden', 0);
                $q->orWhereNull('is_hidden');
            })
            ->where(function ($q) {
                $q->where('is_deleted', 0);
                $q->orWhereNull('is_deleted');
            })
            ->whereBetween('doc_date', $period);

        if (!$nodocs) {
            $invoices = $invoices->where('doc_type', '<>', 'nodoc');
        }

        $invoices = $invoices->get([
            'customer_id',
            'doc_type',
            'doc_subtotal',
            'doc_total',
            'doc_vat'
        ]);

        $customersInvoices = $invoices->groupBy('customer_id');

        $groupedData = [];
        foreach ($customersInvoices as $customerId => $invoices) {

            $docs = $totalInvoices = $totalReceipts = $totalNoDoc = $totalCreditNotes = [];
            foreach ($invoices as $invoice) {

                $customerCode = @$invoice->customer->code;
                $customerName = @$invoice->customer->name;

                if ($invoice->doc_type == Invoice::DOC_TYPE_RC || $invoice->doc_type == Invoice::DOC_TYPE_RG || $invoice->doc_type == Invoice::DOC_TYPE_ND) {
                    $totalReceipts['total']     = @$totalReceipts['total'] + $invoice->doc_total;
                    $totalReceipts['vat']       = @$totalReceipts['vat'] + $invoice->doc_vat;
                    $totalReceipts['subtotal']  = @$totalReceipts['subtotal'] + $invoice->doc_subtotal;
                    $totalReceipts['count']     = @$totalReceipts['count'] + 1;
                }
                
                elseif ($invoice->doc_type == Invoice::DOC_TYPE_FR  || $invoice->doc_type == Invoice::DOC_TYPE_FS ) {
                    $totalInvoices['total']     = @$totalInvoices['total'] + $invoice->doc_total;
                    $totalInvoices['vat']       = @$totalInvoices['vat'] + $invoice->doc_vat;
                    $totalInvoices['subtotal']  = @$totalInvoices['subtotal'] + $invoice->doc_subtotal;
                    $totalInvoices['count']     = @$totalInvoices['count'] + 1;

                    $totalReceipts['total']     = @$totalReceipts['total'] + ($invoice->doc_total * -1); //FR e FS tem sinal positivo, por isso ao somar aqui tem de se inverter o sinal
                    $totalReceipts['vat']       = @$totalReceipts['vat'] + ($invoice->doc_vat * -1);
                    $totalReceipts['subtotal']  = @$totalReceipts['subtotal'] + ($invoice->doc_subtotal * -1);
                    $totalReceipts['count']     = @$totalReceipts['count'] + 1;
                } 

                elseif ($invoice->doc_type == Invoice::DOC_TYPE_NODOC && $nodocs) {
                    $totalNoDoc['total']    = @$totalNoDoc['total'] + $invoice->doc_total;
                    $totalNoDoc['subtotal'] = @$totalNoDoc['subtotal'] + $invoice->doc_subtotal;
                    $totalNoDoc['vat']      = @$totalNoDoc['vat'] + $invoice->doc_vat;
                    $totalNoDoc['count']    = @$totalNoDoc['count'] + 1;
                } 

                elseif ($invoice->doc_type == Invoice::DOC_TYPE_NC) {
                    $totalCreditNotes['total']      = @$totalCreditNotes['total'] + $invoice->doc_total;
                    $totalCreditNotes['vat']        = @$totalCreditNotes['vat'] + $invoice->doc_vat;
                    $totalCreditNotes['subtotal']   = @$totalCreditNotes['subtotal'] + $invoice->doc_subtotal;
                    $totalCreditNotes['count']      = @$totalCreditNotes['count'] + 1;

                } else {
                    $totalInvoices['total']         = @$totalInvoices['total'] + $invoice->doc_total;
                    $totalInvoices['vat']           = @$totalInvoices['vat'] + $invoice->doc_vat;
                    $totalInvoices['subtotal']      = @$totalInvoices['subtotal'] + $invoice->doc_subtotal;
                    $totalInvoices['count']         = @$totalInvoices['count'] + 1;
                }

                if ($docDetails) {
                    $docs[$invoice->doc_type] = [
                        'total'     => @$docs[$invoice->type_id]['total'] + $invoice->doc_total,
                        'vat'       => @$docs[$invoice->type_id]['vat'] + $invoice->doc_vat,
                        'subtotal'  => @$docs[$invoice->type_id]['subtotal'] + $invoice->doc_subtotal,
                        'count'     => @$docs[$invoice->type_id]['count'] + 1
                    ];
                }
            }

            $customerTotal = @$totalInvoices['total'] + (@$totalReceipts['total'] + @$totalCreditNotes['total']);

            if ($nodocs) {
                $customerTotal+= @$totalNoDoc['total'];
            }
            

            $groupedData[$customerId] = [
                'code'      => $customerCode,
                'name'      => $customerName,
                'total'     => $customerTotal,
                'docs'      => $docs,
                'invoices'  => $totalInvoices,
                'receipts'  => $totalReceipts,
                'credit-notes' => $totalCreditNotes,
                'nodoc'     => $totalNoDoc,
            ];

           /*  if($customerId == '18453') {
                dd( @$groupedData);
            } */  
        }

        aasort($groupedData, 'total', SORT_DESC);

        return $groupedData;
    }

    /**
     * Return Invoices summary grouped by month
     *
     * @param $startDate
     * @param $endDate
     * @param bool $docDetails
     * @return array
     */
    public static function getInvoicesSummaryByMonth($year, $groupBy = null, $noDoc = false)
    {

        $startDate = $year . '-01-01';
        $endDate   = $year . '-12-31';


        $period = CarbonPeriod::between($startDate, $endDate);
        $periods = $period->months()->toArray();

        $invoices = Invoice::with(['customer' => function ($q) {
            $q->select(['id', 'name', 'code']);
        }])
            ->filterSource()
            ->filterAgencies()
            ->where(function ($q) {
                $q->where('is_hidden', 0);
                $q->orWhereNull('is_hidden');
            })
            ->where('is_deleted', 0)
            ->where('is_draft', 0)
            //->whereNotIn('doc_type', ['credit-note'])
            ->whereBetween('doc_date', [$startDate, $endDate])
            ->get([
                'customer_id',
                'doc_type',
                'doc_subtotal',
                'doc_total',
                'doc_vat',
                DB::raw('YEAR(doc_date) as year'),
                DB::raw('MONTH(doc_date) as month')
            ]);

        if ($groupBy == 'customer') {
            $customersData = $invoices->sortBy('customer.name')->groupBy('customer_id');

            $groupedData = [];
            foreach ($customersData as $customerId => $customer) {

                $customerName = @$customer->first()->customer->name;

                $customerMonths = $customer->groupBy('month');

                foreach ($customerMonths as $monthInvoices) {

                    $year  = $monthInvoices->first()->year;
                    $month = $monthInvoices->first()->month;

                    $key = $year . $month;

                    $invoicesType = Invoice::separeByInvoiceType($monthInvoices, $noDoc);

                    $groupedData[$customerName][$key] = [
                        'year'      => $year,
                        'month'     => $month,
                        'count'     => @$invoicesType['totals']['count'],
                        'total'     => @$invoicesType['totals']['total'],
                        'vat'       => @$invoicesType['totals']['vat'],
                        'billed'    => @$invoicesType['totals']['billed'],
                        'received'  => @$invoicesType['totals']['received'],
                        'nodoc'     => @$invoicesType['totals']['nodoc'],
                        'details'   => @$invoicesType['details']
                    ];
                }
            }
        } else {
            $customersInvoices = $invoices->groupBy('month');

            $groupedData = [];
            foreach ($periods as $period) {

                $key = $period->year . $period->month;

                $monthInvoices = @$customersInvoices[$period->month];

                $invoicesType = Invoice::separeByInvoiceType($monthInvoices, $noDoc);

                $groupedData[$key] = [
                    'year'     => $period->year,
                    'month'    => $period->month,
                    'count'    => @$invoicesType['totals']['count'],
                    'total'    => @$invoicesType['totals']['total'],
                    'vat'      => @$invoicesType['totals']['vat'],
                    'billed'   => @$invoicesType['totals']['billed'],
                    'received' => @$invoicesType['totals']['received'],
                    'nodoc'    => @$invoicesType['totals']['nodoc'],
                    'details'  => @$invoicesType['details']
                ];
            }
        }

        return $groupedData;
    }

    /**
     * Print customer all invoices
     * 
     * @param $customerId
     */
    public static function printCustomerBalance($customerId, $request = null, $returnMode = 'pdf')
    {

        $ids = $request->get('id');

        $customer = Customer::filterSource()->filterSeller()->find($customerId);
            
        $invoices = Invoice::where('customer_id', $customer->id)
            ->filterBalanceDocs();
            

        if (!empty($ids)) {
            $invoices = $invoices->whereIn('id', $ids);
        } else {

            //filter payment date
            $dtMin = $request->get('date_min');
            if ($request->has('date_min')) {
                $dtMax = $dtMin;
                if ($request->has('date_max')) {
                    $dtMax = $request->get('date_max');
                }
                $invoices = $invoices->whereBetween('due_date', [$dtMin, $dtMax]);
            }
        }

        $invoices = $invoices->orderBy('doc_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();   


        if ($invoices->isEmpty()) {
            throw new \Exception('Este cliente não possui conta corrente.');
        }

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
            'invoices'          => $invoices,
            'customer'          => $customer,
            'currency'          => Setting::get('app_currency'),
            'documentTitle'     => 'Conta Corrente',
            'documentSubtitle'  => $customer->billing_name,
            'view'              => 'admin.printer.billing.balance.summary_customer'
        ];

        $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write

        if ($returnMode == 'string') {
            return $mpdf->Output('Conta Corrente -' . $customer->billing_name . ' ' . date('Y-m-d') . '.pdf', 'S'); //string
        }

        if (Setting::get('open_print_dialog_docs') && $returnMode != 'string') {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        return $mpdf->Output('Conta Corrente -' . $customer->billing_name . ' ' . date('Y-m-d') . '.pdf', 'I'); //output to screen

        exit;
    }

    /**
     * Print list of all customers and total balance values
     *
     * @param [type] $request
     * @param string $returnMode
     * @return void
     */
    public static function printCustomersBalanceSummary($request, $returnMode = 'pdf')
    {

        $data = Customer::filterSource()
            ->filterAgencies()
            ->filterSeller()
            ->select(
                'customers.*',
                DB::raw('(select max(date) from shipments where shipments.customer_id = customers.id and deleted_at is null limit 0,1) as last_shipment'),
                DB::raw('(select count(date) from shipments where shipments.customer_id = customers.id and deleted_at is null) as total_shipments')
            );

        //filter agency
        $value = $request->agency;
        if ($request->has('agency')) {
            $data = $data->where('agency_id', $value);
        }

        //filter seller
        $value = $request->seller;
        if ($request->has('seller')) {
            $data = $data->where('seller_id', $value);
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
                    $q->where('balance_total_unpaid', '=', 0.00);
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

        $data = $data->get();

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
            'customers'         => $data,
            'documentTitle'     => 'Resumo de Contas Correntes',
            'documentSubtitle'  => '',
            'view'              => 'admin.printer.billing.balance.summary'
        ];

        $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;

        if ($returnMode == 'string') {
            return $mpdf->Output('Resumo Contas Correntes ' . date('Y-m-d') . '.pdf', 'S'); //string
        }

        return $mpdf->Output('Resumo Contas Correntes ' . date('Y-m-d') . '.pdf', 'I'); //output to screen

        exit;
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

        if (config('app.source') == 'corridexcelente') { //regime iva de caixa
            return self::getVatBalanceIVACaixa($startDate, $endDate);
        }

        $docTypes = ['receipt', 'proforma-invoice', 'nodoc'];
        $invoicesLines = InvoiceLine::with(['invoice' => function ($q) {
                $q->select(['id', 'doc_type', 'doc_id']);
            }])
            ->whereHas('invoice', function ($q) use ($startDate, $endDate, $docTypes) {
                $q->filterSource();
                $q->filterAgencies();
                $q->whereNotNull('doc_id');
                $q->where(function ($q) {
                    $q->where('is_hidden', 0);
                    $q->orWhereNull('is_hidden');
                });
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
                    $vatToPay     = $total - $incidence;
                    $vatToReceive = $paymentNotes->sum('subtotal') - ($paymentNotes->sum('subtotal') * (1 + (@$vatRate / 100)));

                    $groupedData[$exemptionReason] = [
                        'vat_rate'    => 0,
                        'rate_name'   => $rateName,
                        'total'       => $total,
                        'incidence'   => $incidence,
                        'vat_receive' => $vatToReceive,
                        'vat_pay'     => $vatToPay,
                        'vat'         => $vatToPay,
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
                $vatToPay     = $total - $incidence;
                $vatToReceive = ($paymentNotes->sum('subtotal') * (1 + (@$vatRate / 100))) - $paymentNotes->sum('subtotal');

                $groupedData[$vatRate] = [
                    'vat_rate'    => $vatRate,
                    'rate_name'   => $rateName,
                    'total'       => $total,
                    'incidence'   => $incidence,
                    'vat_receive' => $vatToReceive,
                    'vat_pay'     => $vatToPay,
                    'vat'         => $vatToPay,
                    'count'       => @$invoices->count(),
                ];
            }
        }

        return $groupedData;
    }

    /**
     * Retorna o total de recibos para IVA de caixa
     * @param $startDate
     * @param $endDate
     * @return array
     */
    public static function getVatBalanceIVACaixa($startDate, $endDate)
    {
        //iva de caixa considera os recibos emitidos em vez das faturas

        $receipts = Invoice::filterSource()
            ->filterAgencies()
            ->whereNotNull('doc_id')
            ->where(function ($q) {
                $q->where('is_hidden', 0);
                $q->orWhereNull('is_hidden');
            })
            ->where('doc_type', 'receipt')
            ->whereBetween('doc_date', [$startDate, $endDate])
            ->where(function ($q) {
                $q->where('is_deleted', 0);
                $q->orWhereNull('is_deleted');
            })
            ->get();

        $vatData = $receipts->groupBy('tax_rate');
        $vatRateExempt = $receipts->filter(function ($item) {
            return $item->exemption_reason;
        })->groupBy('exemption_reason');

        $groupedData = [];
        foreach ($vatData as $vatRate => $invoices) {

            $rateName = 'Taxa IVA não definida';

            $incidence    = @$invoices->sum('doc_subtotal');
            $total        = @$invoices->sum('doc_total'); //@$invoices->sum('subtotal') * (1 + (@$vatRate / 100));
            $vatToPay     = 0; //@$invoices->sum('doc_vat');
            $vatToReceive = @$invoices->sum('doc_vat');

            $groupedData[$vatRate] = [
                'vat_rate'    => $vatRate,
                'rate_name'   => $rateName,
                'total'       => $total,
                'incidence'   => $incidence,
                'vat_receive' => $vatToReceive,
                'vat_pay'     => $vatToPay,
                'vat'         => $vatToPay,
                'count'       => @$invoices->count(),
            ];
        }

        return $groupedData;
    }


    public static function separeByInvoiceType($invoices, $noDoc = false)
    {

        $totals = [];
        $counterInvoices = [];
        if ($invoices) {
            foreach ($invoices as $invoice) {

                $type = $invoice->doc_type;

                if (!$noDoc && $type == 'nodoc') {
                    //ignora documentos nodoc
                } else {

                    if($type == 'credit-note' && $invoice->doc_subtotal > 0.00) {
                        $invoice->doc_subtotal  = $invoice->doc_subtotal * -1;
                        $invoice->doc_vat       = $invoice->doc_vat * -1;
                        $invoice->doc_total     = $invoice->doc_total * -1;
                    }

                    $counterInvoices[$type]['total'] = @$counterInvoices[$type]['total'] + $invoice->doc_subtotal;
                    $counterInvoices[$type]['vat'] = @$counterInvoices[$type]['vat'] + $invoice->doc_vat;
                    $counterInvoices[$type]['count'] = @$counterInvoices[$type]['count'] + 1;

                    
                    //if ($type == 'receipt' || $type == 'invoice-receipt') {
                    //if ($type == 'receipt' || $type == 'credit-note') {
                    if ($type == 'receipt' || $type == 'credit-note') {
                        @$totals['received'] = @$totals['received'] + $invoice->doc_subtotal;
                    } elseif ($type == 'nodoc') {
                        @$totals['nodoc'] = @$totals['nodoc'] + $invoice->doc_subtotal;
                    } else {
                        @$totals['billed'] = @$totals['billed'] + $invoice->doc_subtotal;
                    }

                    $totals['count'] = @$totals['count'] + 1;
                    $totals['vat']   = @$totals['vat'] + $invoice->doc_vat;
                    $totals['total'] = @$totals['total'] + $invoice->doc_subtotal;
                }
            }

            $data = [
                'details' => $counterInvoices,
                'totals' => $totals
            ];

            return $data;
        }

        return null;
    }

    /**
     * Send E-mail
     * @param $data
     * @return bool
     */
    public function sendEmail($data)
    {

        $email = @$data['email'];
        $selectedAttachments = $data['attachments'];

        if (empty($data['attachments']) || empty($email) || in_array($this->doc_type, ['internal-doc'])) {
            return false;
        }

        try {

            $data['email_view'] = @$data['email_view'] ? $data['email_view'] : 'emails.billing.customer_month';
            $data['subject'] = 'Envio Documentos: ' . trans('admin/billing.types.' . $this->doc_type) . ' ' . $this->name;
            if ($this->doc_type == 'receipt') {
                $data['subject']    = 'Envio de Documentos: Recibo ' . $this->name;
                $data['email_view'] = 'emails.billing.receipt';
            } elseif ($this->doc_type == 'credit-note') {
                //$data['email_view'] = 'emails.billing.credit_note';
            } elseif ($this->doc_type == 'proforma-invoice') {
                $data['subject'] = 'Envio de Documentos: Fatura-Proforma ' . $this->name;
            } elseif ($this->doc_type == 'nodoc') {
                $data['subject'] = 'Envio de Resumo de Serviços';
            }


            if ($this->target == Invoice::TARGET_CUSTOMER_BILLING) {
                if (@$this->customer_billing->billing_type == 'single') {
                    $attachments = $this->emailShipment($this, $data);
                } else {
                    $attachments = $this->emailBillingMonth($this, $data);
                }
            }

            $attachments = @$attachments['attachments'] ? $attachments['attachments'] : [];

            //Attach invoice
            if (in_array('invoice', $selectedAttachments)) {
                if (@$this->doc_id && $this->doc_type != 'nodoc') {

                    $content = null;
                    if (in_array($this->doc_type, ['proforma-invoice', 'internal-doc']) || Setting::get('invoice_software') == 'EnovoTms') {
                        $content = Invoice::printInvoices([$this->id], 'string');
                        $content = base64_decode($content);
                    } else {
                        $invoiceData = ['id' => $this->id];
                        $content = Invoice::downloadPdf($invoiceData, 'string');
                        $content = base64_decode($content);
                    }

                    if ($content) {
                        $attachments[] = [
                            'mime'      => 'application/pdf',
                            'filename'  => trans('admin/billing.types.' . $this->doc_type) . ' ' . $this->name . '.pdf',
                            'content'   => $content
                        ];
                    }
                }
            }

            //Attach receipt
            if (in_array('receipt', $selectedAttachments)) {
                if (@$this->assigned_receipt || $this->doc_type == 'receipt') {

                    if (Setting::get('invoice_software') == 'EnovoTms') {
                        $content = Invoice::printInvoices([$this->id], 'string');
                        $content = base64_decode($content);
                    } else {
                        $invoiceData = ['id' => $this->id];
                        $content = Invoice::downloadPdf($invoiceData, 'string');
                        $content = base64_decode($content);
                    }

                    $attachments[] = [
                        'mime'      => 'application/pdf',
                        'filename'  => 'Recibo ' . $this->name . '.pdf',
                        'content'   => $content
                    ];
                }
            }

            //Attachments
            if (in_array('summary', $selectedAttachments) || in_array('shipment', $selectedAttachments)) {
                $billingMonth = CustomerBilling::where('customer_id', $this->customer_id)
                    ->where('invoice_doc_id', $this->doc_id)
                    ->where('id', $this->target_id)
                    ->first();

                //shipments releated with invoice
                if ($billingMonth) {
                    $shipmentsIds = $billingMonth->shipments;

                    $shipmentsAttachments = FileRepository::where('source_class', 'Shipment')
                        ->whereIn('source_id', $shipmentsIds)
                        ->get();

                    if ($shipmentsAttachments) {

                        foreach ($shipmentsAttachments as $attachment) {
                            if ($attachment->customer_visible) {
                                $file = public_path($attachment->filepath);
                                if (file_exists($file)) {
                                    $file = file_get_contents($file);

                                    $attachments[] = [
                                        'mime'      => 'application/' . $attachment->extension,
                                        'filename'  => $attachment->name . '.' .  $attachment->extension,
                                        'content'   => $file
                                    ];
                                }
                            }
                        }
                    }
                }
            }

            //validate emails
            $emails = null;
            if (!empty($email)) {
                $emails = validateNotificationEmails($email);
                $emails = $emails['valid'];

                if (!$emails) {
                    throw new \Exception('O e-mail indicado é inválido.');
                }
            }

            //add emails in CC
            $emailsCC = null;
            if (!empty(Setting::get('billing_email_cc'))) {
                $emailsCC = validateNotificationEmails(Setting::get('billing_email_cc'));
                $emailsCC = $emailsCC['valid'];
            }

            if ($this->doc_type == 'receipt') {
                return $this->sendEmailReceipt($this, $emails, $emailsCC, $attachments);
            } else {
                $invoice = $this;
                Mail::send($data['email_view'], compact('data', 'invoice'), function ($message) use ($data, $emails, $emailsCC, $attachments) {

                    $message->to($emails);

                    if ($emailsCC) {
                        $message = $message->cc($emailsCC);
                    }

                    $message = $message->from(config('mail.from.address'), config('mail.from.name'))
                        ->subject($data['subject']);

                    if ($attachments) {
                        foreach ($attachments as $attachment) {

                            if (isset($attachment['content'])) {
                                $message->attachData(
                                    $attachment['content'],
                                    $attachment['filename'],
                                    $attachment['mime'] ? ['mime' => $attachment['mime']] : []
                                );
                            }
                        }
                    }
                });
            }

            if (count(Mail::failures()) > 0) {
                return false;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage() . ' line ' . $e->getLine() . ' file ' . $e->getFile());
        }

        return true;
    }

    /**
     * Send receipt email
     * @param $emails
     * @param $emailsCC
     * @param $attachments
     */
    public function sendEmailReceipt($receipt, $emails, $emailsCC, $attachments)
    {

        $invoicesIds = $receipt->lines->pluck('assigned_invoice_id');
        $invoices = Invoice::whereIn('id', $invoicesIds)->get();

        try {
            Mail::send('emails.billing.receipt', compact('invoices', 'receipt'), function ($message) use ($emails, $attachments, $emailsCC) {
                $message->to($emails)
                    ->from(config('mail.from.address'), config('mail.from.name'));

                if (!empty($emailsCC)) {
                    $message = $message->cc($emailsCC);
                }

                $subject = 'Envio de Documentos: Recibo ' . $this->name;
                $message = $message->subject($subject);

                foreach ($attachments as $attachment) {
                    $message->attachData(
                        $attachment['content'],
                        $attachment['filename'],
                        $attachment['mime'] ? ['mime' => $attachment['mime']] : []
                    );
                }
            });

            if (count(Mail::failures()) > 0) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }


    /**
     * Prepare Billing Email
     * @return array
     */
    public function emailBillingMonth($invoice, $data)
    {

        $billingMonth = CustomerBilling::where('customer_id', $invoice->customer_id)
            ->where('invoice_doc_id', $invoice->doc_id)
            ->where('id', $invoice->target_id)
            ->first();

        if (@$billingMonth->billing_type == 'month') {
            $periodName = Billing::getPeriodName($billingMonth->year, $billingMonth->month, $billingMonth->period);
            $subject    = 'Envio de Fatura: ' . $invoice->name . ' (' . $periodName . ')';

            if ($invoice->doc_type == 'nodoc') {
                $subject = 'Faturação de Serviços (' . $periodName . ')';
            }

            $emailView  = 'emails.billing.customer_month';
        } else {
            $subject    = 'Envio de Fatura: ' . $invoice->name;

            if ($invoice->doc_type == 'nodoc') {
                $subject = 'Faturação de Serviços';
            }

            $emailView  = 'emails.billing.customer_shipment';
        }

        $attachments = [];
        if (in_array('summary', $data['attachments'])) {

            $dataIds = [
                'shipments' => $billingMonth->shipments,
                'covenants' => $billingMonth->covenants,
                'products'  => $billingMonth->products,
            ];

            $attachments[] = [
                'mime'      => 'application/pdf',
                'filename'  => 'Resumo Envios - ' . $invoice->name . '.pdf',

                'content'   => CustomerBilling::printShipments(
                    $billingMonth->customer_id,
                    $billingMonth->month,
                    $billingMonth->year,
                    'string',
                    $dataIds,
                    '30d',
                    null,
                    $invoice
                )
            ];
        }

        if (in_array('excel', $data['attachments'])) {

            $request = new \Illuminate\Http\Request();
            $request->year   = $billingMonth->year;
            $request->month  = $billingMonth->month;
            $request->period = $billingMonth->period;
            $request->id     = $billingMonth->shipments;
            $request->exportString = true;

            $controller = new BillingController();
            $content    = $controller->customerShipments($request, $billingMonth->customer_id);

            $attachments[] = [
                'mime'      => null,
                'filename'  => 'Resumo Envios - ' . $invoice->name . '.xlsx',
                'content'   => $content
            ];
        }

        $data = [
            'attachments' => $attachments,
            'subject'     => $subject,
            'email_view'  => $emailView,
            'shipments'   => @$billingMonth->shipments,
            'period_name' => @$periodName
        ];

        return $data;
    }

    /**
     * Prepare Billing Email
     * @return array
     */
    public function emailShipment($invoice, $data)
    {

        $billingMonth = CustomerBilling::where('customer_id', $invoice->customer_id)
            ->where('invoice_doc_id', $invoice->doc_id)
            ->where('id', $invoice->target_id)
            ->first();

        $shipmentsIds = $billingMonth->shipments;
        $shipment = null;
        $subjectDetail = '';
        if (!empty($shipmentsIds)) {
            $shipment = Shipment::whereIn('id', $shipmentsIds)->first();
            $subjectDetail = ' (Envio ' . @$shipment->tracking_code . ')';
        }

        $covenantsIds = $billingMonth->covenants;
        $covenant = null;
        if (!empty($covenantsIds) && empty($shipmentsIds)) {
            $covenant = CustomerCovenant::whereIn('id', $covenantsIds)->first();
            $subjectDetail = ' (Avença ' . @$covenant->description . ')';
        }

        if (!empty($covenantsIds) && !empty($shipmentsIds)) {
            $subjectDetail = ' (Serviços e Avenças)';
        }

        $subject   = 'Envio de Fatura: ' . $invoice->name . ' ' . $subjectDetail;
        $emailView = 'emails.billing.customer_shipment';

        $attachments = [];
        if ($shipment && in_array('shipment', $data['attachments'])) {

            $attachments[] = [
                'mime'      => 'application/pdf',
                'filename'  => 'Comprovativo de Envio ' . $shipment->tracking_code . '.pdf',
                'content'   => Shipment::printShipmentProof(null, [$shipment], null, 'string')
            ];
        } elseif ($covenant && in_array('shipment', $data['attachments'])) {

            /*$attachments[] = [
                'mime'      => 'application/pdf',
                'filename'  => 'Resumo avença.pdf',
                'content'   => Shipment::printShipmentProof(null, [$shipment], null, 'string')
            ];*/
        } elseif (in_array('summary', $data['attachments'])) {

            $dataIds = [
                'shipments' => $billingMonth->shipments,
                'covenants' => $billingMonth->covenants,
                'products'  => $billingMonth->products,
            ];


            $attachments[] = [
                'mime'      => 'application/pdf',
                'filename'  => 'Resumo Envios - ' . $invoice->name . '.pdf',
                'content'   => CustomerBilling::printShipments(
                    $billingMonth->customer_id,
                    $billingMonth->month,
                    $billingMonth->year,
                    'string',
                    $dataIds,
                    '30d',
                    null,
                    $invoice
                )
            ];
        }

        $data = [
            'attachments' => $attachments,
            'subject'     => $subject,
            'email_view'  => $emailView,
            'shipments'   => @$billingMonth->shipments,
            'covenants'   => @$billingMonth->covenants,
            'shipment'    => @$shipment,
            'covenant'    => @$covenant
        ];

        return $data;
    }

    public function insertOrUpdateCustomer($customer)
    {

        $data = $customer->toArray();
        $data['vat']      = $customer->vat;
        $data['code']     = $customer->code;
        $data['name']     = $customer->billing_name;
        $data['address']  = $customer->billing_address;
        $data['zip_code'] = $customer->billing_zip_code;
        $data['city']     = $customer->billing_city;
        $data['phone']    = $customer->billing_phone;
        $data['email']    = $customer->billing_email;
        $data['obs']      = $customer->obs;
        $data['country']  = $customer->billing_country;
        $data['payment_condition'] = $customer->payment_method;

        if (!empty($data['vat']) && !in_array($data['vat'], ['999999990', '999999999'])) {
            $class = Base::getNamespaceTo('Customer');
            $customerKeyinvoice = new $class();
            $customerKeyinvoice->insertOrUpdateCustomer(
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
                $data['country'],
                $data['payment_condition'],
                $customer
            );
        }
    }

    /**
     * Return document most recent date
     *
     * @return string
     */
    public static function getLastDocDate($apiKey = null)
    {

        if (!$apiKey) {
            $apiKey = App\Models\Billing\ApiKey::getDefaultKey();
        }

        //memoriza a última data lançada
        $folder   = storage_path() . '/keyinvoice-logs/';
        $filename = $folder . 'session.json';

        $content = json_decode(File::get($filename), true);

        $date = $content[$apiKey]['last_docdate'];

        $date = $date ? $date : date('Y-m-d');

        return $date;
    }


    /**
     * Autocreate receipt from invoice
     * @param $data
     * @return int
     * @throws \Exception
     */
    public function autocreateReceiptFromInvoice($data)
    {

        $invoice   = $this;
        $lines     = $invoice->lines;
        $customer  = $this->customer;
        $paymentMethod = @$data['payment_method'] ? @$data['payment_method'] : '';
        $docDate   = @$data['doc_date'] ? @$data['doc_date'] : date('Y-m-d');
        $paidAt    = @$data['payment_date'] ? @$data['payment_date'] : date('Y-m-d');
        $docSerie  = @$data['apiKey'] ? @$data['apiKey'] : $invoice->api_key;
        $sendEmail = @$data['send_email'];
        $isDraft   = false;

        if($invoice->doc_type != Invoice::DOC_TYPE_FT) {
            throw new \Exception('Documento inválido para criar recibo.');
        } elseif($invoice->is_settle) {
            throw new \Exception('Documento já pago.');
        } else if($invoice->is_reversed || $invoice->is_deleted) {
            throw new \Exception('Documento anulado/estornado.');
        }

        try {

            $customer = $invoice->customer;

            //convert prospect em cliente
            if ($customer->is_prospect) {
                $customer->is_prospect = 0;
                $customer->setCode(true);
            }

            $requestCreateReceipt = new Request([
                'customer_id'       => $invoice->customer_id,
                'docdate'           => $docDate,
                'api_key'           => $docSerie,
                'payment_method'    => $paymentMethod,
                'payment_date'      => $paidAt,
                'invoices'          => [$invoice->id => $invoice->doc_total],
                'billing_email'     => @$invoice->customer->billing_email ?? null,
                'send_email'        => $sendEmail,
                'draft'             => $isDraft
            ]);

            $salesController = new \App\Http\Controllers\Admin\Invoices\SalesController();
            $resultCreateRecipt = $salesController->updateReceipt($requestCreateReceipt, null, true);

        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Autocreate payment document from proforma-invoice
     * @param $data
     * @return int
     * @throws \Exception
     */
    public function autocreatePaymentDocument($data)
    {

        $invoice  = $this;
        $lines    = $invoice->lines;
        $customer = $this->customer;
        $today    = date('Y-m-d');
        $submitInvoice = in_array($invoice->doc_after_payment, ['nodoc', 'internal-doc', 'proforma-invoice']) ? false : true;
        $submitInvoice = config('app.env') == 'local' ? false : $submitInvoice;

        /*if($invoice->is_settle) {
          throw new \Exception('Documento já pago.');
        }*/

        if (empty($invoice->doc_after_payment)) {
            return true;
        }

        try {

            $customer = $invoice->customer;

            //convert prospect em cliente
            if ($customer->is_prospect) {
                $customer->is_prospect = 0;
                $customer->setCode(true);
            }

            $newInvoice = new Invoice();
            $newInvoice->fill($invoice->toArray());
            $newInvoice->doc_series     = null;
            $newInvoice->doc_series_id  = null;
            $newInvoice->doc_id         = null;
            $newInvoice->api_key        = @$data['apiKey'] ? $data['apiKey'] : $invoice->api_key;
            $newInvoice->doc_type       = @$data['doc_type'] ? $data['doc_type'] : $invoice->doc_after_payment;
            $newInvoice->doc_date       = @$data['doc_date'] ? $data['doc_date'] : $today;
            $newInvoice->due_date       = @$data['due_date'] ? $data['due_date'] : $today;;
            $newInvoice->payment_method = @$data['payment_method'];
            $newInvoice->payment_date   = @$data['payment_date'] ? $data['payment_date'] : $today;
            $newInvoice->is_settle      = 0;
            $newInvoice->is_deleted     = 0;
            $newInvoice->is_draft       = 1;
            $newInvoice->delete_reason  = null;
            $newInvoice->created_by     = Auth::check() ? Auth::user()->id : null;
            $newInvoice->sort           = null;
            $newInvoice->save();

            foreach ($lines as $line) {
                $newLine = new InvoiceLine();
                $newLine->fill($line->toArray());
                $newLine->invoice_id = $newInvoice->id;
                $newLine->save();
            }

            //submete a fatura
            if ($submitInvoice) {

                //SUBMIT KEYINVOICE
                if (Invoice::getInvoiceSoftware() == Invoice::SOFTWARE_KEYINVOICE) {
                    $input = $newInvoice->toArray();
                    $input['docdate'] = $newInvoice->doc_date;
                    $input['duedate'] = $newInvoice->due_date;
                    $input['docref']  = $newInvoice->reference;

                    $header = $newInvoice->prepareDocumentHeader($input, $customer);
                    $lines  = $newInvoice->prepareDocumentLines();

                    $documentId   = $newInvoice->createDraft($newInvoice->doc_type, $header, $lines);
                    $invoiceDocId = $newInvoice->convertDraftToDoc($documentId, $newInvoice->doc_type);

                    if ($invoiceDocId) {
                        $docData = $newInvoice->setDocumentNo();
                        $newInvoice->is_draft = 0;
                        $newInvoice->internal_code = @$docData['internal_code'];
                    }
                }
            } else {
                $documentId                = $newInvoice->setDocumentNo();
                $newInvoice->doc_id        = @$documentId['doc_id'];
                $newInvoice->doc_series    = @$documentId['doc_serie'];
                $newInvoice->doc_series_id = @$documentId['doc_serie_id'];
                $newInvoice->internal_code = @$documentId['internal_code'];
                $newInvoice->is_draft      = 0;
                $newInvoice->api_key       = null;
                $newInvoice->is_settle     = null;
            }

            if ($newInvoice->doc_id != 'invoice') {
                $newInvoice->is_settle = true;
            }

            $newInvoice->save();

            //se o documento original é uma fatura-proforma, associa à fatura-proforma o ID da fatura gerada
            if ($invoice->doc_type == 'proforma-invoice' && $newInvoice->doc_id) {
                $invoice->update([
                    'assigned_invoice_id' => $newInvoice->id,
                    'payment_method'      => $newInvoice->payment_method,
                    'payment_date'        => $newInvoice->payment_date,
                    'is_settle'           => true
                ]);
            }

            //envia email
            if ($data['send_email']) {

                $email = @$data['billing_email'] ? $data['billing_email'] : $customer->billing_email;

                $data = [
                    'email'         => $email,
                    'popup_payment' => @$data['popup_payment'],
                    'attachments'   => ['invoice']
                ];

                $newInvoice->sendEmail($data);
            }

            return $newInvoice;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function getInvoiceSoftware()
    {
        return ucwords(Setting::get('invoice_software'));
    }

    public static function getPendingDocuments($customerId)
    {

        $documents = Invoice::filterSource()
            ->where('customer_id', $customerId)
            ->filterBalanceDocs()
            ->where('is_settle', 0)
            ->orderBy('doc_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return $documents;
    }

    

    /**
     * Get invoice data from shipments
     * 
     * @param int $shipmentId
     * @return array|null
     */
    public static function getDataFromShipment($shipmentId, $arrayMode = false) {

        $shipment = Shipment::filterAgencies()->find($shipmentId);

        if (!$shipment) {
            throw new \Exception('O serviço não foi encontrado');
        }

        if (@$shipment->invoice_id) {
            throw new \Exception('O serviço já se encontra faturado');
        }

        $customer = @$shipment->customer;
        $billingDate = new Carbon($shipment->billing_date);
        $month  = @$billingDate->month;
        $year   = @$billingDate->year;
        
        $curPeriod  = Setting::get('billing_method') ? Setting::get('billing_method') : '30d';
        
        if ($curPeriod != '30d') {
            if (date('d') < '16') {
                $curPeriod = '1q';
            } else {
                $curPeriod = '2q';
            }
        }
        
        $period = $curPeriod;

        $ids = [$shipmentId];
        $billing = CustomerBilling::getBilling(@$customer->id, $month, $year, $period, $ids, [], true);
        $billing->billing_type = 'single';
        $billing->payment_condition = $customer->payment_method ? $customer->payment_method : '30d';
        $billing->reference         = 'TRK' . $shipment->tracking_code;

        if($customer->billing_reference) {
            $billing->reference = $customer->billing_reference;
        }

        if(!empty(Setting::get('invoice_shipment_obs'))) {
            $billing->obs = Invoice::prefillObs(Setting::get('invoice_shipment_obs'));
        }

        if(Setting::get('invoice_use_shipment_ref')) {
            $billing->reference = $shipment->reference ? $shipment->reference : $billing->reference;
        }

        if($shipment->payment_at_recipient) {
            //procura se existe algum cliente com o NIF indicado
            $customer->id   = null;
            $customer->code = $customer->setCode(false);
            $customer->vat  = $shipment->recipient_vat;

            $customer->billing_name     = $shipment->recipient_name;
            $customer->billing_address  = $shipment->recipient_address;
            $customer->billing_zip_code = $shipment->recipient_zip_code;
            $customer->billing_city     = $shipment->recipient_city;
            $customer->billing_country  = $shipment->recipient_country;
            $customer->billing_email    = $shipment->recipient_email;
            $customer->is_particular    = 0;

            if($shipment->recipient_vat) {
                $newCustomer = Customer::where('vat', $shipment->recipient_vat)->first(); //verifica se o NIF já existe

                if($newCustomer) { //se existe, substitui o cliente
                    $customer = $newCustomer;
                }
            }
        }


        if(Setting::get('invoice_include_shipment_expenses')) {

            $shipmentBillingItem = $shipment->getBillingItem();
            $shipmentVatRate     = $shipment->getVatRate();
            $billingItem         = Item::where('reference', Setting::get('invoice_item_'. $shipmentBillingItem .'_ref'))->first();

            if(app_mode_cargo()) {

                $shipmentDetail = "ORIGEM/LOAD:\n".$shipment->sender_name."\n";
                $shipmentDetail.= $shipment->sender_address." ".$shipment->sender_zip_code." ". $shipment->sender_city ." (".strtoupper($shipment->sender_country).")\n";

                $shipmentDetail.= "\nDESTINO/DESTINATION: \n".$shipment->recipient_name."\n";
                $shipmentDetail.= $shipment->recipient_address." ".$shipment->recipient_zip_code." ". $shipment->recipient_city ." (".strtoupper($shipment->recipient_country).")\n";

                $shipmentDetail .= "\nTRK: ".$shipment->tracking_code;
                $shipmentDetail .= "\nData Carga: " . $shipment->shipping_date->format('Y-m-d H:i');

                if($shipment->delivery_date) {
                    $shipmentDetail .= "\nData Descarga: " . $shipment->delivery_date->format('Y-m-d H:i');
                }

                if ($shipment->vehicle) {
                    $shipmentDetail .= "\nViatura " . $shipment->vehicle . " ".$shipment->trailer ."\n";
                }

            } else {
                $shipmentDetail = "RECOLHA:\n".$shipment->sender_name."\n";
                $shipmentDetail.= $shipment->sender_address." ".$shipment->sender_zip_code." ". $shipment->sender_city ." (".strtoupper($shipment->sender_country).")\n";

                $shipmentDetail.= "\nENTREGA: \n".$shipment->recipient_name."\n";
                $shipmentDetail.= $shipment->recipient_address." ".$shipment->recipient_zip_code." ". $shipment->recipient_city ." (".strtoupper($shipment->recipient_country).")\n";

                $shipmentDetail .= "\nTRK: ".$shipment->tracking_code;
                $shipmentDetail .= "\nData: " . $shipment->shipping_date->format('Y-m-d');
            }

            $lines = [];
            $linesContent = [
                "key"               => $shipmentBillingItem,
                "reference"         => @$billingItem->reference,
                "description"       => @$billingItem->name,
                "qty"               => 1,
                "qty_real"          => 1,
                "total_price"       => $shipment->total_price,
                "subtotal"          => $shipment->total_price,
                "exemption_reason"  => $shipmentVatRate['reason'],
                "tax_rate"          => $shipmentVatRate['code'] ?? Setting::get('vat_rate_normal'),
                "tax_rate_id"       => $shipmentVatRate['id'] ?? Setting::get('vat_rate_normal'),
                "obs"               => $shipmentDetail
            ];

            if (!$arrayMode)
                $lines[] = (object) $linesContent;
            else
                $lines[] = (array) $linesContent;

            if($shipment->fuel_tax && $shipment->fuel_price > 0.00) {
                $linesContent = [
                    "key"               => "item_fuel",
                    "reference"         => 'FUEL',
                    "description"       => 'Taxa de combustível ('.money($shipment->fuel_tax).'%)',
                    "qty"               => 1,
                    "qty_real"          => 1,
                    "total_price"       => $shipment->fuel_price,
                    "subtotal"          => $shipment->fuel_price,
                    "exemption_reason"  => $shipmentVatRate['reason'],
                    "tax_rate"          => $shipmentVatRate['code'] ?? Setting::get('vat_rate_normal'),
                    "tax_rate_id"       => $shipmentVatRate['id'] ?? Setting::get('vat_rate_normal'),
                ];
                if (!$arrayMode)
                    $lines[] = (object) $linesContent;
                else
                    $lines[] = (array) $linesContent;
            }

            if(!$shipment->expenses->isEmpty()) {

                foreach ($shipment->expenses as $key => $expense) {
                    $linesContent = [
                        "key"               => "item_".($key + 1),
                        "reference"         => @$billingItem->reference,
                        "description"       => $expense->name,
                        "qty"               => @$expense->pivot->qty,
                        "qty_real"          => @$expense->pivot->qty,
                        "total_price"       => @$expense->pivot->price,
                        "subtotal"          => @$expense->pivot->subtotal,
                        "exemption_reason"  => @$expense->pivot->tax_rate ? @$expense->pivot->tax_rate : $shipmentVatRate['reason'],
                        "tax_rate"          => $shipmentVatRate['code'] ?? Setting::get('vat_rate_normal'),
                        "tax_rate_id"       => $shipmentVatRate['id'] ?? Setting::get('vat_rate_normal'),
                    ];

                    if (!$arrayMode)
                        $lines[] = (object) $linesContent;
                    else
                        $lines[] = (array) $linesContent;
                }
            }

            //dd($lines);

            $billing->lines = (array) $lines;
        }

        $docDate      = date('Y-m-d');
        $docLimitDate = new Carbon();
        $docLimitDate = $docLimitDate->addDays(@$customer->payment_method)->format('Y-m-d');

        return [
            'billing_date'   => $billingDate,
            'doc_date'       => $docDate,
            'doc_limit_date' => $docLimitDate,
            'month'          => $month,
            'year'           => $year,
            'period'         => $period,
            'shipment'       => $shipment,
            'customer'       => $customer,
            'billing'        => $billing
        ];
    }


    /*
     |--------------------------------------------------------------------------
     | Scopes
     |--------------------------------------------------------------------------
     */
    public function scopeFilterAgencies($query)
    {

        $user = Auth::user();
        $agencies = $user->agencies;

        if (!$user->hasRole([config('permissions.role.admin')]) || !empty($agencies)) {
            return $query->whereHas('customer', function ($q) {
                $q->whereNull('agency_id');
                $q->orWhereIn('agency_id', Auth::user()->agencies);
            });
        }
    }

    public function scopeIsActive($query)
    {
        return $query->where('is_deleted', 0);
    }

    public function scopeIsSettle($query, $settle=true)
    {
        return $query->where('is_settle', $settle);
    }

    public function scopeIsDeleted($query, $deleted=true)
    {
        return $query->where('is_deleted', $deleted);
    }

    public function scopeIsReversed($query, $reversed=true)
    {
        return $query->where('is_reversed', $reversed);
    }

    //filtra documentos não válidos fiscalmente
    public function scopeNotFiscalDocument($query)
    {
        $allowedDocs = [
            Invoice::DOC_TYPE_SIND,
            Invoice::DOC_TYPE_SINC,
            Invoice::DOC_TYPE_INTERNAL_DOC,
        ];

        return $query->whereIn('doc_type', $allowedDocs);
    }
    
    //filtra documentos válidos fiscalmente
    public function scopeIsFiscalDocument($query)
    {
        $allowedDocs = [
            Invoice::DOC_TYPE_SIND,
            Invoice::DOC_TYPE_SINC,
            Invoice::DOC_TYPE_INTERNAL_DOC,
            Invoice::DOC_TYPE_FP,
            Invoice::DOC_TYPE_RG,
            Invoice::DOC_TYPE_GT
        ];

        return $query->whereNotIn('doc_type', $allowedDocs);
    }

    //filtra documentos válidos para emitir regularização
    public function scopeAllowRegularizationDocs($query)
    {
        $allowedDocs = [
            Invoice::DOC_TYPE_SIND,
            Invoice::DOC_TYPE_SINC,
            Invoice::DOC_TYPE_INTERNAL_DOC,
            Invoice::DOC_TYPE_FT,
            Invoice::DOC_TYPE_FS,
            Invoice::DOC_TYPE_NC,
            Invoice::DOC_TYPE_ND,
        ];

        return $query->whereIn('doc_type', $allowedDocs);
    }

    //filtra documentos validos para a conta corrente
    public function scopeFilterBalanceDocs($query)
    {
        $allowedDocs = [
            Invoice::DOC_TYPE_SIND,
            Invoice::DOC_TYPE_SINC,
            Invoice::DOC_TYPE_FT,
            Invoice::DOC_TYPE_FR,
            Invoice::DOC_TYPE_FS,
            Invoice::DOC_TYPE_NC,
            Invoice::DOC_TYPE_ND,
            Invoice::DOC_TYPE_RC,
            Invoice::DOC_TYPE_INTERNAL_DOC,
            Invoice::DOC_TYPE_RG
        ];

        return $query->where(function($q) use($allowedDocs) {
            $q->where('is_deleted', 0);
            $q->where('is_draft', 0);
            $q->where('is_scheduled', 0);
            $q->whereIn('doc_type', $allowedDocs);
        });
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
        return $this->hasMany('App\Models\InvoiceLine', 'invoice_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice', 'assigned_invoice_id');
    }

    public function credit_note()
    {
        return $this->belongsTo('App\Models\Invoice', 'credit_note_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function customer_billing()
    {
        return $this->belongsTo('App\Models\CustomerBilling', 'target_id');
    }

    public function receipts()
    {
        return $this->hasMany('App\Models\InvoiceLine', 'assigned_invoice_id', 'id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo('App\Models\PaymentMethod', 'payment_method', 'code');
    }

    public function settleMethod()
    {
        return $this->belongsTo('App\Models\PaymentMethod', 'settle_method', 'code');
    }

    public function paymentCondition()
    {
        return $this->belongsTo('App\Models\PaymentCondition', 'payment_condition', 'code');
    }

    public function bank()
    {
        return $this->belongsTo('App\Models\Bank', 'payment_bank_id');
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
    public function setCustomerIdAttribute($value)
    {
        $this->attributes['customer_id'] = empty($value) ? null : $value;
    }

    public function setPaymentBankIdAttribute($value)
    {
        $this->attributes['payment_bank_id'] = empty($value) ? null : $value;
    }

    public function setSepaPaymentIdAttribute($value)
    {
        $this->attributes['sepa_payment_id'] = empty($value) ? null : $value;
    }

    public function setDocTotalPendingAttribute($value)
    {
        $this->attributes['doc_total_pending'] = empty($value) || $value == 0.00 || $value == $this->attributes['doc_total'] ? null : $value;
    }

    public function setCreditNoteIdAttribute($value)
    {
        $this->attributes['credit_note_id'] = empty($value) ? null : $value;
    }

    public function setAssignedInvoiceIdAttribute($value)
    {
        $this->attributes['assigned_invoice_id'] = empty($value) ? null : $value;
    }

    public function getCanDeleteAttribute()
    {
        if(in_array($this->doc_type, ['nodoc','internal-doc', 'proforma-invoice'])) {
            return true;
        } elseif(in_array($this->doc_type, ['credit-note', 'debit-note'])) {
            return false;
        }

        $docDate = new Date($this->doc_date);
        $days    = $docDate->diffInDays(Date::today());

        if($days <= 5) {
            return true;
        }

        return false;
    }

    public function getCanReverseAttribute()
    {
        if(in_array($this->doc_type, ['invoice','invoice-receipt', 'simplified-invoice', 'credit-note', 'debit-note'])) {
            return true;
        }

        return false;
    }

    public function getNameAttribute()
    {
        if ($this->attributes['doc_type']) {
            if ($this->attributes['doc_type'] == 'nodoc') {
                return $this->attributes['reference'];
            }
            if (@$this->attributes['doc_id']) {
                return trans('admin/billing.types_code.' . $this->attributes['doc_type']) . ' ' . $this->attributes['doc_id'];
            }

            return 'N/A';
        }

        return $this->attributes['doc_id'];
    }

    public function getDocPendingAttribute()
    {
        return is_null($this->attributes['doc_total_pending']) ? $this->attributes['doc_total'] : $this->attributes['doc_total_pending'];
    }
  
    public function getDueDateDaysLeftAttribute()
    {
        $today   = Date::today();
        $dueDate = new Date($this->attributes['due_date']);

        $daysDiff = $dueDate->diffInDays($today);

        return $daysDiff;
    }
}
