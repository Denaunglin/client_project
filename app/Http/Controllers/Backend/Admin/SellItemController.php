<?php

namespace App\Http\Controllers\Backend\Admin;

use PDF;
use Carbon\Carbon;
use App\Models\Tax;
use App\Models\Item;
use App\Models\User;
use App\Models\Credit;
use App\Models\Invoice;
use App\Models\Cashbook;
use App\Models\Supplier;
use App\Models\Discounts;
use App\Models\SellItems;
use App\Models\ItemLedger;
use App\Models\ShopStorage;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use App\Models\Bussinessinfo;
use App\Helper\ResponseHelper;
use App\Http\Requests\SellItem;
use App\Models\ItemSubCategory;
use Yajra\DataTables\DataTables;
use App\Http\Requests\ItemRequest;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\saleItemResource;

class SellItemController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {

        if (!$this->getCurrentAuthUser('admin')->can('view_item')) {
            abort(404);
        }
        $invoice = Invoice::latest()->first();
        $item = Item::where('trash',0)->get();
        $item_category = ItemCategory::where('trash', 0)->get();
        $item_sub_category = ItemSubCategory::where('trash', 0)->get();
        $tax = Tax::where('trash',0)->first(); 
        
        if ($request->ajax()) {
            $daterange = $request->daterange ? explode(' , ', $request->daterange) : null;

            $sell_items = SellItems::anyTrash($request->trash)->orderBy('id','desc');
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
                ->addColumn('total_item', function ($sell_item) {
                    $data = unserialize($sell_item->item_data);
                    $id = [];
                    foreach($data as $test){
                        $id []= $test['item_id'];
                    }
                    $count = count($data);
                   
                    for ($var = 0 ; $var < $count ;) {
                        $item = Item::findMany($id);    
                        $items = []; 
                        foreach($item as $data_item){
                            $items [] = '<li class="list-group-item">'.$data_item->name.' ('.$data[$var]['qty'].')</li>';
                            $var++;
                        }
                    }

                    $gg = collect($items)->implode(',');

                    return '<ul class="list-group">'.$gg.'</ul>';
                })
                ->addColumn('customer', function ($sell_item) {
                    $customer = $sell_item->customerdata ? $sell_item->customerdata->name.'</br>'.$sell_item->customerdata->phone.'</br>'.$sell_item->customerdata->address : 'Default Customer';
                    $customer_name =  $sell_item->customerdata ? $sell_item->customerdata->name : '-';
                    $customer_phone =  $sell_item->customerdata ? $sell_item->customerdata->phone : '-';
                    $customer_address =  $sell_item->customerdata ? $sell_item->customerdata->address : '-';
                    if($sell_item->customerdata){
                        return '<ul class="list-group">
                        <li class="list-group-item">'.$customer_name.'</li>
                        <li class="list-group-item">'.$customer_phone.'</li>
                        <li class="list-group-item">'.$customer_address.'</li>
                        </ul>';
                    }
                    else{
                        return 'Default Customer';
                    }
                   
                })
               
                ->addColumn('paid_status', function ($credit) {
                if($credit->paid_status == 0){
                    return '<span class="badge badge-success">Paided</span>';
                }else{
                    return '<span class="badge badge-warning">UnPaid</span>';
                }
                    
                })

                ->addColumn('sell_type', function ($credit) {
                if($credit->sell_type == 0){
                    return '<span class="badge badge-warning">Retail</span>';
                }else{
                    return '<span class="badge badge-warning">Wholesale</span>';
                }
                    
                })
               
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action','customer', 'sell_type','total_item','item_id','paid_status'])
                ->make(true);
        }
        
