<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;
use DB, Date, Setting, Mail;

class NotifyInvoicesValidity extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:notifyValidity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify Invoice Validities';

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

        $this->line("\n===============================================");
        $this->line("=========== NOTIFY INVOICE VALIDITIES ==========");
        $this->line("===============================================\n");

        $today = Date::today();

        if (Setting::get('billing_remember_duedate')) {

            //obtem faturas a expirar
            $validityDays = array_filter(explode(',', Setting::get('billing_remember_duedate_days')));
            $validityDays = empty($validityDays) ? [10, 5, 3, 2, 1, 0] : $validityDays;

            $dates = [];
            foreach ($validityDays as $day) {
                $dates[$day] = $today->copy()->addDays($day)->format('Y-m-d');
            }

            //search invoices that expires in dates values
            $invoices = Invoice::with('customer')
                ->whereHas('customer', function ($q) {
                    $q->where('payment_method', '<>', 'dbt');
                })
                ->filterSource()
                ->where('doc_type', 'invoice')
                ->where('is_settle', 0)
                ->where('is_scheduled', 0)
                ->where('is_deleted', 0)
                ->where('is_draft', 0)
                ->whereIn('due_date', $dates)
                ->take(500)
                ->get();

            $sendedEmails = 0;
            foreach ($invoices as $invoice) {

                if ($sendedEmails == 50) {
                    $sendedEmails = 0;
                    sleep(5);
                }

                $customer = @$invoice->customer;
                if ($customer && !empty(@$customer->billing_email)) {

                    $invoiceName = $invoice->doc_series . ' ' . $invoice->doc_id;

                    $dueDate  = new Date($invoice->due_date);
                    $daysLeft = $dueDate->diffInDays($today);

                    $daysLeftLabel = 'Restam ' . $daysLeft . ' dias';
                    if ($daysLeft == 1) {
                        $daysLeftLabel = 'Resta 1 dia';
                    } elseif ($daysLeft == 0) {
                        $daysLeftLabel = 'Último dia';
                    }

                    $subject = 'Aviso Vencimento ' . $invoiceName . ' (' . $daysLeftLabel . ')';
                    $emails = validateNotificationEmails(@$customer->billing_email);
                    try {
                        Mail::send('emails.billing.invoice_validity', compact('locale', 'invoice', 'daysLeft'), function ($message) use ($emails, $subject) {
                            $message->to($emails['valid'])
                                ->subject($subject);
                        });
                    } catch (\Exception $e) {
                        $this->info("Error. Não foi possível enviar o e-mail para " . implode(',', $emails['valid']));
                    }

                    $sendedEmails++;
                }
            }
        }

        $this->info("Notify Invoice Validities");
        return;
    }
}
