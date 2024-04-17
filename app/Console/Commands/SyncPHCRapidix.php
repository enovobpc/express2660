<?php

namespace App\Console\Commands;

use App\Models\BroadcastPusher;
use App\Models\Customer;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use App\Models\ShippingStatus;
use Illuminate\Console\Command;
use File, Mail, Date, Setting;

class SyncPHCRapidix extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:rapidix-phc {action?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync PHC data';

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

        $this->info("Generate PHC importer file");

        $date = date('Y-m-d');

        //for($i=1; $i<=31 ; $i++) {

        /* $dt = $i;
         if($i<10) {
            $dt = '0'.$i;
         }*/

        //$date = '2021-09-'. $dt;
        $startDate = $date .' 00:00:00';
        $endDate   = $date .' 23:59:59';


        $shipments = Shipment::with('last_history')
            /*->whereHas('last_history', function($q) use($startDate, $endDate) {
                $q->whereIn('status_id', [ShippingStatus::DELIVERED_ID, ShippingStatus::INCIDENCE_ID]);
                $q->whereBetween('created_at', [$startDate, $endDate]);
            })*/
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->get();

        foreach ($shipments as $shipment) {
            $this->createCSVfile($shipment);
        }

        //}

        $this->info("Sync finalized");
        return;
    }

    public function createCSVfile($shipment){
        $rowLine    = $this->getCsvLine($shipment);

        /*$timestamp  = date("d-m-Y_H-i", time());*/
        $timestamp  = str_replace(' ','_', $shipment->created_at);

        $filename   = "_pedidos_".$shipment->id.".csv";
        $filepath   = storage_path("exports/passar_phc/" . $filename);

        $fileContent = utf8_decode($rowLine);
        file_put_contents($filepath, $fileContent, FILE_APPEND);
    }

    public function getCsvLine($shipment){

        $createdHour  = new Date($shipment->created_at);
        $createdHour  = $createdHour->format('H:i:s');

        $deliveryDate = $deliveryHour = '';
        if($shipment->status_id == ShippingStatus::DELIVERED_ID) {
            $deliveryDateHour = new Date($shipment->last_history->created_at);
            $deliveryDate = $deliveryDateHour->format('Y-m-d');
            $deliveryHour = $deliveryDateHour->format('H:i:s');
        }


        $totalPrice = $shipment->total_price + $shipment->total_expenses;

        $csvOutput = $shipment->tracking_code . ";";
        $csvOutput.= @$shipment->customer->billing_code . ";";
        $csvOutput.= $shipment->obs . ";";
        $csvOutput.= $shipment->charge_price . ";";
        $csvOutput.= $shipment->weight . ";";
        $csvOutput.= $shipment->volumes . ";";
        $csvOutput.= $createdHour . ";"; //TIME(time_pedido)
        $csvOutput.= $shipment->date . ";"; //DATE_FORMAT(time_pedido, '%Y-%m-%d')
        $csvOutput.= $shipment->sender_attn . ";";
        $csvOutput.= $shipment->sender_name . ";";
        $csvOutput.= $deliveryDate . ";";
        $csvOutput.= $deliveryHour . ";";
        $csvOutput.= $shipment->recipient_attn . ";";
        $csvOutput.= $shipment->recipient_name . ";";
        $csvOutput.= $shipment->sender_city . ";";
        $csvOutput.= $shipment->recipient_city . ";";
        $csvOutput.= $totalPrice . ";";
        $csvOutput.= $shipment->reference . ";"; //id_fullfix
        $csvOutput.= "\n";

        return $csvOutput;
    }
}
