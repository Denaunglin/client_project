<?php

namespace App\Http\Controllers\Backend\Admin;

use Response;
use App\Models\Item;
use App\Models\Rooms;
use Darryldecode\Cart\Cart;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
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
        $tax = 0;
        if($cart){
            foreach($cart as $row) {
                $subtotal += $row['quantity'] * $row['price'];
                $total = $subtotal;
            }
        }
        
        $cart_data = collect($cart)->sortBy('created_at');

        $data_total = [
            'sub_total' => $subtotal,
            'total' => $total,
            'tax' => $tax,
        ];

        return view('backend.admin.index', compact('products','cart_data','data_total','item_category','cart'));
    }

}
