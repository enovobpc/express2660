<?php

namespace App\Console\Commands;

use App\Models\BroadcastPusher;
use App\Models\Customer;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use App\Models\ShippingExpense;
use App\Models\ShippingStatus;
use App\Models\RefundControl;
use Illuminate\Console\Command;
use phpseclib\Net\SFTP;
use File, Mail, Date, Setting;


class SyncEtcp extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:etcp {action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync data with ETCP';

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
        
        $this->info("Sync data with ETCP");

        if($this->argument('action') == 'import') {
            $this->importServices();
        } elseif ($this->argument('action') == 'cod') {
            $this->comunicateCod();
        } else {
            $this->comunicateStatus();
        }


        $this->info("Sync finalized");
        return;
    }

    public function importServices() {

        if(env('APP_ENV') == 'local') {
            $customer  = Customer::first();
            $serviceId = 201;
        } else {
            $customerEtcp = Customer::find(11034);
            $customerTV   = Customer::find(11765);
            $serviceId    = 747;
        }

        // FTP access parameters
        $host   = '83.240.175.81';
        $user   = 'sap_ctempo';
        $pass   = 'ruovaNJ04s';
        $remoteFolder = '/TRANSP/CTEMPO/OUT';
        $localFolder  = storage_path('ftp_importer/etcp');

        $sftp = new SFTP($host);
        $sftp->login($user, $pass);
        $files = $sftp->nlist($remoteFolder);

        if($files) {
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    $remoteFile = $remoteFolder . '/' . $file;
                    $localFile = $localFolder . '/' . $file;
                    $sftp->get($remoteFile, $localFile); //download from remote
                    $sftp->delete($remoteFile);
                }
            }
        }

        $files    = File::files($localFolder);

        if($files) {

            foreach ($files as $shipmentsFile) {

                $headerKeys = [
                    'date',                 //DataServiço
                    'provider_tracking_code', //Nº transporte
                    'reference2',           //Remessa
                    'object_trk',           //Objecto
                    'reference3',           //MacroServiço
                    'service',              //Tipo de Serviço
                    'sender_name',          //Expedidor
                    'recipient_name',       //Destinatário
                    'recipient_address',    //Morada
                    'recipient_city',       //Localidade
                    'recipient_zip_code',   //Cód. Postal
                    'city',                 //Localidade Postal
                    'recipient_phone',      //ContactoDestino
                    'weight',               //Peso (Kg)
                    'weight_unity',         //Unidade Peso
                    'volumes',              //Nº Volumes
                    'desconhecido1',
                    'fator_m3',             //Cubicagem (m3)
                    'vol_type',             //Tipo de Volumes
                    'charge_price',         //Cobrança
                    'source_id',            //Codigo de Origem
                    'field01',              //Outros 1
                    'field02',              //Outros 2
                    'field03',              //Outros 3
                    'field04',              //Outros 4
                ];

                //READ FILE
                $file = fopen($shipmentsFile, "r");

                $fileData = [];
                while(!feof($file)) {
                    $line = utf8_encode(fgets($file));
                    $line = explode('§', $line);

                    $row = [];
                    foreach ($headerKeys as $pos => $key) {
                        $value = trim(@$line[$pos]);
                        if ($key == 'weight') {
                            $value = str_replace(',', '.', $value);
                            $value = (float) $value;
                            $value = $value == 0.00 ? 1 : $value;
                        } elseif($key == 'charge_price' || $key == 'fator_m3') {
                            $value = str_replace(',', '.', $value);
                            $value = (float) $value;
                        } elseif ($key == 'date') {
                            if (!empty($value)) {
                                $value = date_create_from_format('d-m-Y', $value);
                                $value = date_format($value, 'Y-m-d');
                            }
                        }

                        $row[$key] = $value;
                    }

                    if(!empty($row['provider_tracking_code'])) {
                        $fileData[] = $row;
                    }
                }

                fclose($file);

                //PROCESS LINES
                $shipmentsToImport = [];
                $objects = [];
                foreach ($fileData as $key => $shipmentRow) {

                    $trk = @$shipmentRow['provider_tracking_code'];
                    if(@$shipmentsToImport[$trk]) { //se existe já trk soma os volumes
                        $shipmentsToImport[$trk]['volumes']++;
                        $objects[$trk][] = $shipmentsToImport[$trk]['object_trk'];
                    } else {
                        $shipmentsToImport[$trk] = $shipmentRow;
                        $objects[$trk][] = $shipmentsToImport[$trk]['object_trk'];
                    }
                }

                //IMPORT SHIPMENTS
                foreach ($shipmentsToImport as $row) {

                    $shipment = Shipment::firstOrNew([
                        'reference' => $row['provider_tracking_code']
                    ]);

                    $shipmentExists = $shipment->exists;

                  
                    if(@$row['service'] == 'STV_50') {
                        $serviceId = '767';
                        $customer = $customerTV;
                    } elseif(@$row['service'] == 'STV_65') {
                        $serviceId = '768';
                        $customer = $customerTV;
                    } elseif(@$row['service'] == 'STV_CO') {
                        $serviceId = '769';
                        $customer = $customerTV;
                    } elseif(@$row['service'] == 'STV_43') {
                        $serviceId = '770';
                        $customer = $customerTV;
                    } elseif(@$row['service'] == 'STV_49') {
                        $serviceId = '771';
                        $customer = $customerTV;
                    } elseif(@$row['service'] == 'STV_55') {
                        $serviceId = '772';
                        $customer = $customerTV;
                    } elseif(@$row['service'] == 'STV_60') {
                        $serviceId = '773';
                        $customer = $customerTV;
                    } elseif(@$row['service'] == 'STV_70') {
                        $serviceId = '774';
                        $customer = $customerTV;
                    } elseif(@$row['service'] == 'STV_75') {
                        $serviceId = '775';
                        $customer = $customerTV;
                    } else {
                        $serviceId = 747; //serviço por defeito
                        $customer  = $customerEtcp;
                    }

                    unset($row['reference2']);
                    $shipment->fill($row);
                    $shipment->reference        = $shipment->provider_tracking_code;
                    $shipment->provider_tracking_code = null;
                    $shipment->customer_id      = $customer->id;
                    $shipment->agency_id        = $customer->agency_id;
                    $shipment->sender_agency_id = $customer->agency_id;
                    $shipment->recipient_agency_id = $customer->agency_id;
                    $shipment->provider_id      = Setting::get('shipment_default_provider');
                    $shipment->service_id       = $serviceId;
                    $shipment->recipient_country= 'pt';
                    $shipment->billing_date     = @$row['date'];
                    $shipment->sender_name      = $customer->name;
                    $shipment->sender_address   = $customer->address;
                    $shipment->sender_zip_code  = $customer->zip_code;
                    $shipment->sender_city      = $customer->city;
                    $shipment->sender_country   = $customer->country;
                    $shipment->sender_phone     = $customer->mobile;
                    $shipment->start_hour       = '18:00';
                    $shipment->end_hour         = '22:00';

                    if(!$shipmentExists) {
                        $shipment->status_id = ShippingStatus::PENDING_ID;
                    }

                    $shipment->setTrackingCode();

                    //add Price
                    if(!$shipment->price_fixed) {
                        $prices = Shipment::calcPrices($shipment);
                        $shipment->cost_price  = $prices['cost'];
                        $shipment->total_price = $prices['total'];
                        $shipment->total_expenses = $prices['totalExpenses'];
                        $shipment->zone = $prices['zone'];
                        $shipment->save();
                    }

                    /**
                     * Store adicional services
                     */
                    $allExpenses = null;
                    $input = null;
                    if (!empty($shipment->charge_price)) {

                        $allExpenses = ShippingExpense::filterSource()
                            ->get(['id', 'code', 'name', 'price', 'zones', 'type']);

                        $input['complementar_services'][] = Shipment::getChargeExpense($allExpenses);
                    }

                    Shipment::assignExpenses($shipment, $input, $allExpenses);


                    if(!$shipmentExists) {
                        $history = new ShipmentHistory();
                        $history->status_id   = $shipment->status_id;
                        $history->agency_id   = $shipment->agency_id;
                        $history->shipment_id = $shipment->id;
                        $history->api         = 1;
                        $history->save();
                    }

                    /**
                     * Set notification
                     */
                    if(!$shipmentExists) {
                        $shipment->setNotification(BroadcastPusher::getGlobalChannel());
                    }
                }

                File::delete($shipmentsFile);
            }
        }
    }


    public function comunicateStatus() {

        //$user     = 'WS_CTEMPO';
        //$password = 'zJlBlkLQL3C8kGRY66Af';
        $user     = 'WS_TRACKTRACE';
        $password = 'PT@!mj.1';
        $sessionHash = base64_encode($user.':'.$password);

        $date = date('Y-m-d');
        //$date = '2023-08-18';

        $allStatus = Shipment::with('last_history')
            ->whereHas('last_history', function($q) use($date) {
                $q->whereBetween('created_at', [$date.' 00:00:00', $date.' 23:59:59']);
            })
            ->whereIn('customer_id', [11034])
            //->where('id', 781588)
            ->get();
            
            //dd($allStatus->pluck('reference')->toArray());

        $xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:uno="http://DTH/XI/UNO">
           <soapenv:Header/>
           <soapenv:Body>
              <uno:ZCOMSD_PI_TRACKTRACE_TRANSPRequest>';

        $allowedStatus = [
            1,2,3,4,5,7,8,9,36
        ];

        foreach ($allStatus as $shipment) {

            foreach ($shipment->history as $history) {

                if(in_array($history->status_id, $allowedStatus)) {
                    $datetime = new \Jenssegers\Date\Date(@$history->created_at);
                    //$datetime = new \Jenssegers\Date\Date(@$shipment->last_history->created_at);

                    $data = [
                        'empresa' => 'MEO', // TMN ou PTC
                        'acronimo' => 'CORRIDADOTEMPO', // HIER2
                        'provider_trk' => $shipment->reference,
                        'volume_trk' => $shipment->reference,
                        'date' => $datetime->format('Y-m-d'),
                        'hour' => $datetime->format('H:i:s'),
                        'status' => $this->mappingStatus($history->status_id),
                        'incidence' => $shipment->status_id == 9 ? $this->mappingIncidence(@$history->incidence_id) : '',
                        'receiver' => $shipment->status_id == 5 ? @$history->receiver : ''
                        //'status'        => $this->mappingStatus($shipment->status_id),
                        //'incidence'     => $shipment->status_id == 9 ? $this->mappingIncidence(@$shipment->last_history->incidence_id) : '',
                        //'receiver'      => $shipment->status_id == 5 ? @$shipment->last_history->receiver : ''
                    ];

                    $xml .= '<ITEM>
                    <EMPRESA>' . $data['empresa'] . '</EMPRESA>
                    <HIER2>' . $data['acronimo'] . '</HIER2>
                    <VBELN>' . $data['volume_trk'] . '</VBELN>
                    <TRACKN>' . $data['provider_trk'] . '</TRACKN>
                    <DATUM>' . str_replace('-', '', $data['date']) . '</DATUM>
                    <HORA>' . str_replace(':', '', $data['hour']) . '</HORA>
                    <ESTFI>' . $data['status'] . '</ESTFI>
                    <MOTIV>' . $data['incidence'] . '</MOTIV>
                    <RECEB>' . $data['receiver'] . '</RECEB>
                 </ITEM>';
                }
            }

        }


        $xml.= '</uno:ZCOMSD_PI_TRACKTRACE_TRANSPRequest>
           </soapenv:Body>
        </soapenv:Envelope>';


        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://po.telecom.pt/XISOAPAdapter/MessageServlet?channel=:TrackTrace:DTHZCOMSD_PI_TRACKTRACE_TRANSP',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $xml,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . $sessionHash,
                'Content-Type: application/xml',
            ),
        ));

        $response = curl_exec($curl);
        
        curl_close($curl);

        echo $response;
    }
    
    
    public function comunicateCod() {

        //$date = date('Y-m-d');

       $allRefunds = RefundControl::with('shipment')
            ->whereHas('shipment', function($q) {
                $q->where('customer_id', 11034);
            })
            //->where('shipment_id', 793065)
            ->whereNull('submited_at')
            //->whereBetween('submited_at', [$date.' 00:00:00', $date.' 23:59:59'])
            ->get();
            
        $date = date('dmy');
        $docTotal = 0;
        $lines = "";
        $countLines = 2; //header e footer tambem contam como linhas
        foreach($allRefunds as $refund) {

            $shipment = @$refund->shipment;
            $price    = @$shipment->charge_price;
            $docTotal+= $price;
                   
            //preço = 8 digitos (zeros a esquerda e sem virgula decimal);
            $price = str_replace('.', '', $price);
            $price = str_pad($price, 8, '0', STR_PAD_LEFT);
            
            //codigo transporte = 21 posições. 16 antes e 5 após
            $etcpTrk = str_pad($shipment->reference3, 16, '0', STR_PAD_LEFT);
            $etcpTrk = $etcpTrk.'00000';

            $checkDigitPrice = $this->calcCheckDigitPrice($price);
            $checkDigitTransport = $this->calcCheckDigitTransport($etcpTrk);

            $lines.= "00000000";//8 digitos (sempre 0)
            $lines.= $price; //8 digitos
            $lines.= $checkDigitPrice; //1 digito
            $lines.= $etcpTrk; //21 digitos
            $lines.= $checkDigitTransport; // 1 digito
            $lines.= '0'; //1 digito (sempre 0)
            $lines.= $date."\n";

            $countLines++;
        }


        $docTotal   = str_replace('.', '', $docTotal);
        $docTotal   = str_pad($docTotal, 8, '0', STR_PAD_LEFT);
        $countLines = str_pad($countLines, 6, '0', STR_PAD_LEFT);

        //prepara conteúdo ficheiro
        $fileContent = "00000".$date."00000000000000000000000000000000000\n"; //header
        $fileContent.= $lines;
        $fileContent.= "99999".$countLines."0000".$docTotal."09999999999999999999999\n"; //footer
        //dd($fileContent);
        
        if (!File::exists(public_path('uploads/tmp_files'))) {
            File::makeDirectory(public_path('uploads/tmp_files'));
        }

        $filename = 'Receb_ETCP_CDT_'.date('Ymd').'.txt';
        $filepath = public_path('uploads/tmp_files/'.$filename);
        file_put_contents($filepath, $fileContent);

        $this->submit2FTP_COD($filename);
    }


    public function calcCheckDigitPrice($value) {
        
        $number = (string) $value;
        
        $pesos  = array(6,5,4,3,2,7,6,5,4,3,2);
        $pesos  = array_reverse($pesos);

        $sum = 0;
        for ($i = strlen($number) -1; $i >= 0; $i--) {
            $sum += $number[$i] * $pesos[$i];
        }
        
        $remaining = $sum % 11;
        $result    = 11 - $remaining;
  
        if ($result == 11) {
            return 0;
        } elseif ($result == 10) {
            return "X";
        }
        
        return $result;
    }
    
    public function calcCheckDigitTransport($value) {
        
        $number = (string) $value;
        $pesos  = array(4,3,2,7,6,5,4,3,2,7,6,5,4,3,2,7,6,5,4,3,2);

        $sum = 0;
        for ($i = strlen($number) -1; $i >= 0; $i--) {
            $sum += $number[$i] * $pesos[$i];
        }
        
        $remaining = $sum % 11;
        $result    = 11 - $remaining;
  
        if ($result == 11) {
            return 0;
        } elseif ($result == 10) {
            return "X";
        }
        
        return $result;
    }



