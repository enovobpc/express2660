<?php

/**
 * needs to add aliases on config/app.php
 */

namespace App\Models\Webservice;

use App\Models\Customer;
use App\Models\ShipmentExpense;
use App\Models\ShipmentHistory;
use App\Models\ShipmentIncidenceResolution;
use App\Models\CustomerWebservice;
use App\Models\ShippingExpense;
use App\Models\ShippingMethod;
use App\Models\ShippingStatus;
use App\Models\WebserviceLog;
use App\Models\LogViewer;
use App\Models\Agency;
use App\Models\Shipment;
use App\Models\Service;
use Illuminate\Support\Facades\Log;
use Carbon\CarbonPeriod;
use Date, Response, Exception, File, Setting, Auth, DB;

class Base
{

    /**
     * @var string
     */
    private $debug;


    /**
     * Constructor
     * @param $agencia
     * @param $cliente
     * @param $password
     */
    public function __construct($debug = false)
    {
        $this->debug = $debug;
    }

    /**
     * Return webservice method and login for a given shipment
     *
     * @param type $shipment shipment collection
     * @return string
     */
    public function getLogin($shipment)
    {

        if (empty($shipment->customer_id)) {
            throw new Exception('Impossível sincronizar. O envio não está associado a nenhum cliente.');
        }

        $login = CustomerWebservice::remember(config('cache.query_ttl'))
            ->cacheTags(CustomerWebservice::CACHE_TAG)
            ->where('customer_id', $shipment->customer_id)
            ->where('provider_id', $shipment->provider_id)
            ->first();

        if(!$login) {
            throw new Exception('O cliente associado ao envio não tem nenhum webservice configurado para envios via '. $shipment->method, '-001');
        }

        $login->class = studly_case($login->method);

        return $login;
    }

    /**
     * Update shipment history
     *
     * @param int $shipmentId
     * @return type
     */
    public function updateShipmentHistoryByShipmentId($shipmentId)
    {

        $shipment = Shipment::with('customer')
            ->with(['provider' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->select(['id', 'name', 'color']);
            }])
            ->filterAgencies()
            ->findOrFail($shipmentId);

