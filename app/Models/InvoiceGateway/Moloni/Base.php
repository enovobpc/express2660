<?php
namespace App\Models\InvoiceGateway\Moloni;

use App\Models\Agency;
use App\Models\Billing\ApiKey;
use App\Models\CustomerBalance;
use Date, Response, Setting, File;
use Webit\GlsTracking\Api\Exception\Exception;
use App\Models\Customer as Customers;

class Base {

    /**
     * @var string
     */
    public $url = 'https://api.moloni.pt/v1/';

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
     * Constructor
     *
     * @param $agencia
     * @param $cliente
     * @param $password
     */
    public function __construct($apiKey = null)
    {
        if(config('app.env') == 'local') {
            $apiKey = '128046n67p7784709c6f521e82595fc8fd801a70e0'; //TESTES
        } else if(empty($apiKey)) {
            $apiKey = ApiKey::getDefaultKey();
        }

        if(empty($apiKey)) {
            throw new Exception('Não está configurada nenhuma chave da API para ligação com o software de faturação Moloni.');
        }

        $this->keyinvoice = new \SoapClient($this->url);
        $this->apiKey     = $apiKey;
        $this->session_id = $this->login($apiKey);
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

            $client   = new \SoapClient($this->url, ['encoding' => 'UTF-8']);

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
     * Execute a soap request
     *
     * @param $nif
     * @return mixed
     * @throws \Exception
     */
    public function execute($method, $data)
    {
        $this->storeLog($method, $data);

        $url = $this->url . $method . '?access_token='.$this->session_id;

        $con = curl_init();
        curl_setopt($con, CURLOPT_URL, $url);
        curl_setopt($con, CURLOPT_POST, true);
        curl_setopt($con, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($con, CURLOPT_HEADER, false);
        curl_setopt($con, CURLOPT_RETURNTRANSFER, true);

        $res_curl = curl_exec($con);
        curl_close($con);

        $res_txt = json_decode($res_curl, true);
        if(!isset($res_txt['error'])){

            echo 'Sucesso: '.print_r($res_txt,true).'';
        }
        else{
            throw new \Exception($res_txt);
        }
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

        $customers = Customers::whereIn('agency_id', $agencies)
                                ->where(function($q){
                                    $q->where('vat', '<>', '');
                                    $q->orWhere('vat', '<>', '999999999');
                                    $q->orWhereNull('vat');
                                });

        if(!empty($customerId)) {
            $customers = $customers->whereId($customerId);
        }

        $customers = $customers->get(['id', 'vat', 'code', 'payment_method']);

        $imported = 0;
        $errors = [];
        $success = [];

        foreach ($customers as $key => $customer) {

            $history = new Customer();
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


                    foreach ($row as $document) {

                        $sense = empty($document->DEB) ? 'credit' : 'debit';

                        $balance = CustomerBalance::firstOrNew([
                            'customer_id'   => $customer->id,
                            'doc_type'      => $docType,
                            'doc_id'        => $document->IdDoc,
                            'doc_serie_id'  => $document->DocSeriesID,
                            'doc_serie'     => $document->DocSeries,
                            'sense'         => $sense
                        ]);


                        if(!$balance->exists || ($balance->exists && $document->Canceled)) {

                            $date = new Date($document->Date);

                            $days = 30;
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

                            $balance->gateway   = 'keyinvoice';
                            $balance->date      = $date;
                            $balance->due_date  = $date->addDays($days);
                            $balance->total     = empty($document->DEB) ? $document->CRE : $document->DEB;
                            $balance->sense     = $sense;
                            $balance->reference = $document->RefDoc;
                            $balance->canceled  = $document->Canceled;
                            $balance->balance   = $document->BALANCE;
                            if($document->Canceled) {
                                $balance->deleted_at = date('Y-m-d H:i:s');
                            }
                            $balance->save();

                            $imported++;
                            $success[] = $customer->code;
                        }
                    }
                }
            } catch (\Exception $e) {
                $errors[] = 'Erro no cliente ' . @$customer->id.' ' . $e->getMessage();
            }

        }
        return $imported;
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
/*        $keys = [
            '01' => '8e958578ad81ed561351595e28b7775c',
            '02' => 'be73668520f9e1ab4728efc3ed33b927',
            '03' => '1976ae328d773a1bd5d131a599dcba85',
            '04' => 'd5386e8f30ba1bf6ac94603a4a0c2796',
            '05' => '1c934ec8331aaaee38f714f8329b0efa',
            '06' => '46ea6bb6641941114119933d22e6b011',
            '07' => '3e16bb011d6a35f8b49658e00dc0f917',
            '09' => '33cb91c8d05076cb5a2b3a7e3e47a8ce',
            '10' => '4a80859ef54809fd16af074425f65696',
            '11' => 'f178d2b35f6e869558532cfde2d9b087',
            '12' => 'd422c9330d0d9a41c438b80909d29303',
            '13' => 'de57e03f6fa625c69f40955ebffb6cc2',
            '14' => 'f4a091df939d2dcf85d74dcb689e3b48',
            '15' => 'b466783a3325fc12901aab7a9d4d9eb9',
            '16' => '973492e216f56a3888feb4875a22a69f',
            '17' => 'd8ec80af3b2cf2949333f5d56f66cb0d',
            '18' => '462afe7bb2b3021d80f75a9c3ba42e3d',
            '19' => '267a62f31503f2be643686dbe4522c83',
            '20' => '9b47acbfd46096f4afecdb261adf8e06',
            '21' => 'fcc78b448a59906985ff6911dc96dce4',
            '22' => 'b2f4621727198f656cd5a335768e1619',
            '23' => '188497e1871c2038c0a048e88ceee7c2',
            '24' => '2afa7068f064251c79107556554837f7',
            '25' => 'e8b199569927a69c4757769acc0acf0c',
            '26' => '307868493d0b8657964bfd89c3159fe2',
            '27' => '2cddd5179237957a5942b342e9dca842',
            '28' => '19716b96563679ea9a90e65ef74007df',
            '29' => 'd182f8d360bb4baf6194d7ea1033c0ae',
            '30' => '68bc01542cbac74f979ee4900df6b623',
            '31' => 'ba8367f45d6ad2f233ba744eae89e91d',
        ];*/

        //$key = empty(@$keys[date('d')]) ? $masterKey : $keys[date('d')];

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
}