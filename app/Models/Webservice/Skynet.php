<?php

namespace App\Models\Webservice;

use App\Models\Shipment;
use App\Models\ShippingStatus;
use Carbon\Carbon;
use App\Models\ShipmentHistory;
use Illuminate\Support\Facades\Log;
use Date, Response, File, Setting, DB, Mpdf\Mpdf, FTP\Connection;

class Skynet extends \App\Models\Webservice\Base
{

    /**
     * @var null|String
     */
    private $customerCode;

    /**
     * @var null|bool
     */
    private $debug = false;

    /**
     * @var null|String
     */
    private $ftpHost = 'ftp.torrestir.pt';

    /**
     * @var null|String
     */
    private $ftpUser = null;

    /**
     * @var null|String
     */
    private $ftpPass = null;

    /**
     * @var null|int
     */
    private $ftpPort = 21;

    /**
     * @var null|String
     */
    private $agencia = null;

    /**
     * Correos Express constructor.
     * @param $agencia
     * @param $cliente
     * @param $password
     * @param $sessionId
     */
    public function __construct($agencia = null, $user = null, $password = null, $sessionId = null, $department = null, $endpoint = null, $debug = false)
    {
        $this->customerCode = $this->agencia = $agencia;
        $this->ftpUser      = $user;
        $this->ftpPass      = $password;

        $this->debug = $debug;
    }

    /**
     * Permite consultar os estados de um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    // public function getEstadoEnvioByTrk($shipment)
    // {
    //     require_once base_path() . '/resources/helpers/DOMhtml.php';

    //     $trk = $shipment->provider_tracking_code;
    //     $recipientZipCode = substr($shipment->recipient_zip_code, 0, 3);

    //     $url = 'https://www.integra2.es/bin/integra2/tracking?ref=' . $trk . '&cp=' . $recipientZipCode . '&language=ptPT';
    //     //$url = 'https://www.integra2.es/bin/integra2/tracking?ref=134495981&cp=115&language=ptPT';

    //     $content = json_decode(file_get_contents($url));

    //     if (@$content->error) {
    //         return false;
    //     }

    //     $proof      = @$content->docs->docsItem->urlDoc;
    //     $weight     = @$content->datosPrincipales->kilos;
    //     $reference  = @$content->datosPrincipales->refPral;
    //     $expedition = @$content->datosPrincipales->expedicion;


    //     $lastStatus = [
    //         'status' => @$content->tracking->trackingItem->descTracking,
    //         'date'   => @$content->tracking->trackingItem->fecha,
    //         'pobDest' => @$content->datosDestino->pobDest
    //     ];

    //     $html = file_get_html($url);

    //     $tableTr = @$html->find('.celdaTab tr td');

    //     $history = [];
    //     foreach ($tableTr as $key => $tr) {
    //         $parts = $tr->plaintext;
    //         $parts = nl2br($parts);
    //         $parts = explode('<br />', $parts);

    //         try {

    //             if (!empty(@$parts[0])) {
    //                 $status     = trim(str_replace('&nbsp;', ' ', @$parts[0]));
    //                 $location   = trim(str_replace('&nbsp;', ' ', @$parts[1]));
    //                 $location   = explode(' ', $location);
    //                 $location   = @$location[1];


    //                 $date = trim(str_replace('&nbsp;', ' ', @$parts[2]));
    //                 $date = Carbon::createFromFormat('d/m/Y H:i', $date);
    //                 $date = $date->format('Y-m-d H:i:s');


    //                 $statusArr = config('shipments_import_mapping.integra2-status');
    //                 $statusId = @$statusArr[$status];

    //                 $data = [
    //                     'status_id'  => $statusId,
    //                     'obs'        => $location,
    //                     'city'       => $location,
    //                     'created_at' => @$date,
    //                     'weight'     => $weight
    //                 ];


    //                 if ($statusId == 5 && !empty($proof)) {
    //                     $data['obs'] .= '<br/><a href="' . $proof . '" target="_blank">Prova Entrega</a>';
    //                 }

    //                 $history[] = $data;
    //             }
    //         } catch (\Exception $e) {
    //             //dd($e->getMessage());
    //         }
    //     }

    //     return $history;
    // }
    public function getEstadoEnvioByTrk($shipment)
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
    public function getEstadoRecolhaByTrk($codAgeCargo = null, $codAgeOri = null, $trackingCode)
    {
    }

    /**
     * Obtém vários estados de envio
     *
     * @param $params ['trackings]
     * @return type|false|mixed|string
     * @throws \Exception
     */
    public function getEstadoEnvioMassive($trks)
    {
    }

