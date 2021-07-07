<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Item;
use App\Models\Cashbook;
use App\Models\Supplier;
use App\Models\ItemLedger;
use App\Models\OpeningItem;
use App\Models\ShopStorage;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use App\Helper\ResponseHelper;
use App\Models\ItemSubCategory;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;
use App\Http\Requests\BuyingItemRequest;

class OpeningItemController extends Controller
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
            $daterange = $request->daterange ? explode(' , ', $request->daterange) : null;

            $opening_items = OpeningItem::anyTrash($request->trash);
            if ($daterange) {
                $opening_items = OpeningItem::whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
            }
            
            if ($request->item != '') {
                $opening_items = OpeningItem::where('item_id', $request->item);
            }

            return Datatables::of($opening_items)
                ->addColumn('action', function ($opening_item) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = ' ';
                    $trash_or_delete_btn = ' ';

                    if ($this->getCurrentAuthUser('admin')->can('edit_item_category')) {
                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.opening_items.edit', ['opening_item' => $opening_item->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }

                    if ($this->getCurrentAuthUser('admin')->can('delete_item_category')) {

                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $opening_item->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $opening_item->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $opening_item->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                        }

                    }

                    return "${detail_btn} ${edit_btn} ${restore_btn} ${trash_or_delete_btn}";
                })
                ->addColumn('item_id', function ($opening_items) {

                    return $opening_items->item_id ? $opening_items->item->name : '-';
                })
                ->addColumn('item_category', function ($opening_items) {

                    return $opening_items->item_category ? $opening_items->item_category->name : '-';
                })
                ->addColumn('item_sub_category', function ($opening_items) {
                    return $opening_items->item_sub_category_id ? $opening_items->item_sub_category->name : '-';
                })
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action','item_id','item_category','item_sub_category'])
                ->make(true);
        }
        return view('backend.admin.opening_items.index',compact('item','item_category','item_sub_category'));
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
        return view('backend.admin.opening_items.create', compact('item_category','supplier','item', 'item_sub_category'));
    }

    public function store(BuyingItemRequest $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_item')) {
            abort(404);
        }

        $item = Item::findOrFail($request->item_id);
        $item_count = count($item);
        if($item_count == 1){
            $item = $item->first();
            $opening_item = new OpeningItem();
            $opening_item->barcode = $item->barcode;
            $opening_item->item_id = $item->id;
            $opening_item->item_category_id = $item->item_category_id;
            $opening_item->item_sub_category_id = $item->item_sub_category_id;
            $opening_item->qty = $request['qty'];
            $opening_item->price = $request['price'];
            $opening_item->discount = $request['discount'];
            $opening_item->net_price = $request['net_price'];
            $opening_item->save();
            
          
            $shop_storage = ShopStorage::where('item_id',$item->id)->first();
            if($shop_storage){
                $qty = ($shop_storage->qty) + ($opening_item->qty);
                $shop_storage->qty = $qty;
                $shop_storage->update();
            }else{
                $shop_storage = new ShopStorage();
                $shop_storage->item_id = $item->id;
                $shop_storage->qty = $opening_item->qty;
                $shop_storage->save();
            }
                

           
        }else{
            for ($var = 0; $var < $item_count - 1;) {
            foreach ($item as $data) {
                $opening_item = new OpeningItem();
                $opening_item->barcode = $data->barcode;
                $opening_item->item_id = $data->id;
                $opening_item->item_category_id = $data->item_category_id;
                $opening_item->item_sub_category_id = $data->item_sub_category_id;
                $opening_item->qty = $request['qty'];
                $opening_item->price = $request['price'];
                $opening_item->discount = $request['discount'];
                $opening_item->net_price = $request['net_price'];
                $opening_item->save();

                $shop_storage = ShopStorage::where('item_id',$data->id)->first();
                if($shop_storage){
                    $qty = ($shop_storage->qty) + ($opening_item->qty);
                    $shop_storage->qty = $qty;
                    $shop_storage->update();
                }else{
                    $shop_storage = new ShopStorage();
                    $shop_storage->item_id = $data->id;
                    $shop_storage->qty = $opening_item->qty;
                    $shop_storage->save();
                }
        
                $var++;
            }
        }
        }
       

        activity()
            ->performedOn($opening_item)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item  (Admin Panel'])
            ->log(' New Item  (' . $opening_item->name . ') is created ');

        return redirect()->route('admin.opening_items.index')->with('success', 'Successfully Created');
    }

    public function show(OpeningItem $opening_item)
    {
        return view('backend.admin.opening_items.show', compact('item'));
    }

    public function edit($id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_item')) {
            abort(404);
        }

        $item = OpeningItem::findOrFail($id);
        $suppliers = Supplier::where('trash',0)->get();
        $item_category = ItemCategory::where('trash', 0)->get();
        $item_sub_category = ItemSubCategory::where('trash', 0)->get();
        $items = Item::where('trash',0)->get();
        return view('backend.admin.opening_items.edit', compact('suppliers','item','items', 'item_category', 'item_sub_category'));
    }

    public function update(BuyingItemRequest $request, $id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_item')) {
            abort(404);
        }

        $supplier = Supplier::where($request->supplier)->first();
        $item=Item::where('id',$request->item_id)->first();
        $opening_item = OpeningItem::findOrFail($id);
        $shop_storage = ShopStorage::where('item_id',$item->id)->first();

        $qty1 = $opening_item->qty ;
        $qty2 = $request->qty;
        $diff_qty = $qty2 - $qty1 ;
        $shop_qty = $shop_storage->qty + ($diff_qty);

        if($shop_storage){
            $shop_storage->qty = $shop_qty;
            $shop_storage->update();
        }

        $opening_item->barcode = $item->barcode;
        $opening_item->item_id = $item->id;
        $opening_item->item_category_id = $item->item_category_id;
        $opening_item->item_sub_category_id = $item->item_sub_category_id;
        $opening_item->qty = $qty2;
        $opening_item->price = $request['price'];
        $opening_item->discount = $request['discount'];
        $opening_item->net_price = $request['net_price'];
        $opening_item->update();

        activity()
            ->performedOn($opening_item)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item  (Admin Panel'])
            ->log('Item  (' . $opening_item->name . ') is updated');

        return redirect()->route('admin.opening_items.index')->with('success', 'Successfully Updated');
    }

    public function destroy(OpeningItem $opening_item)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_item')) {
            abort(404);
        }

        $opening_item->delete();
        activity()
            ->performedOn($opening_item)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item  (Admin Panel'])
            ->log(' Item  (' . $opening_item->name . ')  is deleted ');

        return ResponseHelper::success();
    }

    public function trash(OpeningItem $opening_item)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_item')) {
            abort(404);
        }

        $opening_item->trash();
        activity()
            ->performedOn($opening_item)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item  (Admin Panel)'])
            ->log(' Item (' . $opening_item->name . ')  is moved to trash ');

        return ResponseHelper::success();
    }

    public function restore(OpeningItem $opening_item)
    {
        $opening_item->restore();
        activity()
            ->performedOn($opening_item)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item  (Admin Panel'])
            ->log(' Item  (' . $opening_item->name . ')  is restored from trash ');

        return ResponseHelper::success();
    }
}
