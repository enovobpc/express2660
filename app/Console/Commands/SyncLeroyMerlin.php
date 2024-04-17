<?php

namespace App\Console\Commands;

use App\Models\BroadcastPusher;
use App\Models\Customer;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use App\Models\ShippingStatus;
use Illuminate\Console\Command;
use File, Mail, Date, Setting;

class SyncLeroyMerlin extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:leroy-merlin {action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync data with Leroy Merlin';

    /**
     * API endpoint
     * @var string
     */
    //DOCUMENTAÇÃO VONZU: https://int-client.apidoc.vonzu.es/
    //private $endpoint = 'https://int-api.app.vonzu.es/api/v2/'; //testes
    private $endpoint = 'https://api.app.vonzu.es/api/v2/'; //produçao

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        $this->info("Sync data with LEROY MERLIN");

        if($this->argument('action') == 'import') {
            $this->importServices();
        } else {
            $this->comunicateStatus();
        }

        $this->info("Sync finalized");
        return;
    }

    /**
     * Import services from webservice
     */
    public function importServices() {

        //https://int-client.apidoc.vonzu.es/

        $endDate = new Date();
        $endDate = $endDate->addDays(5);

        $params = [
            "query" => [
                "bool" => [
                    "must" => [
                        [
                            "range" => [
                                "date" => [
                                    //"gte" => '2021-06-25',
                                    //"lte" => '2021-06-25',
                                    "gte" => date('Y-m-d'),
                                    "lte" => $endDate->format('Y-m-d')
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            "from" => 0,
            "size" => 100
        ];


        $url = $this->endpoint . 'services/findMany';
        $shipments = $this->callWebservicePOST($url, $params);
        
        if(count($shipments) == 100) { //pagina 1 cheia
            $params['from'] = 100; //atualiza na variavel o inicio da pesquisa
            $shipments2 = $this->callWebservicePOST($url, $params);
            $shipments  = empty($shipments2) ? $shipments : array_merge($shipments, $shipments2);
            
            if(count($shipments2) == 100) { //pagina 2 cheia
                $params['from'] = 200;
                $shipments2 = $this->callWebservicePOST($url, $params);
                $shipments  = empty($shipments2) ? $shipments : array_merge($shipments, $shipments2);
                
                if(count($shipments2) == 100) { //pagina 3 cheia
                    $params['from'] = 300;
                    $shipments2 = $this->callWebservicePOST($url, $params);
                    $shipments  = empty($shipments2) ? $shipments : array_merge($shipments, $shipments2);
                }
            }
        }

        if($shipments) {

            foreach ($shipments as $row) {

                if(env('APP_ENV') == 'local') {
                    $customerId = 17925;
                    $serviceId  = 201;
                } else {

                    if(config('app.source') == 'ancorasemargens') {
                        $customerId = 9119;
                        $serviceId  = 681;
                        if (str_contains(@$row['description'], 'domicílio')) {
                            $serviceId = '681'; //entrega dentro de casa
                        }
                    } elseif(config('app.source') == 'logisimple') {
                        $customerId = 44;
                        $serviceId  = 17;
                        if (str_contains(@$row['description'], 'domicílio')) {
                            $serviceId = 17;
                        }
                    }
                }

                /* $row['extraFields'] = ['storeCode' => '036'];
                 $row['contact'] = ['name' => 'xpto', 'phones' => ['90000000']];*/

                $recipientAttn  = @$row['contact']['name'];
                $recipientPhone = @$row['contactPhone'] ?  @$row['contactPhone'] : @$row['contact']['phones'][0];

                $storeId = intval(@$row['extraFields']['storeCode']);
                $store   = $this->getLoja($storeId);
                $type    = $row['type'];


                //Mapping Shipment data
                $vols = $row['packageCount'];
                $row = $this->mappingShipment($row);

                $row['type'] = $type;

                $row['volumes'] = $vols;


                //$row['date']  = date('Y-m-d', ($row['date']/1000)); //original ate 9/maio/2023
                $row['delivery_date'] = date('Y-m-d', ($row['date']/1000)); //data que vem no pedido é a data entrega (ticket #TRV-671-3877)
                $row['date'] = date('Y-m-d'); //data do pedido é sempre a data atual (ticket #TRV-671-3877)

                if($row['delivery_date'] >= date('Y-m-d')) {

                    $customer = Customer::remember(5)
                        ->filterSource()
                        ->find($customerId);


                    if($row['type'] == 'pickup') {
                        $row['reference'] = 'P'.$row['reference'];
                    }

                    $shipment = Shipment::firstOrNew([
                        'customer_id'  => @$customer->id,
                        'reference'    => @$row['reference'],
                        'reference2'   => @$row['reference2']
                    ]);

                    if ($shipment->exists && @$row['canceled']) {
                        $shipment->status_id = ShippingStatus::CANCELED_ID;
                        $shipment->save();

                        $history = new ShipmentHistory();
                        $history->status_id = $shipment->status_id;
                        $history->agency_id = $shipment->agency_id;
                        $history->shipment_id = $shipment->id;
                        $history->api = 1;
                        $history->save();

                    } elseif ($shipment->exists) {
                        $shipment->volumes = $row['volumes'];
                        $shipment->save();
                    } else if(!$shipment->exists) {

                        $shipmentExists = $shipment->exists;

                        $kms = 0;
                        try {
                            $kms = $this->getKms($customer, $row);
                        } catch (\Exception $e) {}


                        $shipment->fill($row);
                        $shipment->reference3 = $storeId;
                        $shipment->customer_id = @$customer->id;
                        $shipment->agency_id = $customer->agency_id;
                        $shipment->sender_agency_id = $customer->agency_id;
                        $shipment->recipient_agency_id = $customer->agency_id;
                        $shipment->provider_id = Setting::get('shipment_default_provider');
                        $shipment->service_id = $serviceId;
                        $shipment->status_id = 1;
                        $shipment->recipient_country = 'pt';
                        $shipment->kms = $kms;

                        $shipment->billing_date = date('Y-m-d H:i:s');
                        $shipment->sender_name = 'Leroy Merlin ' . $store;
                        $shipment->sender_address = $customer->address;
                        $shipment->sender_zip_code = $customer->zip_code;
                        $shipment->sender_city = $customer->city;
                        $shipment->sender_country = $customer->country;
                        $shipment->sender_phone = $customer->mobile;
                        $shipment->recipient_attn = $recipientAttn;
                        $shipment->recipient_phone = $recipientPhone;
                        $shipment->setTrackingCode();

                        //add Price
                        $prices = Shipment::calcPrices($shipment);
                        $shipment->cost_price   = $prices['cost'];
                        $shipment->total_price  = $prices['total'];
                        $shipment->zone         = $prices['zone'];
                        $shipment->fuel_tax     = @$prices['fuelTax'];
                        $shipment->extra_weight = @$prices['extraKg'];
                        $shipment->save();


                        if (!$shipmentExists) {
                            $history = new ShipmentHistory();
                            $history->status_id = $shipment->status_id;
                            $history->agency_id = $shipment->agency_id;
                            $history->shipment_id = $shipment->id;
                            $history->api = 1;
                            $history->save();
                        }

                        //Set notification
                        if (!$shipmentExists) {
                            $shipment->setNotification(BroadcastPusher::getGlobalChannel());
                        }
                    }
                }
            }
        }
    }

    /**
     * Comunicate shipments trackings
     */
    public function comunicateStatus() {

        if(env('APP_ENV') == 'local') {
            $customerId = 17925;
        } else {
            if(config('app.source') == 'ancorasemargens') {
                $customerId = 9119;
            } else if(config('app.source') == 'logisimple') {
                $customerId = 44;
            }
        }


        //$now = Date::now();
        //$now->subHour(3);
        //$startDate = $now->format('Y-m-d H:i:s'); //obtem só atualizações desde a ultima sincronização
        $startDate = date('Y-m-d').' 00:00:00';
        $endDate   = date('Y-m-d').' 23:59:59';

/*        $startDate = '2021-08-07 00:00:00';
        $endDate   = '2021-08-07 23:59:59';*/

        $allStatus = Shipment::with('last_history')
            ->whereHas('last_history', function($q) use($startDate, $endDate) {
                $q->whereIn('status_id', [ShippingStatus::DELIVERED_ID, ShippingStatus::INCIDENCE_ID, ShippingStatus::IN_DISTRIBUTION_ID]);
                $q->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->whereIn('customer_id', [$customerId])
            ->orderBy('id', 'desc')
            ->get();

        //dd($allStatus->toArray());
        $deliveredData = [];
        $save = false; 
        foreach ($allStatus as $shipment) {
            $save = false;

            $updatesArr = [];
            if(!@$shipment->last_history->provider_code) { //Só comunica estados que não estejam comunicados

                //STORE DELIVERED
                if (@$shipment->last_history->status_id == ShippingStatus::DELIVERED_ID && !empty($shipment->reference2)) {
                    $save = true;

                    $date = explode(' ', @$shipment->last_history->created_at);
                    $date = @$date[0] ? @$date[0] : date('Y-m-d');

                    //SOTORE
                    $params = [
                        "id"   => intval($shipment->reference2),
                        "type" => substr($shipment->reference, 0, 1) == "P" ? "pickup" : "delivery",
                        "update" => [
                            "date"      => $date,
                            "comments"  => @$shipment->last_history->obs,
                            //"pod"  => @$shipment->last_history->receiver,
                            "status" => [
                                "code" => "completed",
                            ],
                        ]
                    ];

                    if(@$shipment->last_history->filepath){ //comunica ficheiros anexados
                        $image = base64_encode(file_get_contents(public_path(@$shipment->last_history->filepath)));
                        $params["update"]["pod"] = [
                            "type"  => "photo",
                            "image" => $image
                        ];
                    }

                    $updatesArr[] = $params; //para adicionar a foto de entrega

                    if(@$shipment->last_history->signature){ //comunica assinatura
                        $image = @$shipment->last_history->signature;
                        $params["update"]["pod"] = [
                            "type"  => "photo",
                            "image" => $image
                        ];

                        $updatesArr[] = $params;
                    }

                //STORE INCIDENCE
                } elseif ($shipment->last_history->status_id == ShippingStatus::INCIDENCE_ID && !empty($shipment->reference2)) {
                    $save = true;

                    $date = explode(' ', @$shipment->last_history->created_at);
                    $date = @$date[0] ? @$date[0] : date('Y-m-d');

                    $incidenceCode = $this->getIncidenceCode(@$shipment->last_history->incidence->id);

                    $params = [
                        "id" => intval($shipment->reference2),
                        "type" => substr($shipment->reference, 0, 1) == "P" ? "pickup" : "delivery",
                        "update" => [
                            "date"      => $date,
                            "comments"  => @$shipment->last_history->obs,
                            "status"    => [
                                "code" => $incidenceCode
                            ]
                        ]
                    ];

                    if (@$shipment->last_history->filepath) {

                        try {
                            $image = base64_encode(file_get_contents(public_path(@$shipment->last_history->filepath)));

                            $params["update"]["pod"] = [
                                "type" => "photo",
                                "image" => str_replace('data:image/jpeg;base64,', '', $image)
                            ];

                        } catch (\Exception $e) {
                        }
                    }

                    $updatesArr[] = $params;

            //STORE DISTRIBUITION
            } elseif ($shipment->last_history->status_id == ShippingStatus::IN_DISTRIBUTION_ID && !empty($shipment->reference2)) {
                $save = true;

                $date = explode(' ', @$shipment->last_history->created_at);
                $date = @$date[0] ? @$date[0] : date('Y-m-d');

                $params = [
                    "id"   => intval($shipment->reference2),
                    "type" => substr($shipment->reference, 0, 1) == "P" ? "pickup" : "delivery",
                    "update" => [
                        "date"      => $date,
                        "comments"  => @$shipment->last_history->obs,
                        //"pod"  => @$shipment->last_history->receiver,
                        "status" => [
                            "code" => "pending",
                        ],
                    ]
                ];

                $updatesArr[] = $params;
            }
            


                try {

                    if($save) {
                        $url = $this->endpoint . 'services/update';

                       /* if(count($updatesArr) > 1) {
                            dd($updatesArr);
                        }*/
                        foreach ($updatesArr as $params) {
                            $result = $this->callWebservicePOST($url, $params);
                        }

                        if($result) { //comunicação bem sucedida
                            if(@$shipment->last_history->filepath) { //se foi enviada foto, comunica para não voltar a submeter
                                @$shipment->last_history->provider_code = 'sync';
                                @$shipment->last_history->submited_at   = date('Y-m-d H:i:s');
                                @$shipment->last_history->save();
                            }
                        } elseif(empty($result)) { //comunicação falhada

                            $host = request()->getHttpHost();

                            if($host == 'portal.logisimple.pt') {
                                $notifyEmail = 'tiago.lopes@logisimple.pt';
                            } elseif($host == 'ancorasemargens.pt') {
                                $notifyEmail = 'ancorasemargens@gmail.com';
                            }

                            Mail::raw('Pedido Inválido '.$host.' | Detalhe: ' . print_r($params, true), function($message) use($params, $notifyEmail) {
                                $message->to($notifyEmail)
                                    //->cc('paulo.costa@enovo.pt')
                                    ->subject('LEROY MERLIN - Pedido ' . $params['id'] .' não comunicado');
                            });
                        }
                    }
                } catch (\Exception $e) {
                    throw new \Exception($e->getMessage().' File: '. $e->getFile(). ' Line: ' . $e->getLine());
                }
            }
        }
    }


    public function login() {

        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJwcml2YXRlS2V5IjoiZWIxNGQwYzNlNWI0NjIyMWEwMjY2NmZmZTAzYzQxNWU1ZTI4OWI0NzVmMGMzMDA4ODZkMzNkYTFlMDk4YWI2YTViZTdjODhkMzk0YmE2NjkyODQ3YmRkN2UxNzRmNjY5IiwiaWF0IjoxNjE0MzQ0NDM4LCJhdWQiOiJMZXJveV9NZXJsaW4iLCJzdWIiOiI2MDM4ZjA5MDkyMTgzZTU3YjhiNGQ1N2MifQ.4zuHD5PI8clELnZ4F_k3Xy5K_ihjmhlOyEfb4MC05qg';

        if(config('app.source') == 'ancorasemargens') {
            $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJwcml2YXRlS2V5IjoiZWEyZDMxMGU0YzZkOTg5NDI3ZDRkZDY3MjEzNDY2MTVhYTg1YTlhMWMyYTNjOTY2MDk2ZDIyMTg3MzlkOTAyNWY3ZGNmYzQxYTRhYjE1Y2Q3ZGYxMWMzOTU5ZGI1OGYyIiwiaWF0IjoxNjE2Njc3NjM4LCJhdWQiOiJsZXJveV9tZXJsaW4iLCJzdWIiOiI2MDJjZWUyM2JmNGEzNDE4NjVmMTcwZWMifQ.GA4lwzTI1BIm16yiuXSZe8YC9OkJcw7t8dAQn1W86Og';
        } elseif(config('app.source') == 'logisimple') {
            $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJwcml2YXRlS2V5IjoiNzcyODk3M2E5NGIwZDdmZmZiZWYyNzg0MTJhZmIxM2Y2MWE2Mzc1YjE5NjUzNTMwNjZhNDFlMTI5ZjIyZjEzZDIzZWMzZGQxOTI5ZTY1MDQ4Y2NkNjNkNjc0M2FlODdhIiwiaWF0IjoxNjY3NDY1OTYzLCJhdWQiOiJsZXJveV9tZXJsaW4iLCJzdWIiOiI2MDVjYTI4NTk4MTc5ZjE4Y2NhODE4NDYifQ.xUAU-PGMIRMkCnbSC8gEQqo32ZVSP2s9DyNIK7V2wfA';
        }

        return $token;
    }

    /**
     * Call webservice and return results
     * @param $url
     * @param $data
     */
    public function callWebservicePOST($url, $data) {

        $header = array(
            'secret-api-key: ' . $this->login(),
            'Content-Type: application/json'
        );

        $data = json_encode($data);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL             => $url,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => '',
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 0,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => 'POST',
            CURLOPT_POSTFIELDS      => $data,
            CURLOPT_HTTPHEADER      => $header,
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response, true);

        if(@$response['data']) {
            return @$response['data'];
        }

        return @$response['updated'];
    }

    public function mappingShipment($data) {

        $fields = [
            "id"                        => 'reference2',
            "reference"                 => "reference",
            "type"                      => "deliveryType", //Entrega dentro de casa-49521381
            "platform"                  => "warehouse",
            "weight"                    => "weight",
            "date"                      => "date",
            "contactName"               => "recipient_name",
            "street"                    => 'recipient_address',
            "postalCode"                => 'recipient_zip_code',
            "city"                      => 'recipient_city',
            "volume"                    => "volume_m3",
            //"packageCount"              => "volumes",
        ];

        return mapArrayKeys($data, $fields);
    }

    /**
     * Get distance kms
     * @param $customer
     * @param $shipment
     */
    public function getKms($customer, $shipment) {

        $query = [
            'origin'        => $customer->zip_code.' '. $customer->city . ',portugal',
            'destination'   => @$shipment['recipient_zip_code']. ' ' .@$shipment['recipient_city']. ',portugal',

            'origin_zp'      => $customer->zip_code,
            'destination_zp' => @$shipment['recipient_zip_code'],

            'origin_city'    => $customer->city,
            'destination_city' => @$shipment['recipient_city'],

            'origin_country'        => 'portugal',
            'destination_country'   => 'portugal',
        ];

        $query = http_build_query($query);
        $url = config('app.core') . '/helper/maps/distance?' . $query;
        $response = file_get_contents($url);
        $response = json_decode($response, true);

        if($response['result']) {
            return $response['distance_value'];
        }

        return 0;
    }

    public function getIncidenceCode($id) {

        $mapping = [
            '1'  => 'delivery_absentClient',
            '2'  => 'delivery_absentClient',
            '3'  => 'delivery_wrongAddress',
            '4'  => 'deliveryDamage',
            '5'  => 'notReceived',
            '6'  => 'reschedule_client',
            '7'  => 'warehouseOperator_ok',
            '8'  => 'delivery_missingItems',
            '9'  => 'delivery_wrongAddress',
            '10' => 'notComply_deliveryDay',
            '11' => 'reschedule_logisticOperator',
            '12' => 'notReceived',
            '13' => 'delivery_rejected',
            '14' => '',
            '15' => 'notComply_deliveryDay',
            '16' => 'delivery_wrongAddress',
            '17' => 'notReceived',
            '18' => 'notReceived',
            '19' => 'reschedule_client',
            '20' => '',
            '21' => 'notComply_deliveryDay',
            '22' => 'reschedule_difficultAccess',
            '23' => 'notComply_deliveryDay',
            '24' => '',
            '25' => 'notReceived',
            '26' => 'delivery_cancelled',
            '27' => 'notReceived',
            '28' => 'warehouseOperator_ok',
            '29' => 'reschedule_logisticOperator',
            '30' => 'warehouseOperator_damage',
            '31' => 'notComply_deliveryDay',
            '32' => '',
            '37' => 'delivery_wrongAddress',
        ];

        return @$mapping[$id];
    }

    public function getLoja($lojaId) {

        $arr = [
            "4" => "Albufeira",
            "12" => "Loule",
            "48" => "Funchal",
            "50" => "Portimão",
            "66" => "Ponta Delgada",
            "9" => "Coimbra",
            "13" => "Leiria-LM",
            "18" => "Santarém",
            "43" => "Viseu",
            "54" => "Guarda",
            "56" => "F.Foz",
            "57" => "Caldas Rainha",
            "61" => "T.Novas",
            "62" => "Palacio do Gelo - Viseu",
            "63" => "Castelo Branco",
            "5" => "Alfragide",
            "35" => "Telheiras",
            "39" => "Colombo",
            "40" => "Parque das Nações",
            "49" => "Alverca",
            "65" => "Sacavem",
            "2" => "Sintra",
            "7" => "Amadora",
            "31" => "T.Vedras",
            "38" => "Cascais",
            "44" => "Loures",
            "58" => "Mafra",
            "60" => "Oeiras",
            "68" => "Loures Shopping",
            "3" => "Almada ",
            "34" => "Montijo",
            "36" => "Setúbal",
            "46" => "Évora",
            "55" => "Barreiro",
            "1" => "Gondomar ",
            "10" => "Gaia",
            "14" => "Aveiro",
            "52" => "S.M.Feira",
            "59" => "Bragança",
            "67" => "Chaves",
            "6" => "Matosinhos",
            "8" => "Maia",
            "11" => "Braga",
            "33" => "Guimarães",
            "41" => "Porto",
            "47" => "Viana Castelo",
            "51" => "Penafiel",
        ];

        return @$arr[$lojaId];
    }
}
