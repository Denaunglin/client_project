<?php

namespace App\Providers;

use App\Models\Item;
use App\Models\User;
use App\Models\Booking;
use App\Models\Payslip;
use App\Models\Messages;
use App\Models\ShopStorage;
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
                $newUsers = User::whereDate('created_at', date('Y-m-d'))->where('trash', 0)->get();
                $newPayslip = Payslip::where('trash', 0)->where('read_at', 0)->get();
              //order
                $neworder = 0;
                $item = Item::where('trash',0)->get();
                $shop_storage = ShopStorage::where('trash',0)->get();

                $order_item = [];
                foreach($item as $item_data){
                    $get_order_item  = ShopStorage::where('item_id',$item_data->id)->where('qty','<',$item_data->minimun_qty )->first();
                    if($get_order_item != null){
                        $order_item [] = $get_order_item;
                    }
                }
        
                $order_list = [];
                foreach($order_item as $data){
                    $order_list[] = Item::where('id',$data->item_id)->first();
                }

                $neworder = $order_list;

                

            //order
                $notis = 0;

                if (auth()->check()) {
                    $noti = Auth::user()->notifications();
                    if ($noti) {
                        $notis = $noti->where('read_at', null)->get();
                    }
                }

                $view->with([
                    'newUsers' => $newUsers,
                    'unreadNoti' => $notis,
                    'newPayslip' => $newPayslip,
                    'neworder' => $neworder,
                ]);
            });
        }
    }
}