    /**
     * Devolve a imagem do POD
     *
     * @param $codAgeCargo
     * @param $codAgeOri
     * @param $trakingCode
     * @return string
     * @throws \Exception
     */
    public function getPod($codAgeCargo, $codAgeOri, $trakingCode)
    {
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
        return getEstadoEnvioByTrk(null, null, $referencia);
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
        return false;
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
    public function getEnvioByTrk($codAgeCargo, $codAgeOri, $trackingCode)
    {
        return false;
    }

    /**
     * Insere um envio
     *
     * @param type $data
     * @return type
     */
    public function storeEnvio($data)
    {
        $trk      = $data['tracking_code_fornecedor'];
        $fileName = $data['fileName'];
        unset($data['tracking_code_fornecedor']);
        unset($data['fileName']);

        $fileRow = '';
        foreach ($data as $key => $dataArray) {
            $fileRow .= $key ? "\r\n" : "";
            foreach ($dataArray as $key => $value) {
                $fileRow .= $value . ';';
            }
        }

        $success = $this->storeFTP($fileRow, $fileName);

        if ($this->debug) {
            $request = $fileRow;
            file_put_contents(public_path() . '/dumper/request.txt', $request);
            file_put_contents(public_path() . '/dumper/response.txt', '');
        }

        if (config('app.env') === 'local')
            $success = true;

        return $success ? $trk : false;
    }

    /**
     * Gives the data the proper treatment
     *
     * @param \App\Models\Shipment $shipment
     * @return Array
     */
    private function getStoreEnvioData($shipment)
    {
        /**
         *   1 ExpRemDes Nome do Remitente C 40 S
         *   2 ExpRemDom Morada do Remitente C 80 S
         *   3 ExpRemPai Código do Pais do Remitente N 3 S
         *   4 ExpRemPos Código Postal do Remitente C 7 S
         *   5 ExpRemRep Código de Repetição do Postal do Remitente N 3 N
         *   6 ExpRemPob Localidade Postal Remitente C 45 S
         *   7 ExpRemNif N.I.F. Do Remitente C 12 S
         *   8 ExpRemTel Telefone do Remitente C 15 N
         *   9 ExpDesDes Nome do Destinatário C 40 S
         *   10 ExpDesDom Morada do Destinatário C 80 S
         *   11 ExpDesPai Código do País do Destinatário N 3 S
         *   12 ExpDesPos Código Postal do Destinatário C 7 S
         *   13 ExpDesRep Código de Repetição do Postal do Destinatário N 3 N
         *   14 ExpDesNif N.I.F. do Destinatário C 12 S
         *   15 ExpDesTel Telefone do Destinatário C 15 N
         *   16 ExpRecMail Email Destinatário N 17 N
         *   17 ExpObs1 Observações (1/2) C 70 N
         *   18 ExpObs2 Observações (2/2) C 70 N
         *   19 ExpEntB10 Valor do Reembolso N 16 S
         *   20 ExpB10DvCd Código Divisa do Reembolso N 3 S
         *   21 ExpEntReT Tipo de Reembolso N 1 S
         *   22 ExpEncOg Gestão de comprovativos N 1 S
         *   23 ExpAlbOrd Referência Cliente (Campo Agrupador de Envios) C 100 S
         *   24 ExpReDpCod Código do Dep. de Faturação do Ordenante N 4 S
         *   25 MerCod Código do Tipo de Mercadoria N 4 S
         *   26 IcoCod Código de Incotermo N 2 S
         *   27 ExpOrdCod Código do Ordenante - nº de cliente Skynet N 6 S
         *   28 ExpOrdDes Nome do Ordenante do Serviço C 50 S
         *   29 TraCod Código do Tipo de Serviço N 2 S
         *   30 ExpBar20 Entrega Grandes Superfícies N 17 S
         *   31 ExpBar8 Euro Paletes Entregues N 17 N
         *   32 ExpBar36 Estrado Entregues N 17 N
         *   33 ExpBar32 Chep Entregues N 17 N
         *   34 ExpBar34 LPR Entregue N 17 N
         *   35 ExpBar38 Outras Entregues N 17 N
         *   36 ExpDisAco Data Acordada do Serviço D 10 S
         *   37 ExpAlbRem Referência Externa II do Remitente C 100 N
         *   38 ExpBltMer Código Barras VOL – SSCC (Único por volume) C 40 S
         *   39 ExpBltTip Tipo de Volume B (Volume /P (Palete) C 1 S
         *   40 ExpBltRef Referencia Artigo (Família/Tipo de Artigo) C 25 S
         *   41 ExpBltKil Peso KG Volume/Palete Por Unidade N 13 S
         *   42 ExpBltVol M3 Volume/Palete Por Unidade N 13 S
         *   43 ExpNys Código AT C 255 S/N
         *   44 ExpBar3 Unidades C 255 N
         *   45 ExpBar41 Agendamento de Entrega (0/1) N 1 N
         *   46 ExpBar42 Chamada Prévia (0/1) N 1 N
         */
        // Set up variables/casts
        $agency = $shipment->agency;
        $obs = str_split($shipment->obs, 70);
        $isPackagingPallet = $shipment->service->unity === "volume" && preg_match('(pallet)', $shipment->service->group);
        $serviceType = $shipment->service->is_air ? 'air' : ($shipment->service->is_maritime ? 'ocean' : 'national');

        // Zipcode get agency from datatable
        $senderZipCode = $this->convertZipCode($agency->zip_code);
        $recipientZipCode = $this->convertZipCode($shipment->recipient_zip_code);
        // $agencies = $this->getAgencies(strtolower($shipment->sender_country ?? 'pt'), $senderZipCode, strtolower($shipment->recipient_country ?? 'pt'), $recipientZipCode);
        try {
            // $senderAgency    = $agencies[$senderZipCode];
            // $recipientAgency = $agencies[$recipientZipCode];
            $senderAgency    = $this->getAgency(strtolower($shipment->sender_country ?? 'pt'), $senderZipCode);
        } catch (\Throwable $th) {
            throw new \Exception('Código postal não encontrado.');
        }

        $date = date('ymd', strtotime($shipment->shipping_date)) . date('His'); //AAMMDDHHMMSS

        $fileName = 'edi' . $senderAgency->agency . $this->customerCode . $date; //edi 75 005169 220331000000

        $data = [
            'tracking_code_fornecedor' => $shipment->tracking_code,
            'fileName'      => $fileName,
        ];
        for ($volume = 1; $volume <= $shipment->volumes; $volume++) {
            $data[] = [
                'sender_name'     => str_limit($agency->company, 40),
                'sender_address'  => str_limit($agency->address, 80),
                'sender_country'  => $this->getCodigoPais($agency->country ?? 'pt'),
                'sender_zip_code' => $senderZipCode, // 7 digits 3500062
                'ExpRemRep'       => '', // nullable
                'sender_city'     => str_limit($agency->city, 45),
                'sender_vat'      => $agency->vat,
                'sender_phone'    => $agency->phone ?? '', // nullable

                'recipient_name'     => str_limit($shipment->recipient_name, 40),
                'recipient_address'  => str_limit($shipment->recipient_address, 80),
                'recipient_country'  => $this->getCodigoPais($shipment->recipient_country ?? 'pt'),
                'recipient_zip_code' => $recipientZipCode, // 7 digits 3500062
                'ExpDesRep'          => '', // nullable
                'recipient_vat'      => empty($shipment->recipient_vat) ? '999999990' : $shipment->recipient_vat,
                'recipient_phone'    => $shipment->recipient_phone ?? '', // nullable
                'recipient_email'    => str_limit($shipment->recipient_email, 17) ?? '', // nullable

                'ExpObs1'          => $obs[0], // nullable
                'ExpObs2'          => $obs[1] ?? '', // nullable
                'charge_price'     => $shipment->charge_price ?? '0',
                'ExpB10DvCd'       => 900, // 900=Euro // Código Divisa do Reembolso
                'charge_type'      => trans('admin/webservices.expected_values.shynet.charge_type.'), // Tipo de Reembolso
                'comprovativos'    => trans('admin/webservices.expected_values.shynet.comprovativos.'), // Gestão de comprovativos
                'client_reference' => $shipment->tracking_code, // Referência Cliente (Campo Agrupador de Envios)
                'ExpReDpCod'       => 0, // Código do Dep. de Faturação do Ordenante // 0 - SERVIÇO NORMAL | 1 - ENTREGAS AO DOMICILIO
                'MerCod'           => $isPackagingPallet ? 1 : 0, // Código do Tipo de Mercadoria // 0 - volume | 1 - Palete
                'incotermo'        => trans('admin/webservices.expected_values.shynet.incotermo.invoice'), // Código de Incotermo
                'customer_code'    => $this->customerCode, // Código do Ordenante - nº de cliente Skynet
                'agency_name'      => str_limit($agency->company, 40), // Nome do Ordenante do Serviço
                'service_type'     => trans('admin/webservices.expected_values.shynet.service_type.' . $serviceType), // Código do Tipo de Serviço
                'big_superficie_hour' => trans('admin/webservices.expected_values.shynet.big_superficie_hour.normal'), // Entrega Grandes Superfícies
                'ExpBar8'        => '', // Euro Paletes Entregues // nullable
                'ExpBar36'       => '', // Estrado Entregues // nullable
                'ExpBar32'       => '', // Chep Entregues // nullable
                'ExpBar34'       => '', // LPR Entregue // nullable
                'ExpBar38'       => '', // Outras Entregues // nullable
                'date'           => str_replace('/', '', $shipment->date ?? date('Y-m-d')), // Data Acordada do Serviço
                'ExpAlbRem'      => '', // Referência Externa II do Remitente // nullable
                'barcode'        => $this->getBarcode($shipment, $volume), // Código Barras VOL – SSCC (Único por volume)
                'packaging_type' => $isPackagingPallet ? 'P' : 'B', // Tipo: Volume B / Palete P
                'ExpBltRef'      => '0', // Referencia Artigo (Família/Tipo de Artigo)
                'weight'         => $shipment->weight, // Peso KG Volume/Palete Por Unidade
                'volumetric_weight'  => $shipment->volumetric_weight, // M3 Volume/Palete Por Unidade
                'at_guide_codeat'    => $shipment->at_guide_codeat, // Código AT
                'volumes'       => '', // Unidades // nullable
                'ExpBar41'      => 0, // Agendamento de Entrega (0/1) // nullable
                'ExpBar42'      => 0, // Chamada Prévia (0/1) // nullable
            ];
        }

        return $data;
    }

    /**
     * Insere um envio
     *
     * @param type $data
     * @return type
     */
    // Fazer
    public function storeRecolha($data)
    {
        return $this->storeEnvio($data);
    }

    /**
     * Gives the data the proper treatment
     *
     * @param \App\Models\Shipment $shipment
     * @return Array
     */
    private function getStoreRecolhaData($shipment)
    {
        /**
         *   1 ExpAlbOrd Nº da referência externa da guia/nº fatura ou GRM C 100 S
         *   2 ExpOrdCod Código Cliente N 6 S
         *   3 ExpReDpCod Nº Departamento Faturação N 4 S
         *   4 ExpDatEtd Data de Recolha D 8 S
         *   5 ExpRemCero Fixo - Em branco C 0 S
         *   6 ExpRemDes Nome do Expedidor Recolha C 40 S
         *   7 ExpRemDom Morada do Local de Recolha C 80 S
         *   8 ExpRemPos Código País de Recolha N 3 S
         *   9 ExpRemPos Código Postal de Recolha C 7 S
         *   10 ExpRemPob Localidade Postal Recolha C 45 S
         *   11 ExpObs1 Observações (1/2) C 70 S
         *   12 ExpObs2 Observações (2/2) C 70 S
         *   13 ExpBar4 Total do Peso Bruto N 17 S
         *   14 ExpBar2 Total de Volumes N 17 S
         *   15 ExpBar1 Total de Paletes N 17 S
         *   16 TraCod Código do Tipo de Serviço N 2 S
         *   17 MerCod Código do Tipo de Mercadoria N 2 S
         *   18 ExpDesCod Código Destinatário N 3 S
         *   19 ExpDesDes Nome do Destinatário C 40 S
         *   20 ExpDesDom Morada do Destinatário C 80 S
         *   21 ExpDesPos Código Postal do Destinatário C 7 S
         *   22 ExpDesPai Código do País do Destinatário N 3 S
         *   23 ExpRemTel Telefone do Local de Recolha C 15 S
         */
        $senderZipCode = $this->convertZipCode($shipment->sender_zip_code);
        $recipientZipCode = $this->convertZipCode($shipment->recipient_zip_code);
        $obs = str_split($shipment->obs, 70);

        $date = date('ymd', strtotime($shipment->shipping_date)) . date('His'); //AAMMDDHHMMSS

        $fileName = 'rec' . $this->customerCode . $date; //rec 005169 2203310000

        $data = [
            'tracking_code_fornecedor' => $shipment->tracking_code,
            'fileName'      => $fileName,
        ];

        for ($volume = 1; $volume <= $shipment->volumes; $volume++) {
            $data[] = [
                'client_reference'  => $shipment->tracking_code,
                'customer_code'     => $this->customerCode,
                'ExpReDpCod'    => 0,
                'date'          => str_replace('-', '', $shipment->date ?? date('Ymd')), // Data Acordada do Serviço
                'ExpRemCero'    => "",

                'sender_name'     => str_limit($shipment->sender_name, 40),
                'sender_address'  => str_limit($shipment->sender_address, 80),
                'sender_country'  => $this->getCodigoPais($shipment->sender_country ?? 'pt'),
                'sender_zip_code' => $senderZipCode, // 7 digits 3500062
                'sender_city'     => str_limit($shipment->sender_city, 45),
                'ExpObs1'       => $obs[0], // nullable
                'ExpObs2'       => $obs[1] ?? '', // nullable

                'weight'       => $shipment->weight,
                'volumes'      => $shipment->volumes,
                'palets'       => 0,

                'service_type'  => trans('admin/webservices.expected_values.shynet.service_type.national'), // Código do Tipo de Serviço
                'MerCod'        => 0, // Código do Tipo de Mercadoria

                'ExpDesCod'         => "",
                'recipient_name'    => str_limit($shipment->recipient_name, 40),
                'recipient_address' => str_limit($shipment->recipient_address, 80),
                'recipient_country' => $recipientZipCode, // 7 digits 3500062
                'recipient_country' => $this->getCodigoPais($shipment->recipient_country ?? 'pt'),
                'sender_phone'      => $shipment->sender_phone ?? '', // nullable
            ];
        }

        return $data;
    }

    /**
     * Devolve a etiqueta de envio em PDF
     *
     * @param type $senderAgency Agência
     * @param type $trackingCode Código de Envio
     * @param type $numEtiquetas Nº de etiquetas impressas na folha A4
     * @return type
     */
    public function getEtiqueta($senderZipCode, $trackingCode)
    {

        $shipment = Shipment::where('webservice_method', 'skynet')
            ->where('provider_tracking_code', $trackingCode)
            ->first();

        $senderZipCode = $this->convertZipCode("2830-140");
        // $senderZipCode = $this->convertZipCode($shipment->agency->zip_code); // change this
        $recipientZipCode = $this->convertZipCode($shipment->recipient_zip_code);
        $agencies = $this->getAgencies(strtolower($shipment->sender_country ?? 'pt'), $senderZipCode, strtolower($shipment->recipient_country ?? 'pt'), $recipientZipCode);
        try {
            $senderAgency    = $agencies[$senderZipCode];
            $recipientAgency = $agencies[$recipientZipCode];
        } catch (\Throwable $th) {
            throw new \Exception('Código postal não encontrado.');
        }

        $mpdf = new Mpdf([
            'format'        => [100, 150],
            'margin_top'    => 0,
            'margin_bottom' => 0,
            'margin_left'   => 0,
            'margin_right'  => 0,
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        for ($volume = 1; $volume <= $shipment->volumes; $volume++) {
            $data = [
                'shipment'        => $shipment,
                'senderAgency'    => $senderAgency,
                'recipientAgency' => $recipientAgency,
                'volume'          => $volume,
                'trackingCode'    => $trackingCode,
                'barcode'         => $this->getBarcode($shipment, $volume),
                'view'            => 'admin.printer.shipments.labels.label_skynet'
            ];

            $mpdf->WriteHTML(view('admin.printer.shipments.layouts.adhesive_labels', $data)->render()); //write
        }

        if (Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        return $mpdf->Output('Comprovativo de Envio.pdf', 'I'); //output to screen
    }

    /**
     * Permite eliminar um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function destroyShipment($trackingCode)
    {
        return false;
    }


    /*======================================================
     *
     *              TECHNICAL FUNCTIONS
     *
     ======================================================*/
    /**
     * @param $url
     * @param $xml
     * @return mixed
     */
    private function storeFTP($fileRow, $fileName)
    {
        if (!File::exists(public_path() . '/dumper/')) {
            File::makeDirectory(public_path() . '/dumper/');
        }

        $localFile  = public_path() . '/dumper/' . $fileName . '.txt';
        $remoteFile = '/in/' . $fileName . '.txt';

        file_put_contents($localFile, $fileRow);

        if (config('app.env') == 'local') {
            return false;
        }

        // FTP Login
        $connectionId = $this->loginFTP();

        // perform file upload and check upload status:
        if (!ftp_put($connectionId, $remoteFile, $localFile, FTP_BINARY)) {
            return false;
        }

        // close the FTP stream
        ftp_close($connectionId);

        File::delete($localFile);

        return true;
    }

    /**
     * Creates a new FTP connection
     * 
     * @return Connection|bool
     */
    private function loginFTP()
    {
        // FTP access parameters
        $host = $this->ftpHost;
        $user = $this->ftpUser;
        $pass = $this->ftpPass;
        $port = $this->ftpPort;

        // connect to FTP server
        try {
            $connectionId = ftp_connect($host, $port);
        } catch (\Exception $e) {
            throw new \Exception('FTP ERROR: Cannot connect to host.');
        }

        // send access parameters
        try {
            $login = ftp_login($connectionId, $user, $pass);

            if (!$login) {
                throw new \Exception('Cannot login via FTP');
            }
        } catch (\Exception $e) {
            throw new \Exception('FTP ERROR: Cannot login');
        }

        return $connectionId;
    }

    /**
     * Atualiza estados de um envio
     *
     * @param collection $shipment
     */
    public function updateHistory($shipment)
    {
    }

    /**
     * Grava um envio
     *
     * @param type $shipment
     * @param bool $isCollection
     * @return bool
     */
    public function saveShipment($shipment, $isCollection = false)
    {
        if ($shipment->provider_tracking_code) {
            return $shipment->provider_tracking_code;
        }

        if ($isCollection || $shipment->is_collection) {
            return $this->storeRecolha($this->getStoreRecolhaData($shipment));
        }

        return $this->storeEnvio($this->getStoreEnvioData($shipment));
    }

    /**
     * Create barcode
     * 
     * @param Model $shipment
     * @param int $volume
     * 
     * @return string
     */
    private function getBarcode($shipment, $volume = 1)
    {
        return $shipment->tracking_code . str_pad($shipment->volumes, 3, '0', STR_PAD_LEFT) . str_pad($volume, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Returns the code of the given country
     * 
     * @param string $country
     * 
     * @return string
     */
    private function getCodigoPais($country)
    {
        if (is_int($country)) {
            return $country;
        }

        return trans('admin/webservices.expected_values.shynet.country_code.' . strtolower($country));
    }

    /**
     * Gets Skynet information from table skynet_zip_codes
     *
     * @param String $country
     * @param String $zipcode
     * 
     * @return Collection
     */
    private function getAgency($country, $zipCode)
    {
        $agency = DB::connection('mysql_core')
            ->table('skynet_zip_codes')
            ->where('zip_code', $zipCode)
            ->where('country', $this->getCodigoPais($country))
            ->first();

        if (empty($agency)) {
            $agency = collect([
                "id" => 0,
                "country" => "",
                "zip_code" => "",
                "repetition_code" => 0,
                "city_name" => "",
                "agency" => "",
                "agency_name" => "",
                "service" => "",
                "service_name" => "",
                "route" => "",
                "route_name" => "-",
            ]);
        }

        return $agency;
    }

    /**
     * Gets Skynet information from table skynet_zip_codes
     *
     * @param String $senderCountry
     * @param String $senderZipCode
     * @param String $recipientCountry
     * @param String $recipientZipCode
     *
     * @return Collection
     */
    private function getAgencies($senderCountry, $senderZipCode, $recipientCountry, $recipientZipCode)
    {
        try {
            $senderCountry = $this->getCodigoPais($senderCountry);
            $recipientCountry = $this->getCodigoPais($recipientCountry);

            $agencies = DB::connection('mysql_core')
                ->table('skynet_zip_codes')
                ->where(function ($q) use ($senderCountry, $senderZipCode, $recipientCountry, $recipientZipCode) {
                    $q->where(function ($q) use ($senderCountry, $senderZipCode) {
                        $q->where('zip_code', $senderZipCode)
                            ->where('country', $senderCountry);
                    })
                        ->orWhere(function ($q) use ($recipientCountry, $recipientZipCode) {
                            $q->where('zip_code', $recipientZipCode)
                                ->where('country', $recipientCountry);
                        });
                })
                ->get()
                ->keyBy('zip_code');
        } catch (\Throwable $th) {
            $agencies = collect([
                "id" => 0,
                "country" => "",
                "zip_code" => "",
                "repetition_code" => 0,
                "city_name" => "",
                "agency" => "",
                "agency_name" => "",
                "service" => "",
                "service_name" => "",
                "route" => "",
                "route_name" => "-",
            ]);
        }

        return $agencies;
    }

    /**
     * Gets Route information from table skynet_zipcodes
     *
     * @param String $zipcode (ex. 3500-720)
     *
     * @return String
     */
    private function convertZipCode($zipCode)
    {
        // Converts 3500-720 to 3500720
        $zipCode = str_replace('-', '', $zipCode);

        // Converts 3500 to 3500000
        return str_pad($zipCode, 7, '0', STR_PAD_RIGHT);
    }

    /**
     * Obtem a partir do FTP os envios disponíveis para importação
     *
     * @return type
     */
    public function importShipmentsHistory()
    {
        // FTP Login
        try {
            $connection = $this->loginFTP();
        } catch (\Exception $e) {
            return false;
        }

        // Local file to store the downloaded file
        $localFolder = public_path('uploads/ftp_importer/skynet/');
        if (!File::isDirectory($localFolder)) {
            File::makeDirectory($localFolder, 0777, true, true);
        }

        // Downloads the files from FTP
        $success = $this->downloadShipmentsHistory($connection, $localFolder);
        if (!$success) {
            return false;
        }

        // Closes the FTP connection
        ftp_close($connection);

        // Reads the files and saves them in the database
        $success = $this->saveShipmentsHistory($localFolder);
        if (!$success) {
            return false;
        }

        return true;
    }

    /**
     * Download to the server the history Files from FTP
     * 
     * @param Connection $connection
     * @param String $localFolder
     * 
     * @return bool
     */
    private function downloadShipmentsHistory($connection, $localFolder): bool
    {
        //transfere para as pastas locais os ficheiros de envio
        $remoteFolder = 'out';
        $files = ftp_nlist($connection, $remoteFolder) ?: [];

        foreach ($files as $file) {
            if (!pathinfo($file, PATHINFO_EXTENSION)) { // se não for um ficheiro
                continue;
            }

            // download the file
            $success = ftp_get($connection, $localFolder . basename($file), $file, FTP_BINARY);

            // Remove the file
            if ($success) {
                ftp_delete($connection, $file);
            }
        }

        return $success ?? false;
    }

    /**
     * Reads the files and saves them in the database
     * 
     * @param String $localFolder
     * 
     * @return bool
     */
    private function saveShipmentsHistory($localFolder): bool
    {
        $files = File::files($localFolder);

        foreach ($files as $file) {
            if (!pathinfo($file, PATHINFO_EXTENSION)) { // se não for um ficheiro
                continue;
            }

            // Change the file caracters and parte them into lines
            $lines = explode("\r\n", File::get($file));
            $fileHasError = false;
            foreach ($lines as $key => $line) {
                if (empty($line)) continue;

                // get the columns to Array
                $history = explode(';', $line);
                if (empty($history[1]) || empty($history[2]) || empty($history[3]) || empty($history[4])) {
                    // ex: RACC292022606;20220713;1441;001059969475;3; // ExpAlbOrd,Data,Hora,NºGuia,StatusId
                    $fileHasError = true;
                    $errors[] = 'Status file from Skynet missing parameters.';
                    continue;
                }

                // Finds the Shipment in the database
                try {
                    $data['shipment_id'] = Shipment::where('tracking_code', $history[3])
                        ->firstOrFail(['id'])->id;
                } catch (\Exception $e) {
                    $fileHasError = true;
                    $errors[] = 'Shipment_trk ' . $history[3] . ' doesnt exist - ' . $e->getMessage();
                    continue;
                }

                // Prepare the data to save
                $date = Carbon::createFromFormat('Ymd', $history[1])->format('Y-m-d');
                $hour = substr_replace($history[2], ':', 2, 0) . ':00';
                $data['created_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $hour);

                // Map the Skynet Status to our Shipment Status
                try {
                    $skynetStatus = config('shipments_import_mapping.skynet-status');
                    $data['status_id'] = $skynetStatus[$history[4]];
                } catch (\Exception $e) {
                    $fileHasError = true;
                    $errors[] = 'No status [' . $history[4] . '] in config - ' . $e->getMessage();
                    continue;
                }

                // saves the Status
                $newHistory = ShipmentHistory::firstOrNew([
                    'shipment_id'  => $data['shipment_id'],
                    'created_at'   => $data['created_at'],
                    'status_id'    => $data['status_id']
                ]);
                $newHistory->fill($data);
                $newHistory->save();

                // if the pickup_failed_id(18) added uncomment this
                // if ($newHistory->status_id == ShippingStatus::PICKUP_FAILED_ID) {
                //     $shipment = $newHistory->shipment;
                //     $price = $shipment->addPickupFailedExpense();
                //     $shipment->walletPayment(null, null, $price); //discount payment
                // }
            }

            // handle the file that was readed
            if (!$fileHasError) {
                // File::delete($file);
                //transfer the file to other folder
                $backupFolder = str_replace('/skynet', '/skynet/backup', $localFolder);
                if (!File::isDirectory($backupFolder)) {
                    File::makeDirectory($backupFolder, 0777, true, true);
                }
                File::move($file, str_replace('/skynet', '/skynet/backup', $file));
            } else {
                Log::error('Skynet Errors (' . count($errors) . '): There is some files in /public/uploads/ftp_importer/skynet that need some atention.' . "\n" . implode("\n", $errors));
            }
        }
        return true;
    }
}
