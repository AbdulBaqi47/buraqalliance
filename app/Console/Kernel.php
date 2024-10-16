<?php

namespace App\Console;

use App\Jobs\Scheduled\InstallmentJob;
use App\Jobs\Scheduled\TransactionLedgerJob;
use App\Models\Tenant\Installment;
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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        # Horizon Snapshot - Track stats against job
        $schedule->exec('php8.1 artisan horizon:snapshot')->everyFiveMinutes();

        // $schedule->exec('php8.1 artisan backup:clean')->daily()->at('01:00');
        // $schedule->exec('php8.1 artisan backup:run')->daily()->at('01:30');



    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
