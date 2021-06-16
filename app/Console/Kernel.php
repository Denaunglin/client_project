<?php

namespace App\Console;

use App\Console\Commands\NotificationCleaner;
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
        NotificationCleaner::class,

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('php artisan backup:run --only-db')->dailyAt('23:00')
            ->runInBackground()
            ->environments(['production']);

        // over 1 weeks notification cleaner
        $schedule->command('notificationcleaner:run')
            ->dailyAt('03:00')
            ->runInBackground()
            ->environments(['production', 'local']);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
