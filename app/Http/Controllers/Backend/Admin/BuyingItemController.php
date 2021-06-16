<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Item;
use App\Models\Supplier;
use App\Models\SellItems;
use App\Models\BuyingItem;
use App\Models\ItemLedger;
use App\Models\ShopStorage;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use App\Helper\ResponseHelper;
use App\Models\ItemSubCategory;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;

class BuyingItemController extends Controller
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

            $buying_items = BuyingItem::anyTrash($request->trash);

            if ($request->item != '') {
                $buying_items = $buying_items->where('item_id', $request->item);
            }

            if ($request->item_category != '') {
                $buying_items = $buying_items->where('item_category_id', $request->item_category);
            }

            if ($request->item_sub_category != '') {
                $buying_items = $buying_items->where('item_sub_category_id', $request->item_sub_category);

            }

            return Datatables::of($buying_items)
                ->addColumn('action', function ($buying_item) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = ' ';
                    $trash_or_delete_btn = ' ';

                    if ($this->getCurrentAuthUser('admin')->can('edit_item_category')) {
                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.buying_items.edit', ['buying_item' => $buying_item->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }

                    if ($this->getCurrentAuthUser('admin')->can('delete_item_category')) {

                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $buying_item->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $itbuying_itemem->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $buying_item->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                        }

                    }

                    return "${detail_btn} ${edit_btn} ${restore_btn} ${trash_or_delete_btn}";
                })
                ->addColumn('item_id', function ($buying_items) {

                    return $buying_items->item_id ? $buying_items->item->name : '-';
                })
                ->addColumn('item_category', function ($buying_items) {

                    return $buying_items->item_category ? $buying_items->item_category->name : '-';
                })
                ->addColumn('item_sub_category', function ($buying_items) {
                    return $buying_items->item_sub_category_id ? $buying_items->item_sub_category->name : '-';
                })
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action','item_id','item_category','item_sub_category'])
                ->make(true);
        }
        return view('backend.admin.buying_items.index',compact('item','item_category','item_sub_category'));
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_item')) {
            abort(404);
        }
        $supplier = Supplier::where('trash',0)->get();
        $item = Item::where('trash',0)->get();
        $item_category = ItemCategory::where('trash', 0)->get();
        $item_sub_category = ItemSubCategory::where('trash', 0)->get();
        return view('backend.admin.buying_items.create', compact('item_category','supplier','item', 'item_sub_category'));
    }

    public function store(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_item')) {
            abort(404);
        }

        $item = Item::findOrFail($request->item_id);
        $supplier = Supplier::where($request->supplier)->first();
        $item_count = count($item);
        if($item_count == 1){
            $item = $item->first();
            $buying_item = new BuyingItem();
            $buying_item->barcode = $item->barcode;
            $buying_item->item_id = $item->id;
            $buying_item->supplier_id = $supplier ? $supplier->id : null ;
            $buying_item->unit = $item->unit;
            $buying_item->item_category_id = $item->item_category_id;
            $buying_item->item_sub_category_id = $item->item_sub_category_id;
            $buying_item->qty = $request['qty'];
            $buying_item->price = $request['price'];
            $buying_item->discount = $request['discount'];
            $buying_item->net_price = $request['net_price'];
            $buying_item->save();
    
            $shop_storage = ShopStorage::where('item_id',$item->id)->first();
            if($shop_storage){
                $qty = ($shop_storage->qty) + ($buying_item->qty);
                $shop_storage->qty = $qty;
                $shop_storage->update();
            }else{
                $shop_storage = new ShopStorage();
                $shop_storage->item_id = $item->id;
                $shop_storage->qty = $buying_item->qty;
                $shop_storage->save();
            }
                
            $item_ledger= new ItemLedger();
            $item_ledger->item_id = $item->id;
            $item_ledger->opening_qty = '0';
            $item_ledger->buying_buy = $request->qty;
            $item_ledger->buying_back = '0';
            $item_ledger->selling_sell = '0';
            $item_ledger->selling_back = '0';
            $item_ledger->adjust_in = '0';
            $item_ledger->adjust_out = '0';
            $item_ledger->adjust_list = '0';
            $item_ledger->closing_qty = $request->qty;
            $item_ledger->save();

           
        }else{
            for ($var = 0; $var < $item_count - 1;) {
            foreach ($item as $data) {
                $buying_item = new BuyingItem();
                $buying_item->barcode = $data->barcode;
                $buying_item->supplier_id = $supplier ? $supplier->id : null;
                $buying_item->item_id = $data->id;
                $buying_item->unit = $data->unit;
                $buying_item->item_category_id = $data->item_category_id;
                $buying_item->item_sub_category_id = $data->item_sub_category_id;
                $buying_item->qty = $request['qty'];
                $buying_item->price = $request['price'];
                $buying_item->discount = $request['discount'];
                $buying_item->net_price = $request['net_price'];
                $buying_item->save();

                $shop_storage = ShopStorage::where('item_id',$data->id)->first();
                if($shop_storage){
                    $qty = ($shop_storage->qty) + ($buying_item->qty);
                    $shop_storage->qty = $qty;
                    $shop_storage->update();
                }else{
                    $shop_storage = new ShopStorage();
                    $shop_storage->item_id = $data->id;
                    $shop_storage->qty = $buying_item->qty;
                    $shop_storage->save();
                }
        
                $item_ledger= new ItemLedger();
                $item_ledger->item_id = $data->id;
                $item_ledger->opening_qty = '0';
                $item_ledger->buying_buy = $request->qty;
                $item_ledger->buying_back = '0';
                $item_ledger->selling_sell = '0';
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
       

        activity()
            ->performedOn($buying_item)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item  (Admin Panel'])
            ->log(' New Item  (' . $buying_item->name . ') is created ');

        return redirect()->route('admin.buying_items.index')->with('success', 'Successfully Created');
    }

    public function show(BuyingItem $buying_item)
    {
        return view('backend.admin.buying_items.show', compact('item'));
    }

    public function edit($id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_item')) {
            abort(404);
        }

        $item = BuyingItem::findOrFail($id);
        $suppliers = Supplier::where('trash',0)->get();
        $item_category = ItemCategory::where('trash', 0)->get();
        $item_sub_category = ItemSubCategory::where('trash', 0)->get();
        $items = Item::where('trash',0)->get();
        return view('backend.admin.buying_items.edit', compact('suppliers','item','items', 'item_category', 'item_sub_category'));
    }

    public function update(Request $request, $id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_item')) {
            abort(404);
        }

        $supplier = Supplier::where($request->supplier)->first();
        $item=Item::where('id',$request->item_id)->first();
        $buying_item = BuyingItem::findOrFail($id);
        $shop_storage = ShopStorage::where('item_id',$item->id)->first();

        $qty1 = $buying_item->qty ;
        $qty2 = $request->qty;
        $diff_qty = $qty2 - $qty1 ;
        $shop_qty = $shop_storage->qty + ($diff_qty);

        if($shop_storage){
            $shop_storage->qty = $shop_qty;
            $shop_storage->update();
        }

        $buying_item->barcode = $item->barcode;
        $buying_item->supplier_id = $supplier ? $supplier->id : null;
        $buying_item->item_id = $item->id;
        $buying_item->unit = $item->unit;
        $buying_item->item_category_id = $item->item_category_id;
        $buying_item->item_sub_category_id = $item->item_sub_category_id;
        $buying_item->qty = $qty2;
        $buying_item->price = $request['price'];
        $buying_item->discount = $request['discount'];
        $buying_item->net_price = $request['net_price'];
        $buying_item->update();

        activity()
            ->performedOn($buying_item)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item  (Admin Panel'])
            ->log('Item  (' . $buying_item->name . ') is updated');

        return redirect()->route('admin.buying_items.index')->with('success', 'Successfully Updated');
    }

    public function destroy(BuyingItem $buying_item)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_item')) {
            abort(404);
        }

        $buying_item->delete();
        activity()
            ->performedOn($buying_item)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item  (Admin Panel'])
            ->log(' Item  (' . $buying_item->name . ')  is deleted ');

        return ResponseHelper::success();
    }

    public function trash(BuyingItem $buying_item)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_item')) {
            abort(404);
        }

        $buying_item->trash();
        activity()
            ->performedOn($buying_item)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item  (Admin Panel)'])
            ->log(' Item (' . $buying_item->name . ')  is moved to trash ');

        return ResponseHelper::success();
    }

    public function restore(ItemCategory $buying_item)
    {
        $buying_item->restore();
        activity()
            ->performedOn($buying_item)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item  (Admin Panel'])
            ->log(' Item  (' . $buying_item->name . ')  is restored from trash ');

        return ResponseHelper::success();
    }

}
