<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ItemSubCategoryRequest;
use App\Http\Traits\AuthorizePerson;
use App\Models\ItemSubCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ItemSubCategoryController extends Controller
{
    use AuthorizePerson;
    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_item_sub_category')) {
            abort(404);
        }
        if ($request->ajax()) {

            $item_sub_categories = ItemSubCategory::anyTrash($request->trash);
            return Datatables::of($item_sub_categories)
                ->addColumn('action', function ($item_sub_category) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = ' ';
                    $trash_or_delete_btn = ' ';

                    if ($this->getCurrentAuthUser('admin')->can('edit_item_sub_category')) {
                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.item_sub_categories.edit', ['item_sub_category' => $item_sub_category->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }

                    if ($this->getCurrentAuthUser('admin')->can('delete_item_sub_category')) {

                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $item_sub_category->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $item_sub_category->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $item_sub_category->id . '"><i class="fas fa-trash fa-lg"></i></a>';
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
        return view('backend.admin.item_sub_categories.index');
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_item_sub_category')) {
            abort(404);
        }
        return view(('backend.admin.item_sub_categories.create'));
    }

    public function store(ItemSubCategoryRequest $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_item_sub_category')) {
            abort(404);
        }
        $item_sub_category = new ItemSubCategory();
        $item_sub_category->name = $request['name'];
        $item_sub_category->save();

        activity()
            ->performedOn($item_sub_category)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'ItemSubCategory Type (Admin Panel'])
            ->log(' New Item Category (' . $item_sub_category->name . ') is created ');

        return redirect()->route('admin.item_sub_categories.index')->with('success', 'Successfully Created');
    }

    public function show(ItemSubCategory $item_sub_category)
    {
        return view('backend.admin.item_sub_categories.show', compact('item_sub_category'));
    }

    public function edit($id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_item_sub_category')) {
            abort(404);
        }

        $item_sub_category = ItemSubCategory::findOrFail($id);
        return view('backend.admin.item_sub_categories.edit', compact('item_sub_category'));
    }

    public function update(ItemSubCategoryRequest $request, $id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_item_sub_category')) {
            abort(404);
        }

        $item_sub_category = ItemSubCategory::findOrFail($id);
        $item_sub_category->name = $request['name'];
        $item_sub_category->update();

        activity()
            ->performedOn($item_sub_category)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item Category (Admin Panel'])
            ->log('Item Category (' . $item_sub_category->name . ') is updated');

        return redirect()->route('admin.item_sub_categories.index')->with('success', 'Successfully Updated');
    }

    public function destroy(ItemSubCategory $item_sub_category)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_item_sub_category')) {
            abort(404);
        }

        $item_sub_category->delete();
        activity()
            ->performedOn($item_sub_category)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item Category (Admin Panel'])
            ->log(' Item Category (' . $item_sub_category->name . ')  is deleted ');

        return ResponseHelper::success();
    }

    public function trash(ItemSubCategory $item_sub_category)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_item_sub_category')) {
            abort(404);
        }

        $item_sub_category->trash();
        activity()
            ->performedOn($item_sub_category)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item Category (Admin Panel)'])
            ->log(' Bed Type (' . $item_sub_category->name . ')  is moved to trash ');

        return ResponseHelper::success();
    }

    public function restore(ItemSubCategory $item_sub_category)
    {
        $item_sub_category->restore();
        activity()
            ->performedOn($item_sub_category)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Item Category (Admin Panel'])
            ->log(' Item Category (' . $item_sub_category->name . ')  is restored from trash ');

        return ResponseHelper::success();
    }
}
