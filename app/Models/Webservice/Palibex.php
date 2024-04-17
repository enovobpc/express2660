<?php

namespace App\Models\Webservice;

use App\Models\ZipCode\AgencyZipCode;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Shipment;
use App\Models\ShippingStatus;
use App\Models\WebserviceConfig;
use App\Models\ZipCode;
use Carbon\Carbon;
use Date, Response, Setting;
use App\Models\ShipmentHistory;
use Mockery\Exception;
use File, DB;
use Illuminate\Support\Facades\Log;

class Palibex extends \App\Models\Webservice\Base
{

    /**
     * @var string
     */
    private $url = 'https://gtspalibexpre.alertran.net/gts/';
    private $urlTest = 'https://gtspalibexpre.alertran.net/fgts/';
    private $baseUrl = '';

    /**
     * @var null
     */
    private $username = null;

    /**
     * @var null
     */
    private $password = null;

    /**
     * @var null
     */
    private $client = null;

    /**
     * @var null
     */
    private $debug = false;

    /**
     * GLS constructor.
     * @param $agencia
     * @param $client
     * @param $password
     * @param $sessionId
     */
    public function __construct($agency = null, $client = null, $password = null, $sessionId, $department = null, $endpoint = null, $debug = false)
    {
        $this->baseUrl = $this->url;
        if (config('app.env') === 'local') {
            $this->baseUrl = $this->urlTest;
        }

        $this->username = $client;
        $this->password = $password;
        $this->client  = $agency;
        $this->debug   = $debug;
    }

    /**
     * Permite consultar os estados de um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getEstadoEnvioByTrk($codAgeCargo, $codAgeOri, $trakingCode)
    {
        $data = [
            "DETALLES_EXPEDICION" => [
                "VERSION" => "2",
                "DETALLE_EXPEDICION" => [
                    [
                        "CLIENTE" => $this->client,
                        "CENTRO" => "01",
                        "EXPE_NUMERO" => $trakingCode,
                    ]
                ]
            ]
        ];

        $url = $this->baseUrl . "seam/resource/restv1/auth/detalleExpedicioneService/detalles";
        $response = $this->request($url, $data);
        $response = $response[0];

        $this->handleErrorMessage($response['respuestaDetalleExpediciones'] ?? null);

        return $response['respuestaDetalleExpediciones'];
    }

    /**
     * Permite consultar os estados de uma recolha a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getEstadoRecolhaByTrk($trakingCode, $shipment)
    {
        return false;
    }

    /**
     * Permite consultar os estados dos envios realizados na data indicada
     *
     * @param $date
     * @return mixed
     */
    public function getEstadoEnvioByDate($date)
    {
        return false;
    }


    /**
     * Devolve o histórico dos estados de um envio dada a sua referência
     *
     * @param $referencia
     * @return array|bool|mixed
     */
    public function getEstadoEnvioByReference($referencia)
    {
        return self::getEstadoEnvioByTrk(null, null, $referencia);
    }

    /**
     * Devolve as incidências na data indicada
     *
     * @param $date
     * @return mixed
     */
    public function getIncidenciasByDate($date)
    {
        return false;
    }

    /**
     * Permite consultar as incidências de um envio a partir do seu código de envio
     *
     * @param $date
     * @return mixed
     */
    public function getIncidenciasByTrk($codAgeCargo, $codAgeOri, $trakingCode)
    {
    }


    /**
     * Permite consultar os dados dos envios numa determinada data
     *
     * @param type $date [YYYY-MM-DD]
     * @return type
     */
    public function getEnviosByDate($date)
    {
        return false;
    }

    /**
     * Permite consultar um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getEnvioByTrk($codAgeCargo, $codAgeOri, $trakingCode)
    {
        return false;
    }


    /**
     * Permite obter um envio da base de dados  um envio pelo seu trk caso exista envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function storeEnvioByTrk($trakingCode, $originalShipment)
    {
        // $data = $this->getEnvioByTrk(null, null, $trakingCode);
        return false;
    }

    /**
     * Apaga um envio
     *
     * @param type $shipment
     * @return boolean
     */
    public function destroyShipment($shipment)
    {
        return false;
    }

    /**
     * Grava uma resolução a um envio
     *
     * @param type $shipment
     * @return boolean
     */
    public function saveIncidenceResolution($incidenceResolution, $isCollection = false)
    {
        return false;
    }