        return $this->updateShipmentHistory($shipment);
    }

    /**
     * Update shipment history
     *
     * @param collection $shipment
     * @param collection $webserviceLogin
     * @return type
     */
    public function updateShipmentHistory($shipment, $webserviceLogin = null)
    {

        try {
            if (is_null($webserviceLogin)) {
                $webserviceLogin = $this->getLogin($shipment);
            }

            $method = new $webserviceLogin->class(
                $webserviceLogin->agency,
                $webserviceLogin->user,
                $webserviceLogin->password,
                $webserviceLogin->session_id,
                $webserviceLogin->department,
                $webserviceLogin->endpoint,
                $this->debug
            );

            $originalStatusId = $shipment->status_id;
            $response = $method->updateHistory($shipment);


            //marca as incidencias como resolvidas.
            if ($originalStatusId == 9 && $response != 9) {
                ShipmentHistory::where('shipment_id', $shipment->id)
                    ->where('status_id', ShippingStatus::INCIDENCE_ID)
                    ->update(['resolved' => 1]);
            }

            //store devolution expense
            if ($response == ShippingStatus::DEVOLVED_ID) {
                //se tem reembolso, remove taxa reembolso
                $expensesIds = ShippingExpense::where('type', 'charge')->pluck('id')->toArray();
                ShipmentExpense::whereIn('expense_id', $expensesIds)->where('shipment_id', $shipment->id)->delete();

                $shipment->storeDevolutionExpenseIfExists();
            }

            return $response;
        } catch (\Exception $e) {
            if (!empty(Auth::user()) && Auth::check() && Auth::user()->isAdmin()) {
                throw new Exception($e->getMessage() . ' on file ' . $e->getFile() . ' line ' . $e->getLine());
            } else {
                throw new Exception('Ocorreu um erro ao sincronizar com a rede. [' . $e->getMessage() . ' on file ' . $e->getFile() . ' line ' . $e->getLine() . ']');
            }
        }
    }

    /**
     * Send shipment by webservice
     *
     * @param type $shipmentId shipment id
     * @return type
     */
    public function submitShipmentById($shipmentId, $isCollection = null)
    {

        $shipment = Shipment::with('customer')
            ->with(['service' => function ($q) {
                $q->remember(config('cache.query_ttl'));
            }])
            ->with(['provider' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->select(['id', 'name', 'color']);
            }])
            ->filterAgencies()
            ->findOrFail($shipmentId);

        return $this->submitShipment($shipment, null, $isCollection);
    }

    /**
     * Send shipment by webservice
     *
     * @param collection $shipmentId
     * @return type
     */
    public function submitShipment($shipment, $webserviceLogin = null, $isCollection = null, $forceCurDate = false)
    {

        try {
            if (is_null($webserviceLogin)) {
                $webserviceLogin = $this->getLogin($shipment);
            }
        } catch (\Exception $e) {
            if (!empty(Auth::user()) && Auth::check() && Auth::user()->isAdmin()) {
                throw new Exception($e->getMessage() . ' on file ' . $e->getFile() . ' line ' . $e->getLine());
            } else {
                throw new Exception($e->getMessage());
            }
        }

        //ve se é recolha
        if(is_null($isCollection)) {
            $isCollection = $shipment->is_collection;
        }

        $originalWeight   = $shipment->weight;
        $shipment->weight = !empty($shipment->provider_weight) ? $shipment->provider_weight : $shipment->weight;

        unset($shipment->provider_weight);

        $shipment->provider_sender_agency    = empty($shipment->provider_sender_agency) ? $webserviceLogin->agency : $shipment->provider_sender_agency;
        $shipment->provider_cargo_agency     = empty($shipment->provider_cargo_agency) ? $webserviceLogin->agency : $shipment->provider_cargo_agency;

        if ($webserviceLogin->method == 'ctt') {
            $shipment->provider_cargo_agency = $webserviceLogin->user;
        }

        $shipment->sender_name               = str_replace('&', 'e', $shipment->sender_name);
        $shipment->recipient_name            = str_replace('&', 'e', $shipment->recipient_name);
        $shipment->webservice_method         = $webserviceLogin->method;

        //needs to add aliases on config/app.php
        //get webservice method classs
        $method = new $webserviceLogin->class(
            $webserviceLogin->agency,
            $webserviceLogin->user,
            $webserviceLogin->password,
            $webserviceLogin->session_id,
            $webserviceLogin->department,
            $webserviceLogin->endpoint,
            $this->debug
        );

        $originalShipment = clone $shipment;
        try {
            if (!$webserviceLogin->force_sender && !in_array($webserviceLogin->method, ['envialia', 'nacex', 'tipsa'])) {
                if ((Setting::get('hidden_recipient_on_labels') || Setting::get('hidden_recipient_addr_on_labels')) && !($isCollection || $shipment->is_collection)) {
                    if (Setting::get('hidden_recipient_on_labels')) {
                        $shipment->sender_name = $shipment->agency->company;
                    }
        
                    $shipment->sender_address  = @$shipment->agency->address;
                    $shipment->sender_zip_code = @$shipment->agency->zip_code;
                    $shipment->sender_city     = @$shipment->agency->city;
                    $shipment->sender_country  = @$shipment->agency->country;
                    $shipment->sender_phone    = @$shipment->agency->phone;
                }
            }
            
            if (@$webserviceLogin->settings['force_tracking_as_reference']) {
                if (in_array($webserviceLogin->method, ['correos_express'])) {
                    $shipment->reference = $shipment->tracking_code;
                } else {
                    $shipment->reference = 'TRK' . $shipment->tracking_code;
                }
            }

            $trackingCode = $method->saveShipment($shipment, $isCollection, $webserviceLogin, $forceCurDate);

            $shipment->sender_name      = $originalShipment->sender_name;
            $shipment->sender_address   = $originalShipment->sender_address;
            $shipment->sender_zip_code  = $originalShipment->sender_zip_code;
            $shipment->sender_city      = $originalShipment->sender_city;
            $shipment->sender_country   = $originalShipment->sender_country;
            $shipment->sender_phone     = $originalShipment->sender_phone;
            $shipment->weight           = $originalWeight;
            $shipment->reference        = $originalShipment->reference;

            if ($trackingCode) {
                $shipment->is_closed = $webserviceLogin->method == 'ctt' || $webserviceLogin->method == 'ontime' ? 0 : 1;
                $shipment->provider_tracking_code = $trackingCode;
                $shipment->submited_at            = Date::now();
                $shipment->webservice_error       = null;
                $shipment->save();
            }
        } catch (\Exception $e) {
            
            $shipment->sender_name      = $originalShipment->sender_name;
            $shipment->sender_address   = $originalShipment->sender_address;
            $shipment->sender_zip_code  = $originalShipment->sender_zip_code;
            $shipment->sender_city      = $originalShipment->sender_city;
            $shipment->sender_country   = $originalShipment->sender_country;
            $shipment->sender_phone     = $originalShipment->sender_phone;
            $shipment->weight           = $originalWeight;
            $shipment->reference        = $originalShipment->reference;

            if (empty($shipment->provider_tracking_code)) {
                $shipment->is_closed                = $webserviceLogin->method == 'ctt' || $webserviceLogin->method == 'ontime' ? 0 : 1;
                $shipment->webservice_error         = $e->getMessage();
                $shipment->provider_tracking_code   = null;
                $shipment->submited_at              = null;
                $shipment->weight                   = $originalWeight;
                $shipment->save();
            }

            if (!empty(Auth::user()) && Auth::check() && Auth::user()->isAdmin()) {
                throw new Exception($e->getMessage() . ' on file ' . $e->getFile() . ' line ' . $e->getLine());
            } else {
                throw new Exception($e->getMessage());
            }
        }

        return $trackingCode;
    }

    /**
     * Submit incidence
     *
     * @param type $shipmentId shipment id
     * @return type
     */
    public function submitIncidenceResolution($resolutionId, $resolutionCollection = null, $webserviceLogin = null)
    {

        if (is_null($resolutionCollection)) {
            $resolutionCollection = ShipmentIncidenceResolution::with('shipment')->findOrFail($resolutionId);
        }

        $shipment = $resolutionCollection->shipment;

        try {
            if (is_null($webserviceLogin)) {
                $webserviceLogin = $this->getLogin($shipment);
            }
        } catch (\Exception $e) {
            if (!empty(Auth::user()) && Auth::check() && Auth::user()->isAdmin()) {
                throw new Exception($e->getMessage() . ' on file ' . $e->getFile() . ' line ' . $e->getLine());
            } else {
                throw new Exception($e->getMessage());
            }
        }

        $shipment->provider_sender_agency = empty($shipment->provider_sender_agency) ? $webserviceLogin->agency : $shipment->provider_sender_agency;
        $shipment->provider_cargo_agency  = empty($shipment->provider_cargo_agency) ? $webserviceLogin->agency : $shipment->provider_cargo_agency;
        $shipment->webservice_method      = $webserviceLogin->method;

        //get webservice method classs
        $method = new $webserviceLogin->class(
            $webserviceLogin->agency,
            $webserviceLogin->user,
            $webserviceLogin->password,
            $webserviceLogin->session_id,
            $webserviceLogin->department,
            $webserviceLogin->endpoint,
            $this->debug
        );

        try {

            $result = $method->saveIncidenceResolution($resolutionCollection);
        } catch (\Exception $e) {
            if (!empty(Auth::user()) && Auth::check() && Auth::user()->isAdmin()) {
                throw new Exception($e->getMessage() . ' on file ' . $e->getFile() . ' line ' . $e->getLine());
            } else {
                throw new Exception($e->getMessage());
            }
        }

        return $result;
    }

    /**
     * Get proof url
     *
     * @param collection $shipmentId
     * @return type
     */
    public function getPodUrl($shipment, $webserviceLogin = null)
    {

        try {
            if (is_null($webserviceLogin)) {
                $webserviceLogin = $this->getLogin($shipment);
            }

            //needs to add aliases on config/app.php
            $method = new $webserviceLogin->class(
                $webserviceLogin->agency,
                $webserviceLogin->user,
                $webserviceLogin->password,
                $webserviceLogin->session_id,
                $webserviceLogin->department,
                $webserviceLogin->endpoint,
                $this->debug
            );

            return $method->ConsEnvPODDig($shipment->provider_sender_agency, $shipment->provider_sender_agency, $shipment->provider_tracking_code);
        } catch (\Exception $e) {
            if (!empty(Auth::user()) && Auth::check() && Auth::user()->isAdmin()) {
                throw new Exception($e->getMessage() . ' on file ' . $e->getFile() . ' line ' . $e->getLine());
            } else {
                throw new Exception($e->getMessage());
            }
        }
    }


    /**
     * Sincronize shipments
     *
     * @param type $webservice
     * @param type $date
     * @param type $update
     */
    public function syncShipments($date = null, $webservice = null, $customerId = null, $isRoutine = false, $sourceAgencies = null, $range = null, $syncAllCustomers = false)
    {

        if (!is_array($date)) {
            $date = is_null($date) ? date('Y-m-d') : $date;
            $date = new Date($date);
            $date = [$date, $date];
        }

        $dates = CarbonPeriod::create(@$date[0], @$date[1]);
        $dates = $dates->toArray();

        if (is_null($sourceAgencies)) {
            $sourceAgencies = Agency::remember(config('cache.query_ttl'))
                ->cacheTags(Agency::CACHE_TAG)
                ->filterSource()
                ->pluck('id')
                ->toArray();
        }


        if ($isRoutine || $date[0] == $date[1]) {
            $minDate   = new Date($date[0]);
            $minDate   = $minDate->subDays(8)->format('Y-m-d'); //sincroniza apenas clientes com envios nos últimos 8 dias
            $maxDate   = date('Y-m-d');
        } else { //só sincroniza clientes entre a data indicada
            $minDate = $date[0];
            $maxDate = $date[1];
        }


        //obtem ids dos clientes que vão ser importados
        if (empty($customerId)) {

            if (in_array(config('app.source'), ['okestafetas', 'fprmlogistica', 'log24', 'corridadotempo', 'morluexpress', 'utiltrans'])) {
                $activeCustomers = Customer::filterSource()
                    ->whereCode('CFINAL')
                    ->pluck('id')
                    ->toArray();
            } else {
                $activeCustomers = Customer::filterSource()
                    ->isProspect(false)
                    ->isDepartment(false)
                    ->whereIn('agency_id', $sourceAgencies);

                if (!$syncAllCustomers) {
                    $activeCustomers = $activeCustomers->whereRaw('(select max(date) from shipments where date between "' . $minDate . '" and "' . $maxDate . '" and shipments.customer_id = customers.id and deleted_at is null limit 0,1) >= "' . $minDate . '"');
                }

                $activeCustomers = $activeCustomers->select(['id'])
                    ->pluck('id')
                    ->toArray();
            }
        } else {
            //se é para importar só um cliente, força a sua adição à lista de active customers
            $activeCustomers = [$customerId];
        }

        //obtem os dados dos webservices apenas dos clientes acima
        $customers = CustomerWebservice::remember(config('cache.query_ttl'))
            ->cacheTags(CustomerWebservice::CACHE_TAG)
            ->with('customer')
            ->isActive()
            ->whereHas('customer', function ($q) use ($activeCustomers) {
                $q->whereIn('id', $activeCustomers);
            });

        if (!is_null($customerId)) {
            $customers = $customers->where('customer_id', $customerId);
        }

        if (!is_null($webservice)) {
            $customers = $customers->where('method', $webservice);
        }

        if ($webservice == 'tipsa' && in_array(config('app.source'), ['fozpost'])) { //o webservice é global. basta correr apenas para 1 cliente.
            $customers = $customers->take(1);
        }

        if ($webservice == 'tipsa' && in_array(config('app.source'), ['tma'])) { //o webservice é global. basta correr apenas para 1 cliente.
            $customers = $customers->whereIn('id', [1, 31, 349, 380, 381, 382]);
        }

        /*if(is_null($customerId) && in_array(config('app.source'), ['okestafetas', 'fprmlogistica', 'log24', 'morluexpress', 'corridadotempo'])) { //o webservice é global. basta correr apenas para 1 cliente.
            $customers = $customers->whereHas('customer', function($q) {
                $q->where('code', 'CFINAL');
            });
        }*/

        if (!is_null($range)) {
            $customers = $customers->skip($range[0])->take($range[1]);
        }

        $customers = $customers->groupBy('user')->get(); //GROUP BY ADICIONADO EM 5/11/2020

        if ($customers->isEmpty()) {
            return true;
        }

        //Get service coeficients and min volume
        $allServices = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->with(['volumetricFactors' => function ($q) {
                $q->select('service_id', 'provider_id', 'zone', 'volume_min', 'factor');
            }])
            ->get(['id']);

        $servicesMapping = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()->pluck('id', 'code')->toArray();

        $errors   = [];
        $warnings = [];
        $imported = [];
        $totalShipments = 0;
        foreach ($dates as $date) {

            $date = $date->format('Y-m-d');

            foreach ($customers as $loginDetails) {

                $class = studly_case($loginDetails->method);
                $method = new $class(
                    $loginDetails->agency,
                    $loginDetails->user,
                    $loginDetails->password,
                    $loginDetails->session_id,
                    $loginDetails->department,
                    $loginDetails->endpoint,
                    $loginDetails->id
                );

                $providerId = $loginDetails->provider_id;

                $shipments = null;

                try {
                    $shipments = $method->getEnviosByDate($date);
                } catch (Exception $e) {
                    $warnings[] = 'Erro obter envios por data | Data: ' . $date . ' - User: ' . $loginDetails->customer_id . ':' . $e->getMessage();
                }

                if ($shipments) {

                    foreach ($shipments as $data) {

                        $shipment = Shipment::whereIn('agency_id', $sourceAgencies)
                            ->where(function ($q) use ($data) {
                                $q->where(function ($q) use ($data) {
                                    $q->where('provider_tracking_code', @$data['provider_tracking_code']);
                                    $q->where('provider_cargo_agency', @$data['provider_cargo_agency']);
                                    $q->where('provider_sender_agency', @$data['provider_sender_agency']);
                                });
                            })
                            ->first();

                        if (!$shipment) {
                            $shipment = new Shipment();
                        }

                        $shipmentExists = $shipment->exists;
                        if (!$shipmentExists) {
                            $imported[] = $data['provider_tracking_code'];

                            //envios envialia internacionais faz nova chamada
                            if ($loginDetails->method == 'envialia' && in_array($data['service'], ['200', '100', '101', 'XL', '201'])) {
                                $data = $method->getEnvioByTrk($data['provider_cargo_agency'], $data['provider_sender_agency'], $data['provider_tracking_code']);
                                $data['recipient_country'] = strtolower($data['recipient_country']);
                            }

                            $shipment->customer_id = $loginDetails->customer->id;
                            $shipment->provider_id = $loginDetails->provider_id;

                            $data['sender_zip_code'] = explode('-', @$data['sender_zip_code']);
                            $data['sender_zip_code'] = @$data['sender_zip_code'][0];
                            $data['recipient_zip_code'] = explode('-', @$data['recipient_zip_code']);
                            $data['recipient_zip_code'] = @$data['recipient_zip_code'][0];

                            if ($loginDetails->method == 'tipsa') {
                                if (substr(@$data['sender_zip_code'], 0, 1) === '6') {
                                    $data['sender_zip_code'] = substr(@$data['sender_zip_code'], 1);
                                }

                                if (substr(@$data['recipient_zip_code'], 0, 1) === '6') {
                                    $data['recipient_zip_code'] = substr(@$data['recipient_zip_code'], 1);
                                }
                            }

                            //auto detect recipient zip code
                            $recipientZipCode = Shipment::getAgencyByZipCode($data['recipient_zip_code'], $providerId);
                            $data['sender_country'] = Shipment::countryFromZipCode($data['sender_zip_code']);
                            $data['recipient_country'] = Shipment::countryFromZipCode($data['recipient_zip_code']);

                            if (!empty($recipientZipCode->zone)) {
                                $data['recipient_country'] = $recipientZipCode->zone;
                            }

                            $shipment->sender_country    = $data['sender_country'];
                            $shipment->recipient_country = $data['recipient_country'];

                            //obtem agencia de origem e destino
                            $shipment->agency_id = $loginDetails->customer->agency_id;
                            $shipment->sender_agency_id = $loginDetails->customer->agency_id;
                            $agencyId = $recipientZipCode->agency_id;

                            if (empty($agencyId)) {
                                $agencyId = $shipment->agency_id; //se agencia de destino não encontrada, atribui agencia atual
                            }

                            $shipment->recipient_agency_id = $shipment->agency_id;

                            /**
                             * Obtem serviço
                             */
                            $data['service'] = empty($data['service']) ? '14' : $data['service'];


                            //if (!empty($data['provider_collection_tracking_code']) && empty($data['collection_tracking_code'])) { //servicos de recolha
                            //COMENTADO EM 6 NOVEMBRO 2019

                            /* if (!empty($data['provider_collection_tracking_code']) && empty($data['parent_tracking_code'])) {
                                $collection = Shipment::where('provider_tracking_code', $data['provider_collection_tracking_code'])->first();
                                if (!$collection) {
                                    $collection = self::importCollection($loginDetails, $data['provider_collection_tracking_code'], $servicesMapping, $data);
                                }
                            }*/

                            //Se é uma recolha, obtem o TRK da recolha e adiciona a informação ao envio a importar
                            if (!empty($data['provider_collection_tracking_code']) && $webservice != 'tipsa') { //servicos de recolha

                                $collectionTrk = Shipment::where('provider_tracking_code', $data['provider_collection_tracking_code'])
                                    ->select(['tracking_code', 'customer_id'])
                                    ->first();

                                if (empty($collectionTrk) && $webservice == 'envialia') {
                                    $collectionTrk = self::importCollection($loginDetails, $data['provider_collection_tracking_code'], $servicesMapping, $data);
                                }

                                if (!empty($collectionTrk)) {
                                    $data['type'] = Shipment::TYPE_PICKUP;
                                    $data['parent_tracking_code'] = @$collectionTrk->tracking_code;
                                    $data['customer_id'] = @$collectionTrk->customer_id; //associa ao envio o mesmo cliente da recolha
                                } else {
                                    $data['type'] = Shipment::TYPE_PICKUP;
                                    $data['parent_tracking_code'] = 'Not Found';
                                }
                            }


                            /**
                             *
                             *
                             *
                             * SUBSTITUIR AQUI PELO ARRAY DE MAPEAMENTO
                             *
                             *
                             *
                             */
                            $mappingServices = config('shipments_import_mapping.' . $loginDetails->method . '-services');

                            if (isset($mappingServices[$data['service']])) {
                                $serviceCode = $mappingServices[$data['service']]; //obtem o codigo do

                                //detecta se o código postal é de uma ilha. Se for, altera para serviço ilhas
                                if ($this->detectIslandZipCode($data['sender_zip_code']) || $this->detectIslandZipCode($data['recipient_zip_code'])) {
                                    if (config('app.source') == 'fozpost') {
                                        $serviceCode = 'AI'; //assume serviço ilhas em vez de 24H da envialia
                                    }
                                }

                                $shipment->service_id = @$servicesMapping[$serviceCode];
                            } else {
                                $shipment->service_id = null;
                                $errors[] = $loginDetails->customer_id . ':Serviço ' . $data['service'] . ' não encontrado.';
                            }

                            /**
                             * Obtem status
                             */
                            unset($data['status_id']);
                            $shipment->status_id = ShippingStatus::WAINTING_SYNC_ID; //aguarda sincronização
                            $shipment->fill($data);
                            $shipment->date = $date;

                            //guarda informação sobre execução do webservice
                            $shipment->webservice_method = $loginDetails->method;
                            $shipment->submited_at = Date::now();
                        }


                        /**
                         * Processa o peso
                         * Considera sempre o maior dos 3 pesos possíveis
                         */
                        $weightChanged = false;
                        //original customer weight
                        if ($shipment->exists) {
                            $weight = $shipment->weight;
                            $fatorM3 = @$shipment->fator_m3;
                        } else {
                            $weight = @$data['weight'];
                            $fatorM3 = 0;
                        }

                        //webservice weight
                        if (@$data['weight'] > $weight) {
                            $weight = @$data['weight'];
                            $weightChanged = true;
                        }

                        //sorter weight
                        if (isset($data['weight_sorter'])) {
                            if ($data['weight_sorter'] > $weight) {
                                $weight = $data['weight_sorter']; //se peso no sorter maior que peso inicial
                                $weightChanged = true;
                            }
                        }

                        //volumetric weight
                        $zone = Shipment::getBillingCountry($shipment->sender_country, $shipment->recipient_country);
                        $coeficient = 0;

                        $service = $allServices->filter(function ($item) use ($shipment) {
                            return $item->id == $shipment->service_id;
                        })->first();

                        if ($service) {
                            $service = $service->volumetricFactors->filter(function ($item) use ($shipment, $zone) {
                                return $item->zone == $zone && $item->provider_id == $shipment->provider_id;
                            })->first();

                            if ($service) {
                                $coeficient = $service->factor;
                                $volumeMin = $service->volume_min;
                            }
                        }

                        //Ignore fator M3 se o valor do webservice é menor que o valor do envio
                        if ($fatorM3 > @$data['fator_m3']) {
                            @$data['fator_m3'] = $fatorM3;
                        }

                        $data['volumetric_weight'] = 0;
                        if (isset($data['fator_m3']) && !empty(@$data['fator_m3'])) { //apenas se o webservice devolve a variavel fator_m3
                            $data['volumetric_weight'] = @$data['fator_m3'] * $coeficient;
                        }

                        $shipment->weight   = $weight;
                        $shipment->volumes  = @$data['volumes'] ? @$data['volumes'] : 1;
                        $shipment->fator_m3 = @$data['fator_m3'];
                        $shipment->volumetric_weight = $data['volumetric_weight'];
                        /*$shipment->type     = empty($data['collection_tracking_code']) ? null : Shipment::TYPE_PICKUP;
                        $shipment->parent_tracking_code = empty($data['collection_tracking_code']) ? $shipment->parent_tracking_code : @$data['collection_tracking_code'];*/

                        if (Setting::get('shipments_round_up_weight')) {
                            $shipment->weight = roundUp($shipment->weight);
                            $shipment->volumetric_weight = roundUp($shipment->volumetric_weight);
                        }

                        //calcula preços do envio
                        $prices = null;
                        if ((hasModule('account_wallet') && $weightChanged && $shipment->ignore_billing)
                            || (!$shipment->is_blocked && !$shipment->invoice_id
                                && $shipment->recipient_country && $shipment->provider_id && $shipment->service_id
                                && $shipment->agency_id && $shipment->customer_id)
                        ) {


                            if (!empty($data['total_price_for_recipient'])) {
                                $shipment->cod = 'D';
                            } else {
                                $shipment->cod = null;
                            }

                            $tmpShipment = $shipment->replicate();

                            $prices = Shipment::calcPrices($tmpShipment);

                            if(@$prices['fillable']) {
                            
                                $shipment->fill($prices['fillable']);

                                //desconta da conta corrente
                                if (hasModule('account_wallet') && $weightChanged && $shipment->ignore_billing && !@$loginDetails->customer->is_mensal) {
                                    $diffPrice = $tmpShipment->billing_total - $shipment->billing_total;
                                    if ($diffPrice > 0.00) {
                                        try {
                                            \App\Models\GatewayPayment\Base::logShipmentPayment($shipment, $diffPrice);
                                            $loginDetails->customer->subWallet($diffPrice);
                                        } catch (\Exception $e) {
                                        }
                                    }
                                }
                            }
                        }

                        if ($shipmentExists) {
                            $shipment->save();
                        } else {
                            $shipment->setTrackingCode();
                        }

                        //grava taxas adicionais
                        if(@$prices) {
                            $shipment->storeExpenses($prices);
                        }

                        //CREATE SHIPMENT FROM PICKUP
                        if (!empty($shipment->parent_tracking_code) && $shipment->type == Shipment::TYPE_PICKUP) {
                            $parentShipment = Shipment::where('tracking_code', $shipment->parent_tracking_code)->first();

                            if ($parentShipment) {
                                $parentHistory = new ShipmentHistory();
                                $parentHistory->shipment_id = $parentShipment->id;
                                $parentHistory->status_id = ShippingStatus::PICKUP_CONCLUDED_ID;
                                $parentHistory->obs = 'Gerado TRK' . $shipment->tracking_code;
                                $parentHistory->save();

                                $shipment->insertOrUpdadePickupExpense($parentShipment); //add expense

                                $parentShipment->update([
                                    'children_tracking_code' => $shipment->tracking_code,
                                    'children_type' => Shipment::TYPE_PICKUP,
                                    'status_id' => ShippingStatus::PICKUP_CONCLUDED_ID
                                ]);
                            }
                        }
                    }
                }
            }
        }

        if ($errors || $warnings) {

            if ($errors) {
                $message = print_r($errors, true);
                WebserviceLog::set('All', 'syncShipments', $message, 'error');

                throw new \Exception(count($errors) . ' erros durante a execução.');
            }

            if ($warnings) {
                $message = print_r($warnings, true);
                WebserviceLog::set('All', 'syncShipments', $message, 'warning');
            }
        }

        return true;
    }



    /**
     * Sincronize shipments
     *
     * @param type $webservice
     * @param type $date
     * @param type $update
     */
    public function importCollection($loginDetails, $trackingCode, $servicesMapping = null, $shipmentData = null)
    {

        $weight  = $shipmentData['weight'];
        $volumes = $shipmentData['volumes'];

        $class = studly_case($loginDetails->method);
        $method = new $class(
            $loginDetails->agency,
            $loginDetails->user,
            $loginDetails->password,
            $loginDetails->session_id,
            $loginDetails->department,
            $loginDetails->endpoint
        );

        $shipments = null;
        $providerId = $loginDetails->provider_id;

        try {
            $data = $method->getRecolhaByTrk($trackingCode);
        } catch (Exception $e) {
            if (!empty(Auth::user()) && Auth::check() && Auth::user()->isAdmin()) {
                throw new Exception($e->getMessage() . ' on file ' . $e->getFile() . ' line ' . $e->getLine());
            } else {
                throw new Exception($e->getMessage());
            }
        }

        if (is_null($servicesMapping)) {
            $servicesMapping = Service::filterAgencies()->pluck('id', 'code')->toArray();
        }

        if (!empty($data)) {

            $data['date'] = explode(' ', $data['date']);
            $data['date'] = @$data['date'][0];
            $data['weight']  = $weight; //assume peso e volumes a partir dos dados do envio. Adicionado em 5/julho/2018
            $data['volumes'] = $volumes;
            $shipment = Shipment::firstOrNew(['provider_tracking_code' => $data['provider_tracking_code']]);

            $shipment->is_collection = 1;
            if (!$shipment->exists) {
                $shipment->customer_id = $loginDetails->customer->id;
                $shipment->provider_id = $loginDetails->provider_id;

                $data['sender_zip_code'] = explode('-', $data['sender_zip_code']);
                $data['sender_zip_code'] = $data['sender_zip_code'][0];
                $data['recipient_zip_code'] = explode('-', $data['recipient_zip_code']);
                $data['recipient_zip_code'] = $data['recipient_zip_code'][0];

                if ($loginDetails->method == 'tipsa') {
                    if (substr($data['sender_zip_code'], 0, 1) === '6') {
                        $data['sender_zip_code'] = substr($data['sender_zip_code'], 1);
                    }

                    if (substr($data['recipient_zip_code'], 0, 1) === '6') {
                        $data['recipient_zip_code'] = substr($data['recipient_zip_code'], 1);
                    }
                }

                //auto detect recipient zip code
                $recipientZipCode = Shipment::getAgencyByZipCode($data['recipient_zip_code'], $providerId);

                $data['sender_country'] = (empty($data['sender_zip_code']) || strlen($data['sender_zip_code']) <= 4) ? 'pt' : 'es';
                $data['recipient_country'] = (empty($data['recipient_zip_code']) || strlen($data['recipient_zip_code'])) <= 4 ? 'pt' : 'es';

                if (empty($recipientZipCode->zone)) { //if zone is not auto detected, try detect by length of zip code
                    $data['recipient_country'] = 'pt';
                }

                $shipment->sender_country = $data['sender_country'];
                $shipment->recipient_country = $data['recipient_country'];

                //obtem agencia de origem e destino
                $shipment->agency_id = $loginDetails->customer->agency_id;
                $shipment->sender_agency_id = $loginDetails->customer->agency_id;
                $agencyId = $recipientZipCode->agency_id;

                if (empty($agencyId)) {
                    $agencyId = $shipment->agency_id; //se agencia de destino não encontrada, atribui agencia atual
                    /*if(config('app.source') == 'volumedourado') {
                        $shipment->provider_id = 6; //para a volumedourado, se a agencia de destino não encontrada, assume rangel
                    }*/
                }

                /*if(config('app.source') == 'volumedourado') {
                    $shipment->recipient_agency_id = $agencyId;
                } else {*/
                $shipment->recipient_agency_id = $shipment->agency_id; //excepto a volumedourado, todas as outras agencias ignoram a atribuição automática do destinatário
                /*}*/

                /**
                 * Obtem serviço
                 */
                $data['service'] = empty($data['service']) ? '14' : $data['service'];
                //$mappingServices = config('shipments_import_mapping.'.$loginDetails->method.'-services-collection');
                $mappingServices = config('shipments_import_mapping.' . $loginDetails->method . '-services');

                if (isset($mappingServices[$data['service']])) {
                    //$shipment->service_id = $mappingServices[$data['service']]; //original
                    $serviceCode = $mappingServices[$data['service']]; //obtem o codigo do
                    $shipment->service_id = @$servicesMapping[$serviceCode];
                } else {
                    $shipment->service_id = null;
                    throw new \Exception('Serviço ' . $data['service'] . ' não encontrado. Cliente: ' . $loginDetails->user . ' | Data: ' . $data['date']);
                }

                /**
                 * Obtem status
                 */
                $shipment->status_id = 15; //aguarda sincronização
                $shipment->fill($data);
                $shipment->is_collection = 1;
                $shipment->date = $data['date'];
            }

            if ($loginDetails->method == 'tipsa') {
                $data['sender_zip_code']    = substr($data['sender_zip_code'], 1);
                $data['recipient_zip_code'] = substr($data['recipient_zip_code'], 1);
            }

            if (!$shipment->exists) {
                $recipientZipCode = Shipment::getAgencyByZipCode($data['recipient_zip_code'], $providerId);
                $senderZipCode    = Shipment::getAgencyByZipCode($data['sender_zip_code'], $providerId);
            } else {
                $recipientZipCode = Shipment::getAgencyByZipCode($shipment->recipient_zip_code, $providerId);
                $senderZipCode    = Shipment::getAgencyByZipCode($shipment->sender_zip_code, $providerId);
            }

            $shipment->sender_country    = $senderZipCode->zone;
            $shipment->recipient_country = $recipientZipCode->zone;

            /**
             * Processa o peso
             * Considera sempre o maior dos 3 pesos possíveis
             */
            //original customer weight
            if ($shipment->exists) {
                $weight = $shipment->weight;
            } else {
                $weight = $data['weight'];
            }

            //webservice weight
            if ($data['weight'] > $weight) {
                $weight = $data['weight'];
            }

            $shipment->weight  = $weight;
            $shipment->volumes = $data['volumes'] ? $data['volumes'] : 1;

            /**
             * Calcula o preço e custo do envio
             */
            $service = Service::find($shipment->service_id);

            $tmpShipment = $shipment;
            $tmpShipment->service_id = @$service->assigned_service_id;
            $prices = Shipment::calcPrices($tmpShipment);
            if(@$prices['fillable']) {
                $shipment->fill($prices['fillable']);
            }

            //guarda informação sobre execução do webservice
            $shipment->webservice_method = $loginDetails->method;
            $shipment->submited_at = Date::now()->format('Y-m-d H:i:s');
            $shipment->setTrackingCode();
            return $shipment;
        }

        return true;
    }

    /**
     * Get shipment by trk
     *
     * @param $shipment
     * @param null $webserviceLogin
     * @return string
     * @throws Exception
     */
    public function getShipmentByTrk($shipmentTrk, $cargoAgency = null, $senderAgency = null, $webserviceLogin = null)
    {

        try {

            $method = new $webserviceLogin->class(
                $webserviceLogin->agency,
                $webserviceLogin->user,
                $webserviceLogin->password,
                $webserviceLogin->session_id,
                $webserviceLogin->department,
                $webserviceLogin->endpoint,
                $this->debug
            );

            return $method->getEnvioByTrk($cargoAgency, $senderAgency, $shipmentTrk);
        } catch (\Exception $e) {
            if (!empty(Auth::user()) && Auth::check() && Auth::user()->isAdmin()) {
                throw new Exception($e->getMessage() . ' on file ' . $e->getFile() . ' line ' . $e->getLine());
            } else {
                throw new Exception($e->getMessage());
            }
        }
    }

    /**
     * Get transportation guide
     *
     * @param $shipment
     * @param null $webserviceLogin
     * @return string
     * @throws Exception
     */
    public function deleteShipment($shipment, $webserviceLogin = null)
    {

        try {
            if (is_null($webserviceLogin)) {
                $webserviceLogin = $this->getLogin($shipment);
            }

            $method = new $webserviceLogin->class(
                $webserviceLogin->agency,
                $webserviceLogin->user,
                $webserviceLogin->password,
                $webserviceLogin->session_id,
                $webserviceLogin->department,
                $webserviceLogin->endpoint,
                $this->debug
            );

            return $method->destroyShipment($shipment);
        } catch (\Exception $e) {
            if (!empty(Auth::user()) && Auth::check() && Auth::user()->isAdmin()) {
                throw new \Exception($e->getMessage() . ' on file ' . $e->getFile() . ' line ' . $e->getLine());
            } else {
                throw new \Exception($e->getMessage());
            }
        }
    }

    /**
     * Get shipment by trk
     *
     * @param $shipment
     * @param null $webserviceLogin
     * @return string
     * @throws Exception
     */
    public function getIncidencesByTrk($shipmentTrk, $cargoAgency = null, $senderAgency = null, $webserviceLogin = null)
    {

        try {

            $method = new $webserviceLogin->class(
                $webserviceLogin->agency,
                $webserviceLogin->user,
                $webserviceLogin->password,
                $webserviceLogin->session_id,
                $webserviceLogin->department,
                $webserviceLogin->endpoint,
                $this->debug
            );

            return $method->getIncidenciasByTrk($cargoAgency, $senderAgency, $shipmentTrk);
        } catch (\Exception $e) {
            if (!empty(Auth::user()) && Auth::check() && Auth::user()->isAdmin()) {
                throw new Exception($e->getMessage() . ' on file ' . $e->getFile() . ' line ' . $e->getLine());
            } else {
                throw new Exception($e->getMessage());
            }
        }
    }

    /**
     * Sync shipment history status
     *
     * @param null $webservice
     * @param null $date
     * @param null $agencies
     * @return bool
     * @throws Exception
     */
    public function syncShipmentsHistory($webservice = null, $date = null, $agencies = null, $shipmentsIds = [], $webserviceMethod = null)
    {

        try {
            $massiveWebserviceMethods = ['enovo_tms', 'via_directa', 'mrw'];

            $startTime = microtime(true);
            $limitDate = new Date();
            $limitDate = $limitDate->subDays(40);
            $limitDate = $limitDate->format('Y-m-d');

            $finalStatus = ShippingStatus::remember(config('cache.query_ttl'))
                ->cacheTags(ShippingStatus::CACHE_TAG)
                ->where('is_final', 1)
                ->pluck('id')
                ->toArray();

            if (!$agencies) {
                $source = config('app.source');
                $agencies = Agency::remember(config('cache.query_ttl'))
                    ->cacheTags(Agency::CACHE_TAG)
                    ->whereSource($source)
                    ->pluck('id')
                    ->toArray();
            }

            //obtem todos os envios com webservice e que não estejam no estado entregue
            $shipments = Shipment::whereHas('customer', function ($q) use ($agencies) {
                $q->whereHas('webservices');
                $q->whereIn('agency_id', $agencies);
            })
                ->whereNotIn('status_id', $finalStatus);

            if (!empty($shipmentsIds)) {
                $limitDate = '2000-01-01'; //ilimita a data
                $shipments = $shipments->whereIn('id', $shipmentsIds);
            }

            if(!empty($webserviceMethod)) {
                $shipments = $shipments->where('webservice_method', $webserviceMethod);
            }

            $shipments = $shipments->whereNotNull('customer_id')
                ->whereNotNull('submited_at')
                ->whereNotNull('webservice_method')
                //->where('is_closed', 1) //adicionado em 27/05/2022 //comentado em 20/02/2023
                ->where('date', '>=', $limitDate)
                ->orderBy('webservice_method', 'asc')
                ->orderBy('date', 'desc')
                ->get([
                    DB::raw('CONCAT(customer_id, "-",provider_id) as uniqID'),
                    'shipments.*'
                ]);

            $totalShipments    = $shipments->count();
            $shipmentCustomers = $shipments->groupBy('customer_id');

            //obtem todos os clientes dos envios obtigos que tenham acesso aos webservices
            $customersWebserviceLogin = CustomerWebservice::remember(config('cache.query_ttl'))
                ->cacheTags(CustomerWebservice::CACHE_TAG)
                ->with('customer')
                ->whereIn('customer_id', array_keys($shipmentCustomers->toArray()))
                ->get();

            //obtem a partir dos envios, todos os tipos de ligação a webservice possiveis
            $allWebserviceMethods = $shipments->pluck('webservice_method')->toArray();
            $allWebserviceMethods = array_unique($allWebserviceMethods);

            //verifica os métodos que obtêm dados massivamente
            try {
                foreach ($allWebserviceMethods as $webserviceMethod) {
                    if (in_array($webserviceMethod, $massiveWebserviceMethods)) {
                        $shipmentsToUpdate = $shipments->filter(function ($q) use ($webserviceMethod) {
                            return $q->webservice_method == $webserviceMethod;
                        })->groupBy('uniqID');

                        if (!empty($shipmentsToUpdate)) {
                            if ($webserviceMethod == 'enovo_tms') {
                                $this->syncEnovoShipments($shipmentsToUpdate, $customersWebserviceLogin);
                            } else {
                                $this->syncMassiveHistory($shipmentsToUpdate, $webserviceMethod, $customersWebserviceLogin);
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                //comentado em 23/03/2022 para que caso exista falha não páre a atualização dos restantes estados.
                //throw new \Exception('FALHA na ligação ' . $webserviceMethod . ' - ' . $e->getMessage());
            }


            //remove da lista de envios todos os que sejam dos metodos massivos
            $shipments = $shipments->filter(function ($q) use ($massiveWebserviceMethods) {
                return !in_array($q->webservice_method, $massiveWebserviceMethods);
            });

            $errors = [];
            $shipmentsIds = [];

            foreach ($shipments as $shipment) {

                $shipmentsIds[] = $shipment->id;

                try {

                    if (!in_array($shipment->webservice_method, $massiveWebserviceMethods)) { //metodos que possuem obtenção massiva

                        //Obtem os dados de login do webservice associado ao envio atual
                        $webserviceLogin = $customersWebserviceLogin->filter(function ($item) use ($shipment) {
                            return $item->method == $shipment->webservice_method
                                && $item->customer_id == $shipment->customer_id
                                && $item->provider_id == $shipment->provider_id;
                        })->first();

                        if ($webserviceLogin) {
                            $webserviceLogin->class = studly_case($webserviceLogin->method);
                        }

                        if ($webserviceLogin && $shipment->webservice_method != 'gls') { ///PROVISÓRIO PARA NAO DAR ERRO
                            $this->updateShipmentHistory($shipment, $webserviceLogin);
                        }
                    }
                } catch (Exception $e) {
                    $errors[] = $shipment->tracking_code  . ':' . $e->getMessage() . ' file ' . $e->getFile() . ' line ' . $e->getLine();
                }
            }

            $endTime = microtime(true) - $startTime;

            //marca incidencias como resolvidas nos envios atualizados.
            if (!empty($shipmentsIds)) {
                Shipment::whereIn('id', $shipmentsIds)->update(['operator_id' => null]);

                $sqlIds = implode(',', $shipmentsIds);
                ShipmentHistory::whereRaw('shipment_id in (select id from shipments where status_id <> 9 and id in (' . $sqlIds . '))')
                    ->where('status_id', ShippingStatus::INCIDENCE_ID)
                    ->update(['resolved' => 1]);
            }

            if ($errors) {
                $message = implode('\n', $errors);

                if (empty($webserviceLogin)) {
                    $webserviceLogin = $webservice;
                } else {
                    $webserviceLogin = @$webserviceLogin->class;
                }

                WebserviceLog::set($webserviceLogin, 'syncHistory', $message, 'error', $endTime);

                throw new \Exception(count($errors) . ' de ' . $totalShipments . ' envios não foram atualizados.');
            }

            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage() . ' File ' . $e->getFile() . ' Line ' . $e->getLine());
        }
    }

    /**
     * Sync shipment history status
     *
     * @param null $webservice
     * @param null $date
     * @param null $agencies
     * @return bool
     * @throws Exception
     */
    public function syncMassiveHistory($shipments, $method, $customersWebserviceLogin = null)
    {

        $startTime = microtime(true);
        $allShipments = $shipments;

        $errors = [];
        $shipmentsIds = [];
        $totalShipments = 0;
        foreach ($allShipments as $customerId => $shipments) {

            if(empty($customerId)) {
                $customerId = @$shipments[0]['customer_id'];
            }

            $parts = explode('-', $customerId);
            $customerId = @$parts[0];
            $providerId = @$parts[1];

            $totalShipments += count($shipments);

            $shipmentsIds = array_merge($shipments->pluck('id')->toArray(), $shipmentsIds);

            $webserviceLogin = $customersWebserviceLogin->filter(function ($item) use ($customerId, $method) {
                return $item->method == $method && $item->customer_id == $customerId;
            })->first();

            if ($webserviceLogin) {

                $webserviceLogin->class = studly_case($webserviceLogin->method);

                $methodClass = new $webserviceLogin->class(
                    $webserviceLogin->agency,
                    $webserviceLogin->user,
                    $webserviceLogin->password,
                    $webserviceLogin->session_id,
                    $webserviceLogin->department,
                    $webserviceLogin->endpoint,
                    $this->debug
                );

                $response = $methodClass->updateHistoryMassive($shipments);

                if (is_array($response)) {
                    $errors = $response;
                }
            }
        }

        $endTime = microtime(true) - $startTime;

        //marca incidencias como resolvidas nos envios atualizados.
        if (!empty($shipmentsIds)) {
            $sqlIds = implode(',', $shipmentsIds);
            ShipmentHistory::whereRaw('shipment_id in (select id from shipments where status_id <> 9 and id in (' . $sqlIds . '))')
                ->where('status_id', ShippingStatus::INCIDENCE_ID)
                ->update(['resolved' => 1]);
        }

        if ($errors) {
            $message = implode('|', $errors);

            WebserviceLog::set($webserviceLogin, 'syncHistory', $message, 'error', $endTime);

            $trace = LogViewer::getTrace(null, count($errors) . ' de ' . $totalShipments . ' envios Massivos não foram atualizados.');
            Log::error(br2nl($trace));

            //throw new \Exception(count($errors) . ' de ' . $totalShipments . ' envios ENOVO não foram atualizados.');
        }

        return true;
    }

    /**
     * Sync shipment history status
     *
     * @param null $webservice
     * @param null $date
     * @param null $agencies
     * @return bool
     * @throws Exception
     */
    public function syncEnovoShipments($shipments, $customersWebserviceLogin = null)
    {

        $startTime = microtime(true);
        $allShipments = $shipments;

        $errors = [];
        $shipmentsIds = [];
        $totalShipments = 0;
        foreach ($allShipments as $customerId => $shipments) {

            $parts = explode('-', $customerId);
            $customerId = @$parts[0];
            $providerId = @$parts[1];

            $totalShipments += count($shipments);

            $shipmentsIds = array_merge($shipments->pluck('id')->toArray(), $shipmentsIds);

            $webserviceLogin = $customersWebserviceLogin->filter(function ($item) use ($customerId, $providerId) {
                return $item->method == 'enovo_tms' && $item->customer_id == $customerId && $item->provider_id == $providerId;
            })->first();

            if ($webserviceLogin) {

                $webserviceLogin->class = studly_case($webserviceLogin->method);

                $methodClass = new $webserviceLogin->class(
                    $webserviceLogin->agency,
                    $webserviceLogin->user,
                    $webserviceLogin->password,
                    $webserviceLogin->session_id,
                    $webserviceLogin->department,
                    $webserviceLogin->endpoint,
                    $this->debug
                );

                try {
                    $response = $methodClass->updateHistoryMassive($shipments);

                    if (is_array($response)) {
                        $errors = $response;
                    }
                } catch (\Exception $e) {
                    $errors = [$e->getMessage()];
                }
            }
        }

        $endTime = microtime(true) - $startTime;

        //marca incidencias como resolvidas nos envios atualizados.
        if (!empty($shipmentsIds)) {
            $sqlIds = implode(',', $shipmentsIds);
            ShipmentHistory::whereRaw('shipment_id in (select id from shipments where status_id <> 9 and id in (' . $sqlIds . '))')
                ->where('status_id', ShippingStatus::INCIDENCE_ID)
                ->update(['resolved' => 1]);
        }

        if ($errors) {
            $message = implode('|', $errors);

            WebserviceLog::set($webserviceLogin, 'syncHistory', $message, 'error', $endTime);

            $trace = LogViewer::getTrace(null, count($errors) . ' de ' . $totalShipments . ' envios ENOVO não foram atualizados.');
            Log::error(br2nl($trace));

            //throw new \Exception(count($errors) . ' de ' . $totalShipments . ' envios ENOVO não foram atualizados.');
        }

        return true;
    }


    /**
     * Get adthesive labels
     *
     * @param $shipment
     * @param null $webserviceLogin
     * @param string $outputFormat
     * @return bool
     * @throws Exception
     */
    public function getAdhesiveLabel($shipment, $webserviceLogin = null, $outputFormat = 'I')
    {

        try {
            if (is_null($webserviceLogin)) {
                try {
                    $webserviceLogin = $this->getLogin($shipment);
                } catch (\Exception $e) {
                    return false; //throw new \Exception($e->getMessage(), $e->getCode());
                }
            }

            //needs to add aliases on config/app.php
            $method = new $webserviceLogin->class(
                $webserviceLogin->agency,
                $webserviceLogin->user,
                $webserviceLogin->password,
                $webserviceLogin->session_id,
                $webserviceLogin->department,
                $webserviceLogin->endpoint,
                $this->debug
            );

            return $method->getEtiqueta($shipment->provider_sender_agency, $shipment->provider_tracking_code, $outputFormat, $shipment->tracking_code, $shipment->volumes);
        } catch (\Exception $e) {

            if (!empty(Auth::user()) && Auth::check() && Auth::user()->isAdmin()) {
                throw new Exception('Não foi possível gerar a etiqueta. ' . $e->getMessage() . ' on file ' . $e->getFile() . ' line ' . $e->getLine());
            } else {
                throw new Exception('Não foi possível gerar a etiqueta. ' . $e->getMessage());
            }
        }
    }

    /**
     * Get transportation guide
     *
     * @param $shipment
     * @param null $webserviceLogin
     * @return string
     * @throws Exception
     */
    public function getTransportationGuide($shipment, $webserviceLogin = null)
    {

        try {
            if (is_null($webserviceLogin)) {
                $webserviceLogin = $this->getLogin($shipment);
            }

            //needs to add aliases on config/app.php
            $method = new $webserviceLogin->class(
                $webserviceLogin->agency,
                $webserviceLogin->user,
                $webserviceLogin->password,
                $webserviceLogin->session_id,
                $webserviceLogin->department,
                $webserviceLogin->endpoint,
                $this->debug
            );


            return $method->getGuiaTransporte($shipment->provider_sender_agency, $shipment->provider_tracking_code);
        } catch (\Exception $e) {
            if (!empty(Auth::user()) && Auth::check() && Auth::user()->isAdmin()) {
                throw new Exception($e->getMessage() . ' on file ' . $e->getFile() . ' line ' . $e->getLine());
            } else {
                throw new Exception('Não foi encontrado nenhum ficheiro para impressão. O envio pode ter sido eliminado.');
            }
        }
    }

    /**
     * Return reimbursement guide encoded in base64
     * @param $shipment
     * @return string
     */
    public function getReimbursementGuide($shipment, $webserviceLogin = null)
    {

        try {
            if (is_null($webserviceLogin)) {
                $webserviceLogin = $this->getLogin($shipment);
            }

            //needs to add aliases on config/app.php
            $method = new $webserviceLogin->class(
                $webserviceLogin->agency,
                $webserviceLogin->user,
                $webserviceLogin->password,
                $webserviceLogin->session_id,
                $webserviceLogin->department,
                $webserviceLogin->endpoint,
                $this->debug
            );

            return $method->getGuiaReembolso($shipment->provider_tracking_code);
        } catch (\Exception $e) {
            if (!empty(Auth::user()) && Auth::check() && Auth::user()->isAdmin()) {
                throw new Exception($e->getMessage() . ' on file ' . $e->getFile() . ' line ' . $e->getLine());
            } else {
                throw new Exception('Não foi encontrado nenhum ficheiro para impressão. O envio pode ter sido eliminado.');
            }
        }
    }

    /**
     * Close shipments
     *
     * @param type $webservice
     * @param type $date
     * @param type $update
     * @return array Array with path of all files to print
     */
    public function closeShipments($webservice = null, $ids = array())
    {

        //obtem todos os envios com webservice selecionado, que não estejam no estado entregue
        $shipments = Shipment::with(['customer' => function ($q) use ($webservice) {
            $q->select(['id', 'name', 'address', 'zip_code', 'city', 'country', 'phone']);
            $q->with(['webservices' => function ($q) use ($webservice) {
                $q->where('method', $webservice);
                $q->select(['customer_id', 'user']);
            }]);
        }])
            ->whereHas('customer', function ($q) {
                $q->whereHas('webservices');
            });

        if (!empty($ids)) {
            $shipments = $shipments->whereIn('id', $ids);
        } else {
            $shipments = $shipments->where('is_closed', 0);
        }

        $shipments = $shipments->whereNotNull('customer_id')
            ->where('webservice_method', $webservice)
            ->get(['id', 'customer_id', 'provider_id', 'provider_tracking_code', 'webservice_method', 'service_id', 'volumes', 'weight', 'recipient_country', 'date', 'insurance_price', 'provider_cargo_agency']);

        //Agrupa os envios por user do webservice.
        // Evita chamadas repetidas se houver vários clientes com os mesmos dados do webservices
        $shipments = $shipments->groupBy(function ($item) {
            //return $item->customer->webservices->first()->user; //comentado 4/marco/2022
            return $item->provider_cargo_agency;
        });

        //A partir dos envios selecionados, obtem todos os clientes iniquivocos com acesso aos webservices
        $customers = CustomerWebservice::whereIn('user', array_keys($shipments->toArray()))
            ->where('method', $webservice)
            ->groupBy('user', 'method')
            ->get();

        foreach ($customers as $loginDetails) {

            try {
                $class = studly_case($loginDetails->method);
                $method = new $class(
                    $loginDetails->agency,
                    $loginDetails->user,
                    $loginDetails->password,
                    $loginDetails->session_id,
                    $loginDetails->department,
                    $loginDetails->endpoint
                );

                $customerShipments = $shipments[$loginDetails->user];

                //$files[$loginDetails->user] = $method->fechaEnvios($customerShipments);

                $filepath = $method->fechaEnvios($customerShipments);

                //fecha todos envios
                Shipment::whereIn('id', $ids)->update(['is_closed' => true]);

                return [
                    'result'   => true,
                    'filepath' => $filepath,
                    'feedback' => 'Envios fechados com sucesso.'
                ];
            } catch (\Exception $e) {
                return [
                    'result'   => false,
                    'filepath' => null,
                    'feedback' => $e->getMessage()
                ];
            }
        }
    }


    /**
     * Get transit time
     * @param null $webservice
     */
    public function getTransitTime($webservice = null)
    {
        //Obtem os dados de uma ligação ao webservice.
        //Não precisa ser de um utilizador em especifico pois o webservice é global para qualquer utilizador
        $customerWebservice = CustomerWebservice::where('method', $webservice)
            ->orderBy('id', 'desc')
            ->first();

        if (!$customerWebservice) {
            return [
                'result'   => true,
                'points'   => []
            ];
        }

        try {
            $class = studly_case($customerWebservice->method);
            $method = new $class(
                $customerWebservice->agency,
                $customerWebservice->user,
                $customerWebservice->password,
                $customerWebservice->session_id,
                $customerWebservice->department,
                $customerWebservice->endpoint
            );

            $points = $method->calcTransitTime();

            return [
                'result'   => true,
                'points'   => $points,
            ];
        } catch (\Exception $e) {
            return [
                'result'   => false,
                'points'   => null,
                'feedback' => $e->getMessage()
            ];
        }
    }

    /**
     * Get Pickup Points
     * @param null $webservice
     * @param array $ids
     * @return array
     */
    public function getPickupPoints($webservice = null)
    {

        //Obtem os dados de uma ligação ao webservice.
        //Não precisa ser de um utilizador em especifico pois o webservice é global para qualquer utilizador
        $customerWebservice = CustomerWebservice::where('method', $webservice)
            ->orderBy('id', 'desc')
            ->first();

        if (!$customerWebservice) {
            return [
                'result'   => true,
                'points'   => []
            ];
        }

        try {
            $class = studly_case($customerWebservice->method);
            $method = new $class(
                $customerWebservice->agency,
                $customerWebservice->user,
                $customerWebservice->password,
                $customerWebservice->session_id,
                $customerWebservice->department,
                $customerWebservice->endpoint
            );

            $points = $method->getPontosRecolha();

            return [
                'result'   => true,
                'points'   => $points,
            ];
        } catch (\Exception $e) {
            return [
                'result'   => false,
                'points'   => null,
                'feedback' => $e->getMessage()
            ];
        }
    }

    /**
     * @param $code
     * @return mixed|string
     */
    private function detectIslandZipCode($zipCode)
    {

        $zipCodes = [
            "9000", "9004", "9020", "9024", "9030", "9050", "9054", "9060", "9064", "9100", "9125", "9135", "9200", "9225",
            "9230", "9240", "9270", "9300", "9304", "9325", "9350", "9360", "9370", "9374", "9385", "9400", "9580", "9500", "9504",
            "9545", "9555", "9560", "9600", "9625", "9630", "9650", "9675", "9680", "9684", "9700", "9701", "9760", "9880", "9800",
            "9804", "9850", "9875", "9930", "9934", "9940", "9944", "9950", "9900", "9901", "9904", "9960", "9970", "9980",
            "35329", "35350", "35230", "35107", "35480", "35468", "35299", "35259", "35259", "35260", "35469", "35339", "35458",
            "35479", "35119", "35470", "35149", "35018", "35018", "35216", "35217", "35412", "35300", "35414", "35310", "35400",
            "35309", "35459", "35129", "35333", "35412", "35307", "35329", "35211", "35319", "35120", "35118", "35328", "35108",
            "35478", "35350", "35400", "35329", "35450", "35307", "35369", "35109", "35100", "35210", "35129", "35110", "35414",
            "35269", "35319", "35349", "35129", "35260", "35129", "35431", "35421", "35421", "35457", "35339", "35458", "35468",
            "35468", "35338", "35349", "35128", "35469", "35457", "35432", "35217", "35411", "35411", "35469", "35330", "35412",
            "35457", "35230", "35310", "35450", "35489", "35107", "35110", "35338", "35329", "35459", "35422", "35308", "35468",
            "35330", "35431", "35240", "35149", "35422", "35309", "35128", "35468", "35129", "35458", "35319", "35432", "35106",
            "35215", "35329", "35469", "35450", "35017", "35329", "35432", "35400", "35413", "35412", "35110", "35100", "35459",
            "35299", "35110", "35129", "35350", "35369", "35100", "35217", "35309", "35215", "35413", "35478", "35415", "35470",
            "35349", "35422", "35423", "35413", "35250", "35368", "35240", "35329", "35110", "35328", "35119", "35431", "35149",
            "35280", "35478", "35216", "35458", "35149", "35369", "35214", "35308", "35217", "35457", "35349", "35218", "35218",
            "35108", "35479", "35149", "35308", "35216", "35479", "35300", "35300", "35413", "35107", "35412", "35211", "35218",
            "35479", "35149", "35299", "35128", "35450", "35108", "35400", "35110", "35130", "35120", "35128", "35413", "35329",
            "35368", "35330", "35329", "35217", "35280", "35310", "35216", "35216", "35211", "35307", "35469", "35422", "35307",
            "35120", "35120", "35338", "35469", "35328", "35130", "35328", "35432", "35018", "35350", "35421", "35413", "35100",
            "35118", "35300", "35110", "35479", "35369", "35413", "35328", "35328", "35329", "35432", "35478", "35329", "35215",
            "35479", "35457", "35300", "35310", "35339", "35307", "35414", "35260", "35329", "35369", "35309", "35217", "35350",
            "35329", "35489", "35299", "35368", "35469", "35468", "35369", "35458", "35415", "35458", "35110", "35432", "35458",
            "35423", "35469", "35017", "35017", "35423", "35269", "35216", "35414", "35339", "35338", "35368", "35479", "35307",
            "35328", "35300", "35212", "35468", "35458", "35469", "35338", "35108", "35128", "35430", "35107", "35421", "35018",
            "35215", "35422", "35319", "35412", "35400", "35469", "35328", "35460", "35018", "35328", "35308", "35308", "35219",
            "35218", "35308", "35212", "35118", "35400", "35219", "35307", "35218", "35308", "35339", "35489", "35450", "35413",
            "35411", "35309", "35216", "35218", "35329", "35413", "35413", "35400", "35413", "35468", "35149", "35329", "35330",
            "35149", "35128", "35220", "35108", "35218", "35018", "35308", "35310", "35411", "35328", "35468", "35468", "35400",
            "35299", "35329", "35457", "35299", "35299", "35329", "35110", "35329", "35413", "35421", "35413", "35479", "35478",
            "35330", "35017", "35339", "35128", "35215", "35149", "35250", "35457", "35280", "35431", "35215", "35470", "35215",
            "35217", "35107", "35421", "35368", "35468", "35457", "35479", "35479", "35349", "35280", "35328", "35423", "35340",
            "35329", "35329", "35310", "35217", "35269", "35413", "35330", "35229", "35017", "35459", "35217", "35339", "35468",
            "35149", "35110", "35280", "35328", "35413", "35431", "35217", "35329", "35457", "35220", "35017", "35423", "35329",
            "35328", "35215", "35330", "35411", "35210", "35299", "35458", "35369", "35106", "35109", "35413", "35400", "35338",
            "35250", "35107", "35421", "35415", "35309", "35109", "35411", "35309", "35329", "35458", "35139", "35432", "35413",
            "35215", "35017", "35479", "35411", "35330", "35211", "35211", "35329", "35308", "35350", "35217", "35340", "35308",
            "35488", "35018", "35259", "35217", "35369", "35479", "35450", "35457", "35259", "35488", "35212", "35229", "35100",
            "35400", "35220", "35107", "35015", "35479", "35109", "35215", "35250", "35309", "35214", "35100", "35432", "35229",
            "35018", "35329", "35018", "35109", "35217", "35412", "35338", "35310", "35140", "35138", "35479", "35149", "35478",
            "35308", "35479", "35349", "35259", "35458", "35457", "35415", "35400", "35459", "35210", "35269", "35280", "35118",
            "35109", "35415", "35280", "35400", "35017", "35319", "35300", "35216", "35310", "35109", "35017", "35300", "35422",
            "35280", "35259", "35107", "35130", "35420", "35330", "35280", "35330", "35140", "35469", "35269", "35338", "35219",
            "35309", "35110", "35432", "35414", "35128", "35328", "35478", "35339", "35309", "35001", "35002", "35003", "35004",
            "35005", "35006", "35007", "35008", "35009", "35010", "35011", "35012", "35013", "35014", "35015", "35016", "35017",
            "35018", "35019", "35220", "35229", "35480", "35300", "35411", "35431", "35269", "35413", "35458", "35218", "35422",
            "35109", "35413", "35109", "35423", "35308", "35213", "35339", "35110", "35220", "35280", "35280", "35259", "35106",
            "35149", "35478", "35457", "35129", "35106", "35106", "35339", "35216", "35412", "35431", "35339", "35339", "35414",
            "35412", "35128", "35415", "35299", "35018", "35412", "35017", "35118", "35149", "35018", "35215", "35479", "35309",
            "35309", "35217", "35018", "35328", "35468", "35328", "35299", "35138", "35130", "35214", "35214", "35214", "35138",
            "35219", "35149", "35100", "35214", "35100", "35219", "35479", "35300", "35219", "35118", "35219", "35308", "35412",
            "35328", "35119", "35411", "35414", "35489", "35469", "35130", "35469", "35259", "35339", "35488", "35414", "35307",
            "35213", "35310", "35479", "35369", "35216", "35330", "35280", "35489", "35369", "35328", "35414", "35432", "35107",
            "35414", "35259", "35368", "35217", "35479", "35339", "35432", "35269", "35118", "35468", "35478", "35280", "35149",
            "35017", "35107", "35214", "35106", "35100", "35414", "35431", "35213", "35421", "35290", "35458", "35212", "35414",
            "35100", "35421", "35200", "35328", "35411", "35411", "35200", "35488", "35213", "35338", "35210", "35300", "35458",
            "35200", "35018", "35320", "35339", "35470", "35489", "35110", "35110", "35217", "35128", "35300", "35457", "35412",
            "35280", "35229", "35450", "35411", "35411", "35309", "35489", "35110", "35018", "35300", "35468", "35330", "35328",
            "35250", "35299", "35017", "35339", "35468", "35308", "35280", "35280", "35280", "35328", "35412", "35328", "35368",
            "35218", "35320", "35100", "35128", "35280", "35018", "35489", "35458", "35216", "35149", "35479", "35109", "35421",
            "35017", "35280", "35214", "35018", "35413", "35210", "35414", "35107", "35478", "35478", "35138", "35211", "35217",
            "35109", "35468", "35309", "35360", "35200", "35210", "35211", "35212", "35213", "35214", "35215", "35218", "35219",
            "35220", "35270", "35216", "35330", "35400", "35421", "35369", "35413", "35478", "35422", "35338", "35368", "35310",
            "35413", "35432", "35413", "35280", "35413", "35457", "35458", "35216", "35349", "35422", "35290", "35480", "35369",
            "35149", "35110", "35328", "35489", "35218", "35220", "35211", "35217", "35280", "35340", "35349", "35217", "35269",
            "35489", "35110", "35320", "35328", "35216", "35329", "35128", "35457", "35110", "35458", "35300", "35215", "35310",
            "35328", "35307", "35412", "35328", "35349", "35349", "35431", "38687", "38439", "38618", "38683", "38688", "38111",
            "38670", "38357", "38297", "38610", "38139", "38355", "38687", "38310", "38429", "38591", "38686", "38628", "38111",
            "38129", "38389", "38639", "38438", "38111", "38108", "38360", "38540", "38312", "38690", "38589", "38589", "38688",
            "38678", "38509", "38640", "38628", "38629", "38611", "38315", "38129", "38250", "38291", "38355", "38107", "38510",
            "38627", "38616", "38290", "38479", "38294", "38310", "38627", "38294", "38434", "38129", "38313", "38616", "38109",
            "38509", "38434", "38480", "38627", "38627", "38660", "38460", "38460", "38530", "38379", "38678", "38627", "38293",
            "38109", "38170", "38340", "38300", "38434", "38678", "38530", "38509", "38312", "38350", "38399", "38294", "38150",
            "38340", "38419", "38489", "38460", "38611", "38588", "38357", "38139", "38649", "38470", "38611", "38639", "38129",
            "38595", "38310", "38616", "38652", "38688", "38591", "38594", "38294", "38689", "38688", "38632", "38109", "38611",
            "38108", "38589", "38677", "38441", "38399", "38660", "38660", "38660", "38630", "38109", "38650", "38460", "38441",
            "38626", "38616", "38312", "38616", "38439", "38294", "38413", "38390", "38320", "38180", "38686", "38438", "38150",
            "38314", "38540", "38129", "38180", "38589", "38311", "38616", "38129", "38617", "38314", "38592", "38570", "38435",
            "38614", "38591", "38290", "38679", "38588", "38398", "38570", "38413", "38311", "38311", "38434", "38687", "38632",
            "38629", "38311", "38439", "38107", "38631", "38450", "38589", "38459", "38639", "38311", "38600", "38619", "38419",
            "38330", "38440", "38632", "38358", "38632", "38379", "38680", "38500", "38458", "38639", "38310", "38616", "38311",
            "38449", "38430", "38414", "38592", "38615", "38520", "38140", "38292", "38588", "38379", "38629", "38688", "38419",
            "38358", "38489", "38415", "38434", "38290", "38449", "38617", "38440", "38357", "38129", "38590", "38688", "38290",
            "38390", "38418", "38441", "38312", "38340", "38190", "38540", "38434", "38690", "38678", "38180", "38489", "38370",
            "38110", "38612", "38560", "38677", "38293", "38358", "38439", "38690", "38611", "38649", "38294", "38419", "38459",
            "38441", "38310", "38677", "38340", "38670", "38300", "38297", "38292", "38560", "38489", "38632", "38415", "38617",
            "38300", "38438", "38438", "38292", "38434", "38315", "38687", "38107", "38449", "38311", "38310", "38650", "38687",
            "38588", "38489", "38292", "38310", "38688", "38358", "38678", "38508", "38479", "38360", "38400", "38683", "38358",
            "38330", "38588", "38240", "38560", "38379", "38428", "38390", "38677", "38109", "38359", "38410", "38412", "38410",
            "38314", "38632", "38389", "38690", "38314", "38593", "38294", "38129", "38139", "38629", "38570", "38107", "38428",
            "38290", "38594", "38631", "38435", "38579", "38649", "38589", "38617", "38120", "38379", "38314", "38419", "38479",
            "38108", "38201", "38202", "38203", "38204", "38205", "38206", "38207", "38208", "38291", "38293", "38296", "38320",
            "38329", "38430", "38109", "38611", "38312", "38356", "38428", "38435", "38420", "38459", "38356", "38430", "38108",
            "38620", "38296", "38588", "38460", "38434", "38441", "38358", "38001", "38002", "38003", "38004", "38005", "38006",
            "38007", "38008", "38009", "38010", "38107", "38110", "38111", "38160", "38170", "38320", "38111", "38390", "38690",
            "38441", "38310", "38360", "38479", "38310", "38470", "38107", "38508", "38292", "38579", "38449", "38129", "38190",
            "38591", "38107", "38294", "38108", "38350", "38130", "38358", "38628", "38399", "38684", "38435", "38677", "38589",
            "38280", "38260", "38685", "38630", "38489", "38441", "38435", "38416", "38677", "38677", "38107", "38350", "38670",
            "38398", "38398", "38417", "38626", "38615", "38649", "38180", "38180", "38690", "38139", "38626", "38359", "38270",
            "38329", "38329", "38160", "38150", "38686", "38439", "38439", "38594", "38649", "38688", "38390", "38389", "38107",
            "38616", "38380", "38613", "38580", "38617", "38611", "38509", "38617", "38419", "38579", "38628", "07860", "07872",
            "07872", "07872", "07872", "07871", "07871", "07860", "07870", "07340", "07179", "07400", "07210", "07142", "07150",
            "07199", "07600", "07529", "07159", "07570", "07609", "07609", "07191", "07691", "07470", "07609", "07181", "07579",
            "07350", "07143", "07369", "07101", "07350", "07311", "07110", "07141", "07600", "07314", "07680", "07609", "07559",
            "07660", "07669", "07659", "07590", "07590", "07690", "07589", "07560", "07579", "07688", "07639", "07589", "07590",
            "07690", "07469", "07659", "07669", "07689", "07315", "07689", "07669", "07184", "07160", "07310", "07630", "07610",
            "07458", "07690", "07196", "07580", "07208", "07140", "07181", "07208", "07181", "07638", "07579", "07193", "07330",
            "07183", "07559", "07181", "07690", "07144", "07142", "07639", "07179", "07590", "07600", "07141", "07141", "07315",
            "07689", "07190", "07192", "07200", "07589", "07109", "07180", "07195", "07120", "07669", "07181", "07300", "07144",
            "07690", "07518", "07360", "07430", "07315", "07179", "07620", "07181", "07609", "07400", "07500", "07312", "07610",
            "07519", "07400", "07141", "07141", "07230", "07316", "07440", "07589", "07142", "07349", "07001", "07002", "07003",
            "07004", "07005", "07006", "07007", "07008", "07009", "07010", "07011", "07012", "07013", "07014", "07015", "07120",
            "07121", "07198", "07199", "07600", "07610", "07611", "07181", "07193", "07630", "07609", "07121", "07590", "07160",
            "07520", "07220", "07199", "07400", "07458", "07420", "07460", "07260", "07400", "07157", "07470", "07315", "07108",
            "07190", "07559", "07181", "07181", "07670", "07680", "07141", "07691", "07609", "07194", "07629", "07639", "07511",
            "07315", "07560", "07198", "07193", "07181", "07659", "07640", "07691", "07159", "07240", "07199", "07530", "07142",
            "07450", "07320", "07180", "07650", "07313", "07140", "07600", "07190", "07639", "07100", "07198", "07687", "07510",
            "07181", "07100", "07143", "07540", "07120", "07400", "07181", "07198", "07509", "07170", "07209", "07690", "07208",
            "07209", "07011", "07120", "07459", "07550", "07209", "07609", "07609", "07180", "07310", "07122", "07609", "07170",
            "07639", "07250", "07817", "07811", "07850", "07849", "07850", "07810", "07813", "07819", "07849", "07813", "07849",
            "07819", "07819", "07813", "07800", "07800", "07849", "07850", "07819", "07815", "07819", "07813", "07819", "07840",
            "07820", "07849", "07850", "07810", "07817", "07829", "07829", "07830", "07830", "07817", "07817", "07839", "07818",
            "07839", "07817", "07839", "07812", "07816", "07815", "07816", "07811", "07828", "07850", "07840", "07850", "07849",
            "07850", "07819", "07849", "07849", "07819", "07849", "07819", "07819", "07849", "07849", "07850", "07849", "07850",
            "07819", "07819", "07849", "07849", "07849", "07819", "07849", "07840", "07814", "07849", "07849", "07849", "07730",
            "07713", "07730", "07711", "07711", "07711", "07711", "07711", "07711", "07711", "07712", "07713", "07769", "07769",
            "07769", "07750", "07720", "07769", "07769", "07730", "07730", "07769", "07711", "07760", "07740", "07769", "07712",
            "07720", "07713", "07749", "07713", "07750", "07748", "07700", "07712", "07700", "07701", "07702", "07703", "07714",
            "07713", "07740", "07700", "07700", "07740", "07748", "07740", "07713", "07730", "07713", "07700", "07730", "07712",
            "07710", "07749", "07769", "07740", "07750", "07769", "07730", "07740", "07720", "07711", "07769", "07730", "07711",
            "07720", "07711", "07711"
        ];

        return in_array($zipCode, $zipCodes, true);
    }
}
