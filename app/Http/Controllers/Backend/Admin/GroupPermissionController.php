<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Traits\AuthorizePerson;
use App\Models\GroupPermission;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class GroupPermissionController extends Controller
{
    use AuthorizePerson;
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $grouppermissions = GroupPermission::anyTrash($request->trash)->get();
            return Datatables::of($grouppermissions)
                ->addColumn('action', function ($grouppermission) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.grouppermissions.edit', ['grouppermission' => $grouppermission->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    if ($request->trash == 1) {
                        $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $grouppermission->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                        $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $grouppermission->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                    } else {

                        $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $grouppermission->id . '"><i class="fas fa-trash fa-lg"></i></a>';

                    }
                    return "${detail_btn} ${edit_btn} ${restore_btn} ${trash_or_delete_btn}";
                })
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.admin.grouppermissions.index');
    }

    public function create()
    {

        return view(('backend.admin.grouppermissions.create'));
    }

    public function store(Request $request)
    {

        $grouppermission = new GroupPermission();
        $grouppermission->name = $request['name'];
        $grouppermission->save();
        activity()
            ->performedOn($grouppermission)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' =>  'Group Permission (Admin Panel)'])
            ->log(' New Group Permission is added');

        return redirect()->route('admin.grouppermissions.index')->with('success', 'Successfully Created');
    }

    public function show(GroupPermission $grouppermission)
    {
        return view('backend.admin.grouppermissions.show', compact('grouppermission'));
    }

    public function edit($id)
    {

        $grouppermission = GroupPermission::findOrFail($id);
        return view('backend.admin.grouppermissions.edit', compact('grouppermission'));
    }

    public function update(Request $request, $id)
    {

        $grouppermission = GroupPermission::findOrFail($id);
        $grouppermission->name = $request['name'];
        $grouppermission->update();

        activity()
            ->performedOn($grouppermission)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Group Permission (Admin Panel)'])
            ->log(' Group Permission is updated');

        return redirect()->route('admin.grouppermissions.index')->with('success', 'Successfully Updated');
    }

    public function destroy(GroupPermission $grouppermission)
    {

        $grouppermission->delete();
        activity()
            ->performedOn($grouppermission)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Group Permission (Admin Panel)'])
            ->log(' Group Permission is deleted');

        return ResponseHelper::success();
    }

    public function trash(GroupPermission $grouppermission)
    {

        $grouppermission->trash();
        activity()
            ->performedOn($grouppermission)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Group Permission (Admin Panel)'])
            ->log(' Group Permission is moved to trash');

        return ResponseHelper::success();
    }

    public function restore(GroupPermission $grouppermission)
    {

        $grouppermission->restore();
        activity()
            ->performedOn($grouppermission)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Group Permission (Admin Panel)'])
            ->log(' Group Permission is restored from trash');

        return ResponseHelper::success();
    }
}