    /**
     * Permite consultar os estados de um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function storeRecolha($data)
    {
        $url = $this->baseUrl . "seam/resource/restv1/auth/documentarRecogidaService/crearRecogida";
        $response = $this->request($url, $data);

        $this->handleErrorMessage($response['respuestaRecogidas']);

        return $response['respuestaRecogidas']["recogida"];
    }

    /**
     * Send a submit request to stroe a shipment via webservice
     *
     * @method: GrabaServicios
     * @param $data
     */
    public function storeEnvio($data)
    {
        $url = $this->baseUrl . "seam/resource/restv1/auth/documentarEnvio/json";
        $response = $this->request($url, $data);
        $response = $response[0]; // ?? 

        $this->handleErrorMessage($response['respuestaDocuemtarEnvio']);

        return $response['respuestaDocuemtarEnvio']["numero_envio"];
    }

    /**
     * Permite gravar uma incidencia
     * @param $data
     * @return type
     * @throws \Exception
     */
    public function storeIncidenciaResolution($data)
    {
        return false;
    }

    /**
     * Devolve a etiqueta de envio em PDF
     *
     * @param type $senderAgency Agência
     * @param String $trackingCode Código de Envio
     * @return type
     */
    public function getEtiqueta($senderAgency, $trackingCode)
    {
        $data = [
            "ETIQUETAS" => [
                "ETIQUETA" => [
                    [
                        "CLIENTE" => $this->client,
                        "CENTRO" => "01",
                        "EXPEDICION" => $trackingCode,
                        "BULTO" => "",
                        "FORMATO" => "PDF", // ZPL, PDF
                        "POSICION" => "",
                        "ETIQUETAR" => ""
                    ]
                ]
            ]
        ];

        $url = $this->baseUrl . "seam/resource/restv1/auth/etiquetarService/etiquetar";
        $response = $this->request($url, $data);
        $response = $response[0];

        $this->handleErrorMessage($response['respuestaEtiquetar']);

        return $response['respuestaEtiquetar']['etiqueta'];
    }

    /*======================================================
     *
     *              TECHNICAL FUNCTIONS
     *
     ======================================================*/

    /**
     * @param $url
     * @param $data
     * @return mixed
     */
    public function request($url, $data)
    {
        $header = ["Content-Type: application/json"];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);

        $result = curl_exec($ch);
        $errors = curl_error($ch);
        if ($errors) {
            Log::error($errors);
        }

        curl_close($ch);

