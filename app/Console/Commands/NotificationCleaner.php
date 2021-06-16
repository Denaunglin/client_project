<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class NotificationCleaner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notificationcleaner:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Over 1 Weeks Notification Cleaner';

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
        $now = Carbon::now();
        $result = DB::table('notifications')
            ->where('created_at', '<', $now->subDays(7))
            ->delete();

        return true;

    }
}
