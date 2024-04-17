<?php

namespace App\Console\Commands;

use App\Models\Agency;
use App\Models\Billing\ApiKey;
use App\Models\Budget\Budget;
use App\Models\CacheSetting;
use App\Models\Core\Setting as CoreSetting;
use App\Models\Cpanel\Quota;
use App\Models\FleetGest\Cost;
use App\Models\FleetGest\FixedCost;
use App\Models\GatewayPayment\Base;
use App\Models\Invoice;
use App\Models\Notice;
use App\Models\Saft;
use App\Models\Shipment;
use App\Models\UserExpense;
use Illuminate\Console\Command;
use File, Mail, Date, DB, Setting;

class RunDailyTasks extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:daily-tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run daily tasks and cronjobs';

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

        $this->info("Running Daily Tasks");

        $today = Date::today();
        $curMonth = $today->month;
        $curYear  = $today->year;


        //verifica envio SAFT
        if(hasModule('invoices')) {
            try {

                //força qualquer registo que esteja com valor pendente 0.00 a ficar como pago
                Invoice::where('doc_total_pending', '0.00')
                    ->where('is_settle', 0)
                    ->update(['is_settle' => 1]);


                $saftDay = Setting::get('saft_day') ? Setting::get('saft_day') : '8';
                if (Setting::get('saft_send_auto') && Setting::get('accountant_email') && $today->day == $saftDay) {

                    $lastMonth = $today->subMonth(1);
                    $result = Saft::sendMail($lastMonth->year, $lastMonth->month, Setting::get('accountant_email'));

                    if (!$result['result']) {
                        Saft::sendNotification();
                    }

                } else {
                    Saft::sendNotification();
                }
            } catch (\Exception $e) {
                Saft::sendNotification();
            }
        }

        //verifica validade das chaves API
        ApiKey::where('end_date', '<', date('Y-m-d'))
            ->update(['is_active' => 0]);
        ApiKey::where('start_date', date('Y-m-d'))
            ->update(['is_active' => 1]);

        //Invoices schedule
        $this->call('invoice:schedule');

        //shipment schedules
        $this->call('shipment:schedule');

        //read fixed costs and store on expenses tables
        $this->storeVehiclesFixedCosts($today);
        $this->storeUsersFixedCosts($today);

        //check validities
        $this->call('validities:check');


        //update accounts balance
        $this->call('customer:importInvoicesFromGateway');

        //notify invoices validity
        $this->call('invoice:notifyValidity');

        //update customer ranking
        $endMonth = $today->endOfMonth()->day;
        if(in_array($today->day, [10,20, $endMonth])) {
            $this->call('customer:updateRanking');
        }

        //update Cache Settings
        if(Setting::get('shipments_limit_search')) {

            $minDate = $today->copy()->subMonth(Setting::get('shipments_limit_search'))->format('Y-m-d');

            $sourceAgencies = Agency::filterSource()->pluck('id')->toArray();
            $minShipment = Shipment::whereIn('agency_id', $sourceAgencies)
                ->where('date', '>=', $minDate)->first(['id', 'date']);

            if($minShipment) {
                CacheSetting::set('shipments_limit_search', $minShipment->id);
                CacheSetting::set('shipments_limit_search_date', $minShipment->date);
            }

            if(hasModule('budgets')) {
                $minBudget = Budget::where('source', config('app.source'))
                    ->where('date', '>=', $minDate)
                    ->first(['id', 'date']);

                if($minBudget) {
                    CacheSetting::set('budgets_limit_search', $minBudget->id);
                }
            }
        }

        //cancel payments
        if(hasModule('gateway_payments')) {
            Base::filterSource()
                ->whereNotNull('expires_at')
                ->whereRaw('DATE(expires_at) = "' .date('Y-m-d').'"')
                ->whereIn('status', [Base::STATUS_PENDING, Base::STATUS_WAINTING])
                ->update([
                    'status' => Base::STATUS_REJECTED
                ]);
        }

        $this->checkDiskQuota();

        //update core database settings
        $this->updateAppCoreSettings();

        //clean cache
        $this->call('clean:cache');

        //clean old files
        $this->call('clean:oldfiles');

        $this->info("Sync completed");
        return;
    }

    /**
     * Store vehcile fixed costs
     */
    public function storeVehiclesFixedCosts($today, $forceToday = false, $vehicleId=null){

        //força todos os registos de custos a ficarem com o tipo correto de acordo com a categoria do fornecedor
        $startDate  = date('Y-m-d');
        $endDate    = date('Y-m-d');
        $fleetCosts = \App\Models\FleetGest\Cost::with('provider')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        foreach($fleetCosts as $cost) {
            $cost->type_id = @$cost->provider->category_id;
            $cost->save();
        }

        if($forceToday || in_array($today->day, [1])) {
            $vehicleFixedCosts = FixedCost::filterSource()
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today);

            if($vehicleId) {
                $vehicleFixedCosts = $vehicleFixedCosts->where('vehicle_id', $vehicleId);
            }

            $vehicleFixedCosts = $vehicleFixedCosts->get();

            $todayDt = $today->format('Y-m-d');
            foreach ($vehicleFixedCosts as $vehicleFixedCost) {
                $vehicleCost = Cost::firstOrNew([
                    'source_type' => 'FixedCost',
                    'source_id'   => $vehicleFixedCost->id,
                    'date'        => $todayDt
                ]);
                $vehicleCost->type        = 'fixed';
                $vehicleCost->source_type = 'FixedCost';
                $vehicleCost->source_id   = $vehicleFixedCost->id;
                $vehicleCost->vehicle_id  = $vehicleFixedCost->vehicle_id;
                $vehicleCost->provider_id = @$vehicleFixedCost->provider_id;
                $vehicleCost->type_id     = @$vehicleFixedCost->type_id;
                $vehicleCost->type        = @$vehicleFixedCost->type;
                $vehicleCost->description = @$vehicleFixedCost->description;
                $vehicleCost->total       = @$vehicleFixedCost->total;
                $vehicleCost->obs         = @$vehicleFixedCost->obs;
                $vehicleCost->date        = $todayDt;
                $vehicleCost->save();
            }
        }
    }

    /**
     * Store users fixed costs
     */
    public function storeUsersFixedCosts($today, $forceToday = false){


        if($forceToday || in_array($today->day, [1])) {

            $userFixedCosts = UserExpense::filterSource()
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->get();

            foreach ($userFixedCosts as $userFixedCost) {
                $userCost = new UserExpense();
                $userCost->fill($userFixedCost->toArray());
                $userCost->start_date  = null;
                $userCost->end_date    = null;
                $userCost->is_fixed    = 0;
                $userCost->assigned_invoice_id = null;
                $userCost->created_by  = null;
                $userCost->date        = date('Y-m-d');
                $userCost->save();
            }
        }

    }


    /**
     * Store users fixed costs
     */
    public function checkDiskQuota(){

        $day = date('d');

        if(in_array($day, [1,5,10,15,20,22,25])) {

            $quota = new Quota();
            $quota = $quota->getServerQuota();

            $usageTotal = @$quota['megabytes_used']; //converte para bytes
            //$quotaTotal = @$quota['megabyte_limit']; //obtem da API
            $quotaTotal = CacheSetting::get('quota') * 1000;

            $quotaTotal = $quotaTotal * 1000000; //converte para bytes
            $usageTotal = $usageTotal * 1000000; //converte para bytes

            $percent = $quotaTotal ? ($usageTotal * 100) / $quotaTotal : 0;

            if($percent >= 90) {
                if($percent < 100.00) {
                    $title = '<b><i class="fas fa-exclamation-triangle"></i>Espaço em sistema reduzido ('.money($percent, 1).'% ocupado)</b>';
                    $subtitle = 'O espaço livre no sistema está reduzido. Liberte espaço ou contrate mais espaço para evitar quebras no serviço.';
                    $content = '<p>O espaço livre no sistema está reduzido. Liberte espaço ou contrate mais espaço para evitar quebras no serviço.<br/>Pode libertar espaço apagando e-mails dos quais já não necessita.</p>';
                } else {
                    $title = '<b><i class="fas fa-exclamation-triangle"></i> Espaço em disco excedeu o limite contratado</b>';
                    $subtitle = 'Atualmente está a consumir mais <b>'. human_filesize($usageTotal - $quotaTotal) .'</b> do que o espaço contratado. Poderão ser cobrado custos extra por espaço adicional.';
                    $content = '<p>Esta sem espaço em sistema, pelo que poderão ser cobradas taxas adicionais pelo espaço execido.</p>';
                }

                //get notification recipients
                $recipients = \App\Models\User::where('source', config('app.source'))
                    ->whereHas('roles', function($query) {
                        $query->where('name','administrator');
                        $query->orWhere('name', 'agencia');
                        $query->orWhere('name', 'administrativo');
                    })
                    ->get();


                Notice::where('source_type', 'quota')->delete();

                if (!$recipients->isEmpty()) {
                    $notice = new Notice();
                    $notice->title = $title;
                    $notice->summary = $subtitle;
                    $notice->content = $content;
                    $notice->date = date('Y-m-d');
                    $notice->sources = [config('app.source')];
                    $notice->source_type = 'quota';
                    $notice->level = 'danger';
                    $notice->published = 1;
                    $notice->auto = 1;
                    $notice->save();

                    $recipientsIds = $recipients->pluck('id')->toArray();
                    $notice->users()->sync($recipientsIds);

                    foreach ($recipients as $recipient) {
                        $recipient->count_notices = $recipient->count_notices + 1;
                        $recipient->save();
                    }
                }
            }
        }
    }

    public function updateAppCoreSettings() {
        return CacheSetting::syncCoreDBSettings();
    }
}