        return view('backend.admin.sell_items.index',compact('item','item_category','item_sub_category','tax','invoice'));
    }

    public function createRetail()
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
        $customer = User::where('trash',0)->where('account_type',1)->get();
        $item_sub_category = ItemSubCategory::where('trash', 0)->get();
        return view('backend.admin.sell_items.create_retail', compact('item_category','customer','item', 'item_sub_category'));
    }

    public function createWholesale()
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
        $customer = User::where('trash',0)->where('account_type',2)->get();
        $item_sub_category = ItemSubCategory::where('trash', 0)->get();
        return view('backend.admin.sell_items.create_wholesale', compact('item_category','customer','item', 'item_sub_category'));
    }

    public function store(SellItem $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_item')) {
            abort(404);
        }
     
        $item = Item::findOrFail($request->item_id);
        $customer_id = $request->customer_id ? $request->customer_id : 0;
        $customer = User::where('id',$request->customer_id)->first();
        $date = Carbon::now();
        $item_count = count($item);

        $item_data = [];
        for ($var = 0; $var < $item_count ;) {
            foreach($item as $data){
                $item_data []= ['item_id' => $request['item_id'][$var] , 'qty' => $request['qty'][$var] , 'net_price' => $request['net_price'][$var]];
                $var++;
            }   
        }

        $data_item = serialize($item_data);
        $sell_item = new SellItems();
        $sell_item->item_data = $data_item;
        $sell_item->customer = $request->customer_id ?? 0;
        $sell_item->total_qty = $request['total_qty'];
        $sell_item->discount = $request['discount'];
        $sell_item->total_amount = $request['origin_amount'];
        $sell_item->paid_amount = $request['paid_amount'];
        $sell_item->credit_amount = $request['credit_amount'];
        $sell_item->paid_status = $request['paid_status'];
        $sell_item->sell_type = $request->sell_type;
        $sell_item->save();
 
        if($item_count == 1){
            $shop_storage = ShopStorage::where('item_id',$request->item_id)->first();
            // if($request['qty'] != $shop_storage->qty){
            //     return redirect()->back()->with(["error"=> "Not Enouch Qty !"]);
            // }
            $item = Item::findOrFail($request->item_id)->first();

            if($request->credit_amount != 0){
                $credit = new Credit();
                $credit->item_id = serialize($item->id);
                $credit->qty = $request['total_qty'];
                $credit->customer_id = $customer_id;
                $credit->origin_amount = $request['origin_amount'];
                $credit->paid_amount = $request['paid_amount'];
                $credit->credit_amount = $request['credit_amount'];
                $credit->paid_date = $date;
                $credit->paid_times = 1;
                $credit->late_id= 0;
                $credit->paid_status = $request['paid_status'];
                $credit->save();

            if($request['paid_status'] == 0){
                $cash_book = new Cashbook();
                $cash_book->cashbook_income = $request->paid_amount;
                $cash_book->cashbook_outgoing = 0 ;
                $cash_book->buying_id = null;
                $cash_book->service_id = null;
                $cash_book->selling_id = null;
                $cash_book->expense_id = null;
                $cash_book->credit_id = $credit->id;
                $cash_book->return_id = null;
                $cash_book->save();
            }

            }else{
                $cash_book = new Cashbook();
                $cash_book->cashbook_income = $sell_item->paid_amount;
                $cash_book->cashbook_outgoing = 0 ;
                $cash_book->buying_id = null;
                $cash_book->service_id = null;
                $cash_book->selling_id = $sell_item->id;
                $cash_book->expense_id = null;
                $cash_book->credit_id = null;
                $cash_book->return_id = null;
                $cash_book->save();
            }
         
            $shop_storage = ShopStorage::where('item_id',$item->id)->first();
            $open_qty = $shop_storage ? $shop_storage->qty : 0 ;

            if($shop_storage){
                $qty = ($shop_storage->qty) - ($request['qty'][0]);
                $shop_storage->qty = $qty;
                $shop_storage->update();
            }else{
                $shop_storage = new ShopStorage();
                $shop_storage->item_id = $item->id;
                $shop_storage->qty = $request['qty'][0];
                $shop_storage->save();
            }

            $item_ledger= new ItemLedger();
            $item_ledger->item_id = $item->id;
            $item_ledger->opening_qty = $open_qty;
            $item_ledger->buying_buy = '0';
            $item_ledger->buying_back = '0';
            $item_ledger->selling_sell = $request['qty'][0];
            $item_ledger->selling_back = '0';
            $item_ledger->adjust_in = '0';
            $item_ledger->adjust_out = '0';
            $item_ledger->closing_qty = $shop_storage->qty;
            $item_ledger->save();

        }else{

            for ($var = 0; $var < $item_count - 1;) {
            foreach ($item as $data) {
                $shop_storage = ShopStorage::where('item_id',$data->id)->first();
                $open_qty = $shop_storage ? $shop_storage->qty : 0 ;

                if($shop_storage){
                    $qty = ($shop_storage->qty) - ($request['qty'][$var]);
                    $shop_storage->qty = $qty;
                    $shop_storage->update();
                }else{
                    $shop_storage = new ShopStorage();
                    $shop_storage->item_id = $data->id;
                    $shop_storage->qty = $request['qty'][$var];
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
        if($request->credit_amount != 0){
            $credit = new Credit();
            $credit->item_id = serialize($request->item_id);
            $credit->qty = $request['total_qty'];
            $credit->customer_id = $customer_id;
            $credit->origin_amount = $request['origin_amount'];
            $credit->paid_amount = $request['paid_amount'];
            $credit->credit_amount = $request['credit_amount'];
            $credit->paid_date = $date;
            $credit->paid_times = 1;
            $credit->late_id= 0;
            $credit->paid_status = $request['paid_status'];
            $credit->save();

            if($request['paid_status'] == 0){
                $cash_book = new Cashbook();
                $cash_book->cashbook_income = $credit->paid_amount;
                $cash_book->cashbook_outgoing = 0 ;
                $cash_book->buying_id = null;
                $cash_book->service_id = null;
                $cash_book->selling_id = null;
                $cash_book->expense_id = null;
                $cash_book->credit_id = $credit->id;
                $cash_book->return_id = null;
                $cash_book->save();
            }

        }
        else{
            $cash_book = new Cashbook();
            $cash_book->cashbook_income = $sell_item->paid_amount;
            $cash_book->cashbook_outgoing = 0 ;
            $cash_book->buying_id = null;
            $cash_book->service_id = null;
            $cash_book->selling_id = $sell_item->id;
            $cash_book->expense_id = null;
            $cash_book->credit_id = null;
            $cash_book->return_id = null;
            $cash_book->save();
        }

        }

        activity()
            ->performedOn($sell_item)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => ' Sell Item   (Admin Panel'])
            ->log(' Sell Item  is created ');
        
            // $sell_items = SellItems::where('created_at', $sell_item->created_at)->get();
            // $bussiness_info = Bussinessinfo::where('trash',0)->first();
            // $invoice_pdf = new Invoice();
            // $invoice_pdf->invoice_no = 0;
            // $invoice_pdf->item_id = null;
            // $invoice_pdf->service_id = null;
            // $invoice_pdf->save();
    
            // activity()
            //     ->performedOn($invoice_pdf)
            //     ->causedBy(auth()->guard('admin')->user())
            //     ->withProperties(['source' => ' Invoice (Admin Panel)'])
            //     ->log('New Invoice is created');
    
            // $date = Carbon::now();
            // $total_price = 0;
            // $item_data=[];

            // foreach($sell_items as $data){
            //     $item_name  = $data->item ? $data->item->name : '-';
            //     $item_category  = $data->item_category ? $data->item_category->name : '-';
            //     $item_sub_category  = $data->item_sub_category ? $data->item_sub_category->name : '-';
            //     $qty  = $data->qty;
            //     $price  = $data->price;
            //     $discount  = $data->discount;
            //     $net_price  = $data->net_price;
            //     $total_price  +=  $data->net_price;
            //     $item_data [] = [
            //         "item_name" => $item_name,
            //         "item_category" => $item_category,
            //         "item_sub_category" => $item_sub_category,
            //         "qty" => $qty,
            //         "price" => $price,
            //         "discount" => $discount,
            //         "net_price" => $net_price,
            //     ];
            // }

            // if($request->origin_amount != $request->paid_amount){
            //     $credit = 1;
            // }else{
            //     $credit = 0;
            // }

            // if($request->total_discount != 0){
            //     $discount = 1;
            // }else{
            //     $discount = 0;
            // }
    
            // $today = $date->toFormattedDateString();
            // $invoice_number = str_pad($invoice_pdf->id, 6, '0', STR_PAD_LEFT);
            // $data = [
            //     'shop_name' => $bussiness_info ? $bussiness_info->name : '-',
            //     'shop_email' => $bussiness_info ? $bussiness_info->email : '-',
            //     'shop_phone' => $bussiness_info ? $bussiness_info->phone : '-',
            //     'shop_address' => $bussiness_info ? $bussiness_info->address : '-',
            //     'today_date' => $today,
            //     // 'client_name' => $booking->name,
            //     // 'client_email' => $booking->email,
            //     'invoice_no' => $invoice_number,
            //     'title' => ' Invoice',
            //     'heading1' => '',
            //     'heading2' => 'Invoice',
            //     'total_price' => $total_price,
            //     'item_data' => $item_data,
            //     'total_qty' => $request['total_qty'],
            //     'grand_total' => $request['origin_amount'],
            //     'paid_amount' => $request['paid_amount'],
            //     'credit_amount' => $request['credit_amount'],
            //     'credit' => $credit,
            //     'discount' => $discount,
            //     'total_discount' =>  $request['total_discount'],
            // ];
    
            // $pdf = PDF::loadView('backend.admin.invoices.pdf_view', $data);
            // $pdf_name = uniqid() . '_' . time() . '_' . '.pdf';
            // $invoice_pdf->invoice_no = $invoice_number;
            // $invoice_pdf->invoice_file = $pdf_name;
            // $invoice_pdf->update();
    
            // Storage::put('uploads/pdf/' . $pdf_name, $pdf->output());
            // $pdf->download('sell_invoices.pdf');

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
        $customer = User::where('trash',0)->get();
        $data = unserialize($data_item->item_data);
        $item_id = [];
        foreach ( $data as $item ){
            $item_id [] = $item['item_id'];
        }

        // dd($item_id);

        return view('backend.admin.sell_items.edit', compact('items','customer','data_item','item_id', 'item_category', 'item_sub_category'));
    }

    public function update(SellItem $request, $id)
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
    
    public function addCredit(){

    }
}
