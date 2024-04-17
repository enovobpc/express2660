<?php

namespace App\Console\Commands;

use App\Models\Agency;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use App\Models\ShippingStatus;
use Illuminate\Console\Command;
use File, Mail, Date;

class SyncPickups extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipment:syncPickups {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatic generate shipments from pickups';

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
        
        $this->info("Automatic generate shipments from pickups");
        /*
        Shipment::generateShipmentsFromPickups($this->argument('date'));
        */

        $this->info("Sync completed");
        return;
    }
}