        return json_decode($result, true);
    }

    /**
     * Handle the error message if resultado is ERROR
     * 
     * @param $response
     * @return null
     * @throw \Exception
     */
    public function handleErrorMessage($response)
    {
        if (empty($response)) {
            // Provavelmente houve erro da parte deles e devolveram vazio no $this->request()
            throw new \Exception("Falha na ligação com a rede. Contacte a Enovo.");
        }

        if ($response['resultado'] === 'ERROR') {
            throw new \Exception($response['mensaje']);
        }
    }

    /**
     * Atualiza estados de um envio
     *
     * @param collection $shipment
     */
    public function updateHistory($shipment)
    {
        $data = $this->getEstadoEnvioByTrk(null, null, $shipment->provider_tracking_code);
        // if data has more than one status, just loop the code below

        $eventList = $this->mapEventList($data['listaEventos'], $data['fechaExpedicion']);
        $date = date('Y-m-d H:i:s', strtotime(strstr($data['fechaExpedicion'], '.', true)));
        $obs =  $eventList['descripcion'] ?? '';
        $status = $this->getHistoryStatus($data['estado']);
        if (is_null($status)) {
            Log::Error("Palibex Missing Status: Estado [ '{$data['estado']}' ] não encontrado para o envio {$shipment->provider_tracking_code}");
            return false;
        }

        $history = ShipmentHistory::firstOrNew([
            'shipment_id'  => $shipment->id,
            'obs'          => $obs,
            'created_at'   => $date,
            'status_id'    => $status,
        ]);
        $newHistory = !$history->exists;
        $history->save();

        if ($newHistory) {
            $shipment->status_id   = $history->status_id;
            $shipment->status_date = $history->created_at;
            $shipment->save();

            $this->handleNewHistory($shipment, $history);
        }

        return true;
    }

    /**
     * Map the status from the provider to the local status
     *
     * @param String $status
     * @return int
     */
    private function getHistoryStatus($statusPalibex)
    {
        $statusMapperArray = config('shipments_import_mapping.palibex-status');
        return $statusMapperArray[strtolower($statusPalibex)] ?? null;
    }

    /**
     * Map the event list from the provider to the Event by date
     *
     * @param Array $eventList
     * @param String $date
     * @return Array
     */
    private function mapEventList($eventList, $date)
    {
        // if the event list is not a Array with arrays inside, just return the event list
        if (!empty($eventList['fecha_evento'])) {
            return $eventList;
        }

        foreach ($eventList as $key => $event) {
            if ($event['fecha_evento'] === $date) {
                return $event;
            }
        }

        // If no event is found, return with no details
        return [
            "fecha_evento" => $date,
            "usuario" => "",
            "delegacion" => "",
            "descripcion" => ""
        ];
    }

    /**
     * Handle the new history
     *
     * @param Shipment $shipment
     * @param ShipmentHistory $history
     * @return void
     */
    private function handleNewHistory($shipment, $history)
    {
        if ($history->status_id == ShippingStatus::PICKUP_FAILED_ID) {
            $shipment->addPickupFailedExpense();
        }

        try {
            $history->sendEmail(false, false, true);
        } catch (\Exception $e) {
        }
    }

    /**
     * Grava ou edita um envio
     *
     * @param Shipment $shipment
     * @param Bool $isCollection
     * @return boolean
     */
    public function saveShipment($shipment, $isCollection = false)
    {
        if ($isCollection || $shipment->is_collection) {
            return $this->storeRecolha($this->getDataToStoreRecolha($shipment));
        } else {
            return $this->storeEnvio($this->getDataToStoreEnvio($shipment));
        }
    }

    /**
     * Formats the data to create a new shipment
     * @param Shipment $shipment
     * 
     * @return array
     */
    private function getDataToStoreEnvio(Shipment $shipment)
    {
        $data = [
            "DOCUMENTAR_ENVIOS" => [
                "DOCUMENTAR_ENVIO" => [
                    [
                        "REFERENCIA" => "TRK" . $shipment->tracking_code,
                        "CODIGO_ADMISION" => "",
                        "NUMERO_BULTOS" => $shipment->volumes,
                        "CLIENTE_REMITENTE" => $this->client,
                        "CENTRO_REMITENTE" => "01",
                        "NIF_REMITENTE" => "",
                        "NOMBRE_REMITENTE" => $shipment->sender_name,
                        "DIRECCION_REMITENTE" => $shipment->sender_address,
                        "PAIS_REMITENTE" => strtoupper($shipment->sender_country),
                        "CODIGO_POSTAL_REMITENTE" => $shipment->sender_zip_code,
                        "POBLACION_REMITENTE" => $shipment->sender_city,
                        "PERSONA_CONTACTO_REMITENTE" => $shipment->sender_attn,
                        "TELEFONO_CONTACTO_REMITENTE" => $shipment->sender_phone,
                        "EMAIL_REMITENTE" => "trk@trk.com",
                        "NIF_DESTINATARIO" => "",
                        "NOMBRE_DESTINATARIO" => $shipment->recipient_name,
                        "DIRECCION_DESTINATARIO" => $shipment->recipient_address,
                        "PAIS_DESTINATARIO" => strtoupper($shipment->recipient_country),
                        "CODIGO_POSTAL_DESTINATARIO" => $shipment->recipient_zip_code,
                        "POBLACION_DESTINATARIO" => $shipment->recipient_city,
                        "PERSONA_CONTACTO_DESTINATARIO" => $shipment->recipient_attn,
                        "TELEFONO_CONTACTO_DESTINATARIO" => $shipment->recipient_phone,
                        "EMAIL_DESTINATARIO" => $shipment->recipient_email,
                        "CODIGO_PRODUCTO_SERVICIO" => $this->getService($shipment->service->code),
                        "KILOS" => $shipment->weight,
                        "VOLUMEN" => $shipment->fator_m3,
                        "CLIENTE_REFERENCIA" => $shipment->reference,
                        "IMPORTE_REEMBOLSO" => $shipment->total_price_for_recipient,
                        "IMPORTE_VALOR_DECLARADO" => "0",
                        "TIPO_PORTES" => "P",
                        "OBSERVACIONES1" => $shipment->obs,
                        "OBSERVACIONES2" => "",
                        "TIPO_MERCANCIA" => "",
                        "VALOR_MERCANCIA" => "1",
                        "MERCANCIA_ESPECIAL" => "N",
                        "GRANDES_SUPERFICIES" => "N",
                        "PLAZO_GARANTIZADO" => "N",
                        "LOCALIZADOR" => "N",
                        "NUM_PALETS" => 0,
                        "FECHA_ENTREGA_APLAZADA" => "",
                        "ENTREGA_APLAZADA" => "N",
                        // "TIPOS_DOCUMENTO" => [
                        //     [
                        //         "TIPO" => "GD",
                        //         "REFERENCIA" => "PRUEBA WS2"
                        //     ]
                        // ],
                        // "MULTIREFERENCIA" => [
                        //     [
                        //         "TIPO" => "ADSE",
                        //         "REFERENCIA" => "PRUEBA WS2C;"
                        //     ]
                        // ],
                        "GESTION_DEVOLUCION_CONFORME" => "N",
                        "ENVIO_CON_RECOGIDA" => "N", // tem recolha
                        "IMPRIMIR_ETIQUETA" => "N",
                        "ENVIO_DEFINITIVO" => "S",
                        "TIPO_FORMATO" => "PDF"
                    ]
                ]
            ]
        ];

        $dimensions = $shipment->pack_dimensions;
        if (empty($dimensions) || $dimensions->isEmpty()) {
            throw new \Exception("Não foi possível obter as dimensões do pacote");
        }

        foreach ($dimensions as $key => $dimension) {
            $data["DOCUMENTAR_ENVIOS"]["DOCUMENTAR_ENVIO"][0]["TIPOS_BULTO"][] = [
                "TIPO" => $this->getPackageType(7), // Pluma
                "CANTIDAD" => $dimension->qty,
                "ALTO" => $dimension->height,
                "ANCHO" => $dimension->width,
                "LARGO" => $dimension->length,
                "VOLUMEN" => $dimension->volume,
                "REFERENCIA" => $dimension->description
            ];
        }

        return $data;
    }

    /**
     * Get the Palibex service code
     * 
     * @param string $service
     * @return string
     */
    private function getService($service)
    {
        $services = config('shipments_export_mapping.palibex-services');

        if (empty($services[$service]) || empty($service)) {
            throw new \Exception("Nenhum serviço correspondente a {$service}.");
        }

        return $services[$service];
    }

    /**
     * Get the Palibex Tipo de Bulto
     * 
     * @param string $service
     * @return string
     */
    private function getPackageType($packageTypeId)
    {
        $packageTypes = [
            1 => 'MINI',
            2 => 'CUARTO',
            3 => 'MEDIO',
            4 => 'COMPLETO',
            5 => 'SUPER',
            6 => 'LIGERO',
            7 => 'PLUMA',
            8 => 'MINIMINI',
        ];

        return $packageTypes[$packageTypeId];
    }

    /**
     * Formats the data to create a new shipment
     * @param Shipment $shipment
     * 
     * @return array
     */
    private function getDataToStoreRecolha($shipment)
    {

        return [
            "RECOGIDA" => [
                "CLIENTE" => $this->client,
                "CENTRO" => "01",
                "NOMBRE" => $shipment->sender_name,
                "DIRECCION" => $shipment->sender_address,
                "POBLACION" => $shipment->sender_city,
                "CODIGO_POSTAL" => $shipment->sender_zip_code,
                "PAIS" => strtoupper($shipment->sender_country),
                "CONTACTO" => $shipment->sender_attn,
                "REFERENCIA" => $shipment->reference,
                "TELEFONO" => $shipment->sender_phone,
                "EMAIL" => "trk@trk.com",
                "OBSERVACIONES" => $shipment->obs ?? "",
                "FECHA" => $shipment->date, //Carbon::parse($shipment->date)->format('d/m/Y') ?? date('d-m-Y'),
                "HORA_MA_DESDE" => $shipment->start_hour ?? '',
                "HORA_MA_HASTA" => $shipment->start_hour ?? '',
                "HORA_TARDE_DESDE" => $shipment->end_hour ?? '',
                "HORA_TARDE_HASTA" => $shipment->end_hour ?? '',
                // "DETALLES_RECOGIDA" => [
                //     [
                //         "NOMBRE" => $shipment->recipient_name,
                //         "CONTACTO" => $shipment->recipient_attn,
                //         "DIRECCION" => $shipment->recipient_address,
                //         "POBLACION" => $shipment->recipient_city,
                //         "TELEFONO" => $shipment->recipient_phone ?? 'N/A',
                //         "NIF" => "",
                //         "EMAIL" => "",
                //         "OBSERVACIONES" => $shipment->obs,
                //         "CODIGO_POSTAL" => $shipment->recipient_zip_code,
                //         "PAIS" => strtoupper($shipment->recipient_country),
                //         "NUMERO_BULTOS" => $shipment->volumes,
                //         "PESO" => $shipment->weight,
                //         "VOLUMEN" => "",
                //         "IMPORTE_REEMBOLSO" => $shipment->total_price_for_recipient,
                //         "VALOR_DECLARADO" => "",
                //         "TIPO_PORTES" => "",
                //         "COBRADO" => "",
                //         "CENTRO_DESTINO" => "",
                //         "CLIENTE_DESTINO" => "",
                //         "COMUNICA_SMS" => "",
                //         "COMUNICA_MAIL" => "",
                //         "ENTREGA_SABADO" => "",
                //         "RETORNO" => "",
                //         "PRODUCTO" => "",
                //         "FECHA_ENTREGA" => ""
                //     ]
                // ]
            ]
        ];
    }

    /**
     * Get Pickup Points
     * @param null $webservice
     * @param array $ids
     * @return array
     */
    public function getPontosRecolha($paramsArr = [])
    {
    }
}
