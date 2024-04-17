<?php

namespace App\Console\Commands;

use App\Models\ScheduledTask;
use App\Models\Shipment;
use Illuminate\Console\Command;
use App\Models\Webservice;

class RunScheduledTasks extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:scheduled-tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute scheduled tasks';

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
        
        $this->info("Execute scheduled tasks\n");

        $tasks = ScheduledTask::filterSource()->get();

        $tasks = $tasks->groupBy('target');

        foreach ($tasks as $targetName => $targetTasks) {

            //Process shipments tasks
            if($targetName == 'Shipment') {
                $this->runShipmentsTasks($targetTasks);
            }
        }

        $this->info("Sync completed");
        return;
    }


    public function runShipmentsTasks($targetTasks) {

        $actions = $targetTasks->groupBy('action');

        if(@$actions['notification']) {

            $rows = $actions['notification'];
            $ids = $rows->pluck('target_id')->toArray();
            $shipments = Shipment::with('customer')->whereIn('id', $ids)->get();

            foreach ($shipments as $shipment) {

                try {
                    $shipment->sendEmail();
                } catch (\Exception $e) {}

                try {
                    $shipment->sendSms();
                } catch (\Exception $e) {}

            }

            $tasksIds = $targetTasks->pluck('id')->toArray();
            ScheduledTask::whereIn('id', $tasksIds)->delete();
        }

    }
}
