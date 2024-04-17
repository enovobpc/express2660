<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\SepaTransfer\Payment;
use App\Models\SepaTransfer\PaymentTransaction;
use Illuminate\Console\Command;
use Setting, DB, Mail;

class SepaNotifyErrors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sepa:notify-errors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifica erros SEPA';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Notifica erros SEPA\n");

        try {
            $payments = Payment::filterSource()
                ->whereNotIn('status', [Payment::STATUS_EDITING, Payment::STATUS_PENDING])
                ->where('has_errors', 1)
                ->where('errors_processed', 0)
                ->get();

            foreach ($payments as $payment) {

                $paymentTransactions = PaymentTransaction::where('payment_id', $payment->id)
                    ->whereNotNull('error_code')
                    ->get();

                foreach ($paymentTransactions as $paymentTransaction) {
                    $paymentTransaction->notifyTransactionError();
                }

                $payment->update(['errors_processed' => 1]);
            }
        } catch (\Exception $e) {
            $info = 'Falha ao executar sepa:notify-errors [' .$e->getMessage().' file '. $e->getFile() .' line '. $e->getLine() .']';
            Mail::raw($info, function ($message) {
                $message->to('paulo.costa@enovo.pt')
                    ->subject('Falha de comando');
            });
            return;
        }
    }
}