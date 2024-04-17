<?php

namespace App\Console\Commands;

use App\Http\Controllers\Account\ShipmentsController;
use App\Models\ZipCode\AgencyZipCode;
use App\Models\BroadcastPusher;
use App\Models\Customer;
use App\Models\Route;
use App\Models\Service;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use App\Models\ShippingExpense;
use Illuminate\Console\Command;
use File, Setting, Mail;

class FtpImporter extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ftp:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import files from FTP';

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

        $this->info("Sync application permissions\n");

        if (config('app.source') == 'asfaltolargo') {
            $this->importAlvesBandeira(18005);
        } else if (config('app.source') == 'pesamatrans') {
            $this->importAlvesBandeira(8);
        } else if (config('app.source') == 'invictacargo') {
            $this->importAlvesBandeira(79);
            $this->importAlvesBandeira(166, 'alvesbandeira_bridgstone');
            $this->importAlvesBandeira(182, 'alvesbandeira_lisboa');
        }

        $this->info("Sync completed");
        return;
    }

    /**
     *
     */
    public function importAlvesBandeira($customerId, $folder = 'alvesbandeira')
    {
        $customer = Customer::find($customerId);
        $files = File::files(storage_path('/ftp_importer/' . $folder));

        if ($files) {

            foreach ($files as $shipmentsFile) {

                try {

                    $headerKeys = [
                        'tracking_code',        //Codigo envio
                        'date',                 //Data
                        'service_code',         //Serviço
                        'reference',            //Referencia
                        'recipient_name',       //Destinatário
                        'recipient_address',    //Morada
                        'recipient_zip_code',   //Cód. Postal
                        'recipient_city',       //Localidade
                        'recipient_phone',      //Contacto
                        'recipient_attn',       //Pessoa contacto
                        'volumes',              //Nº Volumes
                        'weight',               //Peso (Kg)
                        'charge_price',         //Cobrança
                        'payment_at_recipient', //Portes destino
                        'has_return',           //Retorno
                        'obs',                  //Observacoes
                    ];

                    //READ FILE
                    $file = fopen($shipmentsFile, "r");

                    $fileData = [];
                    while (!feof($file)) {
                        $line = utf8_encode(fgets($file));
                        $line = explode('|', $line);

                        $row = [];
                        foreach ($headerKeys as $pos => $key) {
                            $value = trim(@$line[$pos]);
                            if ($key == 'weight') {
                                $value = (float) $value;
                                $value = $value == 0.00 ? 1 : $value;
                            } elseif ($key == 'charge_price' || $key == 'fator_m3') {
                                $value = (float) $value;
                            } elseif ($key == 'payment_at_recipient') {
                                $value = $value == 'N' ? 0 : 1;
                            } elseif ($key == 'has_return') {
                                if ($value == 'S') {
                                    $value = ["rpack"];
                                } else {
                                    $value = null;
                                }
                            }

                            $row[$key] = $value;
                        }

                        if (!empty($row['tracking_code'])) {
                            $fileData[] = $row;
                        }
                    }

                    fclose($file);


                    //IMPORT SHIPMENTS
                    foreach ($fileData as $row) {

                        //$row['service_code'] = '24H';
                        $service = Service::where('display_code', $row['service_code'])->first();

                        $shipment = Shipment::firstOrNew([
                            'tracking_code' => $row['tracking_code']
                        ]);

                        $shipmentExists = $shipment->exists;

                        $shipment->fill($row);
                        $shipment->customer_id          = $customer->id;
                        $shipment->agency_id            = $customer->agency_id;
                        $shipment->sender_agency_id     = $customer->agency_id;
                        $shipment->provider_id          = Setting::get('shipment_default_provider');
                        $shipment->service_id           = @$service->id;
                        $shipment->status_id            = 1;
                        $shipment->recipient_country    = 'pt';

                        // Detect recipient agency
                        $fullZipCode  = $shipment->recipient_zip_code;
                        $zipCodeParts = explode('-', $fullZipCode);
                        $zipCode4     = $zipCodeParts[0];
                        $zipCode = AgencyZipCode::where(function ($q) use ($fullZipCode, $zipCode4) {
                            $q->where('zip_code', $zipCode4);
                            $q->orWhere('zip_code', $fullZipCode);
                        })
                            ->where('country', @$shipment->recipient_country)
                            ->orderBy('zip_code', 'desc')
                            ->first();

                        $shipment->recipient_agency_id = @$zipCode->agency_id ?? @$customer->agency_id;
                        //--

                        $shipment->billing_date     = @$row['date'];
                        $shipment->sender_name      = $customer->name;
                        $shipment->sender_address   = $customer->address;
                        $shipment->sender_zip_code  = $customer->zip_code;
                        $shipment->sender_city      = $customer->city;
                        $shipment->sender_country   = $customer->country;
                        $shipment->sender_phone     = $customer->mobile;
                        $shipment->setTrackingCode();

                        if (config('app.source') == 'asfaltolargo') {
                            //add Price
                            $prices = Shipment::calcPrices($shipment);
                            if(@$prices['fillable']) {
                                $shipment->fill($prices['fillable']);
                                $shipment->storeExpenses($prices);
                            }

                            $shipment->save();

                           /*  /**
                            $allExpenses = null;
                            $input = null;
                            if (!empty($shipment->charge_price)) {

                                $allExpenses = ShippingExpense::filterSource()
                                    ->get(['id', 'code', 'name', 'price', 'zones', 'type']);

                                $input['complementar_services'][] = Shipment::getChargeExpense($allExpenses);
                            }

                            Shipment::assignExpenses($shipment, $input, $allExpenses); */


                        } elseif (in_array(config('app.source'), ['pesamatrans', 'invictacargo'])) {
                            if (config('app.source') == 'invictacargo') {
                                $route = Route::getRouteFromZipCode($shipment->recipient_zip_code);
                                $shipment->route_id = empty($route) ? '' : $route->id;
                                if (empty($shipment->operator_id)) {
                                    $shipment->operator_id = empty($route) ? '' : $route->operator_id;
                                }
                            }

                            $prices = Shipment::calcPrices($shipment) ?? [];
                            if (!empty($prices)) {
                                $shipment->fill($prices['fillable']);
                                $shipment->save();
                                $shipment->storeExpenses($prices);
                            } else {
                                $shipment->save();
                            }
                        }

                        if (!$shipmentExists) {
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
                        if (!$shipmentExists) {
                            $shipment->setNotification(BroadcastPusher::getGlobalChannel());
                        }
                    }

                    File::delete($shipmentsFile);
                } catch (\Exception $e) {
                    $info = 'Falha ao ler ficheiro Alves Bandeira - ' . $shipmentsFile . ' [' . $e->getMessage() . ' file ' . $e->getFile() . ' line ' . $e->getLine() . ']';
                    /* Mail::raw($info, function ($message) {
                        $message->to('suporte@enovo.pt')
                            ->subject('Falha ao ler ficheiro Alves Bandeira');
                    }); */


                    $trace = LogViewer::getTrace(null, $info);
                    Log::error(br2nl($trace));
                }
            }
        }
    }
}
