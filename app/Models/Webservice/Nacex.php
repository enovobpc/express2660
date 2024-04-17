<?php

namespace App\Models\Webservice;

use App\Models\Shipment;
use App\Models\ShippingExpense;
use App\Models\ShippingStatus;
use App\Models\WebserviceConfig;
use Date, File, Imagick, View, Setting;
use Mpdf\Mpdf;
use App\Models\ShipmentHistory;

class Nacex extends \App\Models\Webservice\Base
{

    /**
     * @var string
     */
    private $url     = 'http://gprs.nacex.com/nacex_ws/ws';
    private $urlTest = 'http://193.16.153.113/nacex_ws/ws';

    /**
     * @var string
     */
    private $dev = false;

    /**
     * @var null
     */
    private $upload_directory = '/uploads/labels/nacex/';

    /**
     * @var string
     */
    private $agencia;

    /**
     * @var string
     */
    private $cliente;

    /**
     * @var string
     */
    private $password;

    /**
     * @var null
     */
    private $session_id = null;

    /**
     * Nacex constructor.
     * @param $agencia
     * @param $cliente
     * @param $password
     */
    public function __construct($agencia, $cliente, $password, $sessionId)
    {
        $this->agencia  = $agencia;
        $this->user     = $cliente;
        $this->pass     = strtoupper(md5($password)); // Don't ask
        $this->abonado  = $sessionId;
    }

    /**
     * Nacex Destructor
     */
    public function __destruct()
    {
        unset($this->user);
        unset($this->pass);
        unset($this->idagencia);
        unset($this->idcliente);
        unset($this->dev);
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

        $aux = explode("/", $trakingCode);

        if (count($aux) > 1) {
            $codAgeCargo = $aux[0];
            $trakingCode = $aux[1];
        }

        //devolve ultimo estado
        $data = [
            'origen'  => $codAgeCargo,
            'albaran' => $trakingCode,
            'ref_unica' => 'N'
        ];

        $results = $this->call('getEstadoExpedicion', $data);
        $result = $this->mappingData($results, 'status', false);
        $result = [$result];

        /*$data = [
            'ag'     => $codAgeCargo,
            'numalb' => $trakingCode,
        ];

        $results = $this->call('getHistoricoExpedicion', $data);

        $result = [];
        foreach ($results as $item) {
            $result[] = $this->mappingData($item, 'status-history');
        }*/

        return $result;
    }

    /**
     * Permite consultar os estados de uma recolha a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getEstadoRecolhaByTrk($trakingCode, $ageSolicita)
    {
        $aux = explode("/", $trakingCode);

        if(count($aux) > 1) {
            $ageSolicita = $aux[0];
            $trakingCode = $aux[1];
        }
        
        $data = [
            'del_sol' => $ageSolicita,
            'num_rec' => $trakingCode,
            'ref_unica' => 'S'
        ];

        $results = $this->call('getEstadoRecogida', $data);
        $result = $this->mappingData($results, 'collection', false);
        $result = [$result];
        return $result;
    }

    /**
     * Permite consultar os dados dos envios numa determinada data
     *
     * @param type $date [YYYY-MM-DD]
     * @return type
     */
    public function getEnviosByDate($date)
    {

        $date = new \Jenssegers\Date\Date($date);
        $date = $date->format('d/m/Y');
        $fields = config('webservices_mapping.nacex.shipment');
        unset($fields['agencia_origen'], $fields['albaran'], $fields['digitalizar']);

        $data = [
            'fecha_ini' => $date,
            'fecha_fin' => $date,
            'campos'    => implode(';', array_keys($fields))
        ];

        $results = $this->call('getListadoExpediciones', $data);

        $keys = array_values(config('webservices_mapping.nacex.shipment'));

        if (!empty(@$results[0])) {
            $result = [];
            foreach ($results as $item) {
                $values = explode('~', $item);
                $data = [];
                foreach ($values as $pos => $value) {
                    $data[@$keys[$pos]] = $value;
                }

                $result[] = $data;
            }
            return $result;
        }

        return null;
    }

    /**
     * Return cities by zip_code
     *
     * @param $cp
     * @return mixed
     */
    public function getPueblos($zipCode)
    {
        $data = [
            'cp'     => $zipCode,
            'inc_cp' => 'S' //incluir cod postal (S/N)
        ];
        return $this->call('getPueblos', $data);
    }

