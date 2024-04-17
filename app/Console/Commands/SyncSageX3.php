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

class SyncSageX3 extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:sageX3 {action?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync SAGE X3 DB';

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

        $action = $this->argument('action');

        if($action == 'providers') {
            $this->syncProviders();
        } else {
            $this->syncCustomers();
        }

        $this->info("Sync completed");
        return;
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function syncCustomers() {

        $this->info("Sync X3 Customers");

        $agency = Agency::where('source', config('app.source'))->first();

        $types = [
            'MI' => '1',//MI - Mercado Internacional
            'MN' => '2',//MN - Mercado Nacional
            'OM' => '3',//OM - Outros Mercados
        ];

        $sage = new \App\Models\InvoiceGateway\SageX3\Customer();
        $sageData = $sage->listsCustomers();

        foreach ($sageData as $row) {

            unset($row['code']);
            $paymentMethod = @$row['payment_method'] ? str_replace('PT', '', $row['payment_method']).'d' : '';

            if(@$row['vat']) {

                $exists = false;
                $customer = Customer::firstOrNew([
                    'vat' => $row['vat']
                ]);

                if($customer->exists) {
                    $exists = true;
                }

                @$row['active'] = @$row['active'] == '2' ? 1 : 0;


                $customer->fill($row);
                $customer->payment_method = $paymentMethod;
                $customer->type_id   = @$types[$customer->type] ? $types[$customer->type] : '3';
                $customer->country   = strtolower($customer->country);
                $customer->source    = config('app.source');
                $customer->active    = $row['active'];
                $customer->is_active = $row['active'];
                $customer->agency_id = $agency->id;

                if($exists) {
                    $customer->save();
                } else {
                    $customer->setCode();
                }

            }
        }

        $this->info("Sync completed");
        return;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function syncProviders() {

        $this->info("Sync X3 Providrrs");

        $agency = Agency::where('source', config('app.source'))->first();

        $sage = new \App\Models\InvoiceGateway\SageX3\Provider();
        $sageData = $sage->listsProviders();

        foreach ($sageData as $row) {

            if(@$row['payment_method']) {
                $paymentMethod = str_replace('PT', '', $row['payment_method']).'d';
            }

            if($row['vat']) {
                $provider = Provider::firstOrNew([
                    'vat' => @$row['vat']
                ]);

                $exists = false;
                if($provider->exists) {
                    $exists = true;
                }

                $provider->fill($row);
                $provider->payment_method = $paymentMethod;
                $provider->category_id = 10; //outros
                $provider->type        = 'others';
                $provider->source      = config('app.source');
                $provider->agencies    = [$agency->id];

                if($exists) {
                    $provider->save();
                } else {
                    $provider->setCode();
                }
            }
        }

        $this->info("Sync completed");
        return;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function syncInvoices() {

        $this->info("Sync X3 Invoices");


        $this->info("Sync completed");
        return;
    }
}
