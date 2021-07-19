<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Item;
use App\Models\ShopStorage;
use Illuminate\Http\Request;
use App\Helper\ResponseHelper;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;


class ShopStorageController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {
            // if (!$this->getCurrentAuthUser('admin')->can('view_bed_type')) {
            //     abort(404);
            // }
        if ($request->ajax()) {

            $shop_storages = ShopStorage::with('item')->anyTrash($request->trash);
            return Datatables::of($shop_storages)
                ->addColumn('action', function ($shop_storage) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = ' ';
                    $trash_or_delete_btn = ' ';

                    // if ($this->getCurrentAuthUser('admin')->can('edit_bed_type')) {
                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.shop_storages.edit', ['shop_storage' => $shop_storage->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    // }

                    // if ($this->getCurrentAuthUser('admin')->can('delete_bed_type')) {

                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $shop_storage->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $shop_storage->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $shop_storage->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                        }

                    // }

                    return "${detail_btn} ${edit_btn} ${restore_btn} ${trash_or_delete_btn}";
                })
                ->addColumn('item_id', function ($shop_storage) {
                    return $shop_storage->item ? $shop_storage->item->name : '-';
                })
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.admin.shop_storages.index');
    }

    public function create()
    {
        // if (!$this->getCurrentAuthUser('admin')->can('add_bed_type')) {
        //     abort(404);
        // }
        $item = Item::where('trash','0')->get();
        return view('backend.admin.shop_storages.create',compact('item'));
    }

    public function store(Request $request)
    {
        // if (!$this->getCurrentAuthUser('admin')->can('add_bed_type')) {
        //     abort(404);
        // }
        $shop_storage =  ShopStorage::where('item_id',$request->item_id)->first();
        if($shop_storage){
            $qty = $request->qty + $shop_storage->qty;
            $shop_storage->qty = $qty;
            $shop_storage->update();
        }else{
            $shop_storage = new ShopStorage();
            $shop_storage->item_id = $request['item_id'];
            $shop_storage->qty = $request['qty'];
            $shop_storage->save();
        }

        activity()
            ->performedOn($shop_storage)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Shop Storage (Admin Panel'])
            ->log(' New Shop Storage  is created ');

        return redirect()->route('admin.shop_storages.index')->with('success', 'Successfully Created');
    }

    public function show(ShopStorage $shop_storage)
    {
        return view('backend.admin.shop_storages.show', compact('shop_storage'));
    }

    public function edit($id)
    {
        // if (!$this->getCurrentAuthUser('admin')->can('edit_bed_type')) {
        //     abort(404);
        // }
        $item = Item::where('trash','0')->get();

        $shop_storage = ShopStorage::findOrFail($id);
        return view('backend.admin.shop_storages.edit', compact('shop_storage','item'));
    }

    public function update(Request $request, $id)
    {
        // if (!$this->getCurrentAuthUser('admin')->can('edit_bed_type')) {
        //     abort(404);
        // }

        $shop_storage = ShopStorage::findOrFail($id);
        $shop_storage->item_id = $request['item_id'];
        $shop_storage->qty = $request['qty'];      
        $shop_storage->update();

        activity()
            ->performedOn($shop_storage)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Shop Storage (Admin Panel'])
            ->log('Shop Storage is updated');

        return redirect()->route('admin.shop_storages.index')->with('success', 'Successfully Updated');
    }

    public function destroy(ShopStorage $shop_storage)
    {
        // if (!$this->getCurrentAuthUser('admin')->can('delete_bed_type')) {
        //     abort(404);
        // }

        $shop_storage->delete();
        activity()
            ->performedOn($shop_storage)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Shop Storage (Admin Panel'])
            ->log(' Shop Storage  is deleted ');

        return ResponseHelper::success();
    }

    public function trash(ShopStorage $shop_storage)
    {
        // if (!$this->getCurrentAuthUser('admin')->can('delete_bed_type')) {
        //     abort(404);
        // }

        $shop_storage->trash();
        activity()
            ->performedOn($shop_storage)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => ' Shop Storage  (Admin Panel)'])
            ->log('Shop Storage  is moved to trash ');

        return ResponseHelper::success();
    }

    public function restore(ShopStorage $shop_storage)
    {
        $shop_storage->restore();
        activity()
            ->performedOn($shop_storage)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Shop Storage (Admin Panel'])
            ->log('Shop Storage is restored from trash ');

        return ResponseHelper::success();
    }
}
