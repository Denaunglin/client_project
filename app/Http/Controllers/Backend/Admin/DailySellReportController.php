<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Item;
use App\Models\SellItems;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use App\Models\ItemSubCategory;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;


class DailySellReportController extends Controller
{
    public function index(Request $request){
        
        $item = Item::where('trash',0)->get();
        $item_category = ItemCategory::where('trash', 0)->get();
        $item_sub_category = ItemSubCategory::where('trash', 0)->get();
        $check_date = date('Y-m-d');
        $sell_items = SellItems::whereDate('created_at',$check_date)->get();
        if ($request->ajax()) {
            $check_date = date('Y-m-d');
            $daterange = $request->daterange ? explode(' , ', $request->daterange) : null;
            $sell_items = SellItems::where('trash',0)->whereDate('created_at',$check_date)->get();
            if ($daterange) {
                $sell_items = SellItems::whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
            }

            return Datatables::of($sell_items)
                ->addColumn('action', function ($sell_item) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = ' ';
                    $trash_or_delete_btn = ' ';

                
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
    
        return view('backend.admin.daily_sales.index',compact('item','item_category','item_sub_category'));
    
    }
}
