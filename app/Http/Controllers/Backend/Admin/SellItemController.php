<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Tax;
use App\Models\Item;
use App\Models\User;
use App\Models\Cashbook;
use App\Models\Supplier;
use App\Models\Discounts;
use App\Models\SellItems;
use App\Models\ItemLedger;
use App\Models\ShopStorage;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use App\Helper\ResponseHelper;
use App\Models\ItemSubCategory;
use Yajra\DataTables\DataTables;
use App\Http\Requests\ItemRequest;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;
use Illuminate\Support\Facades\Session;


class SellItemController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {

        if (!$this->getCurrentAuthUser('admin')->can('view_item')) {
            abort(404);
        }
        $item = Item::where('trash',0)->get();
        $item_category = ItemCategory::where('trash', 0)->get();
        $item_sub_category = ItemSubCategory::where('trash', 0)->get();
        $tax = Tax::where('trash',0)->first();

        if ($request->ajax()) {
            $daterange = $request->daterange ? explode(' , ', $request->daterange) : null;

            $sell_items = SellItems::anyTrash($request->trash);
            if ($daterange) {
                $sell_items = SellItems::whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
            }
            if ($request->item != '') {
                $sell_items = $sell_items->where('item_id', $request->item);
            }

            if ($request->item_category != '') {
                $sell_items = $sell_items->where('item_category_id', $request->item_category);
            }

            if ($request->item_sub_category != '') {
                $sell_items = $sell_items->where('item_sub_category_id', $request->item_sub_category);

            }

            return Datatables::of($sell_items)
                ->addColumn('action', function ($sell_item) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = ' ';
                    $invoice_btn = '';
                    $trash_or_delete_btn = ' ';

                    if ($this->getCurrentAuthUser('admin')->can('edit_item_category')) {
                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.sell_items.edit', ['sell_item' => $sell_item->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }

                    if ($this->getCurrentAuthUser('admin')->can('delete_item_category')) {
                       
                        $invoice_btn = '<a class="edit text text-primary" href="' . route('admin.sell_invoice', $sell_item->id) . '"><i class="fas fa-file-invoice-dollar fa-lg"></i></a>';

                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $sell_item->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $sell_item->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $sell_item->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                        }

                    }

                    return "${detail_btn} ${edit_btn} ${restore_btn} ${trash_or_delete_btn} ${invoice_btn} ";
                })
                ->addColumn('customer', function ($sell_item) {
                    $customer = $sell_item->customer ? $sell_item->customer->name.'</br>'.$sell_item->customer->phone.'</br>'.$sell_item->customer->address : 'Default Customer';
                    return $customer;
                    
                })
                ->addColumn('item_id', function ($sell_item) {

                    return $sell_item->item ? $sell_item->item->name : '-';
                })
                ->addColumn('item_category', function ($sell_item) {

                    return $sell_item->item_category ? $sell_item->item_category->name : '-';
                })
                ->addColumn('item_sub_category', function ($sell_item) {
                    return $sell_item->item_sub_category ? $sell_item->item_sub_category->name : '-';
                })
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action','customer','item_id','item_category','item_sub_category'])
                ->make(true);
        }
        
        return view('backend.admin.sell_items.index',compact('item','item_category','item_sub_category','tax'));
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_item')) {
            abort(404);
        }
        // $item = Item::where('trash',0)->get();
        $shop_storage = ShopStorage::where('trash',0)->get();
        $item = [];
        foreach($shop_storage as $data){
            $option = Item::where('trash',0)->where('id', $data->item_id)->first();
            if($option != null){
                $item [] = $option;
            }
        }
        
        $item_category = ItemCategory::where('trash', 0)->get();
        $customer = User::where('trash',0)->get();
        $item_sub_category = ItemSubCategory::where('trash', 0)->get();
        return view('backend.admin.sell_items.create', compact('item_category','customer','item', 'item_sub_category'));
    }

    public function store(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_item')) {
            abort(404);
        }
        return $request; 
     
        $item = Item::findOrFail($request->item_id);
        $customer_id = $request->customer_id ? $request->customer_id : 0;
        $customer = User::where('id',$request->customer_id)->first();

        $item_count = count($item);

        if($item_count == 1){
            $shop_storage = ShopStorage::where('item_id',$request->item_id)->first();
            // if($request['qty'] != $shop_storage->qty){
            //     return redirect()->back()->with(["error"=> "Not Enouch Qty !"]);
            // }

            $item = $item->first();
            $sell_item = new SellItems();
            $sell_item->item_id = $item->id;
            $sell_item->customer_id = $customer_id ;
            $sell_item->item_category_id = $item->item_category_id;
            $sell_item->item_sub_category_id = $item->item_sub_category_id;
            $sell_item->qty = $request['qty'][0];
            $sell_item->price = $request['price'][0];
            $sell_item->discount = $request['discount'][0];
            $sell_item->net_price = $request['net_price'][0];
            $sell_item->save();

            $cash_book = new Cashbook();
            $cash_book->cashbook_income = $sell_item->net_price;
            $cash_book->cashbook_outgoing = 0 ;
            $cash_book->buying_id = null;
            $cash_book->service_id = null;
            $cash_book->selling_id = $sell_item->id;
            $cash_book->expense_id = null;
            $cash_book->credit_id = null;
            $cash_book->return_id = null;
            $cash_book->save();
    
            $shop_storage = ShopStorage::where('item_id',$item->id)->first();
            $open_qty = $shop_storage ? $shop_storage->qty : 0 ;

            if($shop_storage){
                $qty = ($shop_storage->qty) - ($sell_item->qty);
                $shop_storage->qty = $qty;
                $shop_storage->update();
            }else{
                $shop_storage = new ShopStorage();
                $shop_storage->item_id = $item->id;
                $shop_storage->qty = $sell_item->qty;
                $shop_storage->save();
            }

            $item_ledger= new ItemLedger();
            $item_ledger->item_id = $item->id;
            $item_ledger->opening_qty = $open_qty;
            $item_ledger->buying_buy = '0';
            $item_ledger->buying_back = '0';
            $item_ledger->selling_sell = $request->qty;
            $item_ledger->selling_back = '0';
            $item_ledger->adjust_in = '0';
            $item_ledger->adjust_out = '0';
            $item_ledger->closing_qty = $shop_storage->qty;
            $item_ledger->save();
        }else{
            for ($var = 0; $var < $item_count - 1;) {
           
            foreach ($item as $data) {
                $shop_storage = ShopStorage::where('item_id',$data->id)->first();
                // if($request['qty'][$var] != $shop_storage->qty){
                //     return redirect()->back()->with(["error"=> "Not Enouch Qty !"]);
                // }
                $sell_item = new SellItems();
                $sell_item->item_id = $data->id;
                $sell_item->customer_id = $customer_id ;
                $sell_item->item_category_id = $data->item_category_id;
                $sell_item->item_sub_category_id = $data->item_sub_category_id;
                $sell_item->qty = $request['qty'][$var];
                $sell_item->price = $request['price'][$var];
                $sell_item->discount = $request['discount'][$var];
                $sell_item->net_price = $request['net_price'][$var];
                $sell_item->save();

                $cash_book = new Cashbook();
                $cash_book->cashbook_income = $sell_item->net_price;
                $cash_book->cashbook_outgoing = 0 ;
                $cash_book->buying_id = null;
                $cash_book->service_id = null;
                $cash_book->selling_id = $sell_item->id;
                $cash_book->expense_id = null;
                $cash_book->credit_id = null;
                $cash_book->return_id = null;
                $cash_book->save();

                $shop_storage = ShopStorage::where('item_id',$data->id)->first();
                $open_qty = $shop_storage ? $shop_storage->qty : 0 ;

                if($shop_storage){
                    $qty = ($shop_storage->qty) + ($sell_item->qty);
                    $shop_storage->qty = $qty;
                    $shop_storage->update();
                }else{
                    $shop_storage = new ShopStorage();
                    $shop_storage->item_id = $data->id;
                    $shop_storage->qty = $sell_item->qty;
                    $shop_storage->save();
                }
        
                $item_ledger= new ItemLedger();
                $item_ledger->item_id = $data->id;
                $item_ledger->opening_qty = $open_qty;
                $item_ledger->buying_buy = '0';
                $item_ledger->buying_back = '0';
                $item_ledger->selling_sell = $request['qty'][$var];
                $item_ledger->selling_back = '0';
                $item_ledger->adjust_in = '0';
                $item_ledger->adjust_out = '0';
                $item_ledger->closing_qty = $shop_storage->qty;
                $item_ledger->save();
                $var++;
            }
        }
        }

        activity()
            ->performedOn($sell_item)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => ' Sell Item   (Admin Panel'])
            ->log(' Sell Item  is created ');
        
            $sell_items = SellItems::where('created_at', $sell_item->created_at)->get();
            $bussiness_info = Bussinessinfo::where('trash',0)->first();
            $invoice_pdf = new Invoice();
            $invoice_pdf->invoice_no = 0;
            $invoice_pdf->item_id = serialize($sell_items->id);
            $invoice_pdf->service_id = null;
            $invoice_pdf->save();
    
            activity()
                ->performedOn($invoice_pdf)
                ->causedBy(auth()->guard('admin')->user())
                ->withProperties(['source' => ' Invoice (Admin Panel)'])
                ->log('New Invoice is created');
    
            $date = Carbon::now();
            $item_name = [];
            $item_category = [];
            $item_sub_category = [];
            $qty = [];
            $price = [];
            $discount = [];
            $net_price = [];
            $total_price = [];

            foreach($sell_items as $data){
                $item_name [] = $sell_items->item ? $sell_items->item->name : '-';
                $item_category [] = $sell_items->item_category ? $sell_items->item_category->name : '-';
                $item_sub_category [] = $sell_items->item_sub_category ? $sell_items->item_sub_category->name : '-';
                $qty [] = $sell_items->qty;
                $price [] = $sell_items->price;
                $discount [] = $sell_items->discount;
                $net_price [] = $sell_items->net_price;
                $total_price [] +=  $sell_items->net_price;
            }
    
            $today = $date->toFormattedDateString();
            $invoice_number = str_pad($invoice_pdf->id, 6, '0', STR_PAD_LEFT);
            $data = [

                'barcode' => $barcode,
                'item_name' => $item_name,
                'item_category' => $item_category,
                'item_sub_category' => $item_sub_category,
                'shop_name' => $bussiness_info ? $bussiness_info->name : '-',
                'shop_email' => $bussiness_info ? $bussiness_info->email : '-',
                'shop_phone' => $bussiness_info ? $bussiness_info->phone : '-',
                'shop_address' => $bussiness_info ? $bussiness_info->address : '-',
                'today_date' => $today,
                // 'client_name' => $booking->name,
                // 'client_email' => $booking->email,
                'invoice_no' => $invoice_number,
                'title' => ' Invoice',
                'heading1' => '',
                'heading2' => 'Invoice',
                'qty' => $qty,
                'price' => $price,
                'discount' => $discount,
                'net_price' => $net_price,
            ];
    
            $pdf = PDF::loadView('backend.admin.invoices.pdf_view', $data);
            $pdf_name = uniqid() . '_' . time() . '_' . $item_name . '.pdf';
            $invoice_pdf->invoice_no = $invoice_number;
            $invoice_pdf->invoice_file = $pdf_name;
            $invoice_pdf->update();
    
            Storage::put('uploads/pdf/' . $pdf_name, $pdf->output());
            $pdf->download('sell_invoices.pdf');

        return redirect()->route('admin.sell_items.index')->with('success', 'Successfully Created');
    }

    public function show(SellItems $sell_item)
    {
        return view('backend.admin.sell_items.show', compact('item'));
    }

    public function edit($id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_item')) {
            abort(404);
        }

        $data_item = SellItems::findOrFail($id);
        $items =  Item::where('trash',0)->get();
        $item_category = ItemCategory::where('trash', 0)->get();
        $item_sub_category = ItemSubCategory::where('trash', 0)->get();

        return view('backend.admin.sell_items.edit', compact('items','data_item', 'item_category', 'item_sub_category'));
    }

    public function update(Request $request, $id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_item')) {
            abort(404);
        }
        $item=Item::where('id',$request->item_id)->first();
        $sell_item = SellItems::findOrFail($id);
        $shop_storage = ShopStorage::where('item_id',$item->id)->first();
        $opening_qty = $shop_storage->qty ? $shop_storage->qty : 0 ;
        if($request->qty > $sell_item->qty){
            $adjust_out= $request->qty - $sell_item->qty;
        }else{
            $adjust_out= $sell_item->qty - $request->qty ;
        }

        $qty1 = $sell_item->qty ;
        $qty2 = $request->qty;
        $diff_qty = $qty2 - $qty1 ;
        $shop_qty = $shop_storage->qty - ($diff_qty);

        if($shop_storage){
            $shop_storage->qty = $shop_qty;
            $shop_storage->update();
        }

        $sell_item->item_id = $item->id;
        $sell_item->customer_id = 0 ;
        $sell_item->item_category_id = $item->item_category_id;
        $sell_item->item_sub_category_id = $item->item_sub_category_id;
        $sell_item->qty = $request['qty'];
        $sell_item->price = $request['price'];
        $sell_item->discount = $request['discount'];
        $sell_item->net_price = $request['net_price'];
        $sell_item->update();

        $cash_book =  Cashbook::where('selling_id', $sell_item->id)->first();;
        $cash_book->cashbook_income = $sell_item->net_price;
        $cash_book->cashbook_outgoing = 0 ;
        $cash_book->buying_id = null;
        $cash_book->selling_id = $sell_item->id;
        $cash_book->service_id = null;
        $cash_book->expense_id = null;
        $cash_book->credit_id = null;
        $cash_book->return_id = null;
        $cash_book->update();

        $item_ledger=ItemLedger::where('item_id',$item->item_id)->first();
        $item_ledger->item_id = $request->item_id;
        $item_ledger->opening_qty = $opening_qty;
        $item_ledger->buying_buy = $item_ledger->buying_buy;
        $item_ledger->buying_back = $item_ledger->buying_back;
        $item_ledger->selling_sell = $request->qty;
        $item_ledger->selling_back = $item_ledger->buying_back;
        $item_ledger->adjust_out = $adjust_out;
        $item_ledger->adjust_in = $item_ledger->adjust_in;
        $item_ledger->closing_qty = $shop_storage->qty;
        $item_ledger->update();

        activity()
            ->performedOn($sell_item)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => ' Sell Item   (Admin Panel'])
            ->log(' Sell Item  is updated');

        return redirect()->route('admin.sell_items.index')->with('success', 'Successfully Updated');
    }

    public function destroy(SellItems $sell_item)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_item')) {
            abort(404);
        }

        $sell_item->delete();
        activity()
            ->performedOn($sell_item)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item  (Admin Panel'])
            ->log(' Item  (' . $sell_item->name . ')  is deleted ');

        return ResponseHelper::success();
    }

    public function trash(SellItems $sell_item)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_item')) {
            abort(404);
        }

        $sell_item->trash();
        activity()
            ->performedOn($sell_item)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Sell Item  (Admin Panel)'])
            ->log(' Sell Item  is moved to trash ');

        return ResponseHelper::success();
    }

    public function restore(SellItems $sell_item)
    {
        $sell_item->restore();
        activity()
            ->performedOn($sell_item)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Sell Item  (Admin Panel'])
            ->log(' Sell Item  is restored from trash ');

        return ResponseHelper::success();
    }

    public function indexSell(Request $request){
      
        $discount_percentage = 0 ;
        $discount_amount = 0 ;
        $subtotal =0;
        $total = 0;
        $total_qty = 0;
        $item_name = '-';
        $rate_per_unit = 0;
        $tax = 0;
        $tax_percent = 0;
        $tax_data = Tax::where('trash',0)->get();
        foreach($tax_data as $amount){
            $tax_percent += $amount->amount;
        }
        $tax = $tax_percent / 100;

        $customer_id = $request->customer ? $request->customer : 0;

        $cart = Session::get('cart');  
        $account_type = null;
        if($cart){  
            if($request->customer != 0){
                $customer = User::where('id',$request->customer)->first();
                $account_type = $customer->accounttype ? $customer->accounttype : null ;
                $discounts =[];
            }

        $item_count = count($cart);
        if($item_count == 1){
            foreach( $cart as $cart_data){
            if($account_type != null){
                $discounts = Discounts::where('user_account_id',$account_type->id)->where('item_id',$cart_data['id'])->first();
                $discount_percentage = $discounts ? $discounts->discount_percentage_mm : 0;
                $discount_amount = $discounts ? $discounts->discount_amount_mm : 0;

                    if($discount_percentage != 0 ){
                        $subtotal += ( $cart_data['quantity'] * $cart_data['price']) ;
                        $total = ($subtotal) * ( ($cart_data['quantity'] * $discount_percentage) / 100);
        
                    }if($discount_amount != 0){
                        $subtotal += ( $cart_data['quantity'] * $cart_data['price']) ;
                        $total =  ($subtotal) - ($discount_amount * $cart_data['quantity']);

                    }
            }
                    
            $item = Item::where('id',$cart_data['id'])->first();

            $sell_item = new SellItems();
            $sell_item->item_id = $item->id;
            $sell_item->customer_id = $customer_id ;
            $sell_item->item_category_id = $item->item_category_id;
            $sell_item->item_sub_category_id = $item->item_sub_category_id;
            $sell_item->qty = $cart_data['quantity'];
            $sell_item->price = $cart_data['price'];
            if($discount_amount !=0){
                $sell_item->discount = $discount_amount;
            }elseif($discount_percentage !=0){
                $sell_item->discount = $discount_percentage;
            }else{
                $sell_item->discount = 0;
            }
            if($total !=0){
                $sell_item->net_price =  $total + ($total * $tax);
            }else{
                $sell_item->net_price = $cart_data['total'] + ($cart_data['total'] * $tax ) ;
            }
            $sell_item->save();

            $cash_book = new Cashbook();
            $cash_book->cashbook_income = $sell_item->net_price;
            $cash_book->cashbook_outgoing = 0 ;
            $cash_book->buying_id = null;
            $cash_book->service_id = null;
            $cash_book->selling_id = $sell_item->id;
            $cash_book->expense_id = null;
            $cash_book->credit_id = null;
            $cash_book->return_id = null;

            $cash_book->save();
    
            $shop_storage = ShopStorage::where('item_id',$item->id)->first();
            $opening_qty = $shop_storage->qty ? $shop_storage->qty : 0 ;

            if($shop_storage){
                $qty = ($shop_storage->qty) - ($sell_item->qty);
                $shop_storage->qty = $qty;
                $shop_storage->update();
            }else{
                $shop_storage = new ShopStorage();
                $shop_storage->item_id = $item->id;
                $shop_storage->qty = $sell_item->qty;
                $shop_storage->save();
            }

            $item_ledger= new ItemLedger();
            $item_ledger->item_id = $item->id;
            $item_ledger->opening_qty = $opening_qty;
            $item_ledger->buying_buy = '0';
            $item_ledger->buying_back = '0';
            $item_ledger->selling_sell = $cart_data['quantity'];
            $item_ledger->selling_back = '0';
            $item_ledger->adjust_in = '0';
            $item_ledger->adjust_out = '0';
            $item_ledger->closing_qty =  $shop_storage->qty;
            $item_ledger->save();
        }
         }else{
            for ($var = 0; $var < $item_count - 1;) {
            foreach( $cart as $cart_data){
                if($account_type != null){
                    $discounts = Discounts::where('user_account_id',$account_type->id)->where('item_id',$cart_data['id'])->first();
                    $discount_percentage = $discounts ? $discounts->discount_percentage_mm : 0;
                    $discount_amount = $discounts ? $discounts->discount_amount_mm : 0;
    
                        if($discount_percentage != 0 ){
                            $subtotal += ( $cart_data['quantity'] * $cart_data['price']) ;
                            $total = ($subtotal) * ( ($cart_data['quantity'] * $discount_percentage) / 100);
            
                        }if($discount_amount != 0){
                            $subtotal += ( $cart_data['quantity'] * $cart_data['price']) ;
                            $total = ($subtotal) - ($discount_amount * $cart_data['quantity']);
    
                        }
                }

             $item = Item::where('id',$cart_data['id'])->get();
            foreach ($item as $data) {
                $sell_item = new SellItems();
                $sell_item->item_id = $data->id;
                $sell_item->customer_id = $customer_id ;
                $sell_item->item_category_id = $data->item_category_id;
                $sell_item->item_sub_category_id = $data->item_sub_category_id;
                $sell_item->qty = $cart_data['quantity'];
                $sell_item->price = $cart_data['price'];
                if($discount_amount !=0){
                    $sell_item->discount = $discount_amount;
                }elseif($discount_percentage !=0){
                    $sell_item->discount = $discount_percentage;
                }else{
                    $sell_item->discount = 0;
                }
                if($total !=0){
                    $sell_item->net_price =  $total + ($total * $tax);
                }else{
                    $sell_item->net_price = $cart_data['total'] + ($cart_data['total'] * $tax);
                }
                $sell_item->save();

                $cash_book = new Cashbook();
                $cash_book->cashbook_income = $sell_item->net_price;
                $cash_book->cashbook_outgoing = 0 ;
                $cash_book->buying_id = null;
                $cash_book->service_id = null;
                $cash_book->selling_id = $sell_item->id;
                $cash_book->expense_id = null;
                $cash_book->credit_id = null;
                $cash_book->return_id = null;
                $cash_book->save();

                $shop_storage = ShopStorage::where('item_id',$data->id)->first();
                $opening_qty = $shop_storage->qty ? $shop_storage->qty : 0 ;

                if($shop_storage){
                    $qty = ($shop_storage->qty) - ($sell_item->qty);
                    $shop_storage->qty = $qty;
                    $shop_storage->update();
                }else{
                    $shop_storage = new ShopStorage();
                    $shop_storage->item_id = $data->id;
                    $shop_storage->qty = $sell_item->qty;
                    $shop_storage->save();
                }
        
                $item_ledger= new ItemLedger();
                $item_ledger->item_id = $data->id;
                $item_ledger->opening_qty = $opening_qty;
                $item_ledger->buying_buy = '0';
                $item_ledger->buying_back = '0';
                $item_ledger->selling_sell = $cart_data['quantity'];
                $item_ledger->selling_back = '0';
                $item_ledger->adjust_in = '0';
                $item_ledger->adjust_out = '0';
                $item_ledger->closing_qty =  $shop_storage->qty;
                $item_ledger->save();
                $var++;
            }
        }
        }
        }

        if($cart){
            $id=[];
            foreach($cart as $data){
                $id=$data['id'];
                unset($cart[$id]);
            }
            Session::put('cart', $cart);
        }

        activity()
            ->performedOn($sell_item)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => ' Sell Item   (Admin Panel'])
            ->log(' Sell Item is created ');

        return redirect()->route('admin.sell_items.index')->with('success', 'Successfully Created');
    }else{
        return redirect()->back()->with('error', 'There is empty Cart');
    }
}
    
    public function creditSellView(){

    }
}
