<?php

namespace App\Console\Commands;

use App\Models\CustomerBalance;
use App\Models\Customer;
use App\Models\LogViewer;
use Illuminate\Console\Command;
use DB, Date, Setting, Mail;
use Illuminate\Support\Facades\Log;

class ImportInvoicesFromGateway extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer:importInvoicesFromGateway';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import invoices from gateway';

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
        $this->line("=========== UPDATE CUSTOMERS BALANCE ==========");
        $this->line("===============================================\n");

        $today    = Date::today();
        $endMonth = $today->copy()->endOfMonth()->day;

        //dias para atualização automática
        $syncDays = [10,15,20,25,30,$endMonth];

        if(in_array($today->day, $syncDays)) {

            //obtem os clientes com documentos por pagar
            /*$customers = Customer::filterSource()
                ->where('balance_count_unpaid', '>', '1')
                ->where('balance_last_update', '<', $today->format('Y-m-d'))
                ->get(['id', 'name', 'contact_email', 'billing_email', 'balance_last_update', 'balance_total_unpaid', 'balance_count_unpaid']);*/

            $customers = Customer::filterSource()->get(['id', 'name', 'contact_email', 'billing_email', 'balance_last_update', 'balance_total_unpaid', 'balance_count_unpaid']);

            $errors = [];
            foreach ($customers as $customer) {

                try {

                    if(in_array(config('app.source'), ['avatrans','rimaalbe','transrimarocha'])) { //atualiza conta corrente de todos os clientes

                        if($customer->balance_last_update < $today->format('Y-m-d') && $customer->balance_count_unpaid > 1) {
                            //atualiza estados de pagamento dos documentos
                            $result = CustomerBalance::updatePaymentStatus($customer->id);

                            $value = @$result['totalUnpaid'];
                            if(str_contains($value, ',')) { //32,48
                                if(str_contains($value, '.')) { //ex: 1.723,32
                                    $value = str_replace('.', '', $value);
                                    $value = str_replace(',', '.', $value);
                                } else {
                                    $value = str_replace(',', '.', $value);
                                }

                                @$result['totalUnpaid'] = $value;
                            }

                            //atualiza os contadores da ficha do cliente
                            Customer::where('id', $customer->id)
                                ->update([
                                    'balance_total_unpaid'  => @$result['totalUnpaid'],
                                    'balance_count_unpaid'  => @$result['countDocsUnpaid'],
                                    'balance_count_expired' => @$result['countDocsExpired'],
                                    'balance_last_update'   => date('Y-m-d H:i:s'),
                                ]);
                        }
                    }

                    if (Setting::get('billing_send_balance_auto') && $today->day == $endMonth) {
                        $customer->sendEmailAccountBalance();
                    }

                } catch (\Exception $e) {
                    $errors[$customer->name] = $e->getMessage();
                }
            }

            if($errors) {
                $trace = LogViewer::getTrace(null, 'Atualização conta corrente ' . count($errors).' erros');
                Log::error(br2nl($trace));
                throw new \Exception(count($errors) .' erros durante a execução.');
            }

        }

        $this->info("Update customers balance");
        return;
    }
}
