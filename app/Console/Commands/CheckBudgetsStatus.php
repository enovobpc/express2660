<?php

namespace App\Console\Commands;

use App\Models\Budget\Ticket;
use App\Models\Budget\BudgetCourier;
use Illuminate\Console\Command;

class CheckBudgetsStatus extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'budget:check-status {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel budgets outdated';

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
        
        $this->info("Cancel budgets outdated\n");

        $date = $this->argument('date');

        //cancel budgets
        if(hasModule('budgets_animals') || hasModule('budgets_courier')) {
            BudgetCourier::cancelOutdated($date);

            //notify budgets
            BudgetCourier::reminderBudget($date);
        }

        if(hasModule('budgets')) {
            //cancel email budgets
            Ticket::cancelOutdated($date);
        }

        $this->info("Sync completed.");
        return;
    }
}
