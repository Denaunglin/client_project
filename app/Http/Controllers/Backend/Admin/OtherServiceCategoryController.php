<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\OtherServiceCategory;
use App\Http\Traits\AuthorizePerson;
use App\Models\OtherServicesCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class OtherServiceCategoryController extends Controller
{
    use AuthorizePerson;
    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_other_service_category')) {
            abort(404);
        }

        if ($request->ajax()) {
            $otherservicescategory = OtherServicesCategory::anyTrash($request->trash)->orderBy('id', 'desc');
            return Datatables::of($otherservicescategory)
                ->addColumn('action', function ($otherservicecategory) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = '';
                    $trash_or_delete_btn = '';

                    if ($this->getCurrentAuthUser('admin')->can('edit_other_service_category')) {
                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.otherservicescategory.edit', ['otherservicescategory' => $otherservicecategory->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }
                    if ($this->getCurrentAuthUser('admin')->can('delete_other_service_category')) {
                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $otherservicecategory->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $otherservicecategory->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {

                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $otherservicecategory->id . '"><i class="fas fa-trash fa-lg"></i></a>';
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
        return view('backend.admin.otherservicescategory.index');
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_other_service_category')) {
            abort(404);
        }
        return view(('backend.admin.otherservicescategory.create'));
    }

    public function store(OtherServiceCategory $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_other_service_category')) {
            abort(404);
        }
        $otherservicescategory = new OtherServicesCategory();
        $otherservicescategory->name = $request['name'];
        $otherservicescategory->save();

        activity()
            ->performedOn($otherservicescategory)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Other Service Category (Admin Panel)'])
            ->log(' Other Service New Category is created');

        return redirect()->route('admin.otherservicescategory.index')->with('success', 'Successfully Created');
    }

    public function show(OtherServicesCategory $otherservicecategory)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_other_service_category')) {
            abort(404);
        }

        return view('backend.admin.otherservicescategory.show', compact('otherservicecategory'));
    }

    public function edit($id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_other_service_category')) {
            abort(404);
        }

        $otherservicecategory = OtherServicesCategory::findOrFail($id);
        return view('backend.admin.otherservicescategory.edit', compact('otherservicecategory'));
    }

    public function update(OtherServiceCategory $request, $id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_other_service_category')) {
            abort(404);
        }

        $otherservicescategory = OtherServicesCategory::findOrFail($id);
        $otherservicescategory->name = $request['name'];
        $otherservicescategory->update();

        activity()
            ->performedOn($otherservicescategory)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Other Service Category (Admin Panel)'])
            ->log(' Other Service Category is updated');

        return redirect()->route('admin.otherservicescategory.index')->with('success', 'Successfully Updated');
    }

    public function destroy(OtherServicesCategory $otherservicecategory)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_other_service_category')) {
            abort(404);
        }

        $otherservicecategory->delete();

        activity()
            ->performedOn($otherservicescategory)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Other Service Category (Admin Panel)'])
            ->log(' Other Service Category is deleted');

        return ResponseHelper::success();
    }

    public function trash(OtherServicesCategory $otherservicecategory)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_other_service_category')) {
            abort(404);
        }

        $otherservicecategory->trash();
        activity()
            ->performedOn($otherservicescategory)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Other Service Category (Admin Panel)'])
            ->log(' Other Service Category is moved to trash');

        return ResponseHelper::success();
    }

    public function restore(OtherServicesCategory $otherservicecategory)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_other_service_category')) {
            abort(404);
        }

        $otherservicecategory->restore();
        activity()
            ->performedOn($otherservicescategory)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Other Service Category (Admin Panel)'])
            ->log(' Other Service Category is restored from trash');

        return ResponseHelper::success();
    }
}
