<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Webservice;
use Jenssegers\Date\Date;
use App\Models\Agency;

class SyncShipment extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipment:sync {source} {date?} {provider?} {range?}';

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
        
        $this->info("Sync shipments by webservice\n");

        $method = 'envialia';
        if(!empty($this->argument('provider'))) {
            $method = $this->argument('provider');
        }

        if(empty($this->argument('date')) || $this->argument('date') == '0') {
            $date = new Date();
            $date = $date->format('Y-m-d');
        } else if($this->argument('date') && $this->argument('date') == '1') {
            $date = Date::yesterday();
            $date = $date->format('Y-m-d');
        }

        $range = null;
        if($this->argument('range')) {
            $range = explode('-', $this->argument('range'));
        }

        $source = $this->argument('source');
        $agencies = Agency::whereSource($source)->pluck('id')->toArray();

        $webservice = new Webservice\Base;
        $webservice->syncShipments($date, $method, null, true, $agencies, $range);

        $this->info("Sync completed. Source: ".$source." Method: ".$method." | Date: " . $date);
        return;
    }
}