    /**
     * Return agency by zip code
     *
     * @param $cp
     * @return mixed
     */
    public function getAgencia($zipCode, $city = null)
    {
        $data = ['cp' => $zipCode];

        if (!empty($city)) {
            $data['pob'] = $city;
        }

        return $this->call('getAgencia', $data);
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
        $aux = explode("/", $trakingCode);
        $data = array('del' => $aux[0], 'num' => $aux[1], 'tipo' => $tipo);
        return $this->call('getInfoEnvio', $data);
    }

    /**
     * Permite consultar um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getRecolhaByTrk($trakingCode)
    {
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
        $d = [
            'delcli'    => $this->agencia,
            'numcli'    => $this->abonado,
            'fecha'     => $data['date'],
            'servicio'  => $data['service'],
            'cobro'     => $data['tipo_cobro'],
            'envase'     => '', //envase
            'referencia'   => $data['reference'],
            'tip_env'   => $data['tipo_envio'],
            'bultos'    => $data['volumes'],
            'peso'      => $data['weight'],

            'nomrec'    => $data['sender_name'],
            'dirrec'    => $data['sender_address'],
            'cprec'     => @$data['sender_zip_code'],
            'pobrec'    => $data['sender_city'],
            'telrec'    => $data['sender_phone'],
            'paisrec'   => @$data['sender_country'],

            'noment'    => $data['recipient_name'],
            'dirent'    => $data['recipient_address'],
            'paisent'   => $data['recipient_country'],
            'cpent'     => $data['recipient_zip_code'],
            'pobent'    => $data['recipient_city'],
            'telent'    => $data['recipient_phone'],
            'paisent'   => $data['recipient_country'],

            'hora_ini1' => '', //hora inicio manha
            'hora_fin1' => '', //hora fim manha

            'obs1'      => $data['obs'], //obs recolha
            'obs1_e'    => $data['obs_delivery'], //obs entrega

            'sc_ree_tipo' => '', //(Adel – (D – Destino / T – Tercera) / Reem - (O – Origen / D – Destino / T – Tercera)) N-No
            'sc_ree_importe' => $data['charge_price'],
            'sc_ret'    => '', //retorno
            'vehiculo'  => 'V', //vehicle
            'solicitante'       => '',
            'email_solicitante' => 'trk@trk.com'
        ];

        $result = $this->call('putRecogida', $d);

        $trk = $result[0];

        return $trk;
    }

    /**
     * Insere um envio
     *
     * @param type $data
     * @return type
     */
    public function storeEnvio($data)
    {
        $d = [];

        $edit = false;
        if (isset($data['albaran']) && !empty($data['albaran'])) {
            $edit = true;
            $d['origen']  = $data['origen'];
            $d['albaran'] = $data['albaran'];
        }

        $d += [
            'del_cli' => $this->agencia,
            'num_cli' => $this->abonado,
            'fec'     => $data['date'],
            'tip_ser' => $data['service'],
            'tip_cob' => $data['tipo_cobro'],
            //'exc'     => '' //numero excessos
            'ref_cli' => $data['reference'],
            'tip_env' => $data['tipo_envio'],
            'bul'     => $data['volumes'],
            'kil'     => $data['weight'],

            'nom_rec' => substr($data['sender_name'], 0, 35),
            'dir_rec' => substr($data['sender_address'], 0, 60),
            'cp_rec'  => @$data['sender_zip_code'],
            'pob_rec' => $data['sender_city'],
            'tel_rec' => $data['sender_phone'],

            'nom_ent' => $data['recipient_name'],
            'per_ent' => $data['recipient_attn'],
            'dir_ent' => $data['recipient_address'],
            'pais_ent' => $data['recipient_country'],
            'cp_ent'  => $data['recipient_zip_code'],
            'pob_ent' => $data['recipient_city'],
            'tel_ent' => $data['recipient_phone'],

            'ree'     => $data['charge_price'],
            'tip_ree' => 'O',
            'ret'     => $data['return'],
            'dig'     => $data['return_guide'],
            
            'obs1'      => $data['obs'], //obs recolha
        ];

        /*if($data['weight'] > 2 AND $data['plusbag']) $data['plusbag'] = FALSE; // Ask Lorenzo

        if($data['plusbag']) {
            $d['tip_ser'] = "04";
            $d['tip_env'] = "1";
        } else {
            $d['tip_ser'] = "26";
            $d['tip_env'] = "2";
        }*/

        if ($edit) {
            $result = $this->call('editExpedicion', $d);
            $trk = $d['origen'] . '/' . $d['albaran'];
        } else {
            $result = $this->call('putExpedicion', $d);
            $trk = $result[1];
        }

        $trkCode = substr($trk, 5);
        $zpl = $this->getEtiquetaFromWebservice($trk);
        $labels = $this->convertZPL2PDF($zpl, $trkCode, $data['volumes']);

        File::put(public_path() . $this->upload_directory . $trkCode . '_labels.txt', $labels);

        return $trk;
    }

