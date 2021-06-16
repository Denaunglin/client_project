<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Item;
use App\Models\Supplier;
use App\Models\SellItems;
use App\Models\ItemLedger;
use App\Models\ShopStorage;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
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
        if ($request->ajax()) {

            $sell_items = SellItems::anyTrash($request->trash);

            if ($request->item != '') {
                $sell_items = $sell_items->where('id', $request->item);
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
                    $trash_or_delete_btn = ' ';

                    if ($this->getCurrentAuthUser('admin')->can('edit_item_category')) {
                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.sell_items.edit', ['sell_item' => $sell_item->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }

                    if ($this->getCurrentAuthUser('admin')->can('delete_item_category')) {

                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $sell_item->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $itsell_itemem->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $sell_item->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                        }

                    }

                    return "${detail_btn} ${edit_btn} ${restore_btn} ${trash_or_delete_btn}";
                })
                ->addColumn('item_id', function ($sell_item) {

                    return $sell_item->item_id ? $sell_item->item->name : '-';
                })
                ->addColumn('item_category', function ($sell_item) {

                    return $sell_item->item_category ? $sell_item->item_category->name : '-';
                })
                ->addColumn('item_sub_category', function ($sell_item) {
                    return $sell_item->item_sub_category_id ? $sell_item->item_sub_category->name : '-';
                })
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action','item_id','item_category','item_sub_category'])
                ->make(true);
        }
        return view('backend.admin.sell_items.index',compact('item','item_category','item_sub_category'));
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_item')) {
            abort(404);
        }
        $item = Item::where('trash',0)->get();
        $item_category = ItemCategory::where('trash', 0)->get();
        $item_sub_category = ItemSubCategory::where('trash', 0)->get();
        return view('backend.admin.sell_items.create', compact('item_category','item', 'item_sub_category'));
    }

    public function store(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_item')) {
            abort(404);
        }

        $cart = Session::get('cart');

        dd($cart);
        if($cart){
            $item = Item::findOrFail($cart['id']);
        }else{
            $item = Item::findOrFail($request->item_id);
        }
      
        $item_count = count($item);
        if($item_count == 1){
            $item = $item->first();
            $sell_item = new SellItems();
            $sell_item->barcode = $item->barcode;
            $sell_item->item_id = $item->id;
            $sell_item->unit = $item->unit;
            $sell_item->item_category_id = $item->item_category_id;
            $sell_item->item_sub_category_id = $item->item_sub_category_id;
            $sell_item->qty = $request['qty'];
            $sell_item->price = $request['price'];
            $sell_item->discount = $request['discount'];
            $sell_item->net_price = $request['net_price'];
            $sell_item->save();
    
            $shop_storage = ShopStorage::where('item_id',$item->id)->first();
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
            $item_ledger->opening_qty = '0';
            $item_ledger->buying_buy = '0';
            $item_ledger->buying_back = '0';
            $item_ledger->selling_sell = $request->qty;
            $item_ledger->selling_back = '0';
            $item_ledger->adjust_in = '0';
            $item_ledger->adjust_out = '0';
            $item_ledger->adjust_list = '0';
            $item_ledger->closing_qty = $request->qty;
            $item_ledger->save();
        }else{
            for ($var = 0; $var < $item_count - 1;) {
            foreach ($item as $data) {
                $sell_item = new SellItems();
                $sell_item->barcode = $data->barcode;
                $sell_item->item_id = $data->id;
                $sell_item->unit = $data->unit;
                $sell_item->item_category_id = $data->item_category_id;
                $sell_item->item_sub_category_id = $data->item_sub_category_id;
                $sell_item->qty = $request['qty'];
                $sell_item->price = $request['price'];
                $sell_item->discount = $request['discount'];
                $sell_item->net_price = $request['net_price'];
                $sell_item->save();

                $shop_storage = ShopStorage::where('item_id',$data->id)->first();
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
                $item_ledger->opening_qty = '0';
                $item_ledger->buying_buy = '0';
                $item_ledger->buying_back = '0';
                $item_ledger->selling_sell = $request->qty;
                $item_ledger->selling_back = '0';
                $item_ledger->adjust_in = '0';
                $item_ledger->adjust_out = '0';
                $item_ledger->adjust_list = '0';
                $item_ledger->closing_qty = $request->qty;
                $item_ledger->save();
                $var++;
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
            ->log(' Sell Item  is created ');

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

        $qty1 = $sell_item->qty ;
        $qty2 = $request->qty;
        $diff_qty = $qty2 - $qty1 ;
        $shop_qty = $shop_storage->qty - ($diff_qty);

        if($shop_storage){
            $shop_storage->qty = $shop_qty;
            $shop_storage->update();
        }

        $sell_item->barcode = $item->barcode;
        $sell_item->item_id = $item->id;
        $sell_item->unit = $item->unit;
        $sell_item->item_category_id = $item->item_category_id;
        $sell_item->item_sub_category_id = $item->item_sub_category_id;
        $sell_item->qty = $request['qty'];
        $sell_item->price = $request['price'];
        $sell_item->discount = $request['discount'];
        $sell_item->net_price = $request['net_price'];
        $sell_item->update();

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

    public function restore(ItemCategory $sell_item)
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
      
        $cart = Session::get('cart');        

        $item_count = count($cart);
        if($item_count == 1){
            foreach( $cart as $cart_data){
            $item = Item::where('id',$cart_data['id'])->first();
            $sell_item = new SellItems();
            $sell_item->barcode = $item->barcode;
            $sell_item->item_id = $item->id;
            $sell_item->unit = $item->unit;
            $sell_item->item_category_id = $item->item_category_id;
            $sell_item->item_sub_category_id = $item->item_sub_category_id;
            $sell_item->qty = $cart_data['quantity'];
            $sell_item->price = $cart_data['price'];
            $sell_item->discount = 0;
            $sell_item->net_price = $cart_data['total'];
            $sell_item->save();
    
            $shop_storage = ShopStorage::where('item_id',$item->id)->first();
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
            $item_ledger->opening_qty = '0';
            $item_ledger->buying_buy = '0';
            $item_ledger->buying_back = '0';
            $item_ledger->selling_sell = $cart_data['quantity'];
            $item_ledger->selling_back = '0';
            $item_ledger->adjust_in = '0';
            $item_ledger->adjust_out = '0';
            $item_ledger->adjust_list = '0';
            $item_ledger->closing_qty =  $shop_storage->qty;
            $item_ledger->save();
        }
         }else{
            for ($var = 0; $var < $item_count - 1;) {
            foreach( $cart as $cart_data){
             $item = Item::where('id',$cart_data['id'])->get();
            foreach ($item as $data) {
                $sell_item = new SellItems();
                $sell_item->barcode = $data->barcode;
                $sell_item->item_id = $data->id;
                $sell_item->unit = $data->unit;
                $sell_item->item_category_id = $data->item_category_id;
                $sell_item->item_sub_category_id = $data->item_sub_category_id;
                $sell_item->qty = $cart_data['quantity'];
                $sell_item->price = $cart_data['price'];
                $sell_item->discount = 0;
                $sell_item->net_price = $cart_data['total'];
                $sell_item->save();

                $shop_storage = ShopStorage::where('item_id',$data->id)->first();
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
                $item_ledger->opening_qty = '0';
                $item_ledger->buying_buy = '0';
                $item_ledger->buying_back = '0';
                $item_ledger->selling_sell = $cart_data['quantity'];
                $item_ledger->selling_back = '0';
                $item_ledger->adjust_in = '0';
                $item_ledger->adjust_out = '0';
                $item_ledger->adjust_list = '0';
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
    }
}
