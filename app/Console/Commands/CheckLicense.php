<?php

namespace App\Console\Commands;

use App\Models\BroadcastPusher;
use App\Models\License;
use App\Models\LicensePayment;
use App\Models\Notification;
use Illuminate\Console\Command;
use File, Mail, Date;

class CheckLicense extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'license:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check license';

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
        
        $this->info("Check license");

  /*      $source = config('app.source');

        $license = License::where('source', $source)->first();
        $emails  = [$license->email];
        $status  = 'active';
        $countUnpaid = 0;
        $totalUnpaid = 0;

        //Detecta próximos pagamentos (quando faltarem 30 dias)
        $unpaid = LicensePayment::whereHas('license', function($q) use($source) {
                $q->where('source', $source);
            })
            ->whereRaw('DATE(payment_deadline) < "'.date('Y-m-d').'"')
            ->whereNull('payment_date')
            ->get();


        if(!$unpaid->isEmpty()) {
            $countUnpaid = $unpaid->count();
            $totalUnpaid = $unpaid->sum('total');
        }


        $nextDeadline = LicensePayment::whereHas('license', function($q) use($source) {
                $q->where('source', $source);
            })
            ->whereNull('payment_method')
            ->whereRaw('payment_deadline > "'.date('Y-m-d').'"')
            ->whereRaw('DATEDIFF(payment_deadline, "'.date('Y-m-d').'") <= 15')
            ->first();

        if(!empty($nextDeadline)) {

            $today = Date::today();
            $date  = new Date($nextDeadline->payment_deadline);
            $diff  = $date->diffInDays($today);

            //envia e-mail aos 5 dias anteriores ao fim do limite para pagamento
            if(in_array($diff, [5, 4, 3, 2, 1])) {

                if($countUnpaid + 1 == $license->unpaid_limit) { //vai atingir o limite
                    $days = $diff;
                    $deadlineDate = $date->format('Y-m-d');
                    Mail::send('emails.license.pre_block', compact('countUnpaid', 'totalUnpaid', 'days', 'deadlineDate'), function ($message) use($emails) {
                        $message->to($emails)
                            ->subject('[AVISO] Pagamentos em Atraso - Bloqueio do Sistema.');
                    });

                } else {

                    $days = $diff;
                    $deadlineDate = $date->format('Y-m-d');
                    Mail::send('emails.license.deadline', compact('countUnpaid', 'totalUnpaid', 'days', 'deadlineDate'), function ($message) use($emails, $days) {
                        $message->to($emails)
                            ->subject('[LEMBRETE] Restam ' . $days . ' dias para a data limite de pagamento');
                    });
                }

                $license->setNotification(BroadcastPusher::getGlobalChannel(), "Restam ".$days." dias para a data limite de pagamento");
                $this->info("Notificação E-mail: " . $diff . " dias para terminar prazo de pagamento.");
            }
        }

        //Detecta pagamentos em atraso
        if(!$unpaid->isEmpty()) {
            //bloqueia sistema se +2 prestações por pagar
            if ($license->unpaid_limit <= $countUnpaid) {
                $status = 'expired';

                //notifica que a plataforma foi bloqueada
                $deadlineDate = $unpaid->last()->payment_deadline->format('Y-m-d');
                Mail::send('emails.license.blocked', compact('countUnpaid', 'totalUnpaid', 'deadlineDate'), function ($message) use($emails, $countUnpaid) {
                    $message->to($emails)
                        ->subject('[AVISO] ACESSO À PLATAFORMA BLOQUEADO');
                });

                $license->setNotification(BroadcastPusher::getGlobalChannel(), "Sistema bloqueado por falta de pagamentos.");
                $this->info("Enviado e-mail de bloqueio");
            } else {

                $today = Date::today();
                $date = new Date($unpaid->last()->payment_deadline);
                $diff = $date->diffInDays($today);

                //envia e-mail aos 5 dias anteriores
                if (in_array($diff, [5, 4, 3, 2, 1])) {

                    $deadlineDate = $unpaid->last()->payment_deadline->format('Y-m-d');
                    Mail::send('emails.license.unpaid', compact('countUnpaid', 'totalUnpaid', 'deadlineDate'), function ($message) use($emails, $countUnpaid) {
                        $message->to($emails)
                            ->subject('[AVISO] Tem '.$countUnpaid.' pagamento(s) em atraso');
                    });

                    $license->setNotification(BroadcastPusher::getGlobalChannel(), "Existe " . $countUnpaid . " pagamento(s) em atraso.");
                    $this->info("Notificação E-mail: " . $diff . " pagamentos em atraso.");
                }
            }
        }

        $license->status = $status;
        $license->save();

        //Prepara array
        $filename = storage_path() . '/license.json';
        if($status == 'expired') {
            File::put($filename, '');
        } else {
            File::delete($filename);
        }*/

        $this->info("License updated");
        return;
    }
}
