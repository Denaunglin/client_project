<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\OtherServiceItem;
use App\Http\Traits\AuthorizePerson;
use App\Models\OtherServicesCategory;
use App\Models\OtherServicesItem;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class OtherServiceItemController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_other_service_item')) {
            abort(404);
        }
        if ($request->ajax()) {
            $otherservicesitem = OtherServicesItem::anyTrash($request->trash)->with('otherservicescategory')->orderBy('id', 'desc');
            return Datatables::of($otherservicesitem)
                ->addColumn('action', function ($otherserviceitem) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = '';
                    $trash_or_delete_btn = '';

                    if ($this->getCurrentAuthUser('admin')->can('edit_other_service_item')) {
                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.otherservicesitem.edit', ['otherservicesitem' => $otherserviceitem->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }
                    if ($this->getCurrentAuthUser('admin')->can('delete_other_service_item')) {
                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $otherserviceitem->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $otherserviceitem->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $otherserviceitem->id . '"><i class="fas fa-trash fa-lg"></i></a>';
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
        return view('backend.admin.otherservicesitem.index');
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_other_service_item')) {
            abort(404);
        }
        $other_services_category = OtherServicesCategory::where('trash', '0')->get();
        return view('backend.admin.otherservicesitem.create', compact('other_services_category'));
    }

    public function store(OtherServiceItem $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_other_service_item')) {
            abort(404);
        }
        $charges_mm = $request->charges_mm;
        $charges_foreign = $request->charges_foreign;
        $otherservicesitem = new OtherServicesItem();
        $otherservicesitem->other_services_category_id = $request->other_services_category_id;
        $otherservicesitem->name = $request['name'];
        $otherservicesitem->charges_mm = number_format($charges_mm, 2, '.', '');
        $otherservicesitem->charges_foreign = number_format($charges_foreign, 2, '.', '');

        $otherservicesitem->save();

        activity()
            ->performedOn($otherservicesitem)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Other Service Item (Admin Panel)'])
            ->log(' Other Service New Item is created');

        return redirect()->route('admin.otherservicesitem.index')->with('success', 'Successfully Created');
    }

    public function show(OtherServicesItem $otherserviceitem)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_other_service_item')) {
            abort(404);
        }

        return view('backend.admin.otherservicesitem.show', compact('otherserviceitem'));
    }

    public function edit($id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_other_service_item')) {
            abort(404);
        }

        $otherserviceitem = OtherServicesItem::findOrFail($id);
        $other_services_category = OtherServicesCategory::where('trash', '0')->get();
        return view('backend.admin.otherservicesitem.edit', compact('otherserviceitem', 'other_services_category'));
    }

    public function update(OtherServiceItem $request, $id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_other_service_item')) {
            abort(404);
        }
        $charges_mm = $request->charges_mm;
        $charges_foreign = $request->charges_foreign;

        $otherservicesitem = OtherServicesItem::findOrFail($id);
        $otherservicesitem->other_services_category_id = $request->other_services_category_id;
        $otherservicesitem->name = $request['name'];
        $otherservicesitem->charges_mm = number_format($charges_mm, 2, '.', '');
        $otherservicesitem->charges_foreign = number_format($charges_foreign, 2, '.', '');
        $otherservicesitem->update();

        activity()
            ->performedOn($otherservicesitem)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Other Service Item (Admin Panel)'])
            ->log(' Other Service  Item is updated');

        return redirect()->route('admin.otherservicesitem.index')->with('success', 'Successfully Updated');
    }

    public function destroy(OtherServicesItem $otherserviceitem)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_other_service_item')) {
            abort(404);
        }
        $otherserviceitem->delete();

        activity()
            ->performedOn($otherserviceitem)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Other Service Item (Admin Panel)'])
            ->log(' Other Service Item is deleted');

        return ResponseHelper::success();
    }

    public function trash(OtherServicesItem $otherserviceitem)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_other_service_item')) {
            abort(404);
        }
        $otherserviceitem->trash();

        activity()
            ->performedOn($otherserviceitem)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Other Service Item (Admin Panel)'])
            ->log(' Other Service Item is moved to trash');

        return ResponseHelper::success();
    }

    public function restore(OtherServicesItem $otherserviceitem)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_other_service_item')) {
            abort(404);
        }
        $otherserviceitem->restore();
        activity()
            ->performedOn($otherserviceitem)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Other Service Item (Admin Panel)'])
            ->log(' Other Service Item is restored from trash');

        return ResponseHelper::success();
    }
}
