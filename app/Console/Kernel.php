<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\SyncPermission::class,
        Commands\FtpImporter::class,
        Commands\SyncShipment::class,
        Commands\SyncShipmentStatus::class,
        //Commands\SyncBalanceDocuments::class,
        Commands\SyncBudgetEmailAnswers::class,
        Commands\SendDailyReport::class,
        Commands\ImportInvoicesFromGateway::class,
        Commands\UpdateCustomerRanking::class,
        Commands\NotifyInvoicesValidity::class,
        Commands\SyncPlatforms::class,
        Commands\RunInvoiceSchedules::class,
        Commands\RunShipmentSchedules::class,
        Commands\CheckBudgetsStatus::class,
        //Commands\SyncPickups::class,
        Commands\CheckValidities::class,
        Commands\RunScheduledTasks::class,
        Commands\RunDailyTasks::class,
        Commands\RunNotify::class,
        Commands\SyncDsv::class,
        Commands\SyncEtcp::class,
        Commands\SyncLeroyMerlin::class,
        Commands\SyncActivos24::class,
        Commands\SyncActivos24Status::class,
        Commands\CleanCache::class,
        Commands\SyncWebserviceSending::class,
        Commands\SyncSageX3::class,
        Commands\SyncPHCRapidix::class,
        Commands\FtpLaRedout::class,
        Commands\FtpPowerBi::class,
        Commands\SepaNotifyErrors::class,
        Commands\CleanOldFiles::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
