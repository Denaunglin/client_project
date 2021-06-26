<?php

namespace App\Http\Controllers\Backend\Admin;
//© 2020 Copyright: Tahu Coding
use DB;
use Auth;
use Cart;
use App\Models\Item;
//sorry kalau ada typo dalam penamaan dalam bahasa inggris 
use App\Models\User;
use App\Models\Transcation;
use Illuminate\Http\Request;

use App\Models\HistoryProduct;

//import dulu packagenya biar bs dipake
use App\Models\ProductTranscation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Haruncpi\LaravelIdGenerator\IdGenerator;


class TransactionController extends Controller
{

    public function addProductCart($id , Request $request){

        $product = Item::where('id',$id)->get();
     
        $discount_amount = 0;
        $discount_percentage = 0;
        $addon_percentage = 0;
        $addon_amount = 0;

        // if($request->customer){
        //     $customer_check = User::where('id',$request->customer)->first();
        //     if($customer_check->accounttype){
        //         $accounttype = $customer_check->accounttype->id;
        //         $discount_type = Discounts::where('trash', '0')->where('user_account_id', $accounttype)->where('room_type_id', $room->id)->first();
        //         $detailprices = ResponseHelper::roomschedulediscount($room, $nationality, $client_user, $discount_type);
              
        //         $customer_account_check = $customer_check->accounttype->discount ? $$customer_check->accounttype->discount : null;
        //         if($customer_account_check){
        //             $discount_amount = $customer_account_check->$discount_amount_mm ;
        //             $discount_percentage = $customer_account_check->$discount_percentage_mm ;
        //             $addon_percentage = $customer_account_check->$addon_percentage_mm ;
        //             $addon_amount = $customer_account_check->$addon_amount_mm ;
                
        //         }
        //     }
        // }

        $cart = Session::get('cart');
        $cart[$product[0]->id] = array(
        "id" => $product[0]->id,
        "name" => $product[0]->name,
        "price" => $product[0]->retail_price,
        "quantity" => 1,
        "sub_total" => $product[0]->retail_price,
        "total" => $product[0]->retail_price
    );

    session()->put('cart', $cart);
    return redirect()->back()->with('success', 'Product added to cart successfully!');

    }

    public function removeProductCart($id){
        $cart = Session::get('cart');
        unset($cart[$id]);
        Session::put('cart', $cart);
        return redirect()->back();
    }

    public function bayar(){

        $cart_total = \Cart::session(Auth()->id())->getTotal();
        $bayar = request()->bayar;
        $kembalian = (int)$bayar - (int)$cart_total;
               
        if($kembalian >= 0){  
            DB::beginTransaction();

            try{

            $all_cart = \Cart::session(Auth()->id())->getContent();
           

            $filterCart = $all_cart->map(function($item){
                return [
                    'id' => $item->id,
                    'quantity' => $item->quantity
                ];
            });

            foreach($filterCart as $cart){
                $product = Item::find($cart['id']);
                
                if($product->qty == 0){
                    return redirect()->back()->with('errorTransaksi','jumlah pembayaran gak valid');  
                }

                HistoryProduct::create([
                    'product_id' => $cart['id'],
                    'user_id' => Auth::id(),
                    'qty' => $product->qty,
                    'qtyChange' => -$cart['quantity'],
                    'tipe' => 'decrease from transaction'
                ]);
                
                $product->decrement('qty',$cart['quantity']);
            }
            
            $id = IdGenerator::generate(['table' => 'transcations', 'length' => 10, 'prefix' =>'INV-', 'field' => 'invoices_number']);

            Transcation::create([
                'invoices_number' => $id,
                'user_id' => Auth::id(),
                'pay' => request()->bayar,
                'total' => $cart_total
            ]);

            foreach($filterCart as $cart){    

                ProductTranscation::create([
                    'product_id' => $cart['id'],
                    'invoices_number' => $id,
                    'qty' => $cart['quantity'],
                ]);                
            }

            \Cart::session(Auth()->id())->clear();

            DB::commit();        
            return redirect()->back()->with('success','Transaksi Berhasil dilakukan Tahu Coding | Klik History untuk print');        
            }catch(\Exeception $e){
            DB::rollback();
                return redirect()->back()->with('errorTransaksi','jumlah pembayaran gak valid');        
            }        
        }
        return redirect()->back()->with('errorTransaksi','jumlah pembayaran gak valid');        

    }

    public function clear(){
        $cart = Session::get('cart');
        if($cart){
            $count = count($cart);
            $id=[];
            foreach($cart as $data){
                $id=$data['id'];
                unset($cart[$id]);
            }
            Session::put('cart', $cart);
        }
        return redirect()->back();
    }

    public function decreasecart($id){
        $cart = Session::get('cart');   
        $cart[$id]['quantity']--;
        $cart[$id]['sub_total'] = $cart[$id]['quantity'] * $cart[$id]['price'];
        $cart[$id]['total'] = $cart[$id]['sub_total'];
        session()->put('cart', $cart);
        return redirect()->back();
    }


    public function increasecart($id){
        $product = Item::find($id);     
        $cart = Session::get('cart');   
        $cart[$id]['quantity']++;
        $cart[$id]['sub_total'] = $cart[$id]['quantity'] * $cart[$id]['price'];
        $cart[$id]['total'] = $cart[$id]['sub_total'];
        session()->put('cart', $cart);
        return redirect()->back();
      
    }

    public function history(){
        $history = Transcation::orderBy('created_at','desc')->paginate(10);
        return view('pos.history',compact('history'));
    }

    public function laporan($id){
        $transaksi = Transcation::with('productTranscation')->find($id);
        return view('laporan.transaksi',compact('transaksi'));
    }

    
}
