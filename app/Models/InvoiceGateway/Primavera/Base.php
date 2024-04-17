<?php
namespace App\Models\InvoiceGateway\Primavera;

use App\Models\Agency;
use App\Models\Traits\Xml2Array;
use Date, Response, Setting, File;

class Base {

    /**
     * @var string
     * https://developers.primaverabss.com/v10/recursos/web-api/
     * https://v10api.primaverabss.com/html/index.html
     */
    public $url;

    /**
     * @var null
     */
    public $session_id = null;

    /**
     * @var string
     */
    public $username = null;

    /**
     * @var string
     */
    public $password = null;

    /**
     * @var string
     */
    public $company = null;

    /**
     * @var string
     */
    public $line = null;

    /**
     * Constructor
     *
     * @param $agencia
     * @param $cliente
     * @param $password
     */
    public function __construct($apiKey = null)
    {

        ini_set('default_socket_timeout', 1200);

        //Empresa de testes AVA
        $this->url        = 'http://62.28.19.97:2018/WebAPI/';
        $this->username   = "webapi";
        $this->password   = "webapi#2022";
        $this->company    = "ENOVO"; //empresa testes
        $this->line       = "LP";
        $this->session_id = $this->login();

        $source = config('app.source');
        if($source == 'rimaalbe') {
            $this->url        = 'http://62.28.19.97:2018/WebAPI/';
            $this->username   = "webapi";
            $this->password   = "webapi#2022";
            $this->company    = "AVA"; //código da empresa testada no Primavera
            $this->line       = "LP"; //equivale à versão Profissional do Primavera que têm instalada
            $this->session_id = $this->login();
        }
    }


    /**
     * Generate a session token
     *
     * @return bool
     */
    public function login()
    {
        $data = [
            'username'   => $this->username,
            'password'   => $this->password,
            'company'    => $this->company,
            'line'       => $this->line,
            'instance'   => 'DEFAULT',
            'grant_type' => 'password',
        ];

        $response = $this->execute('token', $data);

        if(empty(@$response['access_token'])) {
            throw new \Exception('Falha no login. Sessão vazia');
        }

        $this->session_id = $response['access_token'];

        return $this->session_id;
    }

    /**
     * Return company details
     */
    public function getCompanyDetails() {
        
        $response = $this->execute('Administrador/ListaEmpresas', null, 'GET');

        return $response;
    }

    /**
     * Return api documentation
     */
    public function getDocumentation() {
        return false;
    }


    /**
     * Return api documentation
     */
    public function getSalesStats() {
        return false;
    }

    /**
     * Execute a soap request
     *
     * @param $nif
     * @return mixed
     * @throws \Exception
     */
    public function execute($urlMethod, $data = null, $method = 'POST')
    {
        
        $url = $this->url . $urlMethod;

        if($urlMethod == 'token') {
            $header = array(
                'Content-Type: application/x-www-form-urlencoded'
            );
        } else {
            $header = array(
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Bearer '.$this->session_id
            );
        }
        
        $data = $data ? http_build_query($data) : '';

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL             => $url,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => '',
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 0,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => $method,
            CURLOPT_POSTFIELDS      => $data,
            CURLOPT_HTTPHEADER      => $header,
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $responseDecoded = json_decode($response, true);

        if(empty($responseDecoded)) { 
            throw new \Exception('Erro interno servidor Primavera: '. $response);
        } elseif(@$responseDecoded['error']){
            throw new \Exception($responseDecoded['error']);
        }

        if(isset($responseDecoded['DataSet']['Table'])) {
            return $responseDecoded['DataSet']['Table'];
        }

        return $responseDecoded;
    }