    /**
     * Devolve a etiqueta de envio em PDF
     *
     * @param type $senderAgency Agência
     * @param type $trackingCode Código de Envio
     * @param type $numEtiquetas Nº de etiquetas impressas na folha A4
     * @return type
     */
    public function getEtiqueta($senderAgency, $trackingCode)
    {

        $aux = explode("/", $trackingCode);

        if (count($aux) > 1) {
            $trackingCode = $aux[1];
        }

        $file = File::get(public_path() . '/uploads/labels/nacex/' . $trackingCode . '_labels.txt');
        return $file;
    }

    /**
     * Devolve a etiqueta de envio em PDF
     *
     * @param type $senderAgency Agência
     * @param type $trackingCode Código de Envio
     * @param type $numEtiquetas Nº de etiquetas impressas na folha A4
     * @return type
     */
    public function getEtiquetaFromWebservice($trackingCode)
    {

        $aux = explode("/", $trackingCode);

        if (count($aux) > 1) {
            $senderAgency = $aux[0];
            $trackingCode = $aux[1];
        }

        $data = [
            'ag'     => $senderAgency,
            'numero' => $trackingCode,
            'modelo' => 'ZEBRA_B', //TECSV4, TEC452, TEC472, ZEBRA, ZEBRA_B o IMAGEN
        ];

        $result = $this->call('getEtiqueta', $data);
        $zpl = $result[0];

        return $zpl;
    }

    /**
     * Devolve o URL do comprovativo de entrega
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function ConsEnvPODDig($codAgeCargo, $codAgeOri, $trakingCode)
    {
    }


    /**
     * Devolve as informações completas dos envios e respetivo POD de entrega dos envios numa data
     *
     * @param type $date
     * @param type $tracking Se indicado, devolve a informação apenas para o envio com o tkr indicado
     * @return type
     */
    public function InfEnvEstPOD($date, $tracking = null)
    {
        throw new \Exception('Método inexistente.');
    }

    /*======================================================
     *
     *              TECHNICAL FUNCTIONS
     *
     ======================================================*/


    /**
     * @param $service
     * @param $method
     * @param $parameters
     * @return mixed
     */
    private function call($method, $data)
    {

        if ($this->dev) {
            $url = $this->urlTest; //DEV
        } else {
            $url = $this->url; //PROD
        }

        $aux = array();
        if ($data) {
            foreach ($data as $clave => $valor) {
                $aux[] = $clave . "=" . urlencode($valor);
            }
        }

        $data_string = implode("|", $aux);
        $full_url = $url . "?method=" . $method . "&user=" . $this->user . "&pass=" . $this->pass . "&data=" . $data_string;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $full_url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cache-Control: no-cache"));
        $result = utf8_encode(curl_exec($ch));

        curl_close($ch);
        $ret = explode("|", $result);

        if ($ret[0] == "ERROR") {
            throw new \Exception($ret[1], '400');
        }
        return $ret;
    }


    /**
     * Map array of results
     *
     * @param type $data Array of data
     * @param type $mappingArray
     * @return type
     */
    /*    private function mappingResult($data, $mappingArray) {
            $arr = [];

            foreach($data as $row) {
                if(isset($row['@attributes'])) {
                    $row = $row['@attributes'];
                }
                $arr[] = mapArrayKeys($row, config('webservices_mapping.nacex.'.$mappingArray));
            }

            return $arr;
        }*/

