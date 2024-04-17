<?php

namespace App\Console\Commands;

use App\Models\Agency;
use App\Models\Budget\Budget;
use App\Models\CacheSetting;
use App\Models\Cpanel\Quota;
use App\Models\GatewayPayment\Base;
use App\Models\Notice;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use Illuminate\Console\Command;
use File, Mail, Date, Setting;
use Mockery\Matcher\Not;

class RunNotify extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run to send notifications after shipments not delivery';

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
        
        $this->info("Run Send Notifications");


        $lastUpdate = Date::now();
        $lastUpdate->subMinutes(4);
        $lastUpdate = $lastUpdate->format('Y-m-d H:i').':00';

        $shipments =  ShipmentHistory::with('shipment')
                    ->where('status_id', 4)
                    ->where('created_at', '>=', $lastUpdate)
                    ->get();
        

        $ldate = date('H:i');
        if(Setting::get('shipments_limit_hour_notification')){
            
            foreach ($shipments as $shipment) {

                try {
                    if($ldate > Setting::get('shipments_limit_hour_notification')){

                        $emails = validateNotificationEmails($shipment->shipment->recipient_email);
                        if (!empty($emails['valid'])) {

                            try {
                                    Mail::send(transEmail('emails.shipments.delay', $shipment->shipment->recipient_country), compact('shipment'), function($message) use($shipment) {
                                        $message->to($shipment->shipment->recipient_email)
                                            ->subject('Atraso na sua encomenda ' . $shipment->shipment->tracking_code);
                                    });
                                    return true;
                            } catch (\Exception $e) {
                                return false;
                            }
                        }
                        return false;
                    }
                } catch (\Exception $e) {
                    $errors[$shipment->tracking_code] = $e->getMessage();
                }
            }
        }
    }
}