    /**
     * Sync customer history
     *
     * @param $vat
     * @return mixed
     * @throws \Exception
     */
    public function syncCustomerHistory($customerId = null, $source = null)
    {

        if(empty($source)) {
            $source = config('app.source');
        }

        $agencies = Agency::whereSource($source)->pluck('id')->toArray();

        $customers = Customers::whereIn('agency_id', $agencies);
        
        if($customerId > 1) { //ignora validacao de nif se o id é o do consumidor final
            $customers = $customers->where(function($q){
                    $q->where('vat', '<>', '');
                    $q->orWhere('vat', '<>', '999999999');
                    $q->orWhere('vat', '<>', '999999990');
                    $q->orWhereNull('vat');
                });
        }

        if(!empty($customerId)) {
            $customers = $customers->whereId($customerId);
        }

        $customers = $customers->get([
            'id', 'vat', 'code', 'payment_method',
            'name', 'address', 'city', 'zip_code', 'country',
            'billing_name', 'billing_address', 'billing_city', 'billing_zip_code', 'billing_country'
        ]);

        $imported = 0;
        $errors = [];
        $success = [];

        foreach ($customers as $key => $customer) {

            $clienteComMultiplasFichas = \App\Models\Customer::filterSource()->where('vat', $customer->vat)->count();
            $clienteComMultiplasFichas = $clienteComMultiplasFichas > 1 ? true : false;

            $history = new Customer($this->apiKey);
            try {

                $documents = $history->getCustomerHistory($customer->vat);
          
                foreach ($documents as $documentItem) {

                    $types = array_flip(config('webservices_mapping.keyinvoice.doc_type'));

                    $docType = isset($types[$documentItem->DocType]) ? $types[$documentItem->DocType] : 'invoice';

                    $row = [$documentItem];

                    if(!empty($documentItem->DEB) && !empty($documentItem->CRE)) {
                        $documentCre = clone $documentItem;
                        $documentDeb = clone $documentItem;
                        $documentCre->DEB = 0;
                        $documentDeb->CRE = 0;

                        $row = [
                            $documentDeb,
                            $documentCre
                        ];

                    }

                    $invoice = null;
                    foreach ($row as $document) {

                        $sense = empty($document->DEB) ? 'credit' : 'debit';

                        $customerId = $customer->id;

                        if($clienteComMultiplasFichas) {

                            //se o cliente tem multiplas fichas é preciso ir á tabela de faturas ver
                            //se a fatura em causa já lá existe para associar o registo de conta corrente
                            //ao cliente correto
                            $invoice = Invoice::firstOrNew([
                                //'customer_id'   => $customer->id,
                                'vat'           => $customer->vat,
                                'doc_type'      => $docType,
                                'doc_id'        => $document->IdDoc,
                                'doc_series_id' => $document->DocSeriesID,
                                'doc_series'    => $document->DocSeries,
                            ]);


                            /* $balance = CustomerBalance::firstOrNew([
                                'customer_id'   => $invoice->customer_id ? $invoice->customer_id : $customerId, //cliente correto da fatura
                                'doc_type'      => $docType,
                                'doc_id'        => $document->IdDoc,
                                'doc_serie_id'  => $document->DocSeriesID,
                                'doc_serie'     => $document->DocSeries,
                                'sense'         => $sense
                            ]); */

                        } else {

                            //clientes que só existem 1x no sistema
                            /* $balance = CustomerBalance::firstOrNew([
                                'customer_id'   => $customerId,
                                'doc_type'      => $docType,
                                'doc_id'        => $document->IdDoc,
                                'doc_serie_id'  => $document->DocSeriesID,
                                'doc_serie'     => $document->DocSeries,
                                'sense'         => $sense
                            ]); */
                        }

                 

                        $days = 30;
                        if(!$balance->exists || ($balance->exists && $document->Canceled)) {

                            $date = new Date($document->Date);

                            if($customer->payment_method == '15d') {
                                $days = 15;
                            } elseif($customer->payment_method == '45d') {
                                $days = 45;
                            } elseif($customer->payment_method == '60d') {
                                $days = 60;
                            } elseif($customer->payment_method == '90d') {
                                $days = 90;
                            } elseif($customer->payment_method == '120d') {
                                $days = 120;
                            } elseif($customer->payment_method == 'prt') {
                                $days = 0;
                            }

              
                            if($docType == 'regularization') {
                               // $balance->is_hidden = 1; //oculta as regularizações por defeito
                            }

                            $balance->gateway   = 'keyinvoice';
                            $balance->date      = $date;
                            $balance->due_date  = $date->addDays($days);
                            $balance->total     = empty($document->DEB) ? $document->CRE : $document->DEB;
                            $balance->sense     = $sense;
                            $balance->reference = $document->RefDoc;
                            $balance->canceled  = $document->Canceled;
                            $balance->balance   = $document->BALANCE;
                            /* if($document->Canceled) {
                                $balance->deleted_at = date('Y-m-d H:i:s'); //comentado em 2023/03/23. As faturas mesmo anuladas ficavam escondidas da conta corrente
                            } */

                            /* if($balance->doc_type == 'invoice-receipt' && $balance->sense == 'credit') {
                                $balance->is_paid = true; //comentado em 2023/03/23. As FR ficavam como não pagas
                            } */

                            if($balance->doc_type == 'invoice-receipt') {
                                $balance->is_paid = true;                            
                            }
                       
                            $balance->save();

                            $imported++;
                            $success[] = $customer->code;
                        }
                    }


                    //insert or update invoice on invoices tables
                    if(!$invoice) {
                        $invoice = Invoice::firstOrNew([
                            //'customer_id'   => $customer->id,
                            'vat'           => $customer->vat,
                            'doc_type'      => $docType,
                            'doc_id'        => $documentItem->IdDoc,
                            'doc_series_id' => $documentItem->DocSeriesID,
                            'doc_series'    => $documentItem->DocSeries,
                        ]);
                    }

                    if($invoice->exists) {

                        if ($invoice->due_date && $invoice->due_date != $balance->due_date->format('Y-m-d')) { //necessário corrigir
                            $balance->due_date = $invoice->due_date;
                            $balance->save();
                        }

                    } else {

                        //insere na tabela invoices_series caso a série não exista em sistema
                        $invoiceSerie = InvoiceSerie::firstOrNew([
                            'serie_id' => $documentItem->DocSeriesID
                        ]);

                        if(!$invoiceSerie->exists) {
                            $invoiceSerie->source   = config('app.source');
                            $invoiceSerie->serie_id = $documentItem->DocSeriesID;
                            $invoiceSerie->doc_type = $docType;
                            $invoiceSerie->code     = $documentItem->DocSeries;
                            $invoiceSerie->name     = trans('admin/billing.types.' . $docType) . ' ' . $documentItem->DocSeries;
                            $invoiceSerie->save();
                        }

                        $docDt = new Date($documentItem->Date);
                        $dueDate = $docDt->addDays($days)->format('Y-m-d');

                        $invoice->source        = config('app.source');
                        $invoice->target        = 'Invoice';
                        $invoice->gateway       = 'KeyInvoice';
                        $invoice->customer_id   = $customer->id;
                        $invoice->vat           = $customer->vat;
                        $invoice->doc_type      = $docType;
                        $invoice->doc_id        = $documentItem->IdDoc;
                        $invoice->doc_series_id = $documentItem->DocSeriesID;
                        $invoice->doc_series    = $documentItem->DocSeries;
                        $invoice->doc_date      = $documentItem->Date;
                        $invoice->due_date      = $dueDate;
                        $invoice->doc_total     = empty($documentItem->DEB) ? $documentItem->CRE : $documentItem->DEB;
                        $invoice->reference     = $documentItem->RefDoc;
                        $invoice->is_deleted    = $documentItem->Canceled;
                        $invoice->is_draft      = 0;
                        $invoice->is_settle     = 0;

                        $invoice->billing_code      = $customer->code;
                        $invoice->billing_name      = $customer->billing_name;
                        $invoice->billing_address   = $customer->billing_address;
                        $invoice->billing_zip_code  = $customer->billing_zip_code;
                        $invoice->billing_city      = $customer->billing_city;
                        $invoice->billing_country   = $customer->billing_country;
                        $invoice->billing_email     = $customer->billing_email;

                        if($docType == 'regularization') {
                            $invoice->is_hidden = 1; //oculta as regularizações por defeito
                        }

                        $invoice->save();
                    }



                    //se o cliente tem várias fichas de cliente, é preciso forçar a que as faturas pertençam a cada cliente correspondente
                    //para que cada cliente tenha a sua propria conta corrente
                    if($clienteComMultiplasFichas) {
                        if($balance->customer_id != $invoice->customer_id) {
                            //dd($balance->customer_id .' ---> '. $invoice->customer_id . '  ==> '. $invoice->doc_id);
                            $balance->customer_id = $invoice->customer_id;
                            $balance->update([
                                'customer_id' => $invoice->customer_id
                            ]);
                        }

                    }
                }

            } catch (\Exception $e) {
                //dd($e->getMessage(). ' file '. $e->getFile() . ' line '. $e->getLine());
                $errors[] = 'Erro no cliente ' . @$customer->id.' ' . $e->getMessage();
            }


        }
        return $imported;
    }

