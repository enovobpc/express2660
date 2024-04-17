<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncBalanceDocuments extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'balance:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync balance documents by webservice';

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
        
        $this->info("Sync balance documents by webservice\n");

 /*       $source = config('app.source');
        $totalImported = 0;

        try {
            $history = new Customer();
            $totalImported = $history->syncCustomerHistory(null, $source);
        } catch (\Exception $e) {}

        $this->info("Sync completed. Source: " . $source . " Imported: " . $totalImported);*/

        return;
    }
}
