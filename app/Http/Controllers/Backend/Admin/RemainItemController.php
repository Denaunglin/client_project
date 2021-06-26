<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Item;
use App\Models\SellItems;
use App\Models\ShopStorage;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use App\Models\ItemSubCategory;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;


class RemainItemController extends Controller
{
    public function index(Request $request){
        $shop_storages=ShopStorage::where('trash',0)->get();
        $item = Item::where('trash',0)->get();
        $item_category = ItemCategory::where('trash', 0)->get();
        $item_sub_category = ItemSubCategory::where('trash', 0)->get();

        if ($request->ajax()) {
            if ($request->item != '') {
                $shop_storages = $shop_storages->where('item_id', $request->item);
            }

            return Datatables::of($shop_storages)
                ->addColumn('action', function ($remain_item) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = ' ';
                    $trash_or_delete_btn = ' ';
                
                    return "${detail_btn} ${edit_btn} ${restore_btn} ${trash_or_delete_btn}";
                })
                ->addColumn('barcode', function ($shop_storage) {

                    return $shop_storage->item ? $shop_storage->item->barcode : '-';
                })
                ->addColumn('item_id', function ($shop_storage) {

                    return $shop_storage->item ? $shop_storage->item->name : '-';
                })
                ->addColumn('item_category', function ($shop_storage) {

                    return $shop_storage->item->item_category ? $shop_storage->item->item_category->name : '-';
                })
                ->addColumn('item_sub_category', function ($shop_storage) {
                    return $shop_storage->item->item_sub_category ? $shop_storage->item->item_sub_category->name : '-';
                })
                ->addColumn('price', function ($shop_storage) {

                    return $shop_storage->item ? $shop_storage->item->retail_price : '-';
                })
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action','item_id','item_category','item_sub_category'])
                ->make(true);
            }
        return view('backend.admin.remain_items.index',compact('item','item_category','item_sub_category'));
    }
}
