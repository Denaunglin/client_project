<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBedType;
use App\Http\Traits\AuthorizePerson;
use App\Models\BedType;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class BedTypeController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_bed_type')) {
            abort(404);
        }
        if ($request->ajax()) {

            $bedtypes = BedType::anyTrash($request->trash);
            return Datatables::of($bedtypes)
                ->addColumn('action', function ($bedtype) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = ' ';
                    $trash_or_delete_btn = ' ';

                    if ($this->getCurrentAuthUser('admin')->can('edit_bed_type')) {
                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.bedtypes.edit', ['bedtype' => $bedtype->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }

                    if ($this->getCurrentAuthUser('admin')->can('delete_bed_type')) {

                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $bedtype->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $bedtype->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $bedtype->id . '"><i class="fas fa-trash fa-lg"></i></a>';
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
        return view('backend.admin.bedtypes.index');
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_bed_type')) {
            abort(404);
        }
        return view(('backend.admin.bedtypes.create'));
    }

    public function store(StoreBedType $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_bed_type')) {
            abort(404);
        }
        $bedtype = new BedType();
        $bedtype->name = $request['name'];
        $bedtype->save();

        activity()
            ->performedOn($bedtype)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Bed Type (Admin Panel'])
            ->log(' New Bed Type (' . $bedtype->name . ') is created ');

        return redirect()->route('admin.bedtypes.index')->with('success', 'Successfully Created');
    }

    public function show(BedType $bedtype)
    {
        return view('backend.admin.bedtypes.show', compact('bedtype'));
    }

    public function edit($id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_bed_type')) {
            abort(404);
        }

        $bedtype = BedType::findOrFail($id);
        return view('backend.admin.bedtypes.edit', compact('bedtype'));
    }

    public function update(StoreBedType $request, $id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_bed_type')) {
            abort(404);
        }

        $bedtype = BedType::findOrFail($id);
        $bedtype->name = $request['name'];
        $bedtype->update();

        activity()
            ->performedOn($bedtype)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Bed Type (Admin Panel'])
            ->log('Bed Type (' . $bedtype->name . ') is updated');

        return redirect()->route('admin.bedtypes.index')->with('success', 'Successfully Updated');
    }

    public function destroy(BedType $bedtype)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_bed_type')) {
            abort(404);
        }

        $bedtype->delete();
        activity()
            ->performedOn($bedtype)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Bed Type (Admin Panel'])
            ->log(' Bed Type (' . $bedtype->name . ')  is deleted ');

        return ResponseHelper::success();
    }

    public function trash(BedType $bedtype)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_bed_type')) {
            abort(404);
        }

        $bedtype->trash();
        activity()
            ->performedOn($bedtype)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Bed Type (Admin Panel)'])
            ->log(' Bed Type (' . $bedtype->name . ')  is moved to trash ');

        return ResponseHelper::success();
    }

    public function restore(BedType $bedtype)
    {
        $bedtype->restore();
        activity()
            ->performedOn($bedtype)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Bed Type (Admin Panel'])
            ->log(' Bed Type (' . $bedtype->name . ')  is restored from trash ');

        return ResponseHelper::success();
    }
}