    /**
     * Check if invoice exists on invoices table
     *
     * @param $customer
     * @param $docType
     * @param $documentItem
     * @return mixed
     */
    public function checkIfExistsOnInvoicesTable($customer,$docType, $documentItem) {
        //insert or update invoice on invoices tables
        $invoice = Invoice::firstOrNew([
            //'customer_id'   => $customer->id,
            'vat'           => $customer->vat,
            'doc_type'      => $docType,
            'doc_id'        => $documentItem->IdDoc,
            'doc_series_id' => $documentItem->DocSeriesID,
            'doc_series'    => $documentItem->DocSeries,
        ]);

        return $invoice;
    }

    /**
     * Sync customer history
     *
     * @param $vat
     * @return mixed
     * @throws \Exception
     */
    public function massSyncCustomerHistory($source = null)
    {

        if(empty($source)) {
            $source = config('app.source');
        }

        $agencies = Agency::whereSource($source)->pluck('id')->toArray();

        $customers = Customers::whereIn('agency_id', $agencies)
            ->where(function($q){
                $q->where('vat', '<>', '');
                $q->orWhere('vat', '<>', '999999999');
                $q->orWhereNull('vat');
            })
            ->get(['id', 'vat', 'code']);

        $imported = 0;
        $errors = [];
        $success = [];

        foreach ($customers as $key => $customer) {

            $history = new Customer();
            try {
                $documents = $history->getCustomerHistory($customer->vat);
            } catch (\Exception $e) {
                $errors[] = 'Erro no cliente ' . @$customer->id.' ' . $e->getMessage();
            }

        }
        return $imported;
    }

