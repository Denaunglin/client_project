<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Item;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use App\Helper\ResponseHelper;
use App\Models\ItemSubCategory;
use Yajra\DataTables\DataTables;
use App\Http\Requests\ItemRequest;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;
use Illuminate\Support\Facades\Storage;
use Spatie\ImageOptimizer\OptimizerChainFactory;


class ItemController extends Controller
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
            $items = Item::anyTrash($request->trash);

            if ($request->item != '') {
                $items = Item::where('id', $request->item)->get();
            }

            if ($request->item_category != '') {
                $items = Item::where('item_category_id', $request->item_category)->get();
            }

            if ($request->item_sub_category != '') {
                $items = Item::where('item_sub_category_id', $request->item_sub_category)->get();
            }

            return Datatables::of($items)
                ->addColumn('action', function ($item) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = ' ';
                    $trash_or_delete_btn = ' ';

                    if ($this->getCurrentAuthUser('admin')->can('edit_item_category')) {
                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.items.edit', ['item' => $item->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }
                    $detail_btn = '<a class="detail text text-primary" href="' . route('admin.items.detail', ['item' => $item->id]) . '"><i class="fas fa-info-circle fa-lg"></i></a>';


                    if ($this->getCurrentAuthUser('admin')->can('delete_item_category')) {

                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $item->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $item->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $item->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                        }

                    }

                    return "${detail_btn} ${edit_btn} ${restore_btn} ${trash_or_delete_btn}";
                })
                ->addColumn('item_category', function ($item) {

                    // return $item->item_category ? $item->item_category->name : '-';
                })
                ->addColumn('item_sub_category', function ($item) {
                    // return $item->item_sub_category_id ? $item->item_sub_category->name : '-';
                })
             
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.admin.items.index',compact('item','item_category','item_sub_category'));
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_item')) {
            abort(404);
        }
        $item = Item::where('trash',0)->get();
        $item_category = ItemCategory::where('trash', 0)->get();
        $item_sub_category = ItemSubCategory::where('trash', 0)->get();
        return view('backend.admin.items.create', compact('item_category','item', 'item_sub_category'));
    }

    public function store(ItemRequest $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_item')) {
            abort(404);
        }

        $expire_date = $request->expire_status == 1 ? null : $request->expire_date ;
    
        $item = new Item();
        $item->name = $request['name'];
        $item->item_category_id = $request['item_category_id'];
        $item->item_sub_category_id = $request['item_sub_category_id'];
        $item->minimun_qty = $request['minimun_qty'];
        $item->buying_price = $request['buying_price'];
        $item->retail_price = $request['retail_price'];
        $item->wholesale_price = $request['wholesale_price'];
        $item->save();

        activity()
            ->performedOn($item)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item  (Admin Panel'])
            ->log(' New Item  (' . $item->name . ') is created ');

        return redirect()->route('admin.items.index')->with('success', 'Successfully Created');
    }

    public function detail(Item $item)
    {
        $items = $item;
        return view('backend.admin.items.detail', compact('items'));
    }

    public function edit($id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_item')) {
            abort(404);
        }

        $items = Item::findOrFail($id);
        $item_category = ItemCategory::where('trash', 0)->get();
        $item_sub_category = ItemSubCategory::where('trash', 0)->get();

        return view('backend.admin.items.edit', compact('items', 'item_category', 'item_sub_category'));
    }

    public function update(ItemRequest $request, $id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_item')) {
            abort(404);
        }
        $item = Item::findOrFail($id);

      
        $expire_date = $request->expire_status == 1 ? null : $request->expire_date ;
        $item->name = $request->name;
        $item->item_category_id = $request['item_category_id'];
        $item->item_sub_category_id = $request['item_sub_category_id'];
        $item->minimun_qty = $request['minimun_qty'];
        $item->buying_price = $request['buying_price'];
        $item->retail_price = $request['retail_price'];
        $item->wholesale_price = $request['wholesale_price'];
        $item->update();

        activity()
            ->performedOn($item)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item  (Admin Panel'])
            ->log('Item  (' . $item->name . ') is updated');

        return redirect()->route('admin.items.index')->with('success', 'Successfully Updated');
    }

  
    public function trash(Item $item)
    {
    
        $item->trash();
        activity()
            ->performedOn($item)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item  (Admin Panel)'])
            ->log(' Item (' . $item->name . ')  is moved to trash ');

        return ResponseHelper::success();
    }

    public function restore(Item $item)
    {
        $item->restore();
        activity()
            ->performedOn($item)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item  (Admin Panel'])
            ->log(' Item  (' . $item->name . ')  is restored from trash ');

        return ResponseHelper::success();
    }

    public function destroy(Item $item)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_item')) {
            abort(404);
        }
        $item->delete();
        activity()
            ->performedOn($item)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item  (Admin Panel'])
            ->log(' Item  (' . $item->name . ')  is deleted ');

        return ResponseHelper::success();
    }


    public function reorderList(Request $request){
        $item = Item::where('trash',0)->get();
        $item_category = ItemCategory::where('trash', 0)->get();
        $item_sub_category = ItemSubCategory::where('trash', 0)->get();
       
        if ($request->ajax()) {

            $items = Item::anyTrash($request->trash);

            if ($request->item != '') {
                $items = $items->where('id', $request->item);
            }

            if ($request->item_category != '') {
                $items = $items->where('item_category_id', $request->item_category);
            }

            if ($request->item_sub_category != '') {
                $items = $items->where('item_sub_category_id', $request->item_sub_category);

            }

            return Datatables::of($items)
                ->addColumn('action', function ($item) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = ' ';
                    $trash_or_delete_btn = ' ';

                    if ($this->getCurrentAuthUser('admin')->can('edit_item_category')) {
                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.items.edit', ['item' => $item->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }

                    if ($this->getCurrentAuthUser('admin')->can('delete_item_category')) {

                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $item->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $item->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $item->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                        }

                    }

                    return "${detail_btn} ${edit_btn} ${restore_btn} ${trash_or_delete_btn}";
                })
                ->addColumn('item_category', function ($item) {

                    return $item->item_category ? $item->item_category->name : '-';
                })
                ->addColumn('item_sub_category', function ($item) {
                    return $item->item_sub_category_id ? $item->item_sub_category->name : '-';
                })
                ->addColumn('image', function ($item) {
                    return '<img src="' . $item->image_path() . '" width="100px;"/>';
                })
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action','image'])
                ->make(true);
        }
        return view('backend.admin.reorder_list.index',compact('item','item_category','item_sub_category'));
    }
}
