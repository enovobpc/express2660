<?php

namespace App\Models\Webservice;

use App\Models\Shipment;
use Date, File, View, Setting;
use App\Models\ShipmentHistory;
use Excel, Mpdf\Mpdf;

class Expresso24 extends \App\Models\Webservice\Base {

    /**
     * @var string
     */
    private $url = '212.55.146.130';

    /**
     * @var string
     */
    private $port = '21';

    /**
     * @var null
     */
    private $upload_directory = '/uploads/labels/expresso24/';


    /**
     * @var string
     */
    private $password;

    /**
     * @var null
     */
    private $user = null;

    /**
     * @var string
     */
    private $ftpFolder = '';

    /**
     * Tipsa constructor.
     * @param $agencia
     * @param $cliente
     * @param $password
     */
    public function __construct($agencia, $ftpUser, $ftpPassword) {
        $this->user      = $ftpUser;
        $this->password  = $ftpPassword;
        $this->ftpFolder = '2iben';

        $this->user     = 'iben';
        $this->password = 'Ibg45akU3xD24E';
    }

    /**
     * Permite obter a informação de estados a partir dos ficheiros no FTP
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function readEstados() {

        $remoteFolder = '/'.$this->ftpFolder.'/estados';
        $files = $this->getFilepathsFromFTP($remoteFolder);

        $statusHistory = [];
        if(!empty($files)) {
            foreach ($files as $file) {

                $remoteFilename = "ftp://".$this->user.":".$this->password."@".$this->url.$file;

                foreach(file($remoteFilename) as $line) {

                    $line = trim($line);
                    $line = explode(';', $line);

                    $statusHistory[] = [
                        'tracking'      => @$line[0],
                        'status_code'   => @$line[1],
                        'description'   => @$line[2],
                        'created_at'    => @$line[3],
                        'obs'           => @$line[4],
                    ];
                }
            }
        }

        return $statusHistory;
    }

    /**
     * Permite consultar os estados de uma recolha a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getEstadoRecolhaByTrk($trakingCode) {}

    /**
     * Permite consultar os estados dos envios realizados na data indicada
     *
     * @param $date
     * @return mixed
     */
    public function getEstadoEnvioByDate($date) {}


    /**
     * Devolve o histórico dos estados de um envio dada a sua referência
     *
     * @param $referencia
     * @return array|bool|mixed
     */
    public function getEstadoEnvioByReference($referencia) {}

    /**
     * Devolve as incidências na data indicada
     *
     * @param $date
     * @return mixed
     */
    public function getIncidenciasByDate($date) {}

    /**
     * Permite consultar as incidências de um envio a partir do seu código de envio
     *
     * @param $date
     * @return mixed
     */
    public function getIncidenciasByTrk($codAgeCargo, $codAgeOri, $trakingCode) {}


    /**
     * Permite consultar os dados dos envios numa determinada data
     *
     * @param type $date [YYYY-MM-DD]
     * @return type
     */
    public function getEnviosByDate($date) {}

    /**
     * Permite consultar um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getEnvioByTrk($codAgeCargo, $codAgeOri, $trakingCode) {}

    /**
     * Permite consultar um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getRecolhaByTrk($trakingCode) {}

    /**
     * Permite consultar os estados de um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function storeRecolha($data) {

        $remoteFolder = '2ex24/recs/';
        $filename     = 'TEST_4753_'. date('Ymd_His') .'.rec';

        /*$header = [
            'Nome_recolha',
            'Morada_recolha',
            'Morada_aux_recolha',
            'Cpostal_recolha',
            'Local_recolha',
            'NIF_recolha',
            'Tel_recolha',
            'Email_recolha',

            'Nome_destinatario',
            'Morada_destinatario',
            'Morada_aux_destinatario',
            'Cpostal_destinatario',
            'Local_destinatario',
            'NIF_destinatario',
            'Tel_destinatario',
            'Email_destinatario',
            'Num_vols',
            'Peso',
            'Cubico',
            'Referencia_cliente',
            'Observações',
            'Data_para_recolha',
            'Hora_ini1',
            'Hora_fim1',
            'Hora_ini2',
            'Hora_fim2',
            'Tipo_serv',
            'CR' //obrigatorio
        ];*/