    /**
     * Atualiza estados de um envio
     *
     * @param collection $shipment
     */
    public function updateHistory($shipment)
    {

        if ($shipment->is_collection) {
            $data = self::getEstadoRecolhaByTrk($shipment->provider_tracking_code, $shipment->provider_cargo_agency);
            $nacexStatus = config('shipments_import_mapping.nacex-status-collection');
        } else {
            $data = self::getEstadoEnvioByTrk(
                $shipment->provider_cargo_agency,
                $shipment->provider_sender_agency,
                $shipment->provider_tracking_code
            );

            $nacexStatus = config('shipments_import_mapping.nacex-status');
        }

        if ($data) {
            $numeroIncidencia = 0;

            $oldStatus = null;
            $oldDate = null;
            
            foreach ($data as $item) {

                if(!isset($nacexStatus[$item['status_code']])) {
                    throw new \Exception('Estado "'.$item['status'].'" [Codigo '.$item['status_code'].'] sem correspondencia com os estados de sistema.');
                }

                $item['status_id'] = @$nacexStatus[$item['status_code']];
                /* $item['created_at'] = $item['date'] . ' ' . $item['hour'];
                $item['created_at'] = Date::createFromFormat('m/d/Y H:i:s', $item['created_at'])->toDateTimeString(); */
                $item['date'] = explode('/', $item['date']);
                $item['date'] = @$item['date'][2].'-'.@$item['date'][1].'-'.@$item['date'][0];
                $item['created_at'] = $item['date'] . ' ' . $item['hour'];

                if ($item['status_id'] == '9') {

                    $envialiaIncidences = config('shipments_import_mapping.envialia-incidences');
                    $incidenceData = self::getIncidenciasByTrk(
                        $shipment->provider_cargo_agency,
                        $shipment->provider_sender_agency,
                        $shipment->provider_tracking_code
                    );

                    $incidenceCode = @$incidenceData[$numeroIncidencia]['incidence'];

                    $incidenceId = @$envialiaIncidences[$incidenceCode];
                    $item['obs'] = @$incidenceData[$numeroIncidencia]['obs'];
                    $item['incidence_id'] = $incidenceId;
                    $numeroIncidencia++;
                }

                /*if($item['status_id'] == '5') { //entregue
                    $pod = self::InfEnvEstPOD($shipment->date, $shipment->provider_tracking_code);
                    $pod = @$pod['POD'];

                    $item['receiver'] = @$pod['pod_name'];
                    $item['obs']      = @$pod['pod_obs'];
                }*/

                $history = ShipmentHistory::firstOrNew([
                    'shipment_id' => $shipment->id,
                    'created_at'  => $item['created_at'],
                    'status_id'   => $item['status_id']
                ]);

                $history->fill($item);
                $history->shipment_id = $shipment->id;
                $history->save();

                $history->shipment = $shipment;

                if ($history->status_id == ShippingStatus::PICKUP_FAILED_ID) {
                    $price = $shipment->addPickupFailedExpense();
                    $shipment->walletPayment(null, null, $price); //discount payment
                }
            }

            try {
                $history->sendEmail(false, false, true);
            } catch (\Exception $e) {
            }

            $shipment->status_id   = $history->status_id;
            $shipment->status_date = $history->created_at->format('Y-m-d H:i:s');;
            $shipment->save();
            return $history->status_id ? $history->status_id : true;
        }
        return false;
    }

