<?php

namespace App\Models\Webservice;

use App\Models\BroadcastPusher;
use App\Models\Customer;
use App\Models\LogViewer;
use App\Models\Provider;
use App\Models\RefundControl;
use App\Models\Route;
use App\Models\Service;
use App\Models\Shipment;
use App\Models\ShipmentIncidenceResolution;
use App\Models\ShipmentPackDimension;
use App\Models\Traceability\ShipmentTraceability;
use App\Models\ShippingStatus;
use App\Models\WebserviceConfig;
use Carbon\Carbon;
use Date, Response, File, Setting;
use App\Models\ShipmentHistory;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use DB, Mail;

class Sending extends \App\Models\Webservice\Base {

    /**
     * @var null
     */
    private $customerCode;

    /**
     * @var null
     */
    private $debug = false;

    /**
     * @var null
     */
    private $ftpHost = 'ftp.sending.es';

    /**
     * @var null
     */
    private $ftpUser = null;

    /**
     * @var null
     */
    private $ftpPass = null;

    /**
     * @var null
     */
    private $ftpPort = 21;

    /**
     * @var null
     */
    private $sendingCustomerId = null;

    /**
     * @var null
     */
    private $agencia = null;

    /**
     *
     */
    private $sourceAgencies = null;

    /**
     * Correos Express constructor.
     * @param $agencia
     * @param $cliente
     * @param $password
     * @param $sessionId
     */
    public function __construct($agencia = null, $user = null, $password = null, $sessionId = null, $department=null, $endpoint=null, $debug=false)
    {
        $source = config('app.source');

        if(config('app.env') == 'local') {
            $this->agencia = 610;
            $this->ftpUser = 'enovo';
            $this->ftpPass = 'eCDi1Lh79zz7BwpA5Mxe';
            $this->sourceAgencies = [
                610 => '18437', //VISEU
                606 => '18438'  //BEIRAS
            ];

        } else {
            $this->agencia = $agencia;
            $this->ftpUser = 'enovo';
            $this->ftpPass = 'eCDi1Lh79zz7BwpA5Mxe';
            //$this->ftpUser = $user;
            //$this->ftpPass = $password;

            if($source == 'asfaltolargo') {
                //agencia 610 => cliente no programa
                $this->sourceAgencies = [
                    610 => '18761', //VISEU
                    606 => '18761'  //BEIRAS
                ];
            } elseif($source == 'fozpost') {
                $this->sourceAgencies = [
                    603 => '18241', //COIMBRA
                ];
            } elseif($source == 'ventostranquilos') {
                $this->sourceAgencies = [
                    605 => '12' //TMONTES
                ];
            } elseif($source == 'log24') {
                $this->sourceAgencies = [
                    608 => '17792' //LOULÉ
                ];
                $this->shippingCustomer = '60800002-01';
            } elseif($source == 'jamelao') {
                $this->sourceAgencies = [
                    607 => '162' //MONTEPOR
                ];
            } elseif($source == 'tortugaveloz') {
                $this->sourceAgencies = [
                    602 => '18048', //SETUBAL
                ];
            } elseif($source == 'transbag') {
                $this->sourceAgencies = [
                    609 => '443', //MADEIRA
                ];
            } elseif($source == 'sopostal') {
                $this->sourceAgencies = [
                    615 => '795', //PORTO SANTO
                ];

                $this->shippingCustomer = '61500001-01';

            } elseif($source == 'targetignition') {
                $this->sourceAgencies = [
                    604 => '18189', //PORTO
                ];

                $this->shippingCustomer = '60400001-01';

            } elseif($source == 'trpexpress') {
                $this->sourceAgencies = [
                    612 => '222', //BRAGA
                ];

                $this->shippingCustomer = '61200001-01';
            } elseif($source == 'transportesnunes') {
                $this->sourceAgencies = [
                    614 => '93', //AVEIRO
                ];

                $this->shippingCustomer = '61400001-01';
            }
        }

        $this->debug = $debug;

        if (!File::exists(public_path() . '/uploads/ftp_importer/')) {
            File::makeDirectory(public_path() . '/uploads/ftp_importer/');
        }

        if (!File::exists(public_path() . '/uploads/ftp_importer/sending')) {
            File::makeDirectory(public_path() . '/uploads/ftp_importer/sending');
        }
    }

    /**
     * Permite consultar os estados de um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getEstadoEnvioByTrk($shipment) {}


    /**
     * Permite consultar os estados de um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getEstadoRecolhaByTrk($codAgeCargo = null, $codAgeOri = null, $trackingCode) {}

    /**
     * Obtém vários estados de envio
     *
     * @param $params ['trackings]
     * @return type|false|mixed|string
     * @throws \Exception
     */
    public function getEstadoEnvioMassive($trks) {}

    /**
     * Devolve a imagem do POD
     *
     * @param $codAgeCargo
     * @param $codAgeOri
     * @param $trakingCode
     * @return string
     * @throws \Exception
     */
    public function getPod($codAgeCargo, $codAgeOri, $trakingCode){}

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
        return getEstadoEnvioByTrk(null, null, $referencia);
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
     * Obtem a partir do FTP os envios disponíveis para importação
     *
     * @param type $date [YYYY-MM-DD]
     * @return type
     */
    public function getEnviosByDate($date = null) {}