        $rowData = [
            $data['sender_name'],
            $data['sender_address_1'],
            $data['sender_address_2'],
            $data['sender_zip_code'],
            $data['sender_city'],
            '', //nif
            $data['sender_phone'],
            '',
            $data['recipient_name'],
            $data['recipient_address_1'],
            $data['recipient_address_2'],
            $data['recipient_zip_code'],
            $data['recipient_city'],
            '', //nif
            $data['recipient_phone'],
            '',
            $data['volumes'],
            $data['weight'],
            $data['volumetric_weight'],
            $data['reference'],
            $data['obs'],
            $data['date'],
            '',
            '',
            '',
            '',
            $data['service'],
            '',
            '##'
        ];

        if(!File::exists(public_path() . $this->upload_directory)) {
            File::makeDirectory(public_path() . $this->upload_directory, 0777, true, true);
        }

        File::put($this->localStorage($filename), implode(';', $rowData));

        try {
            $result = $this->writeFtp($filename, $remoteFolder);

            if(!$result) {
                throw new \Exception('Erro ao submeter para a Expresso24');
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return $data['num_guia'];
    }

    /**
     * Insere um envio
     *
     * @param type $data
     * @return type
     */
    public function storeEnvio($data)
    {
        $remoteFolder = '2ex24/envs/';
        $filename     = '4753_'. date('Ymd_Hi') .'.env';

        /* $header = [
             'Num_guia',
             'Nome_destinatario',
             'Morada_destinatario',
             'Morada_aux_destinatario',
             'Cpostal_destinatario',
             'Local_destinatario',
             'NIF_destinatario',
             'Tel_destinatario',
             'Email_destinatario',
             'Num_vols',
             'Peso',
             'Cubico',
             'Referencia_cliente',
             'Observações',
             'Data_para_entrega',
             'Portes',
             'Tipo_serv',
             'Valor_reembolso',
             'Forma_pag_reemb',
             'Val_chq1',
             'Val_chq2',
             'Val_chq3',
             'Val_chq4',
             'Dt_chq1',
             'Dt_chq2',
             'Dt_chq3',
             'Dt_chq4',
             'Presc_carimbo' //S = SIM, N = NÃO
             'CR' //obrigatorio
         ];*/

        $rowData = [
            $data['num_guia'],
            $data['recipient_name'],
            $data['recipient_address_1'],
            $data['recipient_address_2'],
            $data['recipient_zip_code'],
            $data['recipient_city'],
            '', //nif
            $data['recipient_phone'],
            '',
            $data['volumes'],
            $data['weight'],
            $data['volumetric_weight'],
            $data['reference'],
            $data['obs'],
            '',
            $data['portes'],
            $data['service'],
            $data['charge_price'],
            $data['payment_method'],
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'N',
            '',
            '##'
        ];

        if(!File::exists(public_path() . $this->upload_directory)) {
            File::makeDirectory(public_path() . $this->upload_directory, 0777, true, true);
        }

        File::put($this->localStorage($filename), implode(';', $rowData));

        try {
            $result = $this->writeFtp($filename, $remoteFolder);

            if(!$result) {
                throw new \Exception('Erro ao submeter para a Expresso24');
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return $data['num_guia'];
    }

    /**
     * Devolve a etiqueta de envio em PDF
     *
     * @param type $senderAgency Agência
     * @param type $trackingCode Código de Envio
     * @param type $numEtiquetas Nº de etiquetas impressas na folha A4
     * @return type
     */
    public function getEtiqueta($senderAgency, $trackingCode, $outputFormat = 'I') {

        $shipment = Shipment::filterAgencies()->where('provider_tracking_code', $trackingCode)->first();

        if(!$shipment) {
            throw new \Exception('Envio com o código '. $trackingCode. ' não encontrado.');
        }

        ini_set("memory_limit", "-1");

        $mpdf = new Mpdf([
            'orientation'   => 'L',
            'format'        => [100,145],
            'margin_left'   => 2,
            'margin_right'  => 2,
            'margin_top'    => 2,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("Paulo Costa");
        $mpdf->shrink_tables_to_fit = 0;

        $data['view']       = 'admin.shipments.pdf.label_expresso24';
        $data['shipment']   = $shipment;
        $data['rota']       = $shipment->agency_id;

        for($count = 1 ; $count <= $shipment->volumes ; $count++) {
            $data['count'] = $count;
            $mpdf->WriteHTML(View::make('admin.printer.shipments.layouts.adhesive_labels', $data)->render()); //write
        }

        if(Setting::get('open_print_dialog_labels')) {
            $mpdf->SetJS('this.print();');
        }

        $mpdf->debug = true;
        if($outputFormat == 'I') {
            $mpdf->Output('Etiquetas.pdf', 'I');
            exit;
        }

        $b64Doc = $mpdf->Output('Etiquetas.pdf', 'S'); //return pdf base64 string
        $b64Doc = base64_encode($b64Doc);
        return $b64Doc;
    }

    /**
     * Devolve o URL do comprovativo de entrega
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function ConsEnvPODDig($codAgeCargo, $codAgeOri, $trakingCode) {}


    /**
     * Devolve as informações completas dos envios e respetivo POD de entrega dos envios numa data
     *
     * @param type $date
     * @param type $tracking Se indicado, devolve a informação apenas para o envio com o tkr indicado
     * @return type
     */
    public function InfEnvEstPOD($date, $tracking = null) {}

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

        $shipments = $this->readEstados();

        if($shipments) {

            foreach ($shipments as $item) {

                $shipment = Shipment::where('provider_tracking_code', $item['tracking'])->where('webservice_method', 'expresso24')->first();

                if($shipment) {
                    $expresso24Status   = config('shipments_import_mapping.expresso24-status');
                    $item['status_id']  = @$expresso24Status[$item['status_code']];
                    $item['created_at'] = new Date($item['created_at']);

                    if($item['status_id'] == '9') {
                        $item['obs'] = $item['description'] .' ' . $item['obs'];
                    } elseif($item['status_id'] == '5') {
                        $item['receiver'] = $item['description'] . ' ' . $item['obs'];
                    }

                    $history = ShipmentHistory::firstOrNew([
                        'shipment_id' => $shipment->id,
                        'created_at'  => $item['created_at'],
                        'status_id'   => $item['status_id']
                    ]);

                    $history->fill($item);
                    $history->shipment_id = $shipment->id;
                    $history->save();

                    $shipment->status_id = $history->status_id;
                    $shipment->save();
                }
            }

            //clean ftp folder
            $remoteFolder = '/'.$this->ftpFolder.'/estados';
            $this->cleanFTPFolder($remoteFolder);
            return true;
        }
        return false;
    }

    /**
     * Grava ou edita um envio
     *
     * @param type $shipment
     * @return boolean
     */
    public function saveShipment($shipment, $isCollection = false) {

        $service = 'N'; //N = normal, M = Maritimo, A = Aéreo

        $reference =  $shipment->reference ? ' | '.$shipment->reference : '';

        $data = [
            "num_guia"           => $this->getNumGuia($shipment),
            "date"               => $shipment->date,
            "service"            => $service,
            "volumes"            => $shipment->volumes,
            "weight"             => $shipment->weight,
            "volumetric_weight"  => '0.00',
            "charge_price"       => $shipment->charge_price ? forceDecimal($shipment->charge_price) : 0,
            "sender_name"        => str_limit($shipment->sender_name, 50),
            "sender_address_1"   => substr($shipment->sender_address, 0, 50),
            "sender_address_2"   => strlen($shipment->sender_address) > 50 ? substr($shipment->sender_address, 50, 50) : '',
            "sender_city"        => str_limit($shipment->sender_city, 50),
            "sender_country"     => strtoupper($shipment->sender_country),
            "sender_zip_code"    => $shipment->sender_zip_code,
            "sender_phone"       => $shipment->sender_phone,
            "recipient_attn"     => str_limit($shipment->recipient_attn, 50),
            "recipient_name"     => str_limit($shipment->recipient_name, 50),
            "recipient_address_1"=> substr($shipment->recipient_address, 0, 50),
            "recipient_address_2"=> strlen($shipment->recipient_address) > 50 ?substr($shipment->recipient_address, 50, 50) : '',
            "recipient_city"     => str_limit($shipment->recipient_city, 50),
            "recipient_country"  => strtoupper($shipment->recipient_country),
            "recipient_zip_code" => $shipment->recipient_zip_code,
            "recipient_phone"    => $shipment->recipient_phone ? str_limit($shipment->recipient_phone, 15) : '',
            "observations"       => $shipment->obs,
            "reference"          => 'TRK'.$shipment->tracking_code . $reference,
            "obs"                => $shipment->obs,
            "portes"             => $shipment->payment_at_recipient ? 'D' : 'E', //E = Expedidor, D= Destinatário
            "charge_price"       => $shipment->charge_price ? $shipment->charge_price : '0.00',
            "payment_method"     => 'I' //I, P, N, C - Indiferente, Pronto, Numerário ou Cheque(s)
        ];

        if($isCollection) {
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
    public function destroyShipment($shipment) {}

    /**
     * Gera um numero de guia
     *
     * @param type $shipment
     * @return boolean
     */
    public function getNumGuia($shipment) {

        $shipment = Shipment::where('provider_tracking_code', 'like', 'IB%')
            ->orderBy('provider_tracking_code', 'desc')
            ->first();

        if($shipment) {
            $latTrk = $shipment->provider_tracking_code;
            $count  = (int) substr($latTrk, 2);
            $count+= 1;
        } else {
            $count = 1;
        }

        $count = str_pad($count, 6, 0, STR_PAD_LEFT);
        return 'IB' . $count;
    }

    /**
     * Gera o código de barras para envios para espanha
     * @param $tracking
     *
     * Caracteres 1 a 12 - numero de envio  (12 caracteres)
     * Caracteres 13 a 15 - delegacao origem (3 caracteres) sempre 604
     * Caracteres 16 a 18 - delegacao destino (3 caracteres) descodificar pelo código postal utilizando as indicações no ficheiro anexo.
     * Caracteres 19 a 21 - fixo 000
     * Caracteres 22 a 23 - fixo 01
     * Caracteres 24 a 26 - indicação do volume 001, 002, ...
     */
    public static function getBarcode($country, $tracking, $volume, $destinationAgency = null) {


        if($country == 'pt') {
            $barcode = $tracking. str_pad($volume, 3, '0', STR_PAD_LEFT);
            return $barcode;
        } else {
            $tracking = str_replace('IB', '604100', $tracking);
            $sourceAgency = '604';
            $barcode = $tracking . $sourceAgency . $destinationAgency.'00001'.str_pad($volume, 3, '0', STR_PAD_LEFT);
        }
        return $barcode;
    }

    /**
     *
     * @param $filename
     * @return string
     */
    public function localStorage($filename) {
        return public_path() . $this->upload_directory . $filename;
    }

    /**
     * Write file to FTP
     *
     * @return bool
     * @throws \Exception
     */
    public function writeFtp($filename, $remoteFolder){

        set_time_limit(5);

        // FTP access parameters
        $host = $this->url;
        $user = $this->user;
        $pass = $this->password;
        $port = $this->port;

        $ftpPath   = $remoteFolder . $filename;
        $localFile = $this->localStorage($filename);

        // connect to FTP server
        try {
            $connectionId = ftp_connect($host, $port);
        } catch (\Exception $e) {
            throw new \Exception('FTP ERROR: Cannot connect to host.');
        }

        // send access parameters
        try {
            $login = ftp_login($connectionId, $user, $pass);

            if(!$login) {
                throw new \Exception('Cannot login via FTP');
            }
        } catch (\Exception $e) {
            throw new \Exception('FTP ERROR: Cannot login');
        }

        //turn on passive mode transfers (some servers need this)
        ftp_pasv ($connectionId, true);

        // perform file upload
        $upload = ftp_put($connectionId, $ftpPath, $localFile, FTP_ASCII);

        // check upload status:
        if(!$upload) {
            return false;
        }

        // close the FTP stream
        ftp_close($connectionId);

        return true;
    }

    /**
     * Read files from FTP
     *
     * @return bool
     * @throws \Exception
     */
    public function getFilepathsFromFTP($remoteFolder){

        set_time_limit(5);

        // FTP access parameters
        $host = $this->url;
        $user = $this->user;
        $pass = $this->password;
        $port = $this->port;

        // connect to FTP server
        try {
            $connectionId = ftp_connect($host, $port);
        } catch (\Exception $e) {
            throw new \Exception('FTP ERROR: Cannot connect to host.');
        }

        // send access parameters
        try {
            $login = ftp_login($connectionId, $user, $pass);

            if(!$login) {
                throw new \Exception('Cannot login via FTP');
            }
        } catch (\Exception $e) {
            throw new \Exception('FTP ERROR: Cannot login');
        }

        //turn on passive mode transfers (some servers need this)
        ftp_pasv ($connectionId, true);

        // perform file upload
        $files = ftp_nlist($connectionId, $remoteFolder);

        // check upload status:
        if(!$files) {
            return false;
        }

        // close the FTP stream
        ftp_close($connectionId);

        return $files;
    }

    /**
     * Read files from FTP
     *
     * @return bool
     * @throws \Exception
     */
    public function cleanFTPFolder($remoteFolder){

        set_time_limit(5);

        // FTP access parameters
        $host = $this->url;
        $user = $this->user;
        $pass = $this->password;
        $port = $this->port;

        // connect to FTP server
        try {
            $connectionId = ftp_connect($host, $port);
        } catch (\Exception $e) {
            throw new \Exception('FTP ERROR: Cannot connect to host.');
        }

        // send access parameters
        try {
            $login = ftp_login($connectionId, $user, $pass);

            if(!$login) {
                throw new \Exception('Cannot login via FTP');
            }
        } catch (\Exception $e) {
            throw new \Exception('FTP ERROR: Cannot login');
        }

        //turn on passive mode transfers (some servers need this)
        ftp_pasv ($connectionId, true);

        // perform file upload
        $files = ftp_nlist($connectionId, $remoteFolder);

        foreach ($files as $file) {
            ftp_delete($connectionId, $file);
        }

        // close the FTP stream
        ftp_close($connectionId);

        return true;
    }

    /**
     * Write file to FTP
     *
     * @return bool
     * @throws \Exception
     */
    public static function getRouteName($zipCode, $country = 'pt'){

        if($country == 'pt') {
            $filepath = storage_path().'/webservices/expresso24_routes_pt.txt';
        } else {
            $filepath = storage_path().'/webservices/expresso24_routes_es.txt';
        }

        foreach (file($filepath) as $row) {
            $row = explode(';', trim($row));

            if($country == 'pt') {
                $rowZipCode = zipcodeCP4(@$row[0]);
                if($zipCode == $rowZipCode) {
                    $routeName = $row[2].' - '.$row[1];
                    return $routeName;
                }
            } else {
                $rowZipCode = @$row[0];
                if($zipCode == $rowZipCode) {
                    $routeName = $row[1].' - '.$row[2];
                    return $routeName;
                }
            }
        }

        return null;
    }
}