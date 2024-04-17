<?php
namespace App\Models\InvoiceGateway\KeyInvoice;

use App\Models\Agency;
use App\Models\Billing\ApiKey;
use App\Models\CustomerBalance;
use App\Models\Invoice;
use App\Models\InvoiceSerie;
use Date, Response, Setting, File;
use Webit\GlsTracking\Api\Exception\Exception;
use App\Models\Customer as Customers;

class Base {

    /**
     * @var string
     */
    //public $url   = 'https://login.keyinvoice.com/API3_ws.php?wsdl';
    public $url   = 'https://quickbox.pt/assets/API3_ws.xml';
    public $urlAo = 'https://app.keyinvoice.ao/API3_ws.php?wsdl'; //213.32.76.141;
    public $externalUrl     = 'https://zantia.com/endpoint/keyinvoice';
    public $externalEndoint = false;

    /**
     * @var string
     */
    public $keyinvoice;

    /**
     * @var string
     */
    public $apiKey;

    /**
     * @var null
     */
    public $session_id = null;

    /**
     * KeyInvoice Constructor
     *
     * @param $agencia
     * @param $cliente
     * @param $password
     */
    public function __construct($apiKey = null)
    {
        if(config('app.source') == 'caminhoslogicos') {
            $this->url = 'https://pt.keyinvoice.com/API3_ws.php?wsdl';
        }

        if(config('app.env') == 'local') {
            $apiKey = '128046n67p11d5312a8d50b907221970ecebca8f38'; //TESTES
        } else if(empty($apiKey)) {
            $apiKey = ApiKey::getDefaultKey();
        }

        //$apiKey = '108097n0fpa9951d74a7be9c4d1f04480ad7500dee';

        if(empty($apiKey)) {
            throw new Exception('Não está configurada nenhuma chave da API para ligação com o software de faturação KeyInvoice.');
        }

        $this->apiKey = $apiKey;
        if(!$this->externalEndoint) {
            $this->keyinvoice = new \SoapClient($this->getUrl(), array('cache_wsdl' => WSDL_CACHE_DISK, 'trace' => true));
            //dd($this->keyinvoice->__getFunctions());
            $this->session_id = $this->login($apiKey);
        }


    }

    /**
     * Return base url
     * @return string
     */
    public function getUrl() {
        if(config('app.source') == 'aselexpress') {
            return $this->urlAo;
        }

        return $this->url;
    }

    /**
     * Generate a session id
     *
     * @return bool
     */
    public function login($apiKey)
    {

        //current time
        $now = Date::now();

        //check stored session
        $folder = storage_path() . '/keyinvoice-logs/';
        $filename = $folder.'session.json';

        if(!File::exists($folder)){
            File::makeDirectory($folder);
        }

        if(!File::exists($filename)){
            File::put($filename, ''); //cria ficheiro em vazio
        } else {
            $content = json_decode(File::get($filename), true);

            $content = @$content[$apiKey];

            if(!empty($content)) {
                $expireTime = new Date(@$content['time']);
                $expireTime = $expireTime->subMinutes(10);

                if($now->lte($expireTime)) {
                    $this->session_id = @$content['authId'];
                }
            }
        }

        //if dont has stored version, create new session
        if(empty($this->session_id)) {

            $this->storeLog('authenticate', [$apiKey]);

            $client   = new \SoapClient($this->getUrl(), ['encoding' => 'UTF-8']);

            $response = $client->authenticate($apiKey);

            if($response[0] > 0 || $response[0] == "1") {
                $this->session_id = $response[1];
            } else {
                throw new \Exception($this->mapError($response[0]));
            }

            $content = json_decode(File::get($filename), true);

            if(empty($content)) {
                $content = [];
            }

            $content[$apiKey] = [
                'apiKey' => $apiKey,
                'authId' => $this->session_id,
                'time'   => $now->addHour(1)->format('Y-m-d H:i:s'),
            ];

            File::put($filename, json_encode($content));
        }

        return $this->session_id;
    }

    /**
     * Return company details
     */
    public function getCompanyDetails() {

        $data = [
            'sid' => $this->session_id
        ];

        return $this->execute('company', $data);
    }

    /**
     * Return api documentation
     */
    public function getDocumentation() {

        $data = [
            //'sid'     => $this->session_id,
            'command' => 'GLOBAL',
            'param' => ''
        ];

        return $this->execute('documentation', $data);
    }


    /**
     * Return api documentation
     */
    public function getSalesStats() {

        $data = [
            'sid'  => $this->session_id,
            'type' => 'sales',
            //'params' => ''
        ];

        return $this->execute('getstatistics', $data);
    }

