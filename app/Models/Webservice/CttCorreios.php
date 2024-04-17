<?php

namespace App\Models\Webservice;

use App\Models\Shipment;
use App\Models\ShippingStatus;
use App\Models\ShipmentHistory;
use Symfony\Component\DomCrawler\Crawler;

class CttCorreios extends \App\Models\Webservice\Base {

    /**
     * @var string
     */
    private $url = 'https://www.ctt.pt/feapl_2/app/open/objectSearch/objectSearch.jspx';

    /**
     * @var string
     */
    private $new_url = 'https://www.trackingencomendas.com/api/get-estado.php?id=%s&modo=online';

    /**
     * @var null
     */
    private $debug = false;

    /**
     * Ctt constructor.
     * @param $agencia
     * @param $cliente
     * @param $password
     * @param $sessionId
     */
    public function __construct($agencia = null, $cliente = null, $password = null, $sessionId = null, $department=null, $endpoint=null, $debug = false)
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
    public function getEstadoEnvioByTrk($codAgeCargo, $codAgeOri, $trackingCode)
    {
        require_once base_path() . '/resources/helpers/DOMhtml.php';

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL             => $this->url,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => '',
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 0,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => 'POST',
            CURLOPT_POSTFIELDS      => 'objects='.$trackingCode,
            CURLOPT_HTTPHEADER      => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $html = str_get_html($response);

        $monthsArr = [
            'Janeiro'   => '01',
            'Fevereiro' => '02',
            'Março'     => '03',
            'Abril'     => '04',
            'Maio'      => '05',
            'Junho'     => '06',
            'Julho'     => '07',
            'Agosto'    => '08',
            'Setembro'  => '09',
            'Outubro'   => '10',
            'Novembro'  => '11',
            'Dezembro'  => '12',
        ];

        try {
            $history = [];
            $date = null;
            $changedDate = false;

            $tableRows = array_reverse($html->find('#details_0 tr'));
            foreach($tableRows as $key => $row) {

                $classAttr = $row->getAttribute('class');

                if($classAttr == 'group') {
                    $date = $row->find('td', 0)->plaintext;
                    $date = explode(',', $date);
                    $date = trim(@$date[1]);
                    $dateParts = explode(' ', $date);
                    $date = @$dateParts[2].'-'.@$monthsArr[@$dateParts[1]].'-'.@$dateParts[0];
                    $changedDate = true;
                } else {

                    if($changedDate) {
                        $startIndex = 1;
                    } else {
                        $startIndex = 0;
                    }
                    if(!empty(@$row->find('td', $startIndex)->plaintext)) {

                        $hour   = trim(@$row->find('td', $startIndex)->plaintext);
                        $status = utf8_encode(trim(@$row->find('td', $startIndex+1)->plaintext));
                        $reason = utf8_encode(trim(@$row->find('td', $startIndex+2)->plaintext));
                        $reason = $reason == '-' ? null : $reason;

                        $city = utf8_encode(trim(@$row->find('td', $startIndex+3)->plaintext));
                        $city = ($city == '-' || $city == 'Local n&atilde;o definido') ? null : $city;
                        $city = str_replace('Centro de Entrega ', '', $city);
                        $city = str_replace('Ponto CTT ', '', $city);

                        $receiver = utf8_encode(trim(@$row->find('td', $startIndex+4)->plaintext));
                        $receiver = $receiver == '-' ? null : $receiver;

                        if(!empty($reason)) {
                            $statusId = 9;
                            $obs = $reason.' '.$city;
                        } else {
                            $statusId = $this->getStatusId($status);
                            $obs = $city;
                        }

                        if(!empty($date)) {
                            $history[] = [
                                'created_at'=> $date.' '.$hour.':00',
                                'status_id' => $statusId,
                                'city'      => $city,
                                'obs'       => $obs,
                                'receiver'  => $receiver,
                                'original_s'=> $status,
                                'date'      => $date,
                                'hour'      => $hour,
                                //'obs'       => @$obs
                            ];

                        }
                        $changedDate = false;
                    }
                }
            }

            return $history;

        } catch (\Exception $e) {
            return [];
        }
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
        return $this->getEstadoEnvioByTrk(null,null,$trakingCode);
    }


