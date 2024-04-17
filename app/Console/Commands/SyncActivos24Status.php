<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\InvoiceGateway\OnSearch\Document;
use App\Models\InvoiceGateway\OnSearch\Item;
use App\Models\Logistic\Product;
use App\Models\Logistic\ProductImage;
use Illuminate\Console\Command;
use DB, File;

class SyncActivos24Status extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:activos24-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync ACTIVOS 24 logistic DB';

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
        
        $this->info("Sync ACTIVOS24");

        $doc = new Document();
        $doc->syncDocumentsStatus();

        $this->info("Sync completed");
        return;
    }
}