    /**
     * Grava ou edita um envio
     *
     * @param type $shipment
     * @return boolean
     */
    public function saveShipment($shipment, $isCollection = false)
    {

        $service = $this->getProviderService($shipment);

        $reference =  $shipment->reference ? '-;' . $shipment->reference : '';

        //format zip code. PT zip code cant have spaces (ex:3505-175 ==> 3505175)
        $senderZipCode    = $this->getZipCode($shipment->sender_zip_code, $shipment->sender_country);
        $recipientZipCode = $this->getZipCode($shipment->recipient_zip_code, $shipment->recipient_country);

        $shipment->has_return = empty($shipment->has_return) ? array() : $shipment->has_return;

        //return pack
        $returnPack = 'N';
        if ($shipment->has_return && in_array('rpack', $shipment->has_return)) {
            $returnPack = 'S';
        }

        //return guide
        $returnGuide = 'N'; //nao digitalizar guia
        if ($shipment->has_return && in_array('rguide', $shipment->has_return)) {
            $returnGuide = 'R'; //digitalizar com retorno ao cliente
        }

        //complementar services
        /*$systemComplementarServices  = ShippingExpense::filterSource()->pluck('id', 'type')->toArray();
        $shipmentComplementarServices = $shipment->complementar_services;

        $sabado = $returnGuide = 0;
        if(!empty($shipmentComplementarServices)) {
            //check service sabado
            if(in_array('sabado', array_keys($systemComplementarServices)) &&
                in_array(@$systemComplementarServices['sabado'], $shipmentComplementarServices)) {
                $sabado = 1;
            }

            //return guide
            if(in_array('rguide', array_keys($systemComplementarServices)) &&
                in_array(@$systemComplementarServices['rguide'], $shipmentComplementarServices)) {
                $returnGuide = 1;
            }
        }*/

        $date = new Date($shipment->date);

        if ($shipment->provider_sender_agency == '7278') {
            $senderZipCode = '2790114';
        }


        $type = in_array($shipment->recipient_country, ['pt', 'es', 'ad']) ? '2' : 'M'; //Tipo de envío (España, Portugal, Andorra: 0 = Docs / 1 = Bag / 2 = Paq - Internacional: D = Documentos / M = Muestras)
        if (str_contains($service, '#')) {
            $auxService = explode('#', $service);
            $service = $auxService[0];
            $type    = $auxService[1];
        }

        $data = [
            'date'           => $date->format('d/m/Y'),
            'age_cargo'      => $shipment->provider_sender_agency,
            'service'        => $service,
            'customer_code'  => @$shipment->customer->code,
            'sender_name'    => utf8_decode(str_replace('&', 'e', trim($shipment->sender_name))),
            'sender_address' => utf8_decode(str_replace('&', 'e', trim($shipment->sender_address))),
            'sender_city'    => utf8_decode(str_replace('&', 'e', trim($shipment->sender_city))),
            'sender_zip_code' => $senderZipCode,
            'sender_phone'   => $shipment->sender_phone,
            'sender_country' => strtoupper($shipment->sender_country),
            'recipient_attn' => utf8_decode(str_replace('&', 'e', trim($shipment->recipient_attn))),
            'recipient_name' => utf8_decode(str_replace('&', 'e', trim($shipment->recipient_name))),
            'recipient_address'   => utf8_decode(str_replace('&', 'e', trim($shipment->recipient_address))),
            'recipient_city'      => utf8_decode(str_replace('&', 'e', trim($shipment->recipient_city))),
            'recipient_zip_code'  => $recipientZipCode,
            'recipient_country'   => strtoupper($shipment->recipient_country),
            'recipient_phone'     => $shipment->recipient_phone,
            'volumes'        => str_pad($shipment->volumes, 3, '0', STR_PAD_LEFT),
            'weight'         => $shipment->weight,
            'charge_price'   => $shipment->charge_price ? $shipment->charge_price : 0,
            'return'         => $returnPack, //com retorno?
            'return_guide'   => $returnGuide,
            'obs'            => utf8_decode(str_replace('&', 'e', trim($shipment->obs))),
            'obs_delivery'   => utf8_decode(str_replace('&', 'e', trim($shipment->obs_delivery))),
            'reference'      => 'TRK' . $shipment->tracking_code . $reference,
            'tipo_cobro'     => $shipment->payment_at_recipient ? 'D' : 'O', //(O – Origen / D – Destino / T – Tercera)
            'tipo_envio'     => $type
        ];

        if (!empty($shipment->provider_tracking_code)) {
            $data['origen']  = substr($shipment->provider_tracking_code, 0, 4);
            $data['albaran'] = substr($shipment->provider_tracking_code, 5);
        }

        //force sender data to hide on labels
        if ((Setting::get('hidden_recipient_on_labels') || Setting::get('hidden_recipient_addr_on_labels')) && !($isCollection || $shipment->is_collection)) {

            if (Setting::get('hidden_recipient_on_labels')) {
                $data['sender_name'] = $shipment->agency->company;
            }

            $data['sender_address']  = $shipment->agency->address;
            //$data['sender_zip_code'] = $shipment->agency->zip_code;
            $data['sender_zip_code'] = $senderZipCode;
            $data['sender_city']     = $shipment->agency->city;
            $data['sender_country']  = $shipment->agency->country;
            $data['sender_phone']    = $shipment->agency->phone;
        }

        //dd($data);
        if ($isCollection) {
            return $this->storeRecolha($data);
        } else {
            return $this->storeEnvio($data);
        }
    }

