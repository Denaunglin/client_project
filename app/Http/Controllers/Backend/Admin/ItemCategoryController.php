<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ItemCategoryRequest;
use App\Http\Traits\AuthorizePerson;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ItemCategoryController extends Controller
{
    use AuthorizePerson;
    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_item_category')) {
            abort(404);
        }
        if ($request->ajax()) {

            $item_categories = ItemCategory::anyTrash($request->trash);
            return Datatables::of($item_categories)
                ->addColumn('action', function ($item_category) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = ' ';
                    $trash_or_delete_btn = ' ';

                    if ($this->getCurrentAuthUser('admin')->can('edit_item_category')) {
                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.item_categories.edit', ['item_category' => $item_category->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }

                    if ($this->getCurrentAuthUser('admin')->can('delete_item_category')) {

                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $item_category->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $item_category->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $item_category->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                        }

                    }

                    return "${detail_btn} ${edit_btn} ${restore_btn} ${trash_or_delete_btn}";
                })

                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.admin.item_categories.index');
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_item_category')) {
            abort(404);
        }
        return view(('backend.admin.item_categories.create'));
    }

    public function store(ItemCategoryRequest $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_item_category')) {
            abort(404);
        }
        $item_category = new ItemCategory();
        $item_category->name = $request['name'];
        $item_category->save();

        activity()
            ->performedOn($item_category)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'ItemCategory Type (Admin Panel'])
            ->log(' New Item Category (' . $item_category->name . ') is created ');

        return redirect()->route('admin.item_categories.index')->with('success', 'Successfully Created');
    }

    public function show(ItemCategory $item_category)
    {
        return view('backend.admin.item_categories.show', compact('item_category'));
    }

    public function edit($id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_item_category')) {
            abort(404);
        }

        $item_category = ItemCategory::findOrFail($id);
        return view('backend.admin.item_categories.edit', compact('item_category'));
    }

    public function update(ItemCategoryRequest $request, $id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_item_category')) {
            abort(404);
        }

        $item_category = ItemCategory::findOrFail($id);
        $item_category->name = $request['name'];
        $item_category->update();

        activity()
            ->performedOn($item_category)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item Category (Admin Panel'])
            ->log('Item Category (' . $item_category->name . ') is updated');

        return redirect()->route('admin.item_categories.index')->with('success', 'Successfully Updated');
    }

    public function destroy(ItemCategory $item_category)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_item_category')) {
            abort(404);
        }

        $item_category->delete();
        activity()
            ->performedOn($item_category)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item Category (Admin Panel'])
            ->log(' Item Category (' . $item_category->name . ')  is deleted ');

        return ResponseHelper::success();
    }

    public function trash(ItemCategory $item_category)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_item_category')) {
            abort(404);
        }

        $item_category->trash();
        activity()
            ->performedOn($item_category)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item Category (Admin Panel)'])
            ->log(' Item Category  (' . $item_category->name . ')  is moved to trash ');

        return ResponseHelper::success();
    }

    public function restore(ItemCategory $item_category)
    {
        $item_category->restore();
        activity()
            ->performedOn($item_category)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item Category (Admin Panel'])
            ->log(' Item Category (' . $item_category->name . ')  is restored from trash ');

        return ResponseHelper::success();
    }
}
