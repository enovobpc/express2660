<?php

namespace App\Console\Commands;

use App\Models\ShipmentSchedule;
use App\Models\ShipmentScheduled;
use Illuminate\Console\Command;
use Jenssegers\Date\Date;

class RunShipmentSchedules extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipment:schedule {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run shipment schedules';

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
        
        $this->info("Run shipment schedules\n");

        try {

            if (empty($this->argument('date'))) {
                $date = new Date();
                $date = $date->format('Y-m-d');
            } else {
                $date = Date::parse($this->argument('date'));
                $date = $date->format('Y-m-d');
            }

            if(config('app.source') == 'corridadotempo') {
                $date = new Date();
                $date = $date->addDay(3)->format('Y-m-d');
            }

            ShipmentSchedule::runSchedule($date);

            $this->info("Run schedules completed. | Date: " . $date);
            return;
        } catch (\Exception $e) {
            $this->info($e->getMessage().' FILE: '. $e->getFile(). ' ON LINE '. $e->getLine());
            return;
        }
    }
}
