<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Webservice;

class SyncShipmentStatus extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipment:syncStatus {provider?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync shipments by webservice';

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
        
        $this->info("Sync shipment status by webservice\n");

        $method = null;
        if(!empty($this->argument('provider'))) {
            $method = $this->argument('provider');
        }

        $webservice = new Webservice\Base;
        $webservice->syncShipmentsHistory(null, null, null, [], $method);

        $this->info("Sync completed");
        return;
    }
}