/**
     * @return bool
     * @throws \Exception
     */
    public function submit2FTP_COD($filename) {
        // FTP access parameters
        $host = '83.240.175.81';
        $user = 'sap_ctempo';
        $pass = 'ruovaNJ04s';
        $port = '21';
       
        $localFile  = public_path('uploads/tmp_files/'.$filename);
        $remoteFile = '/TRANSP/CTEMPO/IN/'.$filename;
        
        $sftp = new SFTP($host);
        $sftp->login($user, $pass);

        $upload = $sftp->put($remoteFile, $localFile);

        if(!$upload) {
            return false;
        } else {
            File::delete($localFile);
        }

        return true;
    }


    /**
     * @return bool
     * @throws \Exception
     */
    public function storeFile() {
        // FTP access parameters
        $host = '83.240.175.81';
        $user = 'sap_ctempo';
        $pass = 'ruovaNJ04s';
        $port = '21';

        //file to move:
        $ftpPath = "API/uploads/importa.txt";


        $sftp = new SFTP($host);
        $sftp->login($user, $pass);
        $upload = $files = $sftp->put($ftpPath, $localFile);

        // check upload status:
        if(!$upload) {
            return false;
        }

        // close the FTP stream
        ftp_close($connectionId);

        return true;
    }





    public function mappingStatus($statusID) {

        if(in_array($statusID, [1,2,15,13,16,22])) {
            $status = 'EMB'; //recepcionado
        }

        else if(in_array($statusID, [9])) {
            $status = 'EMH'; //INCIDENCIA
        }

        else if(in_array($statusID, [8])) {
            $status = 'EMC'; //Anulado
        }

        else if(in_array($statusID, [7])) {
            $status = 'EMC'; //DEVOLVIDO
        }

        else if(in_array($statusID, [5, 12, 14])) {
            $status = 'EMI'; //ENTREGUE
        }

        else if(in_array($statusID, [9])) {
            $status = 'EMH'; //Entrega não Conseguida
        }

        else if(in_array($statusID, [43])) {
            $status = 'EMW'; //Avisado na Estação
        }

        else {
            $status = 'EMZ'; //Em Distribuição
        }

        return $status;
    }


    public function mappingIncidence($incidenceId) {

        $incidences = [
            '' => '22', //	EM DEVOLUÇÃO
            '3' => '10', // ENDEREÇO INCORRECTO
            '16' => '11',// DIFICULDADES EM LOCALIZAR DESTINATÁRIO
            '1' => '12', //	DESTINATÁRIO AUSENTE
            '13' => '13',// RECUSADO
            '' => '131', //	CLIENTE DIZ NÃO TER ENCOMENDADO
            '' => '132', //	CLIENTE SOLICITA SERVIÇO DE INSTALAÇÃO
            '' => '133', //CLIENTE NÃO TEM INDICAÇÃO DE PAGAMENTO NA ENTREGA
            '' => '134', //	RECUSADO (PARCIALMENTE)
            '' => '135', //	RECUSADO (DESISTIU DA COMPRA)
            '' => '136',// RECUSADO (PEDIDO DUPLICADO)
            '' => '137',//	RECUSADO (NÃO ADERIU)
            '' => '138',//	RECUSADO (JÁ ENVIOU)
            '6' => '14',//	DESTINATÁRIO PEDIU 2ª ENTREGA
            '' => '15',//	GREVE DO DESTINATÁRIO
            '7' => '16', //	ENTREGA EM FALTA (NÃO HOUVE SAÍDA)
            //'' => '17', //	OBJECTO MAL ENDEREÇADO
            '4' => '18', //	OBJECTO ESTRAGADO
            '' => '19',//	ARTIGOS PROIBIDOS / RESTRITOS
            '' => '20', //	ARTIGOS COM CÓDIGOS INCORRECTOS
            '32' => '21',//	CLIENTE NÃO EFECTUOU PAGAMENTO
            //'' => '23',//	FALECIDO
            '' => '24',//	OBJECTO NÃO DISTRIBUIDO POR MOTIVO DE FORÇA MAIOR
            '10' => '26',	//FERIADO MUNICIPAL
            '5' => '27',//	OBJECTO PERDIDO / EXTRAVIADO
            '' => '28',//	DESTINATÁRIO MUDOU-SE
            '' => '29',//	FOI DEIXADO NO APARTADO
            '' => '30',//	AGENDADO (agendamento efetuado pelos transportadores)
            '' => '31',//	REAGENDADO (reagendamento efetuado pelos transportadores)
            '' => '32',//	NÃO AGENDADO
            '' => '33',//	REAGENDADO (reagendamento efetuado pela MEO)
            '' => '40',//	RECOLHIDO NO CLIENTE
            '' => '99',//	OUTROS
            '' => '100',//	INICIO VISITA A CLIENTE (sempre que existe agendamento, deve ser enviado este motivo como confirmação de chegada ao destinatário)
            '' => '130',//	DADOS DE VENDA INCORRETOS
            '' => '139',//	CLIENTE DIZ NÃO TER ENCOMENDADO
            '' => '199',//	DEVOLUÇÃO SOLICITADA POR EMPRESA CLIENTE
            '' => '200',//	DOCUMENTAÇÃO RECOLHIDA NOK
            '' => '300',//	DOCUMENTAÇÃO PENDENTE DE CLIENTE - sempre que a documentação fica em posse do cliente para preenchimento e posterior recolha
        ];


        return @$incidences[$incidenceId] ? @$incidences[$incidenceId] : '99';
    }
}
