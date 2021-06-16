<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\OrderList;
use App\Models\Item;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use App\Models\ItemSubCategory;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;
use Yajra\DataTables\DataTables;


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
        if ($request->ajax()) {

            $order_lists = OrderList::anyTrash($request->trash);

            if ($request->item != '') {
                $items = $items->where('id', $request->item);
            }

            if ($request->item_category != '') {
                $items = $items->where('item_category_id', $request->item_category);
            }

            if ($request->item_sub_category != '') {
                $items = $items->where('item_sub_category_id', $request->item_sub_category);

            }

            return Datatables::of($order_lists)
                ->addColumn('action', function ($order_list) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = ' ';
                    $trash_or_delete_btn = ' ';

                    if ($this->getCurrentAuthUser('admin')->can('edit_item_category')) {
                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.order_lists.edit', ['order_list' => $order_list->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }

                    if ($this->getCurrentAuthUser('admin')->can('delete_item_category')) {

                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $order_list->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $order_list->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $order_list->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                        }

                    }

                    return "${detail_btn} ${edit_btn} ${restore_btn} ${trash_or_delete_btn}";
                })
                ->addColumn('item_category', function ($order_list) {

                    return $order_list->item_category ? $order_list->item_category->name : '-';
                })
                ->addColumn('item_sub_category', function ($order_list) {
                    return $order_list->item_sub_category_id ? $order_list->item_sub_category->name : '-';
                })
                ->addColumn('image', function ($order_list) {
                    return '<img src="' . $order_list->image_path() . '" width="100px;"/>';
                })
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action','image'])
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
            ->withProperties(['source' => 'Item  (Admin Panel'])
            ->log(' New Item  (' . $order_list->item_name . ') is created ');

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
            ->withProperties(['source' => 'Item  (Admin Panel'])
            ->log('Item  (' . $order_list->item_name . ') is updated');

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
            ->withProperties(['source' => 'Item  (Admin Panel'])
            ->log(' Item  (' . $order_list->item_name . ')  is deleted ');

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
            ->withProperties(['source' => 'Item  (Admin Panel)'])
            ->log(' Item (' . $order_list->item_name . ')  is moved to trash ');

        return ResponseHelper::success();
    }

    public function restore(OrderList $order_list)
    {
        $order_list->restore();
        activity()
            ->performedOn($order_list)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item  (Admin Panel'])
            ->log(' Item  (' . $order_list->item_name . ')  is restored from trash ');

        return ResponseHelper::success();
    }
}
