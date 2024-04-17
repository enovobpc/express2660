<?php

namespace App\Console\Commands;

use App\Models\BroadcastPusher;
use App\Models\Customer;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use App\Models\ShippingStatus;
use App\Models\ShipmentPackDimension;
use Illuminate\Console\Command;
use App\Models\AgencyZipCode;
use File, Mail, Date, Setting, DB, Log;

class FtpLaRedout extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ftp:la-redout {action}';



    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Communication ftp with LaRedout';

    /**
     * API endpoint
     * @var string
     */


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Sync data with La Redout by ftp\n");
        if ($this->argument('action') == 'send') {
            $this->exportToFtp();
        } else {
            $this->importEnvios();
            $this->importRecolhas();
        }

        $this->info("Sync completed");
        return;
    }

    /**
     * Import services from webservice
     */
    public function exportToFtp()
    {
        $serverFtp      = 'ftp.logisimple.pt';
        $usernameFtp    = 'laredoute@logisimple.pt';
        $passwordFtp    = 'E&w$R~58RT-K';
        $date = date("YmdGi");
        $fileName = 'Logisimple' . $date . '.txt';
        //TODO: after change that ID for ID La Redout
        $shipments = Shipment::where('customer_id', 94)
            ->where('status_id', 5)
            ->whereDate('status_date', date('Y-m-d'))
            ->get()->toArray();

        $country = 'PT';
        $codeClient = 'REDOUP';

        $arr = [];

        foreach ($shipments as $shipment) {
            $expedictionNumber = '00000000';
            $montant = str_replace('.', '', $shipment['total_price']);
            $montant = $this->formatWithZero(11, $montant, '0');
            $CODREG = "   ";

            $validateData = date_create($shipment['status_date']);
            $validateData = date_format($validateData, "Ymd");

            $agenceNumber = "      ";
            // $nameClient = $this->formatWithZero(30, $shipment['recipient_name'], ' ');
            $nameClient = '                              ';
            //$zipCode = $this->formatWithZero(5, $shipment["recipient_zip_code"], ' ');
            $zipCode = "     ";
            //$refex = $shipment['reference'];
            $customerNumber = '000000000';
            $coditionPag = '  ';
            $futuruse = '                                             ';
            $refex = $shipment['reference'];

            $arrRefex = explode(",", $refex);
            foreach ($arrRefex as $row) {
                $arr[] = $country . $codeClient . $expedictionNumber . $montant . $CODREG . $validateData . $agenceNumber . $nameClient . $zipCode . $row . $customerNumber . $coditionPag . $futuruse . PHP_EOL;
            }
        }


        $con = ftp_connect($serverFtp);
        if (false === $con) {
            throw new Exception('Unable to connect');
        }


        $loggedIn = ftp_login($con,  $usernameFtp,  $passwordFtp);
        if (true === $loggedIn) {
            $fp = fopen('php://temp.txt', 'r+');

            foreach ($arr as $row) {
                fputs($fp, $row);
            }

            rewind($fp);
            ftp_fput($con, 'OUT/test/' . $fileName, $fp, FTP_ASCII);
            echo 'Success!';
        } else {
            throw new Exception('Unable to log in');
        }

    }




    /**
     * Comunicate shipments trackings
     */
    public function importEnvios()
    {
        $serverFtp   = 'ftp.logisimple.pt';
        $usernameFtp = 'laredoute@logisimple.pt';
        $passwordFtp = 'E&w$R~58RT-K';
        
        $con = ftp_connect($serverFtp);
        if ($con === false) {
            throw new Exception('Unable to connect');
        }

        $loggedIn = ftp_login($con,  $usernameFtp,  $passwordFtp);

        $file_list = ftp_nlist($con, "/IN/");

        $file_list = empty($file_list) ? [] : $file_list;
        
        $fileName = "";
        foreach ($file_list as $file) {
            if (strpos($file, '.csv') !== false) {
                $fileName = $file;
            }
        }

        if (true === $loggedIn && $fileName != "") {
            echo 'Login with sucess';
            $temp = fopen('php://temp', 'r+');
            if (@ftp_fget($con, $temp, $fileName, FTP_BINARY, 0)) {

                $a = 0;
                $data = [];
                $lastShipmentID = null;
                $lastReference = null;

                $fstats = fstat($temp);
                fseek($temp, 0);
    

                while (($row = fgetcsv($temp, 0, ';')) !== FALSE) //percorre cada linha do csv
                {
                    if ($a != 0) {
                        array_splice($row, 18, 1); //apaga colunas indesejadas
                        array_splice($row, 16, 1);
                        array_splice($row, 6, 1);


                        $refExterna = $row[0];

                        if (strpos($refExterna, 'E+') !== false) //caso seja um numero exponencial
                        {
                            array_splice($row, 0, 1, correcaonumeros($refExterna));
                        }

                        $zipCodeSplit = explode("-", $row[4]);
                        $zipCode = AgencyZipCode::where('zip_code', $zipCodeSplit[0])->first();
                        
                        $agency_id = 106;
                        if(!empty($zipCode)){
                            $agency_id = $zipCode->agency_id; 
                        }
                        

                        $shipment = new Shipment();

                        $shipment->reference = $row[0];
                        $shipment->reference2 = $row[1];
                        $shipment->date = $row[2];
                        $shipment->shipping_date = $row[2];
                        $shipment->recipient_address = $row[3];
                        $shipment->recipient_zip_code = $row[4];
                        $shipment->recipient_city = $row[5];
                        $shipment->recipient_name = $row[7];
                        $shipment->recipient_phone = $row[9];
                        $shipment->service_id = 12;
                        $shipment->status_id = 2;
                        $shipment->provider_id = 1;
                        $shipment->customer_id = 94;
                        $shipment->agency_id = 106;
                        $shipment->sender_agency_id = 106;
                        $shipment->recipient_country = 'pt';
                        $shipment->recipient_agency_id = $agency_id;
                        $shipment->sender_name = 'La Redoute B2C';
                        $shipment->sender_address = 'Zona Industrial da Barosa - Rua Beco dos Petigais, Fracção F, NºS 45 e 65';
                        $shipment->sender_zip_code = '2400-431';
                        $shipment->sender_city = 'Leiria';
                        $shipment->sender_country = 'pt';
                        $shipment->sender_phone = '244811035';
                        $shipment->sender_vat = '501213031';
                        $shipment->recipient_email = $row[14];
                        $shipment->delivery_date = NULL;
                        $shipment->save();
                        $shipment->setTrackingCode();

                        if ($shipment) {
                            $history = new ShipmentHistory();
                            $history->status_id   = $shipment->status_id;
                            $history->agency_id   = $shipment->agency_id;
                            $history->shipment_id = $shipment->id;
                            $history->save();

                            // $nVolume = (int)$row[8];
                            // for($i=$nVolume; $i>0; $i--){
                            $shipment_packs_dimensions = new ShipmentPackDimension();
                            $shipment_packs_dimensions->shipment_id = $shipment->id;
                            $shipment_packs_dimensions->qty = (int)$row[8];
                            $shipment_packs_dimensions->volume = $row[12];
                            $shipment_packs_dimensions->weight = $row[13];
                            $shipment_packs_dimensions->sku = $row[15];
                            $shipment_packs_dimensions->description = $row[10];
                            $shipment_packs_dimensions->type = 'box';
                            $shipment_packs_dimensions->product_id = 0;
                            $shipment_packs_dimensions->save();
                            // }

                            $dataDimensionShipment = ShipmentPackDimension::where('shipment_id', $shipment->id)
                                ->get([
                                    DB::raw('SUM(qty) as quantatyVolume'),
                                    DB::raw('SUM(weight) as pesoTotal'),
                                    DB::raw('SUM(volume) as volumeTotal'),
                                ])->toArray();

                            $shipment->volumes = $dataDimensionShipment[0]['quantatyVolume'];
                            $shipment->weight = $dataDimensionShipment[0]['pesoTotal'];
                            $shipment->fator_m3 = $dataDimensionShipment[0]['volumeTotal'];
                            $shipment->save();
                        }

                        $lastReference = $row[1];
                        $data[] = $row;
                    }
                    $a++;
                }


                $date = $date = date("Ymd");
                $oldfile = '/IN/backup/LOGICSIMPLE_' . $date . '.csv';
                if (ftp_rename($con, $fileName, $oldfile)) {
                    echo 'moved with success';
                } else {
                    echo "problem moving";
                }
            } else {
                Log::info('IMPORTAR ENVIOS LAREDOUTE - Sem ficheiros');
            }
            // $contents = fread($temp, $fstats['size']);
        } else {
            Log::info('IMPORTAR ENVIOS LAREDOUTE - Impossível conectar FTP ou Sem ficheiros');
        }
        
    }

   /**
     * Comunicate shipments trackings
     */
    public function importRecolhas()
    {
        $serverFtp      = 'ftp.logisimple.pt';
        $usernameFtp    = 'laredoute@logisimple.pt';
        $passwordFtp    = 'E&w$R~58RT-K';
        $con = ftp_connect($serverFtp);
        
        if (false === $con) {
            throw new Exception('Unable to connect');
        }

        $loggedIn  = ftp_login($con,  $usernameFtp,  $passwordFtp);
        $file_list = ftp_nlist($con, "/RECOLHAS/");

        $file_list = empty($file_list) ? [] : $file_list;

        $fileName = "";
        foreach ($file_list as $file) {
            if (strpos($file, '.csv') !== false) {
                $fileName = $file;
            }
        }

        if (true === $loggedIn && $fileName != "") {
            echo 'Login with sucess';
            $temp = fopen('php://temp', 'r+');
            if (@ftp_fget($con, $temp, $fileName, FTP_BINARY, 0)) {

                $a = 0;
                $data = [];
                $lastShipmentID = null;
                $lastReference = null;

                $fstats = fstat($temp);
                fseek($temp, 0);
    

                while (($row = fgetcsv($temp, 0, ';')) !== FALSE) //percorre cada linha do csv
                {
                    if ($a != 0) {
                        array_splice($row, 18, 1); //apaga colunas indesejadas
                        array_splice($row, 16, 1);
                        array_splice($row, 6, 1);


                        $refExterna = $row[0];
                        
                        if(isset($row[0])) {

                            if (strpos($refExterna, 'E+') !== false) //caso seja um numero exponencial
                            {
                                array_splice($row, 0, 1, correcaonumeros($refExterna));
                            }
    
                            $zipCodeSplit = explode("-", $row[4]);
                            $zipCode = AgencyZipCode::where('zip_code', $zipCodeSplit[0])->first();
                            
                            $agency_id = 106;
                            if(!empty($zipCode)){
                                $agency_id = $zipCode->agency_id; 
                            }
                            
    
                            $shipment = new Shipment();
                            
                           
                            $shipment->service_id           = 12;
                            $shipment->status_id            = 2;
                            $shipment->provider_id          = 1;
                            $shipment->customer_id          = 94;
                            
                            $shipment->reference            = $row[0];
                            $shipment->reference2           = $row[1];
                            
                            $shipment->date                 = $row[2];
                            $shipment->shipping_date        = $row[2];
                            $shipment->delivery_date = NULL;
                            
                            $shipment->agency_id            = 106;
                            $shipment->sender_agency_id     = $agency_id;
                            $shipment->recipient_agency_id  = 106;
                            
                            $shipment->recipient_name       = 'La Redoute/Devoluções';
                            $shipment->recipient_address    = 'IC2 Nº984 Vale Gracioso';
                            $shipment->recipient_zip_code   = '2400-827';
                            $shipment->recipient_city       = 'Leiria';
                            $shipment->recipient_country    = 'pt';
                            $shipment->recipient_phone      = '244811035';
                            $shipment->recipient_vat        = '501213031';
                            $shipment->recipient_email      = $row[14];
                            
                            $shipment->sender_name = $row[7];
                            $shipment->sender_address = $row[3];
                            $shipment->sender_zip_code = $row[4];
                            $shipment->sender_city = $row[5];
                            $shipment->sender_country = 'pt';
                            $shipment->sender_phone = $row[9];
                            
                            
                            $shipment->save();
                            $shipment->setTrackingCode();
    
                            if ($shipment) {
                                $history = new ShipmentHistory();
                                $history->status_id   = $shipment->status_id;
                                $history->agency_id   = $shipment->agency_id;
                                $history->shipment_id = $shipment->id;
                                $history->save();
    
                                // $nVolume = (int)$row[8];
                                // for($i=$nVolume; $i>0; $i--){
                                $shipment_packs_dimensions = new ShipmentPackDimension();
                                $shipment_packs_dimensions->shipment_id = $shipment->id;
                                $shipment_packs_dimensions->qty = (int)$row[8];
                                $shipment_packs_dimensions->volume = $row[12];
                                $shipment_packs_dimensions->weight = $row[13];
                                $shipment_packs_dimensions->sku = $row[15];
                                $shipment_packs_dimensions->description = $row[10];
                                $shipment_packs_dimensions->type = 'box';
                                $shipment_packs_dimensions->product_id = 0;
                                $shipment_packs_dimensions->save();
                                // }
    
                                $dataDimensionShipment = ShipmentPackDimension::where('shipment_id', $shipment->id)
                                    ->get([
                                        DB::raw('SUM(qty) as quantatyVolume'),
                                        DB::raw('SUM(weight) as pesoTotal'),
                                        DB::raw('SUM(volume) as volumeTotal'),
                                    ])->toArray();
    
                                $shipment->volumes = $dataDimensionShipment[0]['quantatyVolume'];
                                $shipment->weight = $dataDimensionShipment[0]['pesoTotal'];
                                $shipment->fator_m3 = $dataDimensionShipment[0]['volumeTotal'];
                                $shipment->save();
                            }
    
                            $lastReference = $row[1];
                            $data[] = $row;
                        }
                    }
                    $a++;
                }


                $date = $date = date("Ymd");
                $oldfile = '/RECOLHAS/backup/LOGICSIMPLE_' . $date . '.csv';
                if (ftp_rename($con, $fileName, $oldfile)) {
                    echo 'moved with success';
                } else {
                    echo "problem moving";
                }
            } else {
               Log::info('IMPORTAR RECOLHAS LAREDOUTE - Não existem ficheiros.');
                //dd('Dont exist any file!');
            }
            // $contents = fread($temp, $fstats['size']);
        } else {
            Log::info('IMPORTAR RECOLHAS LAREDOUTE - Impossível conectar FTP ou Sem ficheiros');
        }
    }
    
    public function formatWithZero($tamanho, $valor, $limit = 0)
    {

        if (strlen($valor) > $tamanho) {
            print_r('a');
            return ('Erro - Tamanho menor que valor');
        } elseif (strlen($valor) == $tamanho) {
            return ($valor);
        } else {
            $aux = $tamanho - strlen($valor);

            $i = 0;

            while ($i < $aux) {
                $valor = $limit . $valor;
                $i++;
            }

            return ($valor);
        }
    }
}
