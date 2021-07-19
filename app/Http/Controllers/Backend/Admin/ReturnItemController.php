<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Item;
use App\Models\Cashbook;
use App\Models\ItemLedger;
use App\Models\ReturnItem;
use App\Models\ShopStorage;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use App\Helper\ResponseHelper;
use App\Models\ItemSubCategory;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;
use App\Http\Requests\BuyingItemRequest;


class ReturnItemController extends Controller
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

            $return_items = ReturnItem::anyTrash($request->trash);
            
            if ($daterange) {
                $return_items = ReturnItem::whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
            }
            if ($request->item != '') {
                $return_items = $return_items->where('item_id', $request->item);
            }

            if ($request->item_category != '') {
                $return_items = $return_items->where('item_category_id', $request->item_category);
            }

            if ($request->item_sub_category != '') {
                $return_items = $return_items->where('item_sub_category_id', $request->item_sub_category);

            }

            return Datatables::of($return_items)
                ->addColumn('action', function ($return_item) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = ' ';
                    $invoice_btn = '';
                    $trash_or_delete_btn = ' ';

                    if ($this->getCurrentAuthUser('admin')->can('edit_item_category')) {
                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.return_items.edit', ['return_item' => $return_item->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }

                    if ($this->getCurrentAuthUser('admin')->can('delete_item_category')) {
                       

                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $return_item->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $return_item->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $return_item->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                        }

                    }

                    return "${detail_btn} ${edit_btn} ${restore_btn} ${trash_or_delete_btn} ${invoice_btn} ";
                })
                ->addColumn('return_type', function ($return_item) {
                    if($return_item->return_type == 0){
                        return '<span class="badge badge-primary">Buying Return</span>';
                    }else{
                        return '<span class="badge badge-primary">Sale Return</span>';
                    }
                })
                ->addColumn('item_id', function ($return_item) {

                    return $return_item->item ? $return_item->item->name : '-';
                })
                ->addColumn('item_category', function ($return_item) {

                    return $return_item->item_category ? $return_item->item_category->name : '-';
                })
                ->addColumn('item_sub_category', function ($return_item) {
                    return $return_item->item_sub_category_id ? $return_item->item_sub_category->name : '-';
                })
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action','return_type','item_id','item_category','item_sub_category'])
                ->make(true);
        }
        return view('backend.admin.return_items.index',compact('item','item_category','item_sub_category'));
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_item')) {
            abort(404);
        }
        $item = Item::where('trash',0)->get();
        $item_category = ItemCategory::where('trash', 0)->get();
        $item_sub_category = ItemSubCategory::where('trash', 0)->get();
        return view('backend.admin.return_items.create', compact('item_category','item', 'item_sub_category'));
    }

    public function store(BuyingItemRequest $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_item')) {
            abort(404);
        }
        $return_type = $request->return_type ? $request->return_type : 0;
        $item = Item::findOrFail($request->item_id);
    
        $item_count = count($item);
        if($item_count == 1){
            $item = $item->first();
            $return_item = new ReturnItem();
            $return_item->return_type = $return_type;
            $return_item->item_id = $item->id;
            $return_item->item_category_id = $item->item_category_id;
            $return_item->item_sub_category_id = $item->item_sub_category_id;
            $return_item->qty = $request['qty'];
            $return_item->price = $request['price'];
            $return_item->discount = $request['discount'];
            $return_item->net_price = $request['net_price'];
            $return_item->save();

            $cash_book = new Cashbook();
            if($return_type == 0){
                $cash_book->cashbook_income = $return_item->net_price;
            }else{
                $cash_book->cashbook_income = 0;
            }
            if($return_type == 1){
                $cash_book->cashbook_outgoing = $return_item->net_price ;
            }else{
                $cash_book->cashbook_outgoing = 0 ;
            }
            $cash_book->buying_id = null;
            $cash_book->service_id = null;
            $cash_book->selling_id = null;
            $cash_book->expense_id = null;
            $cash_book->credit_id = null;
            $cash_book->return_id = $return_item->id;
            $cash_book->save();

    
            $shop_storage = ShopStorage::where('item_id',$item->id)->first();
            if($shop_storage){
                $qty = ($shop_storage->qty) - ($return_item->qty);
                $shop_storage->qty = $qty;
                $shop_storage->update();
            }else{
                $shop_storage = new ShopStorage();
                $shop_storage->item_id = $item->id;
                $shop_storage->qty = $return_item->qty;
                $shop_storage->save();
            }

            $item_ledger= new ItemLedger();
            $item_ledger->item_id = $item->id;
            $item_ledger->opening_qty = '0';
            $item_ledger->buying_buy = '0';
            if($return_type == 0){
                $item_ledger->buying_back = $request->qty;
            }else{
                $item_ledger->buying_back = '0';
            }
            $item_ledger->selling_sell = '0';
            if($return_type == 1){
                $item_ledger->selling_back = $request->qty;
            }else{
            $item_ledger->selling_back = '0';
            }
            $item_ledger->adjust_in = '0';
            $item_ledger->adjust_out = '0';
            $item_ledger->closing_qty = $shop_storage->qty;
            $item_ledger->save();
        }else{
            for ($var = 0; $var < $item_count - 1;) {
            foreach ($item as $data) {
                $return_item = new ReturnItem();
                $return_item->item_id = $data->id;
                $return_item->return_type = $return_type;
                $return_item->item_category_id = $data->item_category_id;
                $return_item->item_sub_category_id = $data->item_sub_category_id;
                $return_item->qty = $request['qty'];
                $return_item->price = $request['price'];
                $return_item->discount = $request['discount'];
                $return_item->net_price = $request['net_price'];
                $return_item->save();

                $cash_book = new Cashbook();
                if($return_type == 0){
                    $cash_book->cashbook_income = $return_item->net_price;
                }else{
                    $cash_book->cashbook_income = 0;
                }
                if($return_type == 1){
                    $cash_book->cashbook_outgoing = $return_item->net_price ;
                }else{
                    $cash_book->cashbook_outgoing = 0 ;
                }
                $cash_book->buying_id = null;
                $cash_book->service_id = null;
                $cash_book->selling_id = null;
                $cash_book->expense_id = null;
                $cash_book->credit_id = null;
                $cash_book->return_id = $return_item->id;
                $cash_book->save();

                $shop_storage = ShopStorage::where('item_id',$data->id)->first();
                if($shop_storage){
                    $qty = ($shop_storage->qty) - ($return_item->qty);
                    $shop_storage->qty = $qty;
                    $shop_storage->update();
                }else{
                    $shop_storage = new ShopStorage();
                    $shop_storage->item_id = $data->id;
                    $shop_storage->qty = $return_item->qty;
                    $shop_storage->save();
                }
        
                $item_ledger= new ItemLedger();
                $item_ledger->item_id = $data->id;
                $item_ledger->opening_qty = '0';
                $item_ledger->buying_buy = '0';
                if($return_type == 0){
                    $item_ledger->buying_back = $request->qty;
                }else{
                    $item_ledger->buying_back = '0';
                }
                $item_ledger->selling_sell = '0';
                if($return_type == 1){
                    $item_ledger->selling_back = $request->qty;
                }else{
                $item_ledger->selling_back = '0';
                }
                $item_ledger->adjust_in = '0';
                $item_ledger->adjust_out = '0';
                $item_ledger->closing_qty = $shop_storage->qty;
                $item_ledger->save();
                $var++;
            }
        }
        }

        activity()
            ->performedOn($return_item)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => ' Return Item   (Admin Panel'])
            ->log(' Return Item  is created ');

        return redirect()->route('admin.return_items.index')->with('success', 'Successfully Created');
    }

    public function show(ReturnItem $return_item)
    {
        return view('backend.admin.return_items.show', compact('item'));
    }

    public function edit($id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_item')) {
            abort(404);
        }

        $data_item = ReturnItem::findOrFail($id);
        $items =  Item::where('trash',0)->get();
        $item_category = ItemCategory::where('trash', 0)->get();
        $item_sub_category = ItemSubCategory::where('trash', 0)->get();

        return view('backend.admin.return_items.edit', compact('items','data_item', 'item_category', 'item_sub_category'));
    }

    public function update(BuyingItemRequest $request, $id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_item')) {
            abort(404);
        }
        $return_type = $request->return_type ? $request->return_type : 0;
        $item=Item::where('id',$request->item_id)->first();

        $return_item = ReturnItem::findOrFail($id);
        $shop_storage = ShopStorage::where('item_id',$item->id)->first();
        $opening_qty = $shop_storage->qty;
        $qty1 = $return_item->qty ;
        $qty2 = $request->qty;
        $diff_qty = $qty2 - $qty1 ;
        $shop_qty = $shop_storage->qty - ($diff_qty);

        if($shop_storage){
            $shop_storage->qty = $shop_qty;
            $shop_storage->update();
        }

        $return_item->item_id = $item->id;
        $return_item->return_type = $return_type;
        $return_item->item_category_id = $item->item_category_id;
        $return_item->item_sub_category_id = $item->item_sub_category_id;
        $return_item->qty = $request['qty'];
        $return_item->price = $request['price'];
        $return_item->discount = $request['discount'];
        $return_item->net_price = $request['net_price'];
        $return_item->update();

        $cash_book = Cashbook::where('return_id', $return_item->id)->first();
        if($return_type == 0){
            $cash_book->cashbook_income = $return_item->net_price;
        }else{
            $cash_book->cashbook_income = 0;
        }
        if($return_type == 1){
            $cash_book->cashbook_outgoing = $return_item->net_price ;
        }else{
            $cash_book->cashbook_outgoing = 0 ;
        }

        $cash_book->buying_id = null;
        $cash_book->selling_id = null;
        $cash_book->service_id = null;
        $cash_book->expense_id = null;
        $cash_book->credit_id = null;
        $cash_book->return_id = $return_item->id;
        $cash_book->update();

        $item_ledger=ItemLedger::where('item_id',$request->item_id)->first();
        $item_ledger->item_id = $request->item_id;
        $item_ledger->opening_qty = $opening_qty;
        $item_ledger->buying_buy = $item_ledger->buying_buy;
        if($return_type == 0){
            $item_ledger->buying_back = $request->qty;
        }else{
            $item_ledger->buying_back = $item_ledger->buying_back;
        }
        $item_ledger->selling_sell = $return_item->qty;
        if($return_type == 1){
            $item_ledger->selling_back = $request->qty;
        }else{
        $item_ledger->selling_back = $item_ledger->buying_back;
        }
        if($return_type == 0){
            $item_ledger->adjust_out = $request->qty;
        }else{
            $item_ledger->adjust_out = $item_ledger->adjust_out;

        }
        if($return_type == 1){
            $item_ledger->adjust_in = $request->qty;
        }else{
            $item_ledger->adjust_in = $item_ledger->adjust_in;

        }
        $item_ledger->closing_qty = $shop_storage->qty;
        $item_ledger->update();

        activity()
            ->performedOn($return_item)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => ' Return Item   (Admin Panel'])
            ->log(' Return Item  is updated');

        return redirect()->route('admin.return_items.index')->with('success', 'Successfully Updated');
    }

    public function destroy(ReturnItem $return_item)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_item')) {
            abort(404);
        }

        $return_item->delete();
        activity()
            ->performedOn($return_item)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Return  (Admin Panel'])
            ->log(' Return  (' . $return_item->name . ')  is deleted ');

        return ResponseHelper::success();
    }

    public function trash(ReturnItem $return_item)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_item')) {
            abort(404);
        }

        $return_item->trash();
        activity()
            ->performedOn($return_item)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Return Item  (Admin Panel)'])
            ->log(' Return Item  is moved to trash ');

        return ResponseHelper::success();
    }

    public function restore(ReturnItem $return_item)
    {
        $return_item->restore();
        activity()
            ->performedOn($return_item)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Return Item  (Admin Panel'])
            ->log(' Return Item  is restored from trash ');

        return ResponseHelper::success();
    }
    
}
