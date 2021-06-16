<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Messages;
use App\Models\Payslip;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Request $request)
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        if (!$this->app->runningInConsole()) {
            view()->composer('*', function ($view) use ($request) {
                $newBookings = Booking::whereDate('created_at', date('Y-m-d'))->whereIn('status', [0, 1])->where('trash', 0)->get();
                $newMessages = Messages::whereDate('created_at', date('Y-m-d'))->where('trash', 0)->get();
                $newUsers = User::whereDate('created_at', date('Y-m-d'))->where('trash', 0)->get();
                $newPayslip = Payslip::where('trash', 0)->where('read_at', 0)->get();
                $notis = 0;

                if (auth()->check()) {
                    $noti = Auth::user()->notifications();
                    if ($noti) {
                        $notis = $noti->where('read_at', null)->get();
                    }
                }

                $view->with([
                    'newBookings' => $newBookings,
                    'newMessages' => $newMessages,
                    'newUsers' => $newUsers,
                    'unreadNoti' => $notis,
                    'newPayslip' => $newPayslip,
                ]);
            });
        }
    }
}