    /**
     * Apaga um envio
     *
     * @param type $shipment
     * @return boolean
     */
    public function destroyShipment($shipment)
    {

        if ($shipment->is_collection) {
            $data = [
                'reco_codigo' => $shipment->provider_tracking_code
            ];
            return $this->call('cancelRecogida', $data);
        } else {
            $parts = explode('/', $shipment->provider_tracking_code);
            if (count($parts) > 1) {
                $data = [
                    'origen'  => $parts[0],
                    'albaran' => $parts[1],
                ];
            } else {
                $data = ['expe_codigo' => $shipment->provider_tracking_code];
            }
            return $this->call('cancelExpedicion', $data);
        }
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

    /**
     * Map data from results
     *
     * @param $data
     */
    public function mappingData($data, $mapArray, $needExplode = true)
    {

        $keys = array_values(config('webservices_mapping.nacex.' . $mapArray));

        if ($needExplode) {
            $values = explode('~', $data);
        } else {
            $values = $data;
        }

        $data = [];
        foreach ($values as $pos => $value) {
            $data[@$keys[$pos]] = $value;
        }

        $result = $data;

        return $result;
    }

    /**
     * Prepara o código postal
     *
     * @param type $shipment
     * @return boolean
     */
    public function getZipCode($zipCode, $country)
    {
        $zipCode = explode('-', $zipCode);
        $zipCode1 = $zipCode[0];
        $zipCode2 = !empty($zipCode[1]) ? $zipCode[1] : ((in_array($country, ['pt', 'md', 'ac']) && strlen($zipCode1) == 4) ? '000' : '');

        $zipCode = $zipCode1 . $zipCode2;

        //$zipCode = str_replace('-', '', $zipCode);

        /*if {
            //$zipCode = substr($zipCode, 0, -1) . '0';
            $zipCode = $zipCode.'000';
        }*/

        return $zipCode;
    }

    /**
     * Convert a ZPL file to PDF
     */
    public function convertZPL2PDF($zpl, $trk, $volumes)
    {

        $listFiles = [];
        $curl = curl_init();

        for ($i = 0; $i < $volumes; $i++) {

            curl_setopt($curl, CURLOPT_URL, "http://api.labelary.com/v1/printers/8dpmm/labels/4x6/" . $i . "/");
            curl_setopt($curl, CURLOPT_POST, TRUE);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $zpl);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("Accept: application/pdf"));
            $result = curl_exec($curl);

            $fileData = $result;

            $folder = public_path() . '/uploads/labels/nacex/';
            if (!File::exists($folder)) {
                File::makeDirectory($folder);
            }

            $filepath = $folder . $trk . '_label_' . $i . '.pdf';
            File::put($filepath, $fileData);

            $listFiles[] = $filepath;
        }

        curl_close($curl);


        /**
         * Merge files
         */
        $pdf = new \LynX39\LaraPdfMerger\PdfManage;
        foreach ($listFiles as $filepath) {
            $pdf->addPDF($filepath, 'all');
        }

        /**
         * Save merged file
         */
        $filepath = '/uploads/labels/nacex/' . $trk . '_labels.pdf';
        $outputFilepath = public_path() . $filepath;
        $result = base64_encode($pdf->merge('string', $outputFilepath, 'P'));

        if ($result) {
            foreach ($listFiles as $item) {
                File::delete($item);
            }
        }

        return $result;
    }


    /**
     * Get provider service
     *
     * @param $shipment
     */
    public function getProviderService($shipment)
    {

        $providerService = null;

        $source = config('app.source');

        $webserviceConfigs = WebserviceConfig::remember(config('cache.query_ttl'))
            ->cacheTags(WebserviceConfig::CACHE_TAG)
            ->where('source', $source)
            ->where('method', $shipment->webservice_method)
            ->where('provider_id', @$shipment->provider_id)
            ->first();

        try {

            $serviceKey = $shipment->recipient_country;
            if ($serviceKey != 'pt' && $serviceKey != 'es') {
                $serviceKey = 'int';
            }

            $providerService = @$webserviceConfigs->mapping_services[$shipment->service_id][$serviceKey];

            //se não encontrou codigo de serviço, tenta obter os dados default
            //a partir do ficheiro estático de sistema
            if (!$providerService) {
                $mapping = config('shipments_export_mapping.nacex-services');
                $providerService = $mapping[$shipment->service->code];
            }
        } catch (\Exception $e) {
        }

        if (!$providerService) {
            throw new \Exception('O serviço ' . $shipment->service->code . ' não tem correspondência com nenhum serviço Nacex.');
        }

        return $providerService;
    }
}
