<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\FilesImporter\ImporterController;
use App\Http\Controllers\Admin\Invoices\SalesController;
use App\Http\Controllers\Api\Mobile\StatsController;
use App\Models\Bank;
use App\Models\BroadcastPusher;
use App\Models\CacheSetting;
use App\Models\Customer as ModelsCustomer;
use App\Models\CustomerBilling;
use App\Models\FleetGest\TollLog;
use App\Models\GpsGateway\Base as GpsGatewayBase;
use App\Models\Invoice;
use App\Models\InvoiceGateway\KeyInvoice\Base;
use App\Models\InvoiceGateway\KeyInvoice\Customer;
use App\Models\InvoiceGateway\KeyInvoice\Document;
use App\Models\InvoiceGateway\Primavera\Base as PrimaveraBase;
use App\Models\InvoiceGateway\Primavera\Customer as PrimaveraCustomer;
use App\Models\InvoiceSchedule;
use App\Models\Map;
use App\Models\Permission;
use App\Models\PurchaseInvoice;
use App\Models\PurchasePaymentNote;
use App\Models\PurchasePaymentNoteInvoice;
use App\Models\Role;
use App\Models\Service;
use App\Models\Shipment;
use App\Models\ShippingExpense;
use App\Models\Translation;
use App\Models\User;
use App\Models\Webservice\GlsZeta;
use App\Models\Webservice\Mrw;
use App\Models\Webservice\Ontime;
use App\Models\Webservice\Sending;
use App\Models\Webservice\WePickup;
use App\Models\ZipCode;
use App\Models\ZipCodeProvince;
use Dev0102;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Locale;
use LynX39\LaraPdfMerger\PdfManage;
use Mpdf\Mpdf;
use Setting, DB, Date, File, Hash, Response;
use PDF; // at the top of the file

use App\Models\Logistic\ReceptionOrder;
use App\Models\Logistic\ShippingOrder;
use App\Models\Logistic\Product;
use App\Models\Logistic\ProductLocation;
use App\Models\Logistic\ProductHistory;

