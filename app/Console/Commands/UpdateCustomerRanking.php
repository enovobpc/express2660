<?php

namespace App\Console\Commands;

use App\Models\Billing;
use App\Models\Customer;
use App\Models\CustomerCovenant;
use App\Models\CustomerRanking;
use App\Models\Shipment;
use App\Models\ShippingStatus;
use Illuminate\Console\Command;
use DB;

class UpdateCustomerRanking extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer:updateRanking {year?} {month?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync customer ranking';

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

        $this->line("\n===============================================");
        $this->line("=========== UPDATE CUSTOMERS RANKING ==========");
        $this->line("===============================================\n");

        $source = config('app.source');
        $year   = empty($this->argument('year')) ? date('Y') : $this->argument('year');
        $months = empty($this->argument('month')) ? [date('m')] : [$this->argument('month')];

        if($this->argument('month') == 'all') {
            if($year == date('Y')) {
                $months = range(1, date('m'));
            } else {
                $months = range(1, 12);
            }
        }

        foreach ($months as $month) {
            $periodDates = Billing::getPeriodDates($year, $month);
            $periodFirstDay = $periodDates['first'];
            $periodLastDay = $periodDates['last'];

            $covenantsCustomers = CustomerCovenant::leftJoin('customers', 'customers.id', '=', 'customers_covenants.customer_id')
                ->where('start_date', '<=', $periodFirstDay)
                ->where('end_date', '>=', $periodLastDay)
                ->where('type', 'fixed')
                ->where('customers.source', $source)
                ->pluck('customers.id')
                ->toArray();

            $bindings = [
                'customers.id',
                DB::raw('sum(volumes) as volumes'),
                DB::raw('count(total_price) as count_shipments'),
                DB::raw('sum(total_price_for_recipient) as total_recipient'),

                DB::raw('(select sum(total_price) from shipments where deleted_at is null and ignore_billing = 0 and payment_at_recipient = 0 and status_id <> ' . ShippingStatus::CANCELED_ID . ' and (billing_date between "' . $periodFirstDay . '" and "' . $periodLastDay . '") and customer_id = customers.id) as total_shipments'),
                DB::raw('(select sum(subtotal) from products_sales where deleted_at is null and (products_sales.created_at between "' . $periodFirstDay . '" and "' . $periodLastDay . '") and products_sales.customer_id = customers.id) as total_products'),
                DB::raw('(select sum(subtotal) from shipments_assigned_expenses where deleted_at is null and ignore_billing = 0 and shipments_assigned_expenses.shipment_id in (SELECT id from shipments where deleted_at is null and (billing_date between "' . $periodFirstDay . '" and "' . $periodLastDay . '") and customer_id = customers.id)) as total_expenses'),
                DB::raw('(select sum(amount) from customers_covenants where deleted_at is null and start_date <= "' . $periodFirstDay . '" and end_date >= "' . $periodLastDay . '" and customer_id = customers.id) as total_covenants'),

                DB::raw('(select avg(total_price) from shipments where deleted_at is null and ignore_billing = 0 and payment_at_recipient = 0 and status_id <> ' . ShippingStatus::CANCELED_ID . ' and (billing_date between "' . $periodFirstDay . '" and "' . $periodLastDay . '") and customer_id = customers.id) as shipments_avg'),
                //DB::raw('(select sum(cost_price) from shipments_assigned_expenses where deleted_at is null and shipments_assigned_expenses.shipment_id in (SELECT id from shipments where deleted_at is null and (billing_date between "'.$periodFirstDay.'" and "'.$periodLastDay.'") and customer_id = customers.id)) as total_expenses_cost'),
                //DB::raw('sum(cost_price) as total_cost'),
            ];

            $customers = Customer::leftJoin('shipments', 'customers.id', '=', 'shipments.customer_id')
                ->where(function ($q) use ($periodFirstDay, $periodLastDay, $covenantsCustomers) {
                    $q->whereBetween('billing_date', [$periodFirstDay, $periodLastDay]);
                    $q->orWhereIn('customers.id', $covenantsCustomers);
                })
                ->where('customers.source', $source)
                ->groupBy('customers.name')
                ->get($bindings);


            //get AVG values
            $shipmentsAvg = Shipment::whereBetween('billing_date', [$periodFirstDay, $periodLastDay])
                ->groupBy('customer_id')
                ->get([
                    'customer_id',
                    DB::raw('avg(total_price) as price_avg'),
                    DB::raw('avg(total_expenses) as expenses_avg'),
                    DB::raw('avg(weight) as weight_avg'),
                    DB::raw('avg(volumes) as volumes_avg')
                ]);

            $position = [];
            foreach ($customers as $customer) {

                $total = $customer->total_shipments + $customer->total_recipient + $customer->total_expenses + $customer->total_products + $customer->total_covenants;

                if(!empty($customer->count_shipments) && !empty($total)) {

                    $avg = $shipmentsAvg->filter(function($item) use($customer) {
                        return $item->customer_id == $customer->id;
                    })->first();

                    $ranking = CustomerRanking::firstOrNew([
                        'customer_id' => $customer->id,
                        'year' => $year,
                        'month' => $month
                    ]);

                    $ranking->year        = $year;
                    $ranking->month       = $month;
                    $ranking->shipments   = $customer->count_shipments;
                    $ranking->volumes     = $customer->volumes;
                    $ranking->billing     = $total;
                    $ranking->price_avg   = $avg ? $avg->price_avg + $avg->expenses_avg : 0;
                    $ranking->weight_avg  = $avg ? $avg->weight_avg : 0;
                    $ranking->volumes_avg = $avg ? $avg->volumes_avg : 0;
                    $ranking->save();
                }
            }
        }
        $this->info("Ranking updated");
        return;
    }
}
