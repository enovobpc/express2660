<?php

namespace App\Console\Commands;

use App\Models\Agency;
use App\Models\Customer;
use App\Models\InvoiceGateway\OnSearch\Item;
use App\Models\Logistic\Product;
use App\Models\Logistic\ProductImage;
use App\Models\Provider;
use App\Models\Webservice\Sending;
use Illuminate\Console\Command;
use DB, File;

class SyncWebserviceSending extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:webservice-sending {action} {agency?} {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Sending Webservice';

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

        $action     = $this->argument('action');
        $agencyCode = $this->argument('agency');
        $date       = $this->argument('date');
        $date       = $date ? $date : date('Y-m-d');

        $webservice = new Sending();

        if ($action == 'import') {
            $webservice->importShipments();
            $webservice->importIncidencesSolutions();
        } elseif ($action == 'export-tracking') {
            $webservice->exportTrackings($agencyCode);
        } elseif ($action == 'export-traceability') {
            $webservice->exportTraceability($agencyCode);
            $webservice->exportRefunds($agencyCode);
        } elseif($action == 'export-refunds') {
            $webservice->exportRefunds($agencyCode);
        }

        $this->info("Sync completed");
        return;
    }
}