class TestController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',settings']);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        dd(\App\Models\Logistic\ShippingOrderLine::whereHas('shipping_order', function($q){
                $q->whereIn('status_id', [\App\Models\Logistic\ShippingOrderStatus::STATUS_PENDING, \App\Models\Logistic\ShippingOrderStatus::STATUS_PROCESSING]);
            })
            ->where('product_id', 446)
            ->toSql());
            
        dd(1);
        
        $ids = ["264"];
        
        $productsCollection = Product::with('locations')->whereIn('id', $ids)->get();
        dd($productsCollection);
        dd(ProductLocation::getAutomaticLocation("264", "1"));
        
        // $receptionOrders = ReceptionOrder::whereHas('lines.product')->get();
        // $shippingOrders  = ShippingOrder::whereHas('lines.product')->get();

        // Product::whereNull('deleted_at')->update([
        //     'stock_total' => 0,
        //     'stock_available' => 0,
        //     'stock_allocated' => 0
        // ]);

        // $locations = ProductLocation::with('product')->get();
        // foreach ($locations as &$location) {
        //     $linesReception = collect([]);
        //     $receptionOrders->each(function ($order) use ($location, &$linesReception) {
        //         $order->confirmation->each(function ($confirmation) use ($location, &$linesReception) {
        //             if ($confirmation->location_id == $location->location_id && $confirmation->product_id == $location->product_id) {
        //                 $linesReception->push($confirmation);
        //             }
        //         });
        //     });

        //     $linesShipping = collect([]);
        //     $shippingOrders->map(function ($order) use ($location, &$linesShipping) {
        //         return $order->lines->map(function ($line) use ($location, &$linesShipping) {
        //             if ($line->location_id == $location->location_id && $line->product_id == $location->product_id) {
        //                 $linesShipping->push($line);
        //             }
        //         });
        //     });

        //     $adjustments = ProductHistory::where('source_id', $location->location_id)
        //         ->where('product_id', $location->product_id)
        //         ->where('action', 'adjustment')
        //         ->get();

        //     $location->stock = 0;
        //     $location->stock_allocated = 0;

        //     $location->stock += $linesReception->sum('qty_received');
        //     $location->stock_allocated += $linesShipping->where('qty_satisfied', '=', null)->sum('qty');
        //     $location->stock -= $linesShipping->sum('qty_satisfied');
        //     $location->stock += $linesShipping->sum('qty_devolved');
        //     $location->stock += $adjustments->sum('qty');

        //     if ($location->product) {
        //         $location->product->stock_total += $location->stock;
        //         $location->product->stock_allocated += $location->stock_allocated;
        //         $location->product->save();
        //     }

        //     $location->save();
        // }

        // dd($locations->toArray());

        //conection to db
        $db = DB::connection('mysql_core');


        $entrada = 'PUEBLOS_TIPO2.txt';
        $saida = 'SQL.txt';

        // Abre o arquivo de entrada para leitura
        if (($handle = fopen($entrada, 'r')) !== false) {

        
            // Abre o arquivo de saída para escrita
            $outputHandle = fopen($saida, 'w');

            if ($outputHandle !== false) {
                
                // Inicializa a variável para armazenar o bloco de linhas
                $blocoLinhas = array();
                $contadorLinhas = 0;

                // Lê o arquivo de entrada linha por linha
                while (($linha = fgets($handle)) !== false) {
                    // Divide a linha em campos com base no caractere '|'
                    $campos = explode('|', $linha);

                    // Certifica-se de que há dados suficientes nas posições necessárias
                    if (count($campos) >= 6) {
                        // Monta a instrução SQL INSERT
                        $zip_code = trim($campos[0]);
                        $agency = trim($campos[2]);
                        $route = trim($campos[3]);
                        $city = str_replace("'", "’", trim($campos[4]));
                        $country = trim($campos[5]);
                        $country = $country == '035' ? 'pt' : 'es';

                        $sql = "INSERT INTO sending_zip_codes (zip_code, agency, route, city, country) VALUES ('$zip_code', '$agency', '$route', '$city', '$country');";

             
                        // Armazena a instrução SQL no bloco de linhas
                        $blocoLinhas[] = $sql;
                        $contadorLinhas++;

                        // Se o bloco atingir 100 linhas, escreve no arquivo de saída e reinicia o bloco
                        if ($contadorLinhas == 100) {
                            fwrite($outputHandle, implode("\n", $blocoLinhas) . "\n");
                            $blocoLinhas = array();
                            $contadorLinhas = 0;
                        }
                    }
                }

                // Escreve qualquer linha restante no arquivo de saída
                if (!empty($blocoLinhas)) {
                    fwrite($outputHandle, implode("\n", $blocoLinhas) . "\n");
                }

                // Fecha os arquivos
                fclose($outputHandle);
                fclose($handle);

                echo "Concluído. As instruções SQL foram escritas em '$saida'.";
            } else {
                echo "Erro ao abrir o arquivo de saída.";
            }
        } else {
            echo "Erro ao abrir o arquivo de entrada.";
        }




       /*  //abrir o ficheiro
        $file = fopen("PUEBLOS_TIPO2.txt", "r") or die("Unable to open file!");
        fgets($file); //tirar cabeçalhos

        //percorrer o ficheiro
        $line = 0;
        while(!feof($file)) {
            
            if($line > 0) {
                $line = fgets($file);
                $fields = explode("|", $line); //interessa  [0]zip code[5]country

                if($fields[5] == '035'){
                    $country = 'pt';
                } else {
                    $country = 'es';
                }
                
                $db->insert('INSERT INTO sending_zip_codes(zip_code,c,route,city,country) VALUES (?, ? , ? , ?, ?)',[$fields[0],$fields[2],$fields[3],$fields[4], $country]);
            }

            $line++;

            
           
          } 
        
        fclose($file); */
        dd('Terminar');
        dd(1);
        

        Artisan::call('sync:etcp', ['action' => 'import']);
        echo '<pre>';
        Artisan::output();


        dd(1);

        Invoice::identifyRemovesDuplicates(true);

        dd(1);

         //atualiza todas as contas correntes
         $customers = ModelsCustomer::has('invoices')->get();
         foreach($customers as $customer) {
             $customer->updateBalance();
         }


         dd(1);



        $x = new \SoapClient('https://login.keyinvoice.com/API3_ws.php?wsdl', array('cache_wsdl' => WSDL_CACHE_NONE, 'trace' => true));
        dd($x->__getFunctions());
        dd($x);

        
