<?php

namespace App\Http\Controllers\Backend\Admin;

use Response;
use App\Models\Tax;
use App\Models\Item;
use App\Models\User;
use App\Models\Rooms;
use App\Models\Discounts;
use App\Models\AccountType;
use Darryldecode\Cart\Cart;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use App\Models\Bussinessinfo;
use App\Models\BookingCalendar;
use App\Models\ItemSubCategory;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;
use Darryldecode\Cart\CartCondition;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use App\Http\Resources\BookingCalendarResource;



class IndexController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {
        $customer = User::where('trash',0)->get();
        $products = Item::where('trash',0)
                    ->orderBy('created_at','desc')
                    ->paginate(12);

        if($request->search_category){
            $products = Item::with('item_category')->where('item_category_id',$request->search_category)
            ->orderBy('created_at','desc')
            ->paginate(12);
        }
        if($request->search_item){
            $products = Item::where('name','like','%'.$request->search_item.'%')
            ->orderBy('created_at','desc')
            ->paginate(12);
        }
       
        $item_category = ItemCategory::where('trash',0)->get();
        $item_sub_category = ItemSubCategory::where('trash',0)->get();

        if(request()->tax){
            $tax = "";
        }else{
            $tax = "";
        }

        $cart = session()->get('cart');
        $subtotal =0;
        $total = 0;
        $total_qty = 0;
        $tax = 0;

        $credit_check = $request->credit_check ? $request->credit_check : 0;
        if($cart){
            foreach($cart as $row) {
                $subtotal += $row['quantity'] * $row['price'];
                $total = $subtotal;
                $total_qty += $row['quantity'];
            }
        }
        
        $cart_data = collect($cart)->sortBy('created_at');

        $data_total = [
            'sub_total' => $subtotal,
            'total_qty' => $total_qty,
            'total' => $total,
            'tax' => $tax,
            'credit_check' => $credit_check,
        ];

        return view('backend.admin.index', compact('products','cart_data','data_total','customer','item_category','cart'));
    }


    public function show(Request $request){


        $discount_percentage = 0 ;
        $discount_amount = 0 ;
        $total_discount_amount = 0;
        $total_discount_percentage = 0;
        $cart = session()->get('cart');

        $subtotal =0;
        $total = 0;
        $total_qty = 0;
        $tax = 0;
        $item_name = '-';
        $rate_per_unit = 0;
        $grand_total_qty = 0;
        $grand_sub_total = 0;
        $grand_total = 0;
        $datam = [];
        $tax = 0;
        $tax_percent = 0;
        $tax_data = Tax::where('trash',0)->get();
        foreach($tax_data as $amount){
            $tax_percent += $amount->amount;
        }
        $tax = $tax_percent / 100;

        $credit_check = $request->credit_check ? $request->credit_check : 0;
        if($cart){
            if($request->customer != 0){
                    $customer = User::where('id',$request->customer)->first();
                    $account_type = $customer->accounttype ? $customer->accounttype : null ;
                    $discounts =[];
                  
                    foreach($cart as $row) {
                    $discounts = Discounts::where('user_account_id',$account_type->id)->where('item_id',$row['id'])->first();
                    $discount_percentage = $discounts ? $discounts->discount_percentage_mm : 0;
                    $discount_amount = $discounts ? $discounts->discount_amount_mm : 0;
                    $item_name = $row['name'];
                    $rate_per_unit = $row['price'];

                        if($discount_percentage != 0 ){
                            $subtotal = ( $row['quantity'] * $row['price']) ;
                            $total = (($subtotal) * ( ($row['quantity'] * $discount_percentage) / 100));
                            $total_qty = $row['quantity'];
            
                        }elseif($discount_amount != 0){
                            $subtotal = ( $row['quantity'] * $row['price']) ;
                            $total = (($subtotal) - ($discount_amount * $row['quantity']))  ;
                            $total_qty = $row['quantity'];

                        }else{
                            $subtotal = ( $row['quantity'] * $row['price']) ;
                            $total = ($subtotal) ;
                            $total_qty = $row['quantity'];
                        }

                        $datam [] = [
                            'item_name' => $item_name,
                            'rate_per_unit' => $rate_per_unit,
                            'sub_total' => $subtotal,
                            'discount_amount' => $discount_amount,
                            'discount_percentage' => $discount_percentage,
                            'total_qty' => $total_qty,
                            'total' => $total,
                            'tax' => $tax,
                            'credit_check' => $credit_check,
                        ];

                    }

            }else{
                foreach($cart as $row) {
                $item_name = $row['name'];
                $subtotal = $row['quantity'] * $row['price'];
                $total = ($subtotal) ;
                $total_qty = $row['quantity'];
                $rate_per_unit = $row['price'];

                $datam [] = [
                    'item_name' => $item_name,
                    'rate_per_unit' => $rate_per_unit,
                    'sub_total' => $subtotal,
                    'discount_amount' => $discount_amount,
                    'discount_percentage' => $discount_percentage,
                    'total_qty' => $total_qty,
                    'total' => $total,
                    'tax' => $tax,
                    'credit_check' => $credit_check,
                ];
                }
                
            }
        }
        
        $cart_data = collect($cart)->sortBy('created_at');
        $pay_items = $datam;

        foreach($pay_items as $data){
            $grand_total += $data['total'];
            $grand_total_qty += $data['total_qty']; 
            $grand_sub_total += $data['sub_total'];
            $total_discount_amount += $data['discount_amount'];
            $total_discount_percentage += $data['discount_percentage'];
        }
        $total_discount_amount = $total_discount_amount;
        $total_discount_percentage = $total_discount_percentage;
        $grand_total = $grand_total + (($grand_total) * $tax);
        $grand_total_qty = $grand_total_qty;
        $grand_sub_total = $grand_sub_total;    

        $buss_info = Bussinessinfo::where('trash',0)->first();

        return view('backend.admin.final_pay.index',compact('pay_items','grand_total','grand_total_qty','total_discount_percentage','total_discount_amount','tax_data','buss_info','tax'));
    }

}