    /**
     * Execute a soap request
     *
     * @param $nif
     * @return mixed
     * @throws \Exception
     */
    public function execute($method, $data, $uniqueResult = false)
    {
        $this->storeLog($method, $data);

        try {
            if($this->externalEndoint) {
                $response = $this->requestExternal($method, $data, $uniqueResult);
            } else {
                $data = array_values($data);
                $response = call_user_func_array(array($this->keyinvoice, $method), $data);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        if($method == 'checkIfSettle') {
            if(@$response[0] == '-666') {
                return "0"; //por vezes o metodo check if settle devolve -666 para faturas já pagas
            } elseif(@$response[0] == '-777') {
                return "1"; //fatura apagada mas não liquidada
            }
        }

        if(isset($response->RC)) { //devolve vários resultados
            if($response->RC <= 0) {
                throw new \Exception($this->mapError($response->RC));
            }

            if($uniqueResult) {
                return @$response->DAT[0];
            } else {
                return @$response->DAT;
            }

        } else { //devolução de resultados simples ou booleanos

            if(@$response[0] <= 0) {

                if(isset($response[1])) { //se existe mensagem de erro, apresenta-a, caso contrario obtem da lista de erros

                    if(isset($response[1]) && $response[1] == '-308') {
                        return true;
                    }

                    throw new \Exception($this->mapError(utf8_decode($response[1])));
                } else {
                    throw new \Exception($this->mapError($response[0]));
                }

            } else {

                if(isset($response[1])) {
                    return $response[1];
                } else {
                    return $response[0];
                }

            }
        }
    }

    /**
     * Redireciona os pedidos para outra máquina
     *
     * @param $url
     * @param $xml
     * @return bool|string
     */
    private function requestExternal($method, $data, $uniqueResult)
    {
        $urlExternal = $this->externalUrl;

        $data = [
            'source' => config('app.source'),
            'data'   => json_encode($data),
            'method' => $method,
            'unique_result' => $uniqueResult,
            'api_key' => $this->apiKey
        ];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL             => $urlExternal,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => '',
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 0,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => 'POST',
            CURLOPT_POSTFIELDS      => $data,
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($response);
        return $response;
    }


    /**
     * Map erros by code of error
     *
     * @param $errorNo
     * @return mixed
     */
    public function mapError($errorNo) {

        $errors = [
            '1'    => 'Acção efectuada com sucesso.',
            '0'    => 'Código de resposta não documentado.',
            '-1'   => 'Autenticação falhada. Verificar a Chave API_KEY',
            '-2'   => 'Faltam dados de configuração. Verifique a sua conta.',
            '-3'   => 'Não foi possível criar uma sessão (5 tentativas).',
            '-4'   => 'Sessão Expirada. Este código de sessão já não é válido. (TTL 3600s )',
            '-5'   => 'Erro de parâmetros. Restrição de «Parâmetros não vazios» não respeitada',
            '-6'   => 'Funcionalidade indisponível para a sua licença.',
            '-12'  => 'Não foi possível gerar os dados do ficheiro para envio',
            '-101' => 'Não foi possível gravar os dados do cliente!',
            '-102' => 'Não foi possível encontrar o cliente pelo NIF indicado!',
            '-104' => 'Não foi possível ler os dados do cliente!',
            '-105' => 'Não foi possível eliminar o cliente!',
            '-111' => 'Não foi possível criar novo registo de Morada Alternativa',
            '-112' => 'Não foi possível alterar o registo de Morada Alternativa',
            '-113' => 'Não foi possível carregar o registo de Morada Alternativa',
            '-114' => 'Não foi possível apagar o registo de Morada Alternativa',
            '-121' => 'Não foi possível carregar informação do cliente ao qual pretende associar a entidade',
            '-122' => 'Não foi possível carregar informação da entidade indicada',
            '-151' => 'Não foi possível gravar os dados do contacto!',
            '-152' => 'Não foi possível encontrar o contacto pelo ID indicado!',
            '-154' => 'Não foi possível ler os dados do contacto!',
            '-155' => 'Não foi possível eliminar o contacto!',
            '-156' => 'Não foi possível encontrar o contacto!',
            '-201' => 'Não foi possível gravar os dados do artigo!',
            '-202' => 'Não foi possível ler os dados do artigo!',
            '-203' => 'Não foi possível criar o artigo: esta referência já existe.',
            '-204' => 'O artigo não existe.',
            '-205' => 'Não foi possível apagar o artigo.',
            '-206' => 'Não foi possível gravar a taxa de IVA.',
            '-211' => 'Não foi possível copiar a imagem a partir do URL indicado.',
            '-301' => 'Nº de contribuinte Português inválido!',
            '-302' => 'Não foi possível gravar a linha de documento.',
            '-303' => 'Não foi possível ler os dados do cabeçalho para o código indicado.',
            '-304' => 'Não foi possível gravar definitivamente o cabeçalho com o código indicado.',
            '-305' => 'O documento com código indicado não existe.',
            '-306' => 'Não foi possível gravar a linha do documento.',
            '-307' => 'Não foi possível guardar os detalhes do cabeçalho.',
            '-308' => 'Não foi possível guardar o documento.',
            '-311' => 'Não foi possível enviar o email. SMTP inactivo ou com configuração errada.',
            '-312' => 'Não foi possível enviar o email. Erro na geração do ficheiro.',
            '-321' => 'Ocorreu um erro na comunicação com a Autoriadade Tributária',
            '-322' => 'Dados inválidos, ou funcionalidade inexistente no Sistema de Facturação.',
            '-331' => 'Não foi possível adicionar a informação de Cor/Tamanho à linha do documento',
            '-340' => 'Erro ao ler Documento.',
            '-341' => 'O Documento não se encontra fechado, pelo qual não pode ser faturado.',
            '-342' => 'O Documento encontra-se anulado ou já foi convertido.',
            '-343' => 'Erro ao tentar ler Série de Documento.',
            '-344' => 'Erro ao gravar cabeçalho do documento.',
            '-345' => 'Erro ao gravar a linha do documento',
            '-401' => 'Não foi possível gravar os dados do fornecedor!',
            '-402' => 'Não foi possível encontrar o fornecedor pelo NIF indicado!',
            '-403' => 'Não foi possível carregar os dados do fornecedor indicado',
            '-404' => 'Não foi possível ler os dados do fornecedor!',
            '-405' => 'Não foi possível eliminar o fornecedor!',
            '-501' => 'Não foi possível criar o novo registo de cor/tamanho',
            '-511' => 'O País indicado não existe',
            '-512' => 'O País indicado já existe',
            '-513' => 'Não foi possível gravar o novo país.',
            '-521' => 'Não foi possível gravar a nova moeda.',
            '-522' => 'Não foi possível ler o registo de moeda indicado.',
            '-523' => 'Não foi possível actualizar a moeda.',
            '-524' => 'A moeda indicada não existe.',
            '-525' => 'Não foi possível apagar a moeda.',
            '-526' => 'Não foi possível gravar um valor de conversão para esta moeda.',
            '-527' => 'Não foi possível associar esta moeda como segunda moeda do documento.',
            '-528' => 'Não foi possível gravar o documento com os dados de segunda moeda.',
            '-551' => 'Não foi possível gravar os dados da família!',
            '-552' => 'Não foi possível encontrar a família pelo identificador indicado!',
            '-553' => 'Não foi possível carregar os dados da família indicada',
            '-554' => 'Não foi possível ler os dados da família!',
            '-555' => 'Não foi possível eliminar a família!',
        ];

        return isset($errors[$errorNo]) ? $errors[$errorNo] : $errorNo;
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


                            $balance = CustomerBalance::firstOrNew([
                                'customer_id'   => $invoice->customer_id ? $invoice->customer_id : $customerId, //cliente correto da fatura
                                'doc_type'      => $docType,
                                'doc_id'        => $document->IdDoc,
                                'doc_serie_id'  => $document->DocSeriesID,
                                'doc_serie'     => $document->DocSeries,
                                'sense'         => $sense
                            ]);

                        } else {

                            //clientes que só existem 1x no sistema
                            $balance = CustomerBalance::firstOrNew([
                                'customer_id'   => $customerId,
                                'doc_type'      => $docType,
                                'doc_id'        => $document->IdDoc,
                                'doc_serie_id'  => $document->DocSeriesID,
                                'doc_serie'     => $document->DocSeries,
                                'sense'         => $sense
                            ]);
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
/*

                foreach ($documents as $document) {

                    $types = array_flip(config('webservices_mapping.keyinvoice.doc_type'));

                    $docType = isset($types[$document->DocType]) ? $types[$document->DocType] : 'invoice';

                    $balance = CustomerBalance::firstOrNew([
                        'customer_id'   => $customer->id,
                        'doc_type'      => $docType,
                        'doc_id'        => $document->IdDoc,
                        'doc_serie_id'  => $document->DocSeriesID,
                        'doc_serie'     => $document->DocSeries,
                    ]);

                    if(!$balance->exists) {

                        $date = new Date($document->Date);

                        $balance->gateway   = 'keyinvoice';
                        $balance->date      = $date;
                        $balance->due_date  = $date->addDays(30);
                        $balance->total     = empty($document->DEB) ? $document->CRE : $document->DEB;
                        $balance->sense     = empty($document->DEB) ? 'credit' : 'debit';
                        $balance->reference = $document->RefDoc;
                        $balance->save();

                        $imported++;
                        $success[] = $customer->code;
                    }
                }*/

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
        $folder = storage_path() . '/keyinvoice-logs/';
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

        if($year > date('Y')) {
            throw new \Exception('Ano inválido ('. $year.')');
        }

        if($year == date('Y') && $month > date('m')) {
            throw new \Exception('Não pode emitir o ficheiro SAF-T para o mês e ano indicado');
        }

        $gateway = new Document();
        $file = $gateway->getSaftFile($year, $month);

        if($returnFile) {
            $file;
        }

        header("Content-Type: application/zip");
        header('Content-Disposition: inline; filename="SAFT_'.trans('datetime.month.'.$month).'_'.$year.'.zip"');
        echo base64_decode($file);
        exit;
    }



    /* 

    METODOS EM 20/ABRIL/2023
    
    $metodos1 = [
        0 => "UNKNOWN documentation(string $command, string $param)"
        1 => "string responseMessage(string $responseCode, string $lang)"
        2 => "UNKNOWN authenticate(string $apikey)"
        3 => "CompanyResponse company(string $sid)"
        4 => "UNKNOWN verifyUserInsertionPricesWithVAT(string $sid)"
        5 => "UNKNOWN verifyPriceLevelVAT(string $sid)"
        6 => "UNKNOWN getSAFTfile(string $sid, string $year, string $month)"
        7 => "BrandResponse getBrands(string $sid)"
        8 => "TableResponse getColorsSizes(string $sid)"
        9 => "UNKNOWN insertColorSize(string $sid, string $type, string $name)"
        10 => "TableResponse getTaxes(string $sid)"
        11 => "TableResponse getPriceLevels(string $sid)"
        12 => "DiscountResponse getDiscounts(string $sid, UNKNOWN $refs, string $nif)"
        13 => "TableResponse listDocumentSeries(string $sid, UNKNOWN $docType)"
        14 => "TableResponse listAllDocumentSeries(string $sid, UNKNOWN $docType)"
        15 => "TableResponse listStores(string $sid)"
        16 => "TableResponse listWarehouse(string $sid)"
        17 => "WarehouseStockResponse listWarehouseStock(string $sid, string $warehouse)"
        18 => "UNKNOWN familySearch(string $sid, string $idContact)"
        19 => "FamilyResponse getFamilies(string $sid)"
        20 => "UNKNOWN countFamilies(string $sid)"
        21 => "FamilyResponse listFamilies(string $sid, string $offset)"
        22 => "UNKNOWN familyExists(string $sid, string $idfamily)"
        23 => "FamilyResponse getFamily(string $sid, string $idfamily)"
        24 => "UNKNOWN insertFamily(string $sid, string $name, string $ref, string $idparentfamily)"
        25 => "UNKNOWN updateFamily(string $sid, string $idfamily, string $name, string $ref, string $idparentfamily)"
        26 => "UNKNOWN deleteFamily(string $sid, string $idfamily)"
        27 => "UNKNOWN countryExists(string $sid, string $code)"
        28 => "UNKNOWN insertCountry(string $sid, string $code, string $name)"
        29 => "TableResponse getCountries(string $sid)"
        30 => "UNKNOWN countCurrencies(string $sid)"
        31 => "CurrencyResponse listCurrencies(string $sid, string $offset)"
        32 => "UNKNOWN currencyExists(string $sid, string $currency_id)"
        33 => "CurrencyResponse getCurrency(string $sid, string $currency_id)"
        34 => "UNKNOWN insertCurrency(string $sid, string $currency, string $name, string $integername, string $decimalname, string $symbol)"
        35 => "UNKNOWN updateCurrency(string $sid, string $currency_id, string $currency, string $name, string $integername, string $decimalname, string $symbol)"
        36 => "UNKNOWN deleteCurrency(string $sid, string $currency_id)"
        37 => "TableResponse listCurrencyConversions(string $sid, string $currency_id, string $offset)"
        38 => "UNKNOWN insertCurrencyConversion(string $sid, string $currency_id, string $conversiondate, string $conversionvalue)"
        39 => "UNKNOWN countClients(string $sid)"
        40 => "PersonResponse listClients(string $sid, string $offset)"
        41 => "UNKNOWN clientExists(string $sid, string $nif)"
        42 => "PersonResponse getClient(string $sid, string $nif)"
        43 => "UNKNOWN getClientInfo(string $sid, string $nif, string $fieldname)"
        44 => "UNKNOWN insertClient(string $sid, string $nif, string $name, string $address, string $postalCode, string $locality, string $phone, string $fax, string $email, string $obs)"
        45 => "UNKNOWN insertForeignClient(string $sid, string $nif, string $country, string $name, string $address, string $postalCode, string $locality, string $phone, string $fax, string $email, string $obs)"
        46 => "UNKNOWN updateClient(string $sid, string $nif, string $name, string $address, string $postalCode, string $locality, string $phone, string $fax, string $email, string $obs)"
        47 => "UNKNOWN setClientField(string $sid, string $nif, string $fieldname, string $value)"
        48 => "UNKNOWN deleteClient(string $sid, string $nif)"
        49 => "PersonResponse searchClients(string $sid, string $searchTerm)"
        50 => "PersonResponse_custom searchClients_custom(string $sid, string $CodigoCliente, string $Nome, string $Nif, string $Contacto)"
        51 => "ClientHistoryResponse getClientHistory(string $sid, string $nif)"
        52 => "UNKNOWN sendClientHistory2Email(string $sid, string $nif, string $email)"
        53 => "UNKNOWN getClientDetails(string $sid, string $nif)"
        54 => "UNKNOWN insertClientById(string $sid, string $nif, string $name, string $address, string $postalCode, string $locality, string $phone, string $fax, string $email, string $obs, string $client_id)"
        55 => "UNKNOWN insertForeignClientById(string $sid, string $nif, string $country, string $name, string $address, string $postalCode, string $locality, string $phone, string $fax, string $email, string $obs, string $client_id)"
        56 => "UNKNOWN countAltAddresses(string $sid, string $nif)"
        57 => "UNKNOWN listAltAddresses(string $sid, string $nif)"
        58 => "UNKNOWN insertAltAddress(string $sid, string $nif, string $address, string $postalCode, string $locality)"
        59 => "UNKNOWN updateAltAddress(string $sid, string $nif, string $addressRef, string $address, string $postalCode, string $locality)"
        60 => "UNKNOWN deleteAltAddress(string $sid, string $nif, string $addressRef)"
        61 => "UNKNOWN countEntities(string $sid, string $nif)"
        62 => "PersonResponse listEntities(string $sid, string $nif, string $offset)"
        63 => "UNKNOWN entityExists(string $sid, string $internalRef)"
        64 => "PersonResponse getEntity(string $sid, string $internalRef)"
        65 => "UNKNOWN insertEntity(string $sid, string $parentNif, string $nif, string $name, string $address, string $postalCode, string $locality, string $phone, string $fax, string $email, string $obs)"
        66 => "UNKNOWN updateEntity(string $sid, string $internalRef, string $nif, string $name, string $address, string $postalCode, string $locality, string $phone, string $fax, string $email, string $obs)"
        67 => "UNKNOWN deleteEntity(string $sid, string $internalRef)"
        68 => "UNKNOWN countSellers(string $sid)"
        69 => "PersonResponse listSellers(string $sid, string $offset)"
        70 => "UNKNOWN sellerExists(string $sid, string $nif)"
        71 => "PersonResponse getSeller(string $sid, string $nif)"
        72 => "UNKNOWN insertSeller(string $sid, string $nif, string $name, string $address, string $postalCode, string $locality, string $phone, string $fax, string $email, string $obs)"
        73 => "UNKNOWN insertSellerById(string $sid, string $nif, string $name, string $address, string $postalCode, string $locality, string $phone, string $fax, string $email, string $obs, string $seller_id)"
        74 => "UNKNOWN updateSeller(string $sid, string $nif, string $name, string $address, string $postalCode, string $locality, string $phone, string $fax, string $email, string $obs)"
        75 => "UNKNOWN deleteSeller(string $sid, string $nif)"
        76 => "UNKNOWN countProducts(string $sid)"
        77 => "UNKNOWN countProductsSerie(string $sid)"
        78 => "ProductResponse listProducts(string $sid, string $offset)"
        79 => "ProductResponse listProductsSerie(string $sid, string $offset)"
        80 => "UNKNOWN listProductsRef(string $sid, string $offset)"
        81 => "UNKNOWN listProductsCSRef(string $sid, string $offset)"
        82 => "ProductResponse listProductsTitles(string $sid, string $offset)"
        83 => "UNKNOWN listProductsDsc(string $sid, string $offset)"
        84 => "UNKNOWN productExists(string $sid, string $ref)"
        85 => "ProductResponse getProduct(string $sid, string $ref)"
        86 => "ProductInfoResponse getProduct_additionalInfo(string $sid, string $ref)"
        87 => "UNKNOWN getProductInfo(string $sid, string $ref, string $fieldname)"
        88 => "TableResponse getRelatedProducts(string $sid, string $ref)"
        89 => "TableResponse getProductPrices(string $sid, string $ref)"
        90 => "UNKNOWN listProductsPrice(string $sid)"
        91 => "UNKNOWN insertProduct(string $sid, string $ref, string $designation, string $shortName, string $tax, string $obs, string $isService, string $hasStocks, string $active, string $shortDesc, string $longDesc, string $price, string $vendorRef, string $ean)"
        92 => "UNKNOWN updateProduct(string $sid, string $ref, string $designation, string $shortName, string $tax, string $obs, string $isService, string $hasStocks, string $active, string $shortDesc, string $longDesc, string $price, string $vendorRef, string $ean)"
        93 => "UNKNOWN changeProductTax(string $sid, string $ref, string $taxid)"
        94 => "UNKNOWN changeProductFamily(string $sid, string $ref, string $idfamily)"
        95 => "UNKNOWN setProductField(string $sid, string $ref, string $fieldname, string $value)"
        96 => "UNKNOWN insertProductImageByURL(string $sid, string $ref, string $url)"
        97 => "UNKNOWN deleteProduct(string $sid, string $ref)"
        98 => "ProductResponse searchProducts(string $sid, string $searchTerm)"
        99 => "ProductResponseDetails getProductDetails(string $sid, string $ref)"
        100 => "ProductResponse_custom searchProducts_custom(string $sid, string $searchTerm)"
        101 => "TableResponse getProductComplements(string $sid, string $ref)"
        102 => "UNKNOWN getCSInfo(string $sid, string $type, string $ref, string $fieldname)"
        103 => "UNKNOWN getCSFoto(string $sid, string $ref, string $id)"
        104 => "UNKNOWN countProductsCS(string $sid)"
        105 => "ProductResponse listProductsCS(string $sid, string $offset)"
        106 => "TableResponse getProductCSInfo(string $sid, string $ref)"
        107 => "TableResponse getProductCS(string $sid, string $ref)"
        108 => "TableResponse getProductCSstock(string $sid, string $ref)"
        109 => "UNKNOWN getAllProductsCSstock(string $sid)"
        110 => "UNKNOWN insertProductCS(string $sid, string $ref, string $designation, string $shortName, string $tax, string $obs, string $isService, string $hasStocks, string $active, string $shortDesc, string $longDesc, string $price, string $vendorRef, string $ean)"
        111 => "UNKNOWN insertProductColorSize(string $sid, string $ref, string $type, string $internalref)"
        112 => "UNKNOWN countDocuments(string $sid, string $docType)"
        113 => "UNKNOWN countAllDocuments(string $sid, string $date_from, string $date_to, string $read)"
        114 => "DocumentResponse documentsList(string $sid, string $docType, string $offset)"
        115 => "DocumentResponse documentsList_byClient(string $sid, string $docType, string $nif, string $offset)"
        116 => "DocumentResponse documentsList_custom(string $sid, string $docType, string $offset, string $order_by, string $sort_by, string $docseries)"
        117 => "DocumentResponse documentsList_byClient_custom(string $sid, string $docType, string $nif, string $offset, string $order_by, string $sort_by, string $docseries)"
        118 => "DocumentResponse documentsDraftsList(string $sid, string $docType, string $nif, string $offset)"
        119 => "AllDocsResponse listAllDocuments(string $sid, string $date_from, string $date_to, string $read, string $offset)"
        120 => "UNKNOWN documentExists(string $sid, string $idDoc, string $docType)"
        121 => "DocumentResponse getDocument(string $sid, string $idDoc, string $docType)"
        122 => "DocumentResponse getDocument_bySeries(string $sid, string $idDoc, string $docType, string $docSeries)"
        123 => "DocumentDetailsResponse getDocumentDetails_bySeries(string $sid, string $idDoc, string $docType, string $docSeries)"
        124 => "UNKNOWN getDocumentInfo(string $sid, string $idDoc, string $docType, string $fieldname)"
        125 => "DocumentCertificateResponse getDocumentCertificate(string $sid, string $idDoc, string $docType)"
        126 => "DocumentCertificateInfoResponse getDocumentCertificateInfo(string $sid, string $idDoc, string $docType)"
        127 => "DocumentQRCodeInfoResponse getQRCodeInfo(string $sid, string $idDoc, string $docType, string $key)"
        128 => "UNKNOWN getDocumentPDF(string $sid, string $idDoc, string $docType)"
        129 => "UNKNOWN getDocumentPDFSigned(string $sid, string $idDoc, string $docType)"
        130 => "UNKNOWN getDocumentPDF_bySeries(string $sid, string $idDoc, string $docType, string $docSeries)"
        131 => "UNKNOWN getDocumentPDFSigned_bySeries(string $sid, string $idDoc, string $docType, string $docSeries)"
        132 => "UNKNOWN getDocumentPDFLink(string $sid, string $idDoc, string $docType)"
        133 => "UNKNOWN getDocumentPDFLink_bySeries(string $sid, string $idDoc, string $docType, string $docSeries)"
        134 => "UNKNOWN sendDocumentPDF2Email(string $sid, string $idDoc, string $docType, string $email_destinations, string $email_subject, string $email_body)"
        135 => "UNKNOWN sendDocumentPDFSigned2Email(string $sid, string $idDoc, string $docType, string $email_destinations, string $email_subject, string $email_body)"
        136 => "UNKNOWN sendDocumentPDF2Email_bySeries(string $sid, string $idDoc, string $docType, string $docSeries, string $email_destinations, string $email_subject, string $email_body)"
        137 => "UNKNOWN insertDocumentHeader(string $sid, string $nif, string $docType, string $obs, string $opt_name, string $opt_nif, string $opt_address, string $opt_locality, string $opt_postalCode, string $docRef)"
        138 => "UNKNOWN insertEntityOrder(string $sid, string $internalRef, string $obs)"
        139 => "UNKNOWN insertDocumentHeader_additionalInfo(string $sid, string $idDocTemp, string $docType, string $printComment, string $docRef, string $pickupDateTime, string $pickupLocation, string $deliveryDateTime, string $deliveryLocationTxt, string $licencePlate, string $opt_deliveryLocation_address, string $opt_deliveryLocation_postalCode, string $opt_deliveryLocation_city, string $opt_deliveryLocation_countryCode)"
        140 => "UNKNOWN setDocumentClosedSituation(string $sid, string $idDoc, string $docType, string $docSerie, string $situationId)"
        141 => "UNKNOWN setDocumentHeaderField(string $sid, string $idDocTemp, string $docType, string $fieldname, string $fieldvalue)"
        142 => "UNKNOWN insertDocumentAlternativeCurrency(string $sid, string $idDocTemp, string $docType, string $currency_id, string $conversionvalue)"
        143 => "UNKNOWN closeDocument(string $sid, string $idDocTemp, string $docType)"
        144 => "UNKNOWN closeDocument_bySeries(string $sid, string $idDocTemp, string $docType, string $docSeries)"
        145 => "UNKNOWN markDocument(string $sid, string $idDoc, string $docType, string $read)"
        146 => "UNKNOWN markDocument_bySeries(string $sid, string $idDoc, string $docType, string $docSeries, string $read)"
        147 => "UNKNOWN documentCommunication(string $sid, string $idDoc, string $docType)"
        148 => "UNKNOWN documentCommunication_bySeries(string $sid, string $idDoc, string $docType, string $docSeries)"
        149 => "MBRefResponse generateMBRef(string $sid, string $idDoc, string $docType)"
        150 => "UNKNOWN addMBRef(string $sid, string $idDoc, string $docType, string $RefMB, string $rascunho)"
        151 => "UNKNOWN settleInvoice(string $sid, string $idDoc, string $docSeries)"
        152 => "UNKNOWN checkIfSettle(string $sid, string $idDoc, string $docSeries)"
        153 => "UNKNOWN setDocumentVoid(string $sid, string $docType, string $docSeries, string $idDoc, string $c_series, string $c_date, string $c_reason)"
        154 => "UNKNOWN setReceiptVoid(string $sid, string $docSeries, string $idDoc)"
        155 => "UNKNOWN deleteDocumentDraft(string $sid, string $idDocTemp, string $docType)"
        156 => "UNKNOWN insertStockDocumentHeader(string $sid, string $docSeries, string $date, string $docRef, string $obs, string $project, string $warehouse)"
        157 => "UNKNOWN closeStockDocument(string $sid, string $docSeries, string $idDocTemp)"
        158 => "UNKNOWN getDocumentTalao(string $sid, string $idDoc, string $docType, string $docSeries)"
        159 => "UNKNOWN creditRegularization(string $sid, string $docSeries, string $docRef, string $target_idDoc, string $target_docSeries)"
        160 => "DocumentLineResponse getDocumentLines(string $sid, string $idDoc, string $docType)"
        161 => "DocumentLineResponse getDocumentLines_bySeries(string $sid, string $idDoc, string $docType, string $docSeries)"
        162 => "DocumentLineDetailsResponse getDocumentLinesDetails_bySeries(string $sid, string $idDoc, string $docType, string $docSeries)"
        163 => "TableResponse getDocumentLineCS(string $sid, string $idDoc, string $docType, string $idDocLine)"
        164 => "UNKNOWN getDocumentLineInfo(string $sid, string $idDoc, string $docType, string $idDocLine, string $fieldname)"
        165 => "UNKNOWN insertDocumentLine(string $sid, string $idDocTemp, string $docType, string $ref, string $qt, string $price, string $tax, string $prodDesc, string $discount)"
        166 => "UNKNOWN insertDocumentLineCS(string $sid, string $idDocTemp, string $docType, string $line, string $colorref, string $sizeref)"
        167 => "UNKNOWN changeDocumentLineTax(string $sid, string $idDocTemp, string $docType, string $idDocLineTemp, string $taxid)"
        168 => "UNKNOWN insertDocumentLine_bySeries(string $sid, string $idDocTemp, string $docType, string $ref, string $qt, string $docSeries)"
        169 => "UNKNOWN DocumentTransport(string $sid, string $idDocTemp, string $docType, string $docSeries, string $HoraCarga, string $MoradaCarga, string $LocalidadeCarga, string $CodPostalCarga, string $CodigoPaisCarga, string $DataHoraDescarga, string $MoradaDescarga, string $LocalidadeDescarga, string $CodPostalDescarga, string $CodigoPaisDescarga, string $MatriculaViatura)"
        170 => "UNKNOWN insertBudgetHeader(string $sid, string $nif, string $docSeries, string $designation)"
        171 => "UNKNOWN insertBudgetLine(string $sid, string $idDocTemp, string $docSeries, string $ref, string $qt, string $price, string $tax, string $prodDesc, string $discount)"
        172 => "UNKNOWN closeBudget(string $sid, string $idDocTemp, string $docSeries)"
        173 => "UNKNOWN zipDocs(string $sid, string $docType, string $month, string $year, string $mode, string $start_date, string $end_date)"
        174 => "UNKNOWN insertReciptHeader(string $sid, string $nif, string $docSeries, string $opt_name, string $opt_address, string $opt_locality, string $opt_postalCode)"
        175 => "UNKNOWN insertReciptLine(string $sid, string $idDocTemp, string $docSeries, string $reciptRef, string $reciptValue)"
        176 => "UNKNOWN closeRecipt(string $sid, string $idDocTemp, string $docSeries)"
        177 => "SalesmanResponse listSalesman(string $sid, string $offset)"
        178 => "UNKNOWN countSalesmans(string $sid)"
        179 => "UNKNOWN countContacts(string $sid)"
        180 => "ContactResponse listContacts(string $sid, string $offset)"
        181 => "UNKNOWN contactSearch(string $sid, string $idContact)"
        182 => "UNKNOWN contactExists(string $sid, string $idContact)"
        183 => "ContactResponse getContact(string $sid, string $idContact)"
        184 => "UNKNOWN getContactDetails(string $sid, string $nif)"
        185 => "UNKNOWN getContactInfo(string $sid, string $idContact, string $fieldname)"
        186 => "UNKNOWN insertContact(string $sid, string $nif, string $name, string $address, string $postalCode, string $locality, string $phone, string $fax, string $email, string $obs)"
        187 => "UNKNOWN updateContact(string $sid, string $idContact, string $nif, string $name, string $address, string $postalCode, string $locality, string $phone, string $fax, string $email, string $obs)"
        188 => "UNKNOWN convertContact(string $sid, string $idContact)"
        189 => "UNKNOWN setContactField(string $sid, string $idContact, string $fieldname, string $value)"
        190 => "UNKNOWN deleteContact(string $sid, string $idContact)"
        191 => "UNKNOWN getStatistics(string $sid, string $type, string $params)"
        192 => "UNKNOWN insertProductBatch(string $sid, string $ref, string $designation, string $shortName, string $tax, string $obs, string $active, string $shortDesc, string $longDesc, string $price, string $vendorRef, string $ean)"
        193 => "UNKNOWN productBatchNumberExists(string $sid, string $ref, string $batchnumber)"
        194 => "TableBatchNumResponse getProductBatchNumbersStock(string $sid, string $ref, string $warehouse)"
        195 => "UNKNOWN insertBatchNumber(string $sid, string $ref, string $batchnumber, string $name, string $manufacturingDate, string $expirationDate, string $hidden)"
        196 => "UNKNOWN insertDocumentLineBatchNumber_bySeries(string $sid, string $docType, string $docSeries, string $idDocTemp, string $ref, string $batchnumber, string $qt)"
        197 => "AllDocsResponseTESTE listAllDocumentsTESTE(string $sid, string $date_from, string $date_to, string $read, string $offset)"
        198 => "DocResponse insertOrderHeader_byStore(string $sid, string $store_name, string $obs, string $opt_name, string $opt_nif, string $opt_address, string $opt_locality, string $opt_postalCode, string $docRef)"
        199 => "UNKNOWN getReportIVA(string $sid, string $type, string $date_from, string $date_to, string $export_type)"
        200 => "PaymentMethodResponse listPaymentMethods(string $sid, string $offset)"
        201 => "UNKNOWN insertDocumentPaymentMethod(string $sid, string $idDocTemp, string $docType, string $idPaymentMethod, string $value)"
        202 => "UNKNOWN insertExternalDocument(string $sid, string $docType, string $clientId, string $cae, string $oriDocNumber, string $date, string $totalValue)"
        203 => "ProductItemResponse listProductItems(string $sid, string $prodRef)"
        204 => "UNKNOWN insertupdateProductItem(string $sid, string $prodRef, string $itemRef, string $qty)"
        205 => "UNKNOWN deleteProductItem(string $sid, string $prodRef, string $itemRef)"
        206 => "UNKNOWN getDocumentLineIDItem(string $sid, string $idDocTemp, string $docType, string $idDocLineTemp, string $itemRef)"
        207 => "UNKNOWN changeDocumentLine(string $sid, string $idDocTemp, string $docType, string $idDocLineTemp, string $qt, string $price, string $taxid, string $discount, string $obs)"
        208 => "UNKNOWN getBudgetLineIDItem(string $sid, string $idDocTemp, string $idDocLineTemp, string $itemRef)"
        209 => "UNKNOWN changeBudgetLine(string $sid, string $idDocTemp, string $idDocLineTemp, string $qt, string $price, string $taxid, string $discount, string $obs)"
        210 => "UNKNOWN registerCard(string $sid, string $designacao, string $morada, string $local, string $codpost, string $codpais, string $nif, string $tlf, string $tlm, string $email)"
        211 => "UNKNOWN loginCard(string $sid, string $email, string $password)"
        212 => "UNKNOWN passwordCard(string $sid, string $email)"
        213 => "UNKNOWN getCard(string $sid, string $email)"
        214 => "UNKNOWN historyCard(string $sid, string $CodigoCartao)"
        215 => "UNKNOWN closeDocumentCard(string $sid, string $idDocTemp, string $docType, string $CodigoCartao, string $CartaoDescontoVal)"
    ]; */
}