    /**
     * Validate Vat
     * @param Request $request
     * @return array|mixed
     */
    public function validateVat($vat, $country = 'pt')
    {
        $vat = strtoupper($country) . $vat;

        //each month day has a specific key
        $key = 'a08ba9003bbd5c96e60f27472e394bb7';

        if($country == 'pt') {
            $result['valid'] = validarNIF($vat);
        } else {
            $url = 'http://apilayer.net/api/validate?access_key='.$key.'&vat_number='.$vat.'&format=1';
            $result = json_decode(file_get_contents($url));
            $result = (array) $result;
        }

        if(@$result['valid']) {
            $result['result'] = true;
        } elseif(@$result['format_valid']) {
            $result['result'] = true;
        } else {
            $result['result']  = false;
            $result['message'] = 'O NIF indicado não é válido para ' . trans('country.'.$country). '.';
        }

        $result['company_address'] = explode(PHP_EOL, @$result['company_address']);
        $result['address'] = trim(@$result['company_address'][0] . ' ' . @$result['company_address'][1]);
        $result['zip_code'] = @$result['company_address'][2];

        if(!empty($result['zip_code'])) {
            $exploded = explode(' ', $result['zip_code']);
            $result['zip_code'] = trim(@$exploded[0]);
            $result['city'] = trim(@$exploded[1]);
        }

        $result = [
            'result'      => @$result['result'],
            'feedback'    => @$result['message'],
            'name'        => @$result['company_name'],
            'address'     => @$result['address'],
            'zip_code'    => @$result['zip_code'],
            'city'        => @$result['city'],
        ];

        return $result;
    }

    /**
     * Store keyinvoice log
     */
    public function storeLog($method, $data) {
        $folder = storage_path() . '/primavera-logs/';
        $filename = $folder.date('Y-m-d').'.json';

        if(!File::exists($folder)){
            File::makeDirectory($folder);
        }

        $logRow = '['.date('Y-m-d H:i:s').'] Method: '. $method.' Input: ' . print_r($data, true);
        file_put_contents($filename, $logRow . PHP_EOL, FILE_APPEND);
    }

    /**
     * Download SAFT file
     * @param $year
     * @param $month
     * @return mixed
     */
    public static function downloadSaft($year, $month, $returnFile = false){
        return false;
    }

}