    /**
     * Permite consultar os estados de um envío a partir do seu código de envio
     *
     * @param string $codAgeCargo Código da agência de Destino
     * @param string $codAgeOri Código da Agência de Origem
     * @param string $trakingCode Código de Encomenda
     * @return array
     */
    public function getEstadoEnvioByTrkNew($codAgeCargo, $codAgeOri, $trackingCode) {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL             => sprintf($this->new_url, $trackingCode),
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => '',
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 0,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => 'GET',
            CURLOPT_SSL_VERIFYHOST  => 0,
            CURLOPT_SSL_VERIFYPEER  => 0
        ]);
        $response = curl_exec($curl);
        curl_close($curl);

        if (str_contains($response, '404')) {
            return [];
        }

        // Remove os carateres estranhos
        $response = utf8_decode($response);
        // Criar o crawler baseado na resposta
        $crawler = new Crawler($response);
        // Processar o crawler e construir um array
        $dirtyHistoryArr = $crawler->filter('table')
            ->filter('tr')
            ->each(function ($tr, $i) {
                return $tr->filter('td')
                    ->each(function ($td, $i) {
                        if ($i == 4 && empty($td->text())) {
                            return null;
                        }

                        return trim($td->text());
                    });
            });
        // Remover linha da tabela sem informação
        unset($dirtyHistoryArr[0]);

        // dd($dirtyHistoryArr);

        $monthsArr = [
            'Jan' => 1,
            'Fev' => 2,
            'Mar' => 3,
            'Abr' => 4,
            'Mai' => 5,
            'Jun' => 6,
            'Jul' => 7,
            'Ago' => 8,
            'Set' => 9,
            'Out' => 10,
            'Nov' => 11,
            'Dez' => 12,
        ];

        $cleanHistoryArr = [];
        foreach ($dirtyHistoryArr as $history) {
            $splittedDate = explode(' ', $history[0]);
            $date = date(sprintf('Y-%d-%d %s', $monthsArr[$splittedDate[1]], $splittedDate[0], $splittedDate[2]));

            if (empty($this->getStatusIdNew($history[2]))) {
                continue;
            }

            $cleanHistoryArr[] = [
                'created_at'=> $date,
                'status_id' => $this->getStatusIdNew($history[2]),
                'city'      => $history[3],
                'obs'       => '',
                'receiver'  => $history[4]
            ];
        }

        $cleanHistoryArr = array_reverse($cleanHistoryArr);

        return $cleanHistoryArr;
    }

    /**
     * Permite consultar os estados de uma recolha a partir do seu código de envio
     *
     * @param string $trakingCode Código de Encomenda
     * @param Shipment $shipment Código de Encomenda
     * @return array
     */
    public function getEstadoRecolhaByTrkNew($trakingCode, $shipment)
    {
        return $this->getEstadoEnvioByTrkNew(null, null, $trakingCode);
    }


    /**
     * Permite consultar os estados dos envios realizados na data indicada
     *
     * @param $date
     * @return mixed
     */
    public function getEstadoEnvioByDate($date) {
        return false;
    }


    /**
     * Devolve o histórico dos estados de um envio dada a sua referência
     *
     * @param $referencia
     * @return array|bool|mixed
     */
    public function getEstadoEnvioByReference($referencia){
        return false;
    }

    /**
     * Devolve as incidências na data indicada
     *
     * @param $date
     * @return mixed
     */
    public function getIncidenciasByDate($date) {
        return false;
    }

    /**
     * Permite consultar as incidências de um envio a partir do seu código de envio
     *
     * @param $date
     * @return mixed
     */
    public function getIncidenciasByTrk($codAgeCargo, $codAgeOri, $trakingCode) {
        return false;
    }


    /**
     * Permite consultar os dados dos envios numa determinada data
     *
     * @param type $date [YYYY-MM-DD]
     * @return type
     */
    public function getEnviosByDate($date) {
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
    public function getRecolhaByTrk($trakingCode)
    {
        return false;
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
        return null;
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
        return '';
    }


    /**
     * Submit a shipment
     *
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public function storeEnvio($data)
    {
        return '';
    }

    /*======================================================
     *
     *              TECHNICAL FUNCTIONS
     *
     ======================================================*/

    /**
     * Atualiza estados de um envio
     *
     * @param collection $shipment
     */
    public function updateHistory($shipment) {

        if($shipment->is_collection) {
            $data = $this->getEstadoRecolhaByTrkNew($shipment->provider_tracking_code, $shipment);
        } else {
            $data = $this->getEstadoEnvioByTrkNew(null, null, $shipment->provider_tracking_code);
        }

        if($data) {
            $weightChanged = true;
            $deliveredTrks = [];
            foreach ($data as $key => $item) {

                $history = ShipmentHistory::firstOrNew([
                    'shipment_id'  => $shipment->id,
                    'obs'          => $item['obs'],
                    'created_at'   => $item['created_at'],
                    'status_id'    => $item['status_id']
                ]);

                $history->fill($item);
                $history->shipment_id = $shipment->id;
                $history->save();

                $history->shipment = $shipment;

                if($history->status_id == ShippingStatus::DELIVERED_ID) {
                    $deliveredTrks[$shipment->provider_tracking_code] = $shipment->provider_tracking_code;
                }

            }

            try {
                if($history) {
                    $history->sendEmail(false, false, true);
                }
            } catch (\Exception $e) {}


            if($history) {
                $shipment->status_id = $history->status_id;
            }

            /**
             * Calcula o preço e custo do envio
             */
            if ((hasModule('account_wallet') && $weightChanged && $shipment->ignore_billing)
                || (!$shipment->price_fixed && !$shipment->is_blocked && !$shipment->invoice_id
                    && $shipment->recipient_country && $shipment->provider_id && $shipment->service_id
                    && $shipment->agency_id && $shipment->customer_id && $weightChanged)) {

                $serviceId = $shipment->service_id;
                if($shipment->is_collection) {
                    $serviceId = @$shipment->service->assigned_service_id;
                }

                $tmpShipment = $shipment;
                $tmpShipment->service_id = $serviceId;
                $prices = Shipment::calcPrices($tmpShipment);

                $oldPrice = $shipment->total_price;
                $shipment->cost_price = @$prices['cost'];

                if (!empty($data['total_price_for_recipient'])) {
                    $shipment->total_price = 0;
                    $shipment->payment_at_recipient = 1;
                    $shipment->total_price_for_recipient = $data['total_price_for_recipient'];
                } else {
                    $shipment->payment_at_recipient = 0;
                    $shipment->total_price_for_recipient = 0;

                    if (!$shipment->price_fixed) {
                        $shipment->total_price  = @$prices['total'];
                        $shipment->fuel_tax     = @$prices['fuelTax'];
                        $shipment->extra_weight = @$prices['extraKg'];
                    }

                    //DISCOUNT FROM WALLET THE DIFERENCE OF PRICE
                    if(hasModule('account_wallet') && $weightChanged && $shipment->ignore_billing && !@$shipment->customer->is_mensal) {
                        $diffPrice = $shipment->total_price - $oldPrice;
                        if($diffPrice > 0.00) {
                            try {
                                \App\Models\GatewayPayment\Base::logShipmentPayment($shipment, $diffPrice);
                                $shipment->customer->subWallet($diffPrice);
                            } catch (\Exception $e) {}
                        }
                    }
                }
            }

            $shipment->save();

            if($history->status_id == ShippingStatus::PICKUP_FAILED_ID) {
                $price = $shipment->addPickupFailedExpense();
                $shipment->walletPayment(null, null, $price); //discount payment
            }

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
    public function saveShipment($shipment, $isCollection = false, $webserviceLogin = null) {
        if($shipment->provider_tracking_code) {
            return $shipment->provider_tracking_code;
        }

        return '#SEM CODIGO';
    }

    /**
     * Apaga um envio
     *
     * @param type $shipment
     * @return boolean
     */
    public function destroyShipment($shipment) {
        return true;
    }

    public function getStatusId($statusText) {

        $statusText = str_replace('&atilde;', 'ã', $statusText);
        $statusText = str_replace('&ccedil;', 'ç', $statusText);
        $statusText = str_replace('&iacute;', 'í', $statusText);
        $statusText = str_replace('', 'à', $statusText);
        $statusText = str_replace('', 'à', $statusText);
        $statusText = str_replace('', 'à', $statusText);


        if(str_contains($statusText, 'O envio saiu para entrega.')) {
            return 4;
        } elseif(
            str_contains($statusText, 'O envio foi aceite.') ||
            str_contains($statusText, 'envio foi recebida')) {
            return 2;

        } elseif(
            str_contains($statusText, 'Saiu do centro operacional') ||
            str_contains($statusText, 'O envio foi encaminhado')) {
            return 3;

        } elseif(str_contains($statusText, 'Ponto de Entrega')) {
            return 43;

        } elseif(
            str_contains($statusText, 'Chegou ao centro operacional') ||
            str_contains($statusText, 'Chegou ao país de destino')) {
            return 33; //entrada em armazem

        } elseif(str_contains($statusText, 'A entrega do envio não foi conseguida')) {
            return 9;
        } elseif(str_contains($statusText, 'desalfandegado')) {
            return 30; //em alfandega
        } elseif(str_contains($statusText, 'O envio foi entregue')) {
            return 5;
        }

        return null;
    }

    public function getStatusIdNew($statusText) {
        if (str_contains($statusText, 'O envio saiu para entrega.')) {
            return 4;
        } elseif (
            str_contains($statusText, 'O envio foi aceite.') ||
            str_contains($statusText, 'envio foi recebida')) {
            return 2;
        } elseif (
            str_contains($statusText, 'Saiu do centro operacional') ||
            str_contains($statusText, 'O envio foi encaminhado')) {
            return 3;
        } elseif (str_contains($statusText, 'Ponto de Entrega')) {
            return 43;
        } elseif (
            str_contains($statusText, 'Chegou ao centro operacional') ||
            str_contains($statusText, 'Chegou ao país de destino')) {
            return 33; //entrada em armazem
        } elseif (str_contains($statusText, 'A entrega do envio não foi conseguida')) {
            return 9;
        } elseif (str_contains($statusText, 'desalfandegado')) {
            return 30; //em alfandega
        } elseif (str_contains($statusText, 'O envio foi entregue')) {
            return 5;
        }

        return null;
    }
}