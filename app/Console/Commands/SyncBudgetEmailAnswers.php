<?php

namespace App\Console\Commands;

use App\Models\Budget\Message;
use Illuminate\Console\Command;
use App\Models\Webservice;

class SyncBudgetEmailAnswers extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'budget:SyncEmailAnswers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync budget email answers';

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
        
        $this->info("Sync budget email answers\n");

        $message = new Message();
        $message->checkAnswers();

        $this->info("Sync completed");
        return;
    }
}
