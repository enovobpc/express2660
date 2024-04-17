<?php

namespace App\Console\Commands;

use App\Models\Agency;
use App\Models\Customer;
use App\Models\InvoiceGateway\OnSearch\Item;
use App\Models\Logistic\Product;
use App\Models\Logistic\ProductImage;
use App\Models\Provider;
use Illuminate\Console\Command;
use DB, File;
use Illuminate\Filesystem\Filesystem;

class CleanCache extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean system cache';

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

        $folders = [
            storage_path('/framework/cache'),
            storage_path('/framework/views'),
            storage_path('/framework/sessions'),
            storage_path('/debugbar'),
            storage_path('/importer'),
            storage_path('/keyinvoice-logs')
        ];

        foreach ($folders as $directory) {
            $file = new Filesystem();
            @$file->cleanDirectory($directory);

            File::put($directory.'/.gitignore', '');
        }

        $this->info("clean completed");
        return;
    }

}