    /**
     * Permite consultar um envío a partir do seu código de envio
     *
     * @param type $codAgeCargo Código da agência de Destino
     * @param type $codAgeOri Código da Agência de Origem
     * @param type $trakingCode Código de Encomenda
     * @return type
     */
    public function getEnvioByTrk($codAgeCargo, $codAgeOri, $trackingCode) {
        return false;
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
     * Insere um envio
     *
     * @param type $data
     * @return type
     */
    public function storeEnvio($data){

        $bultosXml = '';

        $newShipment = new Shipment();
        $newShipment->provider_tracking_code    = $data['provider_tracking_code'];
        $newShipment->provider_sender_agency    = $data['provider_sender_agency'];
        $newShipment->provider_recipient_agency = $data['provider_recipient_agency'];

        for ($vol = 1; $vol<=$data['volumes']; $vol++) {

            $barcode = $this->getBarcode($newShipment, $vol, $data['delivery_zone'], $data['service']);

            $bultosXml.= '<Bulto>
                            <NumeroBulto>'.$vol.'</NumeroBulto>
                            <CodigoBarrasCliente>'.$barcode.'</CodigoBarrasCliente>
                          </Bulto>';
        }


        $xml = '<?xml version="1.0" encoding="ISO-8859-1"?>
                <Expediciones>
                  <Expedicion>
                    <NumeroEnvio>'.$data['provider_tracking_code'].'</NumeroEnvio>
                    <ClienteRemitente>'.$data['provider_customer'].'</ClienteRemitente>
                    <NombreRemitente>'.$data['sender_name'].'</NombreRemitente>
                    <DireccionRemitente>'.$data['sender_address'].'</DireccionRemitente>
                    <PoblacionRemitente>'.$data['sender_city'].'</PoblacionRemitente>
                    <CodigoPostalRemitente>'.$data['sender_zip_code'].'</CodigoPostalRemitente>
                    <PaisRemitente>'.$data['sender_country'].'</PaisRemitente>
                    <TelefonoContactoRemitente>'.$data['sender_phone'].'</TelefonoContactoRemitente>
                    <NombreDestinatario>'.$data['recipient_name'].'</NombreDestinatario>
                    <DireccionDestinatario>'.substr($data['recipient_address'], 0, 60).'</DireccionDestinatario>
                    <Direccion2Destinatario>'.substr($data['recipient_address'], 61, 120).'</Direccion2Destinatario>
                    <PoblacionDestinatario>'.$data['recipient_city'].'</PoblacionDestinatario>
                    <CodigoPostalDestinatario>'.$data['recipient_zip_code'].'</CodigoPostalDestinatario>
                    <PaisDestinatario>'.$data['recipient_country'].'</PaisDestinatario>
                    <TelefonoContactoDestinatario>'.$data['recipient_phone'].'</TelefonoContactoDestinatario>
                    <PersonaContactoDestinatario>'.$data['recipient_attn'].'</PersonaContactoDestinatario>
                    <ReferenciaCliente>'.$data['reference'].'</ReferenciaCliente>
                    <Fecha>'.$data['date'].'</Fecha>
                    <Bultos>'.$data['volumes'].'</Bultos>
                    <Kilos>'.$data['weight'].'</Kilos>
                    <Volumen>'.$data['fator_m3'].'</Volumen>
                    <TipoPortes>'.$data['cod'].'</TipoPortes>';


        if($data['charge_price'] > 0.00) {
            $xml.= "<ImporteReembolso>".$data['charge_price']."</ImporteReembolso>";
        }

        if(!empty($data['provider_pickup_code'])) {
            $xml.= '<NumeroRecogida>'.$data['provider_pickup_code'].'</NumeroRecogida>';
        }

        $xml.= '<ProductoServicio>'.$data['service'].'</ProductoServicio>
                    <DevolucionConforme>N</DevolucionConforme>
                    <Retorno>'.$data['rpack'].'</Retorno>
                    <BultosExpedicion>
                      '.$bultosXml.'
                    </BultosExpedicion>';

        if($data['service'] == '99') {
            $xml.= '<ReferenciasExpedicion>
                            <Referencias>
                                <Tipo>DGEN</Tipo>
                                <Referencia>'.$data['provider_original_trk'].'</Referencia>
                            </Referencias>
                        </ReferenciasExpedicion>';
        }

        $xml.= '</Expedicion>
                </Expediciones>';

        //memoriza na pasta local ficheiro de leitura
        if(!empty($xml)) {

            if (!File::exists(public_path() . '/uploads/ftp_importer/sending/in/expediciones/')) {
                File::makeDirectory(public_path() . '/uploads/ftp_importer/sending/in/expediciones/');
            }

            $filename = "S".$data['provider_sender_agency'].$data['provider_recipient_agency'].'_'.date('Ymd')."_".date('His').".xml";
            File::put(public_path('uploads/ftp_importer/sending/in/expediciones/'.$filename), $xml);
        }

        $this->storeFTP('in/expediciones');

        return $data['provider_tracking_code'];
    }

    /**
     * Insere um envio
     *
     * @param type $data
     * @return type
     */
    public function storeRecolha($data){ return false; }

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

        $shipment = Shipment::whereHas('provider', function($q){
            $q->where('webservice_method', 'sending');
        })
            ->where('provider_tracking_code', $trackingCode)
            ->first();


        $serviceCode = explode('#', $shipment->reference3);
        $serviceCode = @$serviceCode[0];

        if(!$serviceCode) {
            $serviceCode = $this->getProviderService($shipment);
        }

        $serviceName = $serviceCode . ' ' . strtoupper($this->getServiceName($serviceCode));

        $senderAgency = $shipment->provider_sender_agency;
        if (empty($senderAgency)) {
            return false;
        } else {
            $senderAgency = $senderAgency. ' ' . $this->getAgencyName($senderAgency);
        }

        $recipientAgency = $shipment->provider_recipient_agency;
        if (empty($recipientAgency)) {
            return false;
        } else {
            $recipientAgency = $recipientAgency. ' ' . $this->getAgencyName($recipientAgency);
        }

        $zonaEntrega = $this->getZonaEntrega($shipment->recipient_zip_code, $shipment->recipient_country);

        $mpdf = new Mpdf([
            'format'        => [100,150],
            'margin_top'    => 0,
            'margin_bottom' => 0,
            'margin_left'   => 0,
            'margin_right'  => 0,
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        for ($vol = 1; $vol <= $shipment->volumes ; $vol++) {

            $barcode = $this->getBarcode($shipment, $vol, $zonaEntrega['code'], $serviceCode);

            $data = [
                'shipment'        => $shipment,
                'volume'          => $vol,
                'trackingCode'    => $trackingCode,
                'barcode'         => @$barcode,
                'route'           => $zonaEntrega['name'],
                'serviceName'     => $serviceName,
                'senderAgency'    => $senderAgency,
                'recipientAgency' => $recipientAgency,
                'view'            => 'admin.printer.shipments.labels.label_sending'
            ];

            $mpdf->WriteHTML(view('admin.printer.shipments.layouts.adhesive_labels', $data)->render()); //write
        }


        if(Setting::get('open_print_dialog_docs')) {
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
        return true;
    }


    /*======================================================
     *
     *              TECHNICAL FUNCTIONS
     *
     ======================================================*/


    /**
     * Obtem a partir do FTP os envios disponíveis para importação
     *
     * @return type
     */
    public function importShipments() {

        $localFolder    = public_path('uploads/ftp_importer/sending');

        $conn = ftp_connect($this->ftpHost);
        ftp_login($conn, $this->ftpUser, $this->ftpPass);

        //transfere para as pastas locais os ficheiros de envio
        $remoteFolder = 'out';
        $files = ftp_nlist($conn, $remoteFolder);

        if($files) {
            //le todos os ficheiros do FTP, sejam ou não para a agencia da plataforma atual
            foreach ($files as $remoteFile) {

                if (str_contains($remoteFile, 'ConsultaHistorico')) {

                    try {
                        $localFile = public_path() . '/uploads/ftp_importer/sending/' . basename($remoteFile);
                        //descarrega o ficheiro para o servidor.
                        //Só depois ao importar vamos saber se este ficheiro é ou não para esta plataforma.
                        ftp_get($conn, $localFile, $remoteFile, FTP_BINARY);
                        $xml = File::get($localFile);
                        $shipmentsArr = xml2Arr($xml, 'Expediciones'); //original

                        //obtem o codigo de barras do XML
                        if(@$shipmentsArr['Expedicion']['Bultos'] == 1) {
                            $barcode = @$shipmentsArr['Expedicion']['BultosExpedicion']['Bulto']['CodigoBarrasCliente']; //['CodigoBarrasCliente'];
                        } else {
                            $barcode = @$shipmentsArr['Expedicion']['BultosExpedicion']['Bulto'][0]['CodigoBarrasCliente']; //['CodigoBarrasCliente'];
                        }


                        $agencyCode = null;
                        if($barcode) {
                            $senderAgency = substr($barcode, 12, 3);
                            $agencyCode   = substr($barcode, 15, 3);


                            //se não está no array, apaga da pasta local para não ser importado.
                            //caso esteja, renomeia o ficheiro local para poder ser importado normalmente.
                            //S280601_XXXXXXX ==> 280 = agencia origem, 601=> agencia destino
                            if(in_array($agencyCode, array_keys($this->sourceAgencies))) {
                                $originalFilename = basename($remoteFile);
                                $newName = public_path() . '/uploads/ftp_importer/sending/S'.$senderAgency.$agencyCode.'_'.$originalFilename;
                                File::move($localFile, $newName); //renomeia o ficheiro
                            } else {
                                File::delete($localFile);
                            }
                        }

                    } catch(\Exception $e) {
                        /*$agencyCode = @$agencyCode ? @$agencyCode : '';
                        $msg = "FILENAME = ".basename(@$remoteFile)."\n";
                        $msg.= "AGENCY = ".$agencyCode."\n";
                        $msg.= "========================================================================\n";
                        $msg.= $e->getMessage(). ' file '. $e->getFile(). ' line '. $e->getLine();

                        Mail::raw($msg, function ($message) use($remoteFile, $agencyCode) {
                            $message->to('paulo.costa@enovo.pt');
                            $message->subject('SENDING '.$agencyCode.' - Falha ao importar ' . basename(@$remoteFile));
                        });*/
                    }

                } else {
                    //obtem o ID da agência sending a patir do nome do ficheiro
                    $agencyCode = $this->getAgencyFromFilename($remoteFile);
                    $fileType   = $agencyCode['type'];
                    $agencyCode = $agencyCode['recipient'];

                    //só importa os ficheiros que não sejam pastas (se têm extensão) e em que a agencia pertença às agencias desta plataforma (ex: asfaltolargo tem 2 agencias)
                    if ($fileType == 'S' && pathinfo($remoteFile, PATHINFO_EXTENSION) && in_array($agencyCode, array_keys($this->sourceAgencies))) { //ignora pastas e ficheiros de outras agencias

                        //faz download para o nosso servidor dos ficheiros do FTP
                        ftp_get($conn, public_path() . '/uploads/ftp_importer/sending/' . basename($remoteFile), $remoteFile, FTP_BINARY);
                    }
                }
            }
        }

        //transfere para as pastas locais os ficheiros de recolha
        $remoteFolder = 'out/recogidas';
        $files = ftp_nlist($conn, $remoteFolder);

        if($files) {
            foreach ($files as $remoteFile) {

                $backupFile = str_replace($remoteFile, 'out/', 'out/backup/');

                $agencyCode = $this->getAgencyFromFilename($remoteFile);
                $fileType   = $agencyCode['type'];
                $agencyCode = $agencyCode['recipient'];

                if ($fileType == 'R' && pathinfo($remoteFile, PATHINFO_EXTENSION) && in_array($agencyCode, array_keys($this->sourceAgencies))) { //ignora pastas
                    ftp_get($conn, public_path().'/uploads/ftp_importer/sending/'.basename($remoteFile), $remoteFile, FTP_BINARY);
                }
            }
        }


        //processa a importação de ficheiros que foram importados para o nosso servidor
        $files = File::files($localFolder);

        try {

            $sourceAgencies = array_keys($this->sourceAgencies);

            foreach ($files as $filepath) {

                $filename = basename($filepath);

                $agencyCodes = $this->getAgencyFromFilename($filepath);
                $fileType    = $agencyCodes['type'];
                $agencyCode  = (int)$agencyCodes['recipient'];


                if (in_array($agencyCode, $sourceAgencies)) {

                    //se o ficheoro contem "reco" é uma recolha
                    if (str_contains($filename, 'RECO')) {
                        $this->importRecolhaFromFile($filepath, $agencyCodes);

                        //movimenta no FTP o ficheiro para a pasta de backups
                        $remoteFile = 'out/recogidas/' . basename($filepath);
                        $backupFile = str_replace('out/recogidas/', 'out/recogidas/backup/', $remoteFile);
                        ftp_rename($conn, $remoteFile, $backupFile);
                    } elseif($fileType != 'H') { //envio
                        $this->importEnvioFromFile($filepath, $agencyCodes);

                        if (str_contains($filepath, 'ConsultaHistorico')) {
                            //movimenta o ficheiro para a pasta de backups
                            //tem de se renomear o nome do fichjeiro porque foi manipuado antes (Sxxxxxx_ConsultaHistorico....)
                            $filename = substr(basename($filepath), 8);

                            $remoteFile = 'out/' . $filename;
                            $backupFile = str_replace('out/', 'out/backup/', $remoteFile);
                            ftp_rename($conn, $remoteFile, $backupFile);
                        } else {

                            //movimenta o ficheiro para a pasta de backups
                            $remoteFile = 'out/' . basename($filepath);
                            $backupFile = str_replace('out/', 'out/backup/', $remoteFile);

                            //dd('2 ==> '.$backupFile.' ===> '.$remoteFile);
                            ftp_rename($conn, $remoteFile, $backupFile);
                        }
                    }

                    File::delete($filepath);
                }


            }
        } catch (\Exception $e) {

            $agencyCode = @$agencyCode ? @$agencyCode : '';
            $msg = "FILENAME = ".basename(@$filepath)."\n";
            $msg.= "AGENCY = ".$agencyCode."\n";
            $msg.= "========================================================================\n";
            $msg.= $e->getMessage(). ' file '. $e->getFile(). ' line '. $e->getLine();

            Mail::raw($msg, function ($message) use($filepath, $agencyCode) {
                $message->to('paulo.costa@enovo.pt');
                $message->subject('SENDING '.$agencyCode.' - Falha ao importar ' . basename(@$filepath));
            });

            //throw new \Exception('SENDING - Falha ao importar '.basename(@$filepath)); //comentado - se descomentado vai parar toda a execução
        }

        ftp_close($conn);
    }

    /**
     * Import recolha from file
     * @param $filepath
     */
    public function importRecolhaFromFile($filepath, $agencyCodes) {

        $xml = File::get($filepath);
        $sourceApp = config('app.source');
        $filename  = basename($filepath);
        $now       = date('Y-m-d H:i:s');

        $shipmentsArr = xml2Arr($xml, 'Recogidas');

        if(isset($shipmentsArr['Recogida'][0])) {
            $shipmentsArr = @$shipmentsArr['Recogida'];
        } else {
            $shipmentsArr = [@$shipmentsArr['Recogida']];
        }

        $agencyCode          = @$agencyCodes['recipient'];
        $providerCargoAgency = @$agencyCodes['sender'];

        if(empty($shipmentsArr)) {
            return null;
        } else {

            $customer = Customer::filterSource()
                ->where('id', @$this->sourceAgencies[$agencyCode])
                ->first();

            $agencyId = $customer->agency_id;
            if(config('app.source') == 'asfaltolargo' && $agencyCode == 606) {
                $agencyId = '11'; //associa a covilha
            }

            $provider = Provider::filterSource()
                ->where('webservice_method', 'sending')
                ->isCarrier()
                ->first();

            if(!$customer) {
                throw new \Exception('Cliente não encontrado.');
            }

            if(!$provider) {
                throw new \Exception('Fornecedor não encontrado.');
            }

            $services = Service::filterSource()->pluck('id', 'code')->toArray();

            $insertHistories = [];
            $statusId  = 24; //solicitada
            $createdAt = new Date();
            $createdAt1 = $createdAt->format('Y-m-d H:i:s');
            $createdAt2 = $createdAt->addSecond()->format('Y-m-d H:i:s');
            $insertShipments = [];

            foreach ($shipmentsArr as $data) {

                $date = Date::createFromFormat('d/m/Y', @$data['FechaRecogida']);

                $obs = $this->getServiceName(@$data['ProductoServicio'])."\n";
                $obs.= @$data['DetallesRecogida']['Detalle']['Observaciones'];

                if(str_contains($obs, 'E1')) {
                    $obs ="APENAS NUMERÁRIO\n".$obs;
                }

                //$providerCargoAgency = substr(@$data['ClienteRemitente'], 0, 3);

                $bultos = @$data['DetallesRecogida']['Detalle']['Bulto'];

                //STORE ENVIO
                $shipment = Shipment::firstOrNew([
                    'customer_id' => $customer->id,
                    'reference'   => @$data['Codigo']
                ]);

                //$shipment->provider_tracking_code    = @$data['NumeroEnvio'];
                $shipment->is_collection             = 1;
                $shipment->provider_cargo_agency     = $providerCargoAgency;
                $shipment->provider_recipient_agency = $agencyCode;
                $shipment->provider_sender_agency    = $providerCargoAgency;
                $shipment->provider_tracking_code    = @$data['Codigo'];

                $shipment->customer_id          = @$customer->id;
                $shipment->agency_id            = $agencyId;
                $shipment->sender_agency_id     = $agencyId;
                $shipment->provider_id          = $provider->id;
                $shipment->service_id           = $this->convertService2Enovo(@$data['DetallesRecogida']['Detalle']['ProductoServicio'], $services);

                $shipment->sender_attn          = mb_convert_encoding(@$data['PersonaContacto'], 'iso-8859-9', 'utf-8');
                $shipment->sender_name          = mb_convert_encoding(@$data['Nombre'], 'iso-8859-9', 'utf-8');
                $shipment->sender_address       = mb_convert_encoding(@$data['Direccion'], 'iso-8859-9', 'utf-8');
                $shipment->sender_zip_code      = @$data['CodigoPostal'];
                $shipment->sender_city          = mb_convert_encoding(@$data['Poblacion'], 'iso-8859-9', 'utf-8');
                $shipment->sender_country       = $this->getCountryCode(@$data['Pais']);
                $shipment->sender_vat           = @$data['NifRemitente'];
                $shipment->sender_phone         = @$data['Telefono'];

                $shipment->recipient_attn       = mb_convert_encoding(@$data['DetallesRecogida']['Detalle']['PersonaContactoDestinatario'], 'iso-8859-9', 'utf-8');
                $shipment->recipient_name       = mb_convert_encoding(@$data['DetallesRecogida']['Detalle']['NombreDestinatario'], 'iso-8859-9', 'utf-8');
                $shipment->recipient_address    = mb_convert_encoding(@$data['DetallesRecogida']['Detalle']['DireccionDestinatario'], 'iso-8859-9', 'utf-8');
                $shipment->recipient_zip_code   = @$data['DetallesRecogida']['Detalle']['CodigoPostalDestinatario'];
                $shipment->recipient_city       = mb_convert_encoding(@$data['DetallesRecogida']['Detalle']['PoblacionDestinatario'], 'iso-8859-9', 'utf-8');
                $shipment->recipient_country    = $this->getCountryCode(@$data['DetallesRecogida']['Detalle']['PaisDestinatario']);
                $shipment->recipient_vat        = @$data['DetallesRecogida']['Detalle']['NifDestinatario'];
                $shipment->recipient_phone      = @$data['DetallesRecogida']['Detalle']['TelefonoContactoDestinatario'];

                $shipment->reference            = @$data['Codigo'];
                $shipment->reference2           = @$data['DetallesRecogida']['Detalle']['ReferenciaClienteRecogida'];
                $shipment->reference3           = @$data['DetallesRecogida']['Detalle']['ProductoServicio'].'#'.@$data['ClienteRemitente'];

                $shipment->date                 = $date->format('Y-m-d');
                $shipment->start_hour           = @$data['HoraRecogidaDesde'];
                $shipment->end_hour             = @$data['HoraRecogidaHasta'];
                $shipment->billing_date         = date('Y-m-d');
                $shipment->volumes              = (int) @$data['DetallesRecogida']['Detalle']['Bultos'];
                $shipment->weight               = (float) @$data['DetallesRecogida']['Detalle']['Kilos'];

                $shipment->charge_price         = empty(@$data['DetallesRecogida']['Detalle']['ImporteReembolso']) ? null : @$data['DetallesRecogida']['Detalle']['ImporteReembolso'];
                $shipment->obs                  = $obs;
                $shipment->has_return           = @$data['DetallesRecogida']['Detalle']['Retorno'] == 'S' ? ['rpack'] : null;
                $shipment->payment_at_recipient = @$data['DetallesRecogida']['Detalle']['TipoPortes'] == 'P' ? 0 : 1;
                /*$shipment->saturday = @$data['DetallesRecogida']['Detalle']['EntregaSabado'] == 'S' ? 0 : 1;*/

                //detecta rotas e agencias destino
                if(!$shipment->exists) {
                    $route = Route::getRouteFromZipCode($shipment->sender_zip_code, $shipment->service_id, null, 'pickup');
                    $shipment->recipient_agency_id = @$route->agency_id ? $route->agency_id : $customer->agency_id;

                    if ($route) {
                        $schedule = $route->getSchedule($shipment->start_hour, $shipment->end_hour);
                        $shipment->operator_id = @$schedule['operator']['id'];
                    }
                }

                /*$prices = Shipment::calcPrices($shipment);
                $shipment->fill($prices['fillable']);

                //APAGAR QUANDO POSSIVEL
                $shipment->total_price    = $prices['prices']['shipping'];
                $shipment->total_expenses = $prices['prices']['expenses'];
                */

                $shipmentExists = $shipment->exists;
                if(!$shipmentExists) {
                    $shipment->status_id = $statusId; //pendente chegada
                }

                $shipment->setTrackingCode();

                if(!$shipmentExists) {
                    $insertHistories[] = [
                        'shipment_id' => $shipment->id,
                        'status_id'   => 28, //documentado API
                        'created_at'  => $createdAt1
                    ];

                    $insertHistories[] = [
                        'shipment_id' => $shipment->id,
                        'status_id'   => $statusId,
                        'created_at'  => $createdAt2
                    ];
                }

                $insertShipments[] = [
                    'filename'          => $filename,
                    'is_pickup'         => 1,
                    'source'            => $sourceApp,
                    'tracking_code'     => $shipment->tracking_code,
                    'provider_trk'      => $shipment->provider_tracking_code,
                    'sender_agency'     => $shipment->provider_sender_agency,
                    'recipient_agency'  => $shipment->provider_recipient_agency,
                    'sender_name'       => $shipment->sender_name,
                    'recipient_name'    => $shipment->recipient_name,
                    'date'              => $shipment->date,
                    'service'           => @$data['DetallesRecogida']['Detalle']['ProductoServicio'],
                    'customer'          => @$data['ClienteRemitente'],
                    'reference'         => @$data['DetallesRecogida']['Detalle']['ReferenciaClienteRecogida'],
                    'created_at'        => $now
                ];
            }

            if(!empty($insertShipments)) {
                \DB::connection('mysql_core')
                    ->table('sending_shipments')
                    ->insert($insertShipments);
            }
        }

    }

    /**
     * Import envio from file
     * @param $filepath
     */
    public function importEnvioFromFile($filepath, $agencyCodes) {

        //S280601_XXXXXXX ==> 280 = agencia origem, 601=> agencia destino
        $xml = File::get($filepath);
        $sourceApp = config('app.source');
        $filename  = basename($filepath);
        $now       = date('Y-m-d H:i:s');
  
        $shipmentsArr = xml2Arr($xml, 'Expediciones');
        if(isset($shipmentsArr['Expedicion'][0])) {
            $shipmentsArr = @$shipmentsArr['Expedicion'];
        } else {
            $shipmentsArr = [@$shipmentsArr['Expedicion']];
        }


        $agencyCode          = @$agencyCodes['recipient'];
        $providerCargoAgency = @$agencyCodes['sender'];
        $insertHistories     = [];

        if(empty($shipmentsArr)) {
            return null;
        } else {

            $customer = Customer::filterSource()
                ->where('id', @$this->sourceAgencies[$agencyCode])
                ->first();

            $agencyId = $customer->agency_id;
            if(config('app.source') == 'asfaltolargo' && $agencyCode == 606) {
                $agencyId = '11'; //associa a covilha
            }

            if(!$customer) {
                throw new \Exception('Cliente não encontrado.');
            }

            $provider = Provider::filterSource()
                ->where('webservice_method', 'sending')
                ->isCarrier()
                ->first();

            if(!$provider) {
                throw new \Exception('Fornecedor não encontrado.');
            }

            if($customer && $provider) {

                $services  = Service::filterSource()->pluck('id', 'code')->toArray();
                $statusId  = 13; //pendente chegada
                $createdAt = new Date();
                $createdAt1 = $createdAt->format('Y-m-d H:i:s');
                $createdAt2 = $createdAt->addSecond()->format('Y-m-d H:i:s');

                $insertShipments = [];
                foreach ($shipmentsArr as $data) {

                    try {

                        $date = Date::createFromFormat('d/m/Y', @$data['Fecha']);

                        $obs = $this->getServiceName(@$data['ProductoServicio'])."\n";
                        $obs.= @$data['Observaciones2'];

                        if(str_contains($obs, 'E1')) {
                            $obs ="APENAS NUMERÁRIO\n".$obs;
                        }elseif(str_contains($obs, 'E2')) {
                            $obs ="APENAS CHEQUE COM DATA ENTREGA\n".$obs;
                        }if(str_contains($obs, 'E0')) {
                            $obs ="APENAS CHEQUE\n".$obs;
                        }

                        if(@$data['Bultos'] == 1) {
                            $bultos = [$data['BultosExpedicion']['Bulto']];
                        } else {
                            $bultos = $data['BultosExpedicion']['Bulto'];
                        }

                        $recipientCountry = $this->getCountryCode(@$data['PaisDestinatario']);
                        $recipientZipCode = @$data['CodigoPostalDestinatario'];

                        if($recipientCountry == 'pt') {

                            if(substr($recipientZipCode, 0, 1) == '0') {
                                $recipientZipCode = substr($recipientZipCode, 1);
                            }

                            $recipientZipCode4d = substr($recipientZipCode, 0, 4);
                            $recipientZipCode3d = substr($recipientZipCode, 4, 3);
                            $recipientZipCode = $recipientZipCode4d . ($recipientZipCode3d ? '-'.$recipientZipCode3d :'');
                        }


                        $recipientPhone = @$data['TelefonoContactoDestinatario'];
                        if(substr($recipientPhone, 0, 3) == '351') {
                            $recipientPhone = '+'.$recipientPhone;
                        }elseif(substr($recipientPhone, 0, 2) == '34') {
                            $recipientPhone = '+'.$recipientPhone;
                        }

                        //STORE ENVIO
                        $shipment = Shipment::firstOrNew([
                            'customer_id' => $customer->id,
                            'reference'   => @$data['NumeroEnvio']
                        ]);

                        //$shipment->provider_tracking_code    = @$data['NumeroEnvio'];
                        $shipment->provider_cargo_agency     = $providerCargoAgency;
                        $shipment->provider_sender_agency    = $providerCargoAgency;
                        $shipment->provider_recipient_agency = $agencyCode;
                        $shipment->provider_tracking_code    = @$data['NumeroEnvio'];

                        $shipment->customer_id          = @$customer->id;
                        $shipment->agency_id            = $agencyId;
                        $shipment->sender_agency_id     = $agencyId;
                        $shipment->provider_id          = $provider->id;
                        $shipment->service_id           = $this->convertService2Enovo(@$data['ProductoServicio'], $services);

                        $shipment->sender_attn          = @$data['PersonaContactoRemitente'];
                        $shipment->sender_name          = mb_convert_encoding(@$data['NombreRemitente'], 'iso-8859-9', 'utf-8');
                        $shipment->sender_address       = mb_convert_encoding(@$data['DireccionRemitente'], 'iso-8859-9', 'utf-8');
                        $shipment->sender_zip_code      = @$data['CodigoPostalRemitente'];
                        $shipment->sender_city          = mb_convert_encoding(@$data['PoblacionRemitente'], 'iso-8859-9', 'utf-8');
                        $shipment->sender_country       = $this->getCountryCode(@$data['PaisRemitente']);
                        $shipment->sender_vat           = @$data['NifRemitente'];
                        $shipment->sender_phone         = @$data['TelefonoContactoRemitente'];

                        $shipment->recipient_attn       = @$data['PersonaContactoDestinatario'];
                        $shipment->recipient_name       = mb_convert_encoding(@$data['NombreDestinatario'], 'iso-8859-9', 'utf-8');
                        $shipment->recipient_address    = mb_convert_encoding(@$data['DireccionDestinatario'], 'iso-8859-9', 'utf-8');
                        $shipment->recipient_zip_code   = $recipientZipCode;
                        $shipment->recipient_city       = mb_convert_encoding(@$data['PoblacionDestinatario'], 'iso-8859-9', 'utf-8');
                        $shipment->recipient_country    = $recipientCountry;
                        $shipment->recipient_vat        = @$data['NifDestinatario'];
                        $shipment->recipient_phone      = $recipientPhone;

                        $shipment->reference            = @$data['NumeroEnvio'];
                        $shipment->reference2           = @$data['ReferenciaCliente'];
                        $shipment->reference3           = @$data['ProductoServicio'].'#'.@$data['ClienteRemitente'];

                        $shipment->date                 = $date->format('Y-m-d');
                        $shipment->billing_date         = date('Y-m-d');
                        $shipment->volumes              = (int) @$data['Bultos'];
                        $shipment->weight               = (float) @$data['Kilos'];

                        $shipment->charge_price         = empty(@$data['ImporteReembolso']) ? null : @$data['ImporteReembolso'];
                        $shipment->obs                  = $obs;
                        $shipment->has_return           = @$data['Retorno'] == 'S' ? ['rpack'] : null;
                        $shipment->payment_at_recipient = @$data['TipoPortes'] == 'P' ? 0 : 1;

                        //detecta rotas e agencias destino
                        if(!$shipment->exists) {
                            $route = Route::getRouteFromZipCode($shipment->recipient_zip_code, $shipment->service_id, null, 'delivery');
                            $shipment->recipient_agency_id = @$route->agency_id ? $route->agency_id : $agencyId;

                            if ($route) {
                                $schedule = $route->getSchedule($shipment->start_hour, $shipment->end_hour);
                                $shipment->operator_id = @$schedule['operator']['id'];
                            }
                        }

                        /*$prices = Shipment::calcPrices($shipment);
                        $shipment->fill($prices['fillable']);

                        //APAGAR QUANDO POSSIVEL
                        $shipment->total_price    = $prices['prices']['shipping'];
                        $shipment->total_expenses = $prices['prices']['expenses'];*/

                        $shipmentExists = $shipment->exists;
                        if(!$shipmentExists) {
                            $shipment->status_id = $statusId; //pendente chegada
                        }

                        $shipment->setTrackingCode();

                        if(!$shipmentExists) {
                            $insertHistories[] = [
                                'shipment_id' => $shipment->id,
                                'status_id'   => 28, //documentado API
                                'created_at'  => $createdAt1
                            ];

                            $insertHistories[] = [
                                'shipment_id' => $shipment->id,
                                'status_id'   => $statusId,
                                'created_at'  => $createdAt2
                            ];
                        }

                        //STORE PACKAGES
                        $packDetailsArr = [];
                        $shpBarcodes = [];
                        foreach ($bultos as $key => $bulto) {

                            $packNo = $key+1;

                            $shpBarcodes[] = @$bulto['CodigoBarrasCliente'];

                            if(@$bulto['CodigoBarrasCliente2']) {
                                $shpBarcodes[] = @$bulto['CodigoBarrasCliente2'];
                            }

                            if(@$bulto['ReferenciaBultoCliente']) {
                                $shpBarcodes[] = @$bulto['ReferenciaBultoCliente'];
                            }

                            $packDetailsArr[] = [
                                'shipment_id' => $shipment->id,
                                'type'        => 'box',
                                'description' => @$bulto['CodigoBarrasCliente'],
                                'barcode'     => @$bulto['CodigoBarrasCliente'],
                                'barcode2'    => @$bulto['ReferenciaBultoCliente'],
                                'barcode3'    => @$bulto['CodigoBarrasCliente2'],
                                'width'       => (float) @$bulto['Largo'],
                                'height'      => (float) @$bulto['Alto'],
                                'length'      => (float) @$bulto['Ancho'],
                                'weight'      => (float) @$bulto['Kilos'],
                                'pack_no'     => $packNo
                            ];
                        }

                        ShipmentPackDimension::insert($packDetailsArr);


                        $insertShipments[] = [
                            'filename'          => $filename,
                            'is_pickup'         => 0,
                            'source'            => $sourceApp,
                            'tracking_code'     => $shipment->tracking_code,
                            'provider_trk'      => $shipment->provider_tracking_code,
                            'sender_agency'     => $shipment->provider_sender_agency,
                            'recipient_agency'  => $shipment->provider_recipient_agency,
                            'sender_name'       => $shipment->sender_name,
                            'recipient_name'    => $shipment->recipient_name,
                            'date'              => $shipment->date,
                            'service'           => @$data['ProductoServicio'],
                            'customer'          => @$data['ClienteRemitente'],
                            'reference'         => @$data['ReferenciaCliente'],
                            'barcodes'          => implode(',', $shpBarcodes),
                            'created_at'        => $now
                        ];

                    } catch(\Exception $e) {

                    }
                }

                if(!empty($insertHistories)) {
                    ShipmentHistory::insert($insertHistories);
                }

                if(!empty($insertShipments)) {
                    \DB::connection('mysql_core')
                        ->table('sending_shipments')
                        ->insert($insertShipments);
                }
            }
        }
    }

    /**
     * Obtem a partir do FTP os envios disponíveis para importação
     *
     * @return type
     */
    public function importIncidencesSolutions() {

        $localFolder    = public_path('uploads/ftp_importer/sending');

        $conn = ftp_connect($this->ftpHost);
        ftp_login($conn, $this->ftpUser, $this->ftpPass);

        //transfere para as pastas locais os ficheiros de envio
        $remoteFolder = 'out';
        $files = ftp_nlist($conn, $remoteFolder);

        if($files) {
            //le todos os ficheiros do FTP, sejam ou não para a agencia da plataforma atual
            foreach ($files as $remoteFile) {

                //obtem o ID da agência sending a partir do nome do ficheiro
                $agencyCode = $this->getAgencyFromFilename($remoteFile);
                $fileType   = $agencyCode['type'];
                $senderAgencyCode    = $agencyCode['sender'];
                $recipientAgencyCode = $agencyCode['recipient'];


                //só importa os ficheiros que não sejam pastas (se têm extensão) e em que a agencia pertença às agencias desta plataforma (ex: asfaltolargo tem 2 agencias)
                if ($fileType == 'H' && pathinfo($remoteFile, PATHINFO_EXTENSION) && (in_array($senderAgencyCode, array_keys($this->sourceAgencies)) || in_array($recipientAgencyCode, array_keys($this->sourceAgencies)))) { //ignora pastas e ficheiros de outras agencias
                    //faz download para o nosso servidor dos ficheiros do FTP
                    ftp_get($conn, public_path().'/uploads/ftp_importer/sending/'.basename($remoteFile), $remoteFile, FTP_BINARY);
                }
            }
        }


        //processa a importação de ficheiros que foram importados para o nosso servidor
        $files = File::files($localFolder);

        try {

            $sourceAgencies = array_keys($this->sourceAgencies);

            foreach ($files as $filepath) {

                $filename = basename($filepath);

                $agencyCode = $this->getAgencyFromFilename($filepath);
                $filetype    = $agencyCode['type'];
                $senderAgencyCode    = (int) $agencyCode['sender'];
                $recipientAgencyCode = (int) $agencyCode['recipient'];


                if($filetype == 'H' && (in_array($senderAgencyCode, $sourceAgencies) || in_array($recipientAgencyCode, $sourceAgencies))) {

                    $this->importIncidenceSolutionFromFile($filepath, $agencyCode);

                    //movimenta o ficheiro para a pasta de backups
                    $remoteFile = 'out/'.basename($filepath);
                    $backupFile = str_replace('out/', 'out/backup/', $remoteFile);
                    ftp_rename($conn, $remoteFile, $backupFile);

                    File::delete($filepath);
                }

            }
        } catch (\Exception $e) {

            $agencyCode = @$agencyCode ? @$agencyCode : '';
            $msg = "FILENAME = ".basename(@$filepath)."\n";
            $msg.= "AGENCY = ".$agencyCode."\n";
            $msg.= "========================================================================\n";
            $msg.= $e->getMessage(). ' file '. $e->getFile(). ' line '. $e->getLine();

            Mail::raw($msg, function ($message) use($filepath, $agencyCode) {
                $message->to('paulo.costa@enovo.pt');
                $message->subject('SENDING '.$agencyCode.' - Falha ao importar ' . basename(@$filepath));
            });

            //throw new \Exception('SENDING - Falha ao importar '.basename(@$filepath)); //comentado - se descomentado vai parar toda a execução
        }

        ftp_close($conn);
    }

    /**
     * Import envio from file
     * @param $filepath
     */
    public function importIncidenceSolutionFromFile($filepath, $agencyCode) {


        //H280601_XXXXXXX ==> 280 = agencia origem, 601=> agencia destino
        $lines = File::get($filepath);
        $sourceApp = config('app.source');
        $filename  = basename($filepath);
        $now       = date('Y-m-d H:i:s');

        $lines = explode("\r\n", $lines);
        $lines = array_filter($lines);

        foreach ($lines as $line) {

            $lineParts = explode('|', $line);

            $providerTrk    = str_replace('"', '', $lineParts[0]);
            $providerAgency = str_replace('"', '', $lineParts[1]);
            $createdAt      = str_replace('"', '', $lineParts[2]);
            $eventType      = str_replace('"', '', $lineParts[3]); //E = entrega ou distribuição
            $operatorCode   = $lineParts[4];
            //$operatorCode   = $lineParts[5];
            $eventDate      = str_replace('"', '', $lineParts[6]);
            $solutionCode   = str_replace('"', '', $lineParts[7]);
            $solutionDesc   = str_replace('"', '', $lineParts[8]);
            $obs            = str_replace('"', '', $lineParts[9]);


            try {

                $createdAt = Carbon::createFromFormat('d/m/Y H:i:s', $createdAt);
                $createdAt = $createdAt->subHour(1)->format('Y-m-d H:i:s');

                if (empty($eventType)) {

                    $shipment = Shipment::where('provider_tracking_code', $providerTrk)
                        //->where('provider_recipient_agency', $agencyCode)
                        ->first();

                    if ($shipment) {
                        if ($solutionCode == '7103') {  //mal canalizado

                            //coloca o envio como incidencia por mal canalizado
                            if($shipment->status_id == '13') { //pendente chegada
                                $shipment->status_id = ShippingStatus::INCIDENCE_ID;
                                $shipment->save();

                                $shipmentHistory = new ShipmentHistory();
                                $shipmentHistory->insert([
                                    'shipment_id' => $shipment->id,
                                    'status_id' => $shipment->status_id,
                                    'agency_id' => $shipment->agency_id,
                                    'api' => 1,
                                    'created_at' => $createdAt,
                                    'submited_at' => $createdAt, //para não submeter de novo para a sending
                                    'incidence_id' => 11, //mal canalizado
                                    'obs' => '['.$solutionCode . '] ' . $solutionDesc . ($obs ? ' - ' . $obs : '')
                                ]);
                            }

                        } elseif (in_array($solutionCode, ['9500'])) { //notas internas
                            //procura ultima incidencia
                            $shipmentHistory = ShipmentHistory::where('shipment_id', $shipment->id)
                                ->where('status_id', ShippingStatus::INCIDENCE_ID)
                                ->orderBy('created_at', 'desc')
                                ->orderBy('id', 'desc')
                                ->first();

                            if($shipmentHistory) {
                                $incidenceSolution = new ShipmentIncidenceResolution();
                                $incidenceSolution->shipment_id         = $shipment->id;
                                $incidenceSolution->shipment_history_id = $shipmentHistory->id;
                                $incidenceSolution->resolution_type_id  = 1;
                                $incidenceSolution->obs     = '[CENTRAL] '.$obs;
                                $incidenceSolution->is_api  = 1;
                                $incidenceSolution->created_at = $createdAt;
                                $incidenceSolution->setCode();

                                $incidenceSolution->setNotification(BroadcastPusher::getGlobalChannel(), 'Resposta à incidência do envio ' . $shipment->tracking_code, true);
                            }

                        } elseif (in_array($solutionCode, ['VECO', '9600', '9999'])) { //Entrega em vizinho
                            //procura ultima incidencia
                            $shipmentHistory = ShipmentHistory::where('shipment_id', $shipment->id)
                                ->where('status_id', ShippingStatus::INCIDENCE_ID)
                                ->orderBy('created_at', 'desc')
                                ->orderBy('id', 'desc')
                                ->first();

                            if($shipmentHistory) {
                                $incidenceSolution = new ShipmentIncidenceResolution();
                                $incidenceSolution->shipment_id         = $shipment->id;
                                $incidenceSolution->shipment_history_id = $shipmentHistory->id;
                                $incidenceSolution->resolution_type_id  = 1;
                                $incidenceSolution->obs     = '[CENTRAL] '.$obs;
                                $incidenceSolution->is_api  = 1;
                                $incidenceSolution->created_at = $createdAt;
                                $incidenceSolution->setCode();

                                $incidenceSolution->setNotification(BroadcastPusher::getGlobalChannel(), 'Resposta à incidência do envio ' . $shipment->tracking_code, true);
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                dd($e->getMessage());
            }
        }

        return true;
    }

    /**
     * Exporta para o FTP
     *
     * @return type
     */
    public function exportTrackings($agencyCode, $date = null, $download=null) {
        $file1 = $this->exportTrackingsShipments($agencyCode, $date, $download);
        $file2 = $this->exportTrackingsPickups($agencyCode, $date, $download);

        if(empty($download)) {
            $this->storeFTP();
        }

        return $file1;
    }

    /**
     * Export shipments tracking history
     * @return array
     */
    public function exportTrackingsShipments($agencyCode, $date=null, $ignoreAlreadySubmited=false) {

        $deliveredIds  = [];
        $incidenceIds  = [];
        $statusFile    = '';

        $agencia    = $agencyCode;
        $customerId = @$this->sourceAgencies[$agencyCode];
        $eventDate  = empty($date) ? date('Y-m-d') : $date;
        $repartidor = $agencia."000";
        $now        = date('Y-m-d H:i:s');

        $shipments = Shipment::with('lastHistory')
            ->where(function($q){
                $q->where('status_id', ShippingStatus::DELIVERED_ID);
                $q->orWhere('status_id', ShippingStatus::INCIDENCE_ID);
                $q->orWhere('status_id', ShippingStatus::IN_DISTRIBUTION_ID);
            })
            ->where('is_collection', 0)
            ->where(function($q) use($agencyCode){
                $q->where('provider_recipient_agency', $agencyCode);
                //$q->orWhere('provider_cargo_agency', $agencyCode);
            })
            //->whereNull('webservice_method')
            ->where('customer_id', $customerId)
            ->whereHas('lastHistory', function($q) use($eventDate, $ignoreAlreadySubmited) {
                $q->whereIn('status_id', [
                    ShippingStatus::DELIVERED_ID,
                    ShippingStatus::INCIDENCE_ID,
                    ShippingStatus::IN_DISTRIBUTION_ID
                ]);

                if($ignoreAlreadySubmited) {
                    $q->whereRaw('date(created_at)="'.$eventDate.'"'); //comunica tudo da data indicada
                } else {
                    $q->whereNull('submited_at'); //só comunica estados que não tenham sido submetidos
                }
            })
            ->get(['id', 'tracking_code', 'provider_tracking_code', 'status_date', 'status_id', 'operator_id', 'reference', 'created_at']);


        foreach ($shipments as $shipment) {

            $eventDate  = new Date(@$shipment->lastHistory->created_at ? @$shipment->lastHistory->created_at : null);
            $eventDate2 = $eventDate->format('dmy');
            $eventDate  = $eventDate->format('d/m/Y H:i:s');

            if(!empty($shipment)) {

                if($shipment->status_id == ShippingStatus::DELIVERED_ID){
                    $deliveredIds[] = $shipment->id;
                    $receiver = @$shipment->lastHistory->receiver;
                    $receiver = str_replace('"', '', $receiver);
                    $receiver = str_replace("'", '', $receiver);
                    $statusFile.= $shipment->reference . '|"'.$agencia.'"|'.$eventDate.'|"E"|'.$repartidor.'|'.$eventDate2.'|'.$eventDate.'|"9807"|"ENTREGADO OK"|"'.$receiver.'"'."\n";
                }
                elseif($shipment->status_id == ShippingStatus::IN_DISTRIBUTION_ID){
                    $statusFile.= $shipment->reference . '|"'.$agencia.'"|'.$eventDate.'|"ER"|'.$repartidor."|" .$eventDate2. '|'.$eventDate.'|"9993"|"EN REPARTO"|""'."\n";
                }
                elseif($shipment->status_id == ShippingStatus::INCIDENCE_ID){
                    $incidenceIds[] = $shipment->id;
                    $incidenceId    = $this->getIncidenceCode(@$shipment->lastHistory->incidence_id);
                    $incidenceName  = $this->getIncidenceName($incidenceId);
                    $obs            = str_replace(array("\r", "\n"), ' ', @$shipment->lastHistory->obs);
                    $obs            = str_replace('"', '', $obs);
                    $obs            = str_replace("'", '', $obs);
                    $statusFile.= $shipment->reference . '|"'.$agencia.'"|'.$eventDate.'|""|'.$repartidor.'|'.$eventDate2.'|'.$eventDate.'|"'.$incidenceId.'"|"' . $incidenceName . '"|"' . $obs . "\"\n";
                }

                if(!$ignoreAlreadySubmited) {
                    ShipmentHistory::where('id', @$shipment->lastHistory->id)
                        ->update(['submited_at' => $now]);

                    //coloca todos os estados anteriores do id 4 e 9 como sincronizados também
                    ShipmentHistory::where('shipment_id', @$shipment->id)
                        ->whereNull('submited_at')
                        ->whereIn('status_id', ['9', '4'])
                        ->where('id', '<', @$shipment->lastHistory->id)
                        ->update(['submited_at' => $now]);
                }
            }
        }


        //memoriza na pasta local ficheiro de estados
        $localFilename = null;
        if (!empty($statusFile)) {
            $filename = "IE" . $agencia . date('YmdHis') . ".txt";
            File::put(public_path('uploads/ftp_importer/sending/in/incidencias/' . $filename), $statusFile);
            File::put(public_path('uploads/ftp_importer/sending/enovo_backups/' . $filename), $statusFile);

            $localFilename = public_path('uploads/ftp_importer/sending/enovo_backups/' . $filename);
        }

        //memoriza na pasta local ficheiros de POD
        if (!empty($deliveredIds)) {
            $histories = ShipmentHistory::with('shipment')
                ->whereIn('shipment_id', $deliveredIds)
                ->where('status_id', ShippingStatus::DELIVERED_ID)
                ->get();

            foreach ($histories as $history) {

                $filename = @$history->shipment->reference . '.png';
                $filename = public_path('uploads/ftp_importer/sending/in/imagenes/' . $filename);

                try {
                    if($history->submited_at) {
                        if ($history->signature) {

                            if (str_contains($history->signature, 'png;')) {
                                $signature = str_replace('data:image/png;base64,', '', $history->signature);

                            } else {
                                $signature = str_replace('data:image/jpeg;base64,', '', $history->signature);
                            }

                            File::put($filename, base64_decode($signature));

                        } elseif ($history->filepath) {
                            File::put($filename, File::get(public_path($history->filepath)));
                        }
                    }

                } catch (\Exception $e) {
                    $trace = LogViewer::getTrace(null, 'SENDING: Imagem não comunicada [historyId = ' . $history->id . ']');
                    Log::error(br2nl($trace));
                }
            }
        }

        return $localFilename;
    }


    /**
     * Export pickups tracking history
     * @return array
     */
    public function exportTrackingsPickups($agencyCode, $date=null, $ignoreAlreadySubmited=false) {

        $deliveredIds  = [];
        $incidenceIds  = [];
        $statusFile    = '';

        $agencia    = $agencyCode;
        $customerId = @$this->sourceAgencies[$agencyCode];
        $eventDate  = empty($date) ? date('Y-m-d') : $date;
        $repartidor = $agencyCode."000";
        $now        = date('Y-m-d H:i:s');

        $shipments = Shipment::with('lastHistory')
            ->where(function($q){
                $q->where('status_id', ShippingStatus::PICKUP_DONE_ID);
                $q->orWhere('status_id', ShippingStatus::PICKUP_CONCLUDED_ID);
                $q->orWhere('status_id', ShippingStatus::PICKUP_FAILED_ID);
                $q->orWhere('status_id', ShippingStatus::INCIDENCE_ID);
                $q->orWhere('status_id', ShippingStatus::PICKUP_ACCEPTED_ID);
                $q->orWhere('status_id', ShippingStatus::PENDING_OPERATOR);
                $q->orWhere('status_id', ShippingStatus::SHIPMENT_PICKUPED);
            })
            ->where('is_collection', 1)
            ->where('provider_recipient_agency', $agencyCode)
            ->where('customer_id', $customerId)
            ->whereHas('lastHistory', function($q) use($eventDate, $ignoreAlreadySubmited) {
                $q->whereIn('status_id', [
                    ShippingStatus::PICKUP_DONE_ID,
                    ShippingStatus::PICKUP_CONCLUDED_ID,
                    ShippingStatus::PICKUP_FAILED_ID,
                    ShippingStatus::INCIDENCE_ID,
                    ShippingStatus::PICKUP_ACCEPTED_ID,
                    ShippingStatus::PENDING_OPERATOR,
                    ShippingStatus::SHIPMENT_PICKUPED,
                ]);

                if($ignoreAlreadySubmited) {
                    $q->whereRaw('date(status_date)="'.$eventDate.'"'); //comunica tudo da data indicada
                } else {
                    $q->whereNull('submited_at'); //só comunica estados que não tenham sido submetidos
                }
            })
            ->get(['id', 'tracking_code', 'provider_tracking_code', 'status_date', 'status_id', 'operator_id', 'reference', 'created_at']);

        foreach ($shipments as $shipment) {

            $eventDate  = new Date($shipment->status_date);
            $eventDate2 = $eventDate->format('dmy');
            $eventDate  = $eventDate->format('d/m/Y H:i:s');

            if(!empty($shipment)) {

                if($shipment->status_id == ShippingStatus::PICKUP_DONE_ID || $shipment->status_id == ShippingStatus::PICKUP_CONCLUDED_ID){
                    $deliveredIds[] = $shipment->id;
                    $statusFile.= $shipment->reference . '|"'.$agencia.'"|'.$eventDate.'|"E"|'.$repartidor.'|'.$eventDate.'|"9807"|"ENTREGADA OK"'."|\n";
                }elseif($shipment->status_id == ShippingStatus::PICKUP_ACCEPTED_ID || $shipment->status_id == ShippingStatus::PENDING_OPERATOR){
                    $deliveredIds[] = $shipment->id;
                    $statusFile.= $shipment->reference . '|"'.$agencia.'"|'.$eventDate.'|"ER"|'.$repartidor.'|'.$eventDate.'|"9993"|"ASIGNADA A RECOGEDOR"'."|\n";
                }
                elseif($shipment->status_id == ShippingStatus::INCIDENCE_ID || $shipment->status_id == ShippingStatus::PICKUP_FAILED_ID){
                    $incidenceIds[] = $shipment->id;
                    $incidenceId    = $this->getIncidenceCode(@$shipment->lastHistory->incidence_id, 1);
                    $incidenceName  = $this->getIncidenceName($incidenceId, 1);
                    $obs            = str_replace(array("\r", "\n"), ' ', @$shipment->lastHistory->obs);
                    $obs            = str_replace('"', '', $obs);
                    $obs            = str_replace("'", '', $obs);
                    $statusFile.= $shipment->reference . '|"'.$agencia.'"|'.$eventDate.'|""|'.$repartidor.'|'.$eventDate.'|"'.$incidenceId.'"|"' . $incidenceName . '"|"' . $obs . "\"\n";
                }


                if(!$ignoreAlreadySubmited) {
                    ShipmentHistory::where('id', @$shipment->lastHistory->id)
                        ->update(['submited_at' => $now]);
                }
            }
        }

        //memoriza na pasta local ficheiro de incidencias
        if(!empty($statusFile)) {
            $filename = "IR" . $agencia . date('YmdHis') . ".txt";
            File::put(public_path('uploads/ftp_importer/sending/in/incidencias_reco/' . $filename), $statusFile);
            File::put(public_path('uploads/ftp_importer/sending/enovo_backups/'.$filename), $statusFile);
        }

        //memoriza na pasta local ficheiros de POD
        if(!empty($deliveredIds)) {
            $histories = ShipmentHistory::with('shipment')
                ->whereIn('shipment_id', $deliveredIds)
                ->where('status_id', ShippingStatus::DELIVERED_ID)
                ->get();

            foreach ($histories as $history) {

                $filename = @$history->shipment->reference.'.png';
                $filename = public_path('uploads/ftp_importer/sending/in/imagenes/'.$filename);

                if($history->signature) {
                    $signature = str_replace('data:image/jpeg;base64,','', $history->signature);
                    File::put($filename, base64_decode($signature));
                } elseif($history->filepath) {
                    File::put($filename, File::get(public_path($history->filepath)));
                }
            }
        }

        return true;
    }

    /**
     * Export historico de rastreabilidade
     *
     * @return array
     */
    public function exportTraceability($agencyCode) {

        $agencia    = $agencyCode;
        $eventDate  = date('Y-m-d');
        $customerId = @$this->sourceAgencies[$agencia];


        $shipmentsTraceability = ShipmentTraceability::with('shipment')
            ->whereHas('shipment', function($q) use($customerId, $agencyCode) {
                $q->where('customer_id', $customerId);
                $q->where('provider_recipient_agency', $agencyCode);
                $q->where('is_collection', 0);
            })
            //->where('read_point', 'in')
            ->whereRaw('date(created_at)="'.$eventDate.'"')
            ->get();


        $fileContent = '';
        foreach ($shipmentsTraceability as $traceability) {

            $eventDateFormatted = new Date($traceability->created_at);
            $eventDateFormatted = $eventDateFormatted->format('d/m/Y H:i:s');

            if(!empty($traceability)) {
                $operationCode = '17'; //17 - llegadas portugal
                if($traceability->read_point == 'out') {
                    $operationCode = '92'; //92 - Salidas portal
                } elseif($traceability->read_point == 'supervisor') {
                    $operationCode = '508'; //508 - mercancia en armazem
                }

                $fileContent.= $traceability->barcode . '|'.$agencia.'|'.$operationCode.'|'.$eventDateFormatted."\n";
            }
        }

        //memoriza na pasta local ficheiro de leitura
        $localFilename = null;
        if(!empty($fileContent)) {
            $filename = "LB_".$agencia.date('YmdHis').".txt";
            File::put(public_path('uploads/ftp_importer/sending/in/lecturas/'.$filename), $fileContent);
            File::put(public_path('uploads/ftp_importer/sending/enovo_backups/'.$filename), $fileContent);

            $localFilename = public_path('uploads/ftp_importer/sending/enovo_backups/'.$filename);
        }


        //PROCESSA LEITURAS FALHADAS
        $traceabilityFails = DB::table('traceability_fails')
            ->whereRaw('date(created_at)="'.$eventDate.'"')
            ->groupBy('barcode')
            ->get();

        $fileContent = '';
        foreach ($traceabilityFails as $traceability) {

            $eventDateFormatted = new Date($traceability->created_at);
            $eventDateFormatted = $eventDateFormatted->format('d/m/Y H:i:s');

            if(config('app_source') == 'asfaltolargo') {
                $agencia = 610;
                if($traceability->operator_id == '697') {
                    $agencia=606;
                }
            }

            if(!empty($traceability) && strlen($traceability->barcode) > 6) { //delenext
                $fileContent.= $traceability->barcode . '|'.$agencia.'|17|'.$eventDateFormatted."\n";
            }
        }

        $localFilename = null;
        if(!empty($fileContent)) {
            $filename = "LB_".$agencia.date('YmdHis')."_fails.txt";
            File::put(public_path('uploads/ftp_importer/sending/in/lecturas/'.$filename), $fileContent);
            File::put(public_path('uploads/ftp_importer/sending/enovo_backups/'.$filename), $fileContent);
        }

        $this->storeFTP();

        return $localFilename;
    }

    /**
     * Export refunds
     * @return array
     */
    public function exportRefunds($agencyCode, $date=null) {

        $customerId = Customer::where('vat', 'A85508299')->first()->id;

        $date = $date ? $date : date('Y-m-d');

        $shipmentsRefunds = RefundControl::with('shipment')
            ->whereHas('shipment', function($q) use($customerId) {
                $q->where('customer_id', $customerId);
                $q->where('is_collection', 0);
            })
            ->where(function($q) use($date) {
                $q->where('payment_method', 'transfer');
                $q->where('payment_date', $date);
                $q->where('canceled', 0);
            })
            ->get();

        $fileContent = '';
        foreach ($shipmentsRefunds as $shipmentRefund) {

            $price = (float) @$shipmentRefund->shipment->charge_price;

            $date = new Date(@$shipmentRefund->payment_date);
            $date = $date->format('d/m/Y');

            $fileContent.= @$shipmentRefund->shipment->reference2 . ';'
                . @$shipmentRefund->shipment->provider_tracking_code . ';'
                . $date . ';'
                . $price . ';'
                . $price . ';'
                . $date . ";\n";
        }


        //memoriza na pasta local ficheiro de reembolsos
        $localFilename = null;
        if(!empty($fileContent)) {
            $filename = "reembolsos_enovo_" . $agencyCode . "_" . date('Ymd_His') . ".csv";
            File::put(public_path('uploads/ftp_importer/sending/in/reembolsos/' . $filename), $fileContent);
            File::put(public_path('uploads/ftp_importer/sending/enovo_backups/'.$filename), $fileContent);

            $localFilename = public_path('uploads/ftp_importer/sending/enovo_backups/'.$filename);
        }

        return $localFilename;
    }

    /**
     * Submete as pastas locais para o FTP
     *
     * @param $url
     * @param $xml
     * @return mixed
     */
    public function storeFTP($folder = null)
    {

        $publicPath = public_path('uploads/ftp_importer/sending/');

        if($folder) {
            $folders = [$folder]; //envia só para a pasta indicada
        } else {
            $folders = [
                'in/lecturas',
                'in/incidencias',
                'in/incidencias_reco',
                'in/reembolsos',
                'in/expediciones',
                'in/recogidas',
                'in/imagenes'
            ];
        }

        // FTP access parameters
        $host = $this->ftpHost;
        $user = $this->ftpUser;
        $pass = $this->ftpPass;

        // connect to FTP server
        try {
            $connectionId = ftp_connect($host);
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

        foreach ($folders as $folder) {

            $files = File::files($publicPath.$folder);

            foreach ($files as $file) {
                if(File::exists($file)) {
                    $remoteFile = $folder.'/'.basename($file);

                    // perform file upload
                    $upload = ftp_put($connectionId, $remoteFile, $file, FTP_BINARY);

                    // check upload status:
                    if ($upload) {
                        File::delete($file);
                    }
                }
            }
        }

        // close the FTP stream
        ftp_close($connectionId);

        return true;
    }

    /**
     * Atualiza estados de um envio
     *
     * @param collection $shipment
     */
    public function updateHistory($shipment) {

        /*$data = self::getEstadoEnvioByTrk($shipment);

        if($data) {

            $webserviceFatorM3 = 0;
            $webserviceWeight  = 0;
            unset($data['weight'], $data['fator_m3']);

            //sort status by date
            foreach ($data as $key => $value) {
                $date = $value['created_at'];
                $sort[$key] = strtotime($date);
            }
            array_multisort($sort, SORT_ASC, $data);

            foreach ($data as $key => $item) {

                if(empty($item['status_id'])) {
                    $item['status_id'] = 31; //aguarda expedição
                }

                $history = ShipmentHistory::firstOrNew([
                    'shipment_id'  => $shipment->id,
                    'obs'          => @$item['obs'],
                    //'incidence_id' => @$item['incidence_id'],
                    'created_at'   => @$item['created_at'],
                    'status_id'    => @$item['status_id']
                ]);

                $history->fill($item);
                $history->shipment_id = $shipment->id;
                $history->save();

                $history->shipment = $shipment;

                if($history->status_id == ShippingStatus::PICKUP_FAILED_ID) {
                    $price = $shipment->addPickupFailedExpense();
                    $shipment->walletPayment(null, null, $price); //discount payment
                }
            }

            try {
                $history->sendEmail(false,false,true);
            } catch (\Exception $e) {}

            //update shipment price
            $weightChanged = ($webserviceWeight > $shipment->weight || $webserviceFatorM3);
            if((hasModule('account_wallet') && $weightChanged && $shipment->ignore_billing)
                || ($weightChanged && empty($shipment->invoice_id)
                    && empty($shipment->ignore_billing)
                    && empty($shipment->is_blocked))) {

                $shipment->weight = $webserviceWeight > $shipment->weight ? $webserviceWeight : $shipment->weight;

                //$agencyId, $serviceId, $customerId, $providerId, $weight, $volumes, $charge = null, $volumeM3 = 0, $fatorM3 = 0, $zone = 'pt'
                $prices = Shipment::calcPrices($shipment);
                $oldPrice = $shipment->total_price;
                $shipment->fator_m3 = $webserviceFatorM3;
                $shipment->volumetric_weight  = @$prices['volumetricWeight'];
                $shipment->cost_price         = @$prices['cost'];
                $shipment->fuel_tax           = @$prices['fuelTax'];
                $shipment->extra_weight       = @$prices['extraKg'];

                if(!$shipment->price_fixed) {
                    $shipment->total_price = $prices['total'];
                    $shipment->fuel_tax    = $prices['fuel_tax'];
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

            $shipment->status_id   = $history->status_id;
            $shipment->status_date = $history->created_at;
            $shipment->save();
            return $history->status_id ? $history->status_id : true;
        }
        return false;*/
    }

    /**
     * Grava ou edita um envio
     *
     * @param type $shipment
     * @return boolean
     */
    public function saveShipment($shipment, $isCollection = false) {

        if(!in_array($shipment->recipient_country, ['pt', 'es'])) {
            throw new \Exception('Serviço indisponível para o país de destino.');
        }

        if($shipment->reference3 && str_contains($shipment->reference3, '#DEV#')) { //envios originais sending
            $parts                = explode('#',$shipment->reference3);
            $serviceId            = '99'; //serviço devolução
            $providerCustomerCode = @$parts[2];
            $providerPickupCode   = '';
            $reference            = $shipment->reference2;
            $providerOriginalTrk  = @$parts[3]; //codigo envio original
        } elseif($shipment->reference3 && str_contains($shipment->reference3, '#')) { //envios originais sending
            $parts                = explode('#',$shipment->reference3);
            $serviceId            = @$parts[0];
            $providerCustomerCode = @$parts[1];
            $providerPickupCode   = str_replace('PICKTRK', '', @$parts[2]);
            $reference            = $shipment->reference2;
            $providerOriginalTrk  = '';
        } else { //envios criados de limpo
            $serviceId            = $this->getProviderService($shipment);
            $reference            = $shipment->reference;
            $providerCustomerCode = $this->shippingCustomer ?? $shipment->provider_cargo_agency.'00001-01'; //ex: 61300001-01
            $providerPickupCode   = '';
            $providerOriginalTrk  = '';

            if(config('app.source') == 'asfaltolargo') {
                $providerCustomerCode = '61000002-01'; //ex: 61300001-01
            } elseif(config('app.source') == 'tortugaveloz') {
                $providerCustomerCode = '60200002-01'; //ex: 61300001-01
            }

            $shipment->provider_recipient_agency = $this->getAgencyFromZipCode($shipment->recipient_zip_code, $shipment->recipient_country);
        }

        $zonaEntrega = $this->getZonaEntrega($shipment->recipient_zip_code, $shipment->recipient_country);

        $data = $shipment->toArray();
        $data['date']                   = new Date($data['date']);
        $data['date']                   = $data['date']->format('d/m/Y');
        $data['delivery_zone']          = $this->getCountryCode($data['sender_country'], true);
        $data['sender_country']         = $this->getCountryCode($data['sender_country'], true);
        $data['recipient_country']      = $this->getCountryCode($data['recipient_country'], true);
        $data['provider_tracking_code'] = $this->getSendingTracking($data['id']);
        $data['delivery_zone']          = @$zonaEntrega['code'];
        $data['weight']                 = $shipment->weight ? ceil($shipment->weight) : '0';
        $data['fator_m3']               = $shipment->fator_m3 ? (float) number($shipment->fator_m3) : '0.00';
        $data['cod']                    = $shipment->payment_at_recipient ? "D" : "P";
        $data['charge_price']           = $shipment->charge_price ? (float) $shipment->charge_price : '0';
        $data['service']                = $serviceId;
        $data['reference']              = $reference;
        $data['provider_customer']      = $providerCustomerCode;
        $data['provider_pickup_code']   = $providerPickupCode;
        $data['provider_original_trk']  = $providerOriginalTrk;

        $shipment->has_return = empty($shipment->has_return) ? array() : $shipment->has_return;

        //return pack
        $data['rpack'] = 'N';
        if($shipment->has_return && in_array('rpack', $shipment->has_return)) {
            $data['rpack'] = 'S';
        }

        //if(!$shipment->provider_tracking_code) {
        return $this->storeEnvio($data, @$data['is_collection']);
        //}

        return $shipment->provider_tracking_code;
    }


    /**
     * Retorna o codigo do envio na sending
     * @param $shipmentId
     * @return string
     */
    public function getSendingTracking($shipmentId) {
        //'6010xxxxxxx';
        $trk = $this->agencia.'0'.str_pad($shipmentId, 8, '0', STR_PAD_LEFT);

        return $trk;
    }

    /**
     * Create barcode
     * @param $shipment
     * @param $volume
     * @return string
     */
    public function getBarcode($shipment, $volume, $zonaEntrega, $service=null) {

        //formato
        //numero envio (12 digitos)
        //agencia origem (3 digitos)
        //agencia destino (3 digitos)
        //zona reparto (3 digitos)
        //tipo serviço (2 digitos)
        //numero volume (3 digitos)


        //numero do envio
        $barcode = $shipment->provider_tracking_code;

        //Delegação origem
        $barcode.= $shipment->provider_sender_agency;

        //Delegação destino
        $barcode.= $shipment->provider_recipient_agency;

        //Zona de Entrega (3 digigos)
        $barcode.= $zonaEntrega;

        //Tipo Serviço (2 digitos)
        $barcode.= $service;

        //Nº de volume
        $barcode.= str_pad($volume, 3, '0', STR_PAD_LEFT);

        return $barcode;
    }


    /**
     * Get service Name
     * @param $sendingServiceCode
     * @return string
     */
    public function getAgencyFromFilename($filename)
    {

        //formatos do nome => ficheiro envios
        //S280601_20220527_054609.xml
        //Sendo S indica que e uma expedição de SAIDA, com origem agência 280 (Madrid) e destino 601 (LISBOA).

        //formatos do nome => ficheiro recolhas
        //RECO_080601_20220428_143004.xml

        //obtem so o nome do ficheiro a partir do URL do ficheiro
        $filename = basename($filename);

        $type = substr($filename, 0, 1); //obtem a primeira letra do ficheiro

        //valores por defeito
        $agency = [
            'sender'    => null,
            'recipient' => null,
            'type'      => null
        ];

        //se o ficheiro começa por "S" é do tipo envio
        if($type == 'S') {
            $origin = substr($filename, 1, 3); //obtem agencia de expedicao
            $dest   = substr($filename, 4, 3); //obtem agencia de destino

            $agency = [
                'sender'    => $origin,
                'recipient' => $dest,
                'type'      => 'S'
            ];

        } elseif($type == 'R') {

            $origin = substr($filename, 5, 3); //obtem agencia de expedicao
            $dest   = substr($filename, 8, 3); //obtem agencia de destino

            $agency = [
                'sender'    => $origin,
                'recipient' => $dest,
                'type'      => 'R'
            ];
        } elseif($type == 'H') {

            $origin = substr($filename, 1, 3); //obtem agencia de expedicao
            $dest   = substr($filename, 4, 3); //obtem agencia de destino

            $agency = [
                'sender'    => $origin,
                'recipient' => $dest,
                'type'      => 'H'
            ];
        }


        return $agency;
    }

    /**
     * Get service Name
     * @param $sendingServiceCode
     * @return string
     */
    public function getServiceName($sendingServiceCode){

        $services = [
            '01' => 'Send Expres',
            '02' => 'Send Top 10h',
            '03' => 'Send Sectorial',
            '08' => 'Send Ecommerce',
            '10' => 'Send Masivo',
            '18' => 'Send Maritimo',
            '40' => 'Send Optica',
            '99' => 'Devoluciones'
        ];

        return @$services[$sendingServiceCode];
    }

    public function getCountryCode($countryCode, $enovo2Sending=false) {

        $codes = [
            '035' => 'pt',
            '034' => 'es'
        ];

        if($enovo2Sending) {
            $sendingCountries = array_flip($codes);
            return @$sendingCountries[$countryCode];
        } else {
            return @$codes[$countryCode];
        }

    }

    public function convertService2Enovo($serviceCode, $allServices, $isPickup = false) {

        $services = [
            '01' => '24H', //Send Exprés
            '02' => '10H', //Send Top 10h
            '03' => '24H', //Send Sectorial
            '08' => '24H', //Send Ecommerce
            '10' => '72H', //Send Masivo
            '18' => 'MI', //Send Maritimo
            '40' => '24H', //Send Optica
            '99' => 'DEV' //Devolução
        ];

        $serviceCode = @$services[$serviceCode];

        $service = @$allServices[$serviceCode];

        if($serviceCode == '40') {
            if(config('app.source') == 'asfaltolargo') {
                $service = '636';
            } elseif(config('app.source') == 'jamelao') {
                $service = '10';
            } elseif(config('app.source') == 'fozpost') {
                //$service = '';
            } elseif(config('app.source') == 'ventostranquilos') {
                //$service = '';
            } elseif(config('app.source') == 'mrctransportes') {
                //$service = '';
            }
        }

        return $service;
    }

    /**
     * @param $shipment
     * @return string
     */
    public function convertEnovo2Sending($shipment) {

        return '01';
    }

    /**
     * Get zona de entrega
     * @param $zipCode
     * @param $country
     * @return mixed
     */
    public function getZonaEntrega($zipCode, $country) {

        if($country == 'pt') {
         
            $zipCodes = explode('-', $zipCode);

            if(isset($zipCodes[1])) {
                $zipCode = $zipCodes[0].$zipCode[1];
            } else {
                $zipCode = '0'.$zipCode[0]; //se só forem os 4 digitos, temos de acrescentar um 0 antes
            }
        }

        $zone = DB::connection('mysql_core')
            ->table('sending_zip_codes')
            ->where('zip_code', $zipCode)
            ->where('country', $country)
            ->first();

        return [
            'code' => str_pad(@$zone->route, 3, '0', STR_PAD_LEFT),
            'name' => @$zone->route ? str_pad(@$zone->route, 3, '0', STR_PAD_LEFT) : 'MUEL'
        ];
    }

    /**
     * Get agency name
     * @param $sendingServiceCode
     * @return string
     */
    public function getAgencyName($agencyCode){

        $agency = DB::connection('mysql_core')
            ->table('sending_agencies')
            ->where('code', $agencyCode)
            ->first();

        return @$agency->name;
    }

    /**
     * Get incidence code
     *
     * @param $incidenceCode
     * @param false $isPickup
     * @return string
     */
    public function getIncidenceCode($incidenceCode, $isPickup=false) {

        if($isPickup) {
            $incidences = [
                '27' => '8101',//	NO TIENEN MERCANCIA
                '1' => '8103',//	AUSENTE
                '19' => '8106',//	NO DAN RETORNO
                //'' => '8107',//	DEVUELVEN MISMA VALIJA
                //'' => '8108',//	PASAR RECOGIDA A PENDIENTE
                '16' => '8501',//	DOMICILIO DESCONOCIDO
                '2' => '8503',//	CERRADO POR VACACIONES
                //'' => '8504',//	EMPRESA DESAPARECIDA
                '2'  => '8505',//	CERRADO EN HORAS DE COMERCIO
                '26' => '8506',//	RECOGIDA DUPLICADA
                '31' => '8507',//	NO DIO TIEMPO A REALIZAR LA RECOGIDA
                //'' => '8509',//	REQUIERE MEDIOS ADICIONALES
                '25' => '8510',//	NO LO TIENE PREPARADO
            ];
        } else {
            $incidences = [
                '8'  => '1102',//	FALTAN BULTOS
                '30' => '2101',//	AVERIA EN RUTA REPARTO
                '5'  => '3101',//	MERCANCIA ROBADA
                '13' => '4102',//	NO ACEPTAN MERCANCIA
                '24' => '4103',//	NO ACEPTAN P.D/REEMBOLSOS
                //'' => '4105',//	DEVOLUCION P.O. CLIENTE
                '3' => '5101',//	DIRECCION INCORRECTA
                '9'  => '5102',//	FALTAN DATOS ENTREGA
                //'' => '5103',//	CODIGO POSTAL INCORRECTO
                //'' => '5104',//	EMPRESA DESAPARECIDA
                '16' => '5105',//	DESCONOCIDO DESTINATARIO
                //'' => '5106',//	CAMBIO DE DOMICILIO
                '1'  => '6101',//	AUSENTE
                //'1' => '6102',//	AUSENTE POR SEGUNDA VEZ
                //'1' => '6106',//	AUSENTE POR TERCERA VEZ
                //'1' => '6107',//	AUSENTE REITERADO
                '2'  => '6205',//	CERRADO EN HORAS DE COMERCIO
                '10' => '7101',//	FESTIVO
                '6'  => '7202',//	ENTREGA CONCERTADA PARA :
                '15' => '7202',//   ENTREGA CONCERTADA PARA
                //'' => '7301',//	PASARAN A RECOGER
                '31' => '9301' //FALTA TEMPO PARA ENTREGAR
            ];
        }

        return @$incidences[$incidenceCode] ? @$incidences[$incidenceCode] : 9300;
    }

    public function getIncidenceName($incidenceCode, $isPickup=false) {

        if($isPickup) {
            $incidences = [
                '8101' => 'NO TIENEN MERCANCIA',//	NO TIENEN MERCANCIA
                '8103' => 'AUSENTE',//	AUSENTE
                '8106' => 'NO DAN RETORNO',//	NO DAN RETORNO
                '8107' => 'DEVUELVEN MISMA VALIJA',//	DEVUELVEN MISMA VALIJA
                '8108' => 'PASAR RECOGIDA A PENDIENTE',//	PASAR RECOGIDA A PENDIENTE
                '8501' => 'DOMICILIO DESCONOCIDO',//	DOMICILIO DESCONOCIDO
                '8503' => 'CERRADO POR VACACIONES',//	CERRADO POR VACACIONES
                '8504' => 'EMPRESA DESAPARECIDA',//	EMPRESA DESAPARECIDA
                '8505' => 'CERRADO EN HORAS DE COMERCIO',//	CERRADO EN HORAS DE COMERCIO
                '8506' => 'RECOGIDA DUPLICADA',//	RECOGIDA DUPLICADA
                '8507' => 'NO DIO TIEMPO A REALIZAR LA RECOGIDA',//	NO DIO TIEMPO A REALIZAR LA RECOGIDA
                '8509' => 'REQUIERE MEDIOS ADICIONALES',//	REQUIERE MEDIOS ADICIONALES
                '8510' => 'NO LO TIENE PREPARADO',//	NO LO TIENE PREPARADO
            ];
        } else {
            $incidences = [
                '1102' => 'FALTAN BULTOS',
                '2101' => 'AVERIA EN RUTA REPARTO',
                '3101' => 'MERCANCIA ROBADA',
                '4102' => 'NO ACEPTAN MERCANCIA',//	NO ACEPTAN MERCANCIA
                '4103' => 'NO ACEPTAN P.D/REEMBOLSOS',//	NO ACEPTAN P.D/REEMBOLSOS
                '4105' => 'DEVOLUCION P.O. CLIENTE',//	DEVOLUCION P.O. CLIENTE
                '5101' => 'DIRECCION INCORRECTA',//	DIRECCION INCORRECTA
                '5102' => 'FALTAN DATOS ENTREGA',//	FALTAN DATOS ENTREGA
                '5103' => 'CODIGO POSTAL INCORRECTO',//	CODIGO POSTAL INCORRECTO
                '5104' => 'EMPRESA DESAPARECIDA',//	EMPRESA DESAPARECIDA
                '5105' => 'DESCONOCIDO DESTINATARIO',//	DESCONOCIDO DESTINATARIO
                '5106' => 'CAMBIO DE DOMICILIO',//	CAMBIO DE DOMICILIO
                '6101' => 'AUSENTE',//	AUSENTE
                '6102' => 'AUSENTE POR SEGUNDA VEZ',//	AUSENTE POR SEGUNDA VEZ
                '6106' => 'AUSENTE POR TERCERA VEZ',//	AUSENTE POR TERCERA VEZ
                '6107' => 'AUSENTE REITERADO',//	AUSENTE REITERADO
                '6205' => 'CERRADO EN HORAS DE COMERCIO',//	CERRADO EN HORAS DE COMERCIO
                '7101' => 'FESTIVO',//	FESTIVO
                '7202' => 'ENTREGA CONCERTADA PARA',//	ENTREGA CONCERTADA PARA :
                '7301' => 'PASARAN A RECOGER',//	PASARAN A RECOGER
                '9301' => 'FALTA DE TEMPO PARA ENTREGA - PT',
            ];
        }

        return @$incidences[$incidenceCode] ? @$incidences[$incidenceCode] : "";
    }

    /**
     * Get provider service
     *
     * @param $shipment
     */
    public function getProviderService($shipment) {

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
            if($serviceKey != 'pt' && $serviceKey != 'es') {
                $serviceKey = 'int';
            }

            $providerService = @$webserviceConfigs->mapping_services[$shipment->service_id][$serviceKey];

            //se não encontrou codigo de serviço, tenta obter os dados default
            //a partir do ficheiro estático de sistema
            if(!$providerService) {
                $mapping = config('shipments_export_mapping.sending-services');
                $providerService = $mapping[$shipment->service->code];
            }

        } catch (\Exception $e) {}

        if(!$providerService) {
            throw new \Exception('O serviço ' . $shipment->service->code . ' não tem correspondência com nenhum serviço Sending.');
        }

        return $providerService;
    }

    /**
     * Obtem o Codigo da agencia a partir do codigo postal e pais
     * @param $zipCode
     * @param $country
     * @return mixed
     */
    public function getAgencyFromZipCode($zipCode, $country) {

        if($country == 'pt') {
            $zipCode = explode('-', trim($zipCode));
            $zipCode = '0'.$zipCode[0];
        }

        $zipCode = \DB::connection('mysql_core')
            ->table('sending_zip_codes')
            ->where('zip_code', $zipCode)
            ->where('country', $country)
            ->first();

        return @$zipCode->agency;
    }
}