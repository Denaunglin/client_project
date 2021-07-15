<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Item;
use App\Models\OrderList;
use App\Models\ShopStorage;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use App\Models\ItemSubCategory;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;


class OrderListController extends Controller
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
        $shop_storage = ShopStorage::where('trash',0)->get();

        $order_item = [];
        foreach($item as $item_data){
            $get_order_item  = ShopStorage::where('item_id',$item_data->id)->where('qty','<',$item_data->minimun_qty )->first();
            if($get_order_item != null){
                $order_item [] = $get_order_item;
            }
        }


        $order_list = [];
        foreach($order_item as $data){
            $order_list[] = Item::where('id',$data->item_id)->first();
        }

        if ($request->ajax()) {
            if($request->item){
              $order_list =   $order_list->where('id',$request->item)->get();
            }


            return Datatables::of($order_list)
                ->addColumn('action', function ($order_list) use ($request) {
                    // $detail_btn = '';
                    // $restore_btn = '';
                    // $edit_btn = ' ';
                    // $trash_or_delete_btn = ' ';

                    // if ($this->getCurrentAuthUser('admin')->can('edit_item_category')) {
                    //     $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.order_lists.edit', ['order_list' => $order_list->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    // }

                    // if ($this->getCurrentAuthUser('admin')->can('delete_item_category')) {

                    //     if ($request->trash == 1) {
                    //         $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $order_list->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                    //         $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $order_list->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                    //     } else {
                    //         $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $order_list->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                    //     }

                    // }

                    // return "${detail_btn} ${edit_btn} ${restore_btn} ${trash_or_delete_btn}";
                })
                ->addColumn('item_group', function ($order_list) {

                    return $order_list->item_category ? $order_list->item_category->name : '-';
                })
                ->addColumn('item_sub_group', function ($order_list) {
                    return $order_list->item_sub_category_id ? $order_list->item_sub_category->name : '-';
                })
                ->editColumn('minimun_qty', function ($order_list) {
                    $minimun_qty = $order_list->minimun_qty ? $order_list->minimun_qty : '-';
                    return "<span class='badge badge-danger'>".$minimun_qty."</span>";

                })
                ->editColumn('stock_in_hand',function($order_list){
                    $shop_storage = ShopStorage::where('item_id',$order_list->id)->first();
                    $stock_in_hand = $shop_storage->qty;
                    return "<span class='badge badge-danger'>".$stock_in_hand."</span>";

                })
                ->editColumn('to_re_order',function($order_list){
                    $shop_storage = ShopStorage::where('item_id',$order_list->id)->first();
                    $stock_in_hand = $shop_storage->qty;
                    $minimun_qty = $order_list->minimun_qty;
                    $to_re_order = $minimun_qty - $stock_in_hand;
                    return "<span class='badge badge-warning'>".$to_re_order."</span>";

                })
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action','minimun_qty','stock_in_hand','to_re_order'])
                ->make(true);
        }
        return view('backend.admin.order_lists.index',compact('item','item_category','item_sub_category'));
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_item')) {
            abort(404);
        }
        $item = Item::where('trash',0)->get();
        $item_category = ItemCategory::where('trash', 0)->get();
        $item_sub_category = ItemSubCategory::where('trash', 0)->get();
        return view('backend.admin.order_lists.create', compact('item_category','item', 'item_sub_category'));
    }

    public function store(ItemRequest $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_item')) {
            abort(404);
        }

        $order_list = new OrderList();
        $order_list->item_group = $request['item_group'];
        $order_list->item_sub_group = $request['item_sub_group'];
        $order_list->item_name = $request['item_name'];
        $order_list->unit = $request['unit'];
        $order_list->minimun_qty = $request['minimun_qty'];
        $order_list->stock_in_hand = $request['stock_in_hand'];
        $order_list->to_reorder = $request['to_reorder'];
        $order_list->save();

        activity()
            ->performedOn($order_list)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Order List  (Admin Panel'])
            ->log(' New Order List  (' . $order_list->item_name . ') is created ');

        return redirect()->route('admin.order_lists.index')->with('success', 'Successfully Created');
    }

    public function show(OrderList $order_list)
    {
        return view('backend.admin.order_lists.show', compact('item'));
    }

    public function edit($id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_item')) {
            abort(404);
        }

        $order_list = OrderList::findOrFail($id);
        $item_category = ItemCategory::where('trash', 0)->get();
        $item_sub_category = ItemSubCategory::where('trash', 0)->get();

        return view('backend.admin.order_lists.edit', compact('order_list', 'item_category', 'item_sub_category'));
    }

    public function update(ItemRequest $request, $id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_item')) {
            abort(404);
        }
        $order_list = OrderList::findOrFail($id);

        $order_list->item_group = $request['item_group'];
        $order_list->item_sub_group = $request['item_sub_group'];
        $order_list->item_name = $request['item_name'];
        $order_list->unit = $request['unit'];
        $order_list->minimun_qty = $request['minimun_qty'];
        $order_list->stock_in_hand = $request['stock_in_hand'];
        $order_list->to_reorder = $request['to_reorder'];
        $order_list->update();

        activity()
            ->performedOn($order_list)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Order List  (Admin Panel'])
            ->log('Order List  (' . $order_list->item_name . ') is updated');

        return redirect()->route('admin.order_lists.index')->with('success', 'Successfully Updated');
    }

    public function destroy(OrderList $order_list)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_item')) {
            abort(404);
        }

        $order_list->delete();
        activity()
            ->performedOn($order_list)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Order List  (Admin Panel'])
            ->log(' Order List  (' . $order_list->item_name . ')  is deleted ');

        return ResponseHelper::success();
    }

    public function trash(OrderList $order_list)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_item')) {
            abort(404);
        }

        $order_list->trash();
        activity()
            ->performedOn($order_list)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Order List  (Admin Panel)'])
            ->log(' Order List (' . $order_list->item_name . ')  is moved to trash ');

        return ResponseHelper::success();
    }

    public function restore(OrderList $order_list)
    {
        $order_list->restore();
        activity()
            ->performedOn($order_list)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Order List  (Admin Panel'])
            ->log(' Order List  (' . $order_list->item_name . ')  is restored from trash ');

        return ResponseHelper::success();
    }
}