/* 
        $invoice = Invoice::find(2514);
        $res = $invoice->updateBalanceFields(); */
       
        $customer = ModelsCustomer::find(18453);
        $result = $customer->updateBalance(true);
        dd($result);
        
 
        dd(1);
        $primavera = new PrimaveraCustomer();
        $token = $primavera->getCustomerDetails('500731993');

        dd($token);


       /*  $gps = new GpsGatewayBase();
        $gps = $gps->listVehicles(); */


        $x = new Dev0102();
        $x = $x->updatePermissions();

        dd('1FIM');


        Artisan::call('run:daily-tasks');
        echo '<pre>';
        Artisan::output();

        dd(1);

        $invoice = Invoice::orderBy('id', 'desc')->first();
        $x = $invoice->getQRcode();

        $x = new Document();
        $x = $x->getActiveSeries('220','invoice');
        dd($x);
        $x = new Document();
        $x = $x->getDocumentPdf('220','invoice');

        dd($x);


        Artisan::call('validities:check', ['date' => '2023-06-20']);
        echo '<pre>';
        Artisan::output();

        dd('STOP');

        
        $params = [];
        $params['return_totals'] = 1;
        $params['start_date'] = '1970-01-01 00:00:00';
        $users = User::getNotifications(null, $params);

        dd($users);

     /*    $webservice = new Mrw('08203', 'SGC8203DMLCunip', 'SGC8203@DMLCunip', '110003#');
        $webservice->getEnvioByTrk('08203', '08203', '07005F007019');
        dd(1);

        $x = Bank::getBankCodeFromIban('PT50003300004566634802205');

        dd($x);
        /* $x = new Document();
        $doc = $x->creditRegularization(49, 'TESTE', 173, 62);

        dd($doc); */

        $details = Invoice::updateCustomerBalance(18453);

        dd($details);

        $cliente = new Customer();
        $x = $cliente->getCustomerHistory('501455906');
        
        dd($x);


    


        $result = "21";
        dd(is_numeric($result));


        $invoices = PurchaseInvoice::orderBy('doc_date', 'asc')->get();

        //adiciona uma nota de pagamento para o tipo de documento fatura-recibo
        foreach ($invoices as $invoice) {
            if($invoice->doc_type == 'provider-invoice-receipt') {

                $paymentNote = PurchasePaymentNote::firstOrNew([
                    'provider_id' => $invoice->provider_id,
                    'reference'   => $invoice->reference
                ]);

                $paymentNote->source            = config('app.source');
                $paymentNote->provider_id       = $invoice->provider_id;
                $paymentNote->reference         = $invoice->reference;
                $paymentNote->vat               = $invoice->vat;
                $paymentNote->billing_code      = $invoice->billing_code;
                $paymentNote->billing_name      = $invoice->billing_name;
                $paymentNote->billing_address   = $invoice->billing_address;
                $paymentNote->billing_zip_code  = $invoice->billing_zip_code;
                $paymentNote->billing_city      = $invoice->billing_city;
                $paymentNote->billing_country   = $invoice->billing_country;
                $paymentNote->doc_date          = $invoice->doc_date;
                $paymentNote->total             = $invoice->total;
                $paymentNote->user_id           = $invoice->created_by;
                $paymentNote->setCode(true);

                if($paymentNote->id) {
                    $paymentNoteInvoice = PurchasePaymentNoteInvoice::firstOrNew([
                        'payment_note_id' => $paymentNote->id,
                        'invoice_id'      => $invoice->id
                    ]);

                    $paymentNoteInvoice->payment_note_id = $paymentNote->id;
                    $paymentNoteInvoice->invoice_id      = $invoice->id;
                    $paymentNoteInvoice->total           = $invoice->total;
                    $paymentNoteInvoice->total_pending   = 0;
                    $paymentNoteInvoice->invoice_total   = $invoice->total;
                    $paymentNoteInvoice->invoice_unpaid  = 0;
                    $paymentNoteInvoice->save();
                }
            }
        }


        dd(1);




        $statusToShow = Setting::get('delivery_map_status_show_on_map');
        if (!$statusToShow) {
            $statusToShow = 1;
        }
        $shipments = Shipment::where('status_id', $statusToShow)
            ->get();
        $shipmentsIds = [1361746, 1361512, 1361524];

        //ordena envios pela ordem correta
        $shipments = Shipment::whereIn('id', $shipmentsIds)->get(['id']);

        foreach ($shipments as $shipment) {

            $position = array_search($shipment->id, $shipmentsIds);

            $shipment->update([
                'status_id'   => 5,
                'operator_id' => 1,
                'sort'        => $position
            ]);
        }

        $shipments = Shipment::whereIn('id', $shipmentsIds)->orderBy('sort', 'asc')->get(['id', 'sort', 'status_id']);

        dd($shipments->toArray());


        /*$startMemory = memory_get_usage();*/

        $shipment = Shipment::find(1361737);
        $x = Shipment::calcPrices($shipment);

        dd($x);

        /*$memory = memory_get_usage() - $startMemory; // 36640

        dd(human_filesize2($memory));*/

        dd('FIM');


        $locations = [];
        foreach ($shipments as $shipment) {
            if ($shipment->map_lat != NULL && $shipment->map_lat != "" && $shipment->map_lng != "" && $shipment->map_lng != NULL) {
                $zipCode = $shipment->recipient_zip_code;
                $city =  $shipment->recipient->city;
                $address = $zipCode . ' ' . $city;
                $latLng = $shipment->map_lamat . ',' . $shipment->map_lng . ',' . $shipment->id . ',' . $shipment->tracking_code . ',' . $address;
                array_push($locations, $latLng);
            } else {
                $zipCode = $shipment->recipient_zip_code;
                $city =  $shipment->recipient->city;
                $address = $zipCode . ' ' . $city;

                $coord = $this->getCoordinates($address);

                $shipment->map_lat = $coord['lat'];
                $shipment->map_lng = $coord['lng'];
                $shipment->save();

                $latLng = $coord['lat'] . ',' . $coord['lng'] . ',' . $shipment->id . ',' . $shipment->tracking_code . ',' .  $address;
                array_push($locations, $latLng);
            }
        }
        return $this->setContent('admin.map_test',  compact('locations'));

        dd(1);
        $webservice = new Sending();


        //$webservice->importShipments();
        $webservice->importIncidencesSolutions();

        /*//asf $x = [1683830, 1682906, 1682728];

        $x = [3567, 3798, 3810];
        foreach ($x as $id) {
            $shipment = Shipment::find($id);
            $webservice->saveShipment($shipment);
        }



        if ($request->get('action') == 'sending-import') {
            $webservice->importShipments();
        }

        if ($request->get('action') == 'sending-export-trackings') {
            $webservice->exportTrackings($request->get('agency'), $request->get('date'));
        }

        if ($request->get('action') == 'sending-export-traceability') {
            $webservice->exportTraceability($request->get('agency'), $request->get('date'));
            $webservice->exportRefunds($request->get('agency'));
        }*/

        dd('STOP');


        Artisan::call('sync:etcp', ['action' => 'status']);
        echo '<pre>';
        Artisan::output();

        dd('STOP');
    }

    function getCoordinates($address)
    {

        //replace all the white space with "+" sign to match with google search pattern
        $address = str_replace(" ", "+", strtolower($address));
        $address = str_replace("º", '', $address);
        $address = str_replace("ª", '', $address);

        $url = "https://maps.google.com/maps/api/geocode/json?sensor=false&address=$address&key=" . getGoogleMapsApiKey();

        $response = file_get_contents($url);
        $json = json_decode($response, TRUE);
        if (!empty(@$json['error_message'])) {
            return $json;
        }

        $result = [
            'lat' => $json['results'][0]['geometry']['location']['lat'],
            'lng' => $json['results'][0]['geometry']['location']['lng']
        ];

        return $result;
    }
}
