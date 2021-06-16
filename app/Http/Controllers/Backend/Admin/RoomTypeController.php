<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoomType;
use App\Http\Traits\AuthorizePerson;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class RoomTypeController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_room_type')) {
            abort(404);
        }

        if ($request->ajax()) {

            $data = RoomType::anyTrash($request->trash);

            return Datatables::of($data)
                ->addColumn('action', function ($row) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = '';
                    $trash_or_delete_btn = '';

                    if ($this->getCurrentAuthUser('admin')->can('edit_room_type')) {
                        $edit_btn = '<a class="edit text text-primary mr-3" href="' . route('admin.roomtypes.edit', ['roomtype' => $row->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }

                    if ($this->getCurrentAuthUser('admin')->can('delete_room_type')) {
                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-3" href="#" data-id="' . $row->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-3" href="#" data-id="' . $row->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-3" href="#" data-id="' . $row->id . '"><i class="fas fa-trash fa-lg"></i></a>';
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
        return view('backend.admin.roomtypes.index');
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_room_type')) {
            abort(404);
        }
        $roomtype = RoomType::where('trash', '0')->get();
        return view('backend.admin.roomtypes.create', compact('roomtype'));
    }

    public function store(StoreRoomType $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_room_type')) {
            abort(404);
        }

        $roomtype = new RoomType();
        $roomtype->name = $request['name'];
        $roomtype->join_roomtype = $request['join_roomtype'];
        $roomtype->save();

        activity()
            ->performedOn($roomtype)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Room Type (Admin Panel)'])
            ->log('New Room Type is created');

        return redirect()->route('admin.roomtypes.index')->with('success', 'Successfully Created');
    }

    public function edit($id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_room_type')) {
            abort(404);
        }
        $join_roomtype = RoomType::where('trash', '0')->get();
        $roomtype = RoomType::where('id', $id)->firstOrFail();
        return view('backend.admin.roomtypes.edit', compact('roomtype', 'join_roomtype'));
    }

    public function update(StoreRoomType $request, $id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_room_type')) {
            abort(404);
        }

        $roomtype = RoomType::findOrFail($id);
        $roomtype->name = $request['name'];
        $roomtype->join_roomtype = $request['join_roomtype'];
        $roomtype->update();

        activity()
            ->performedOn($roomtype)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Room Type (Admin Panel)'])
            ->log(' Room Type is updated');

        return redirect()->route('admin.roomtypes.index')->with('success', 'Successfully Updated');
    }

    public function destroy(RoomType $roomtype)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_room_type')) {
            abort(404);
        }

        $roomtype->delete();
        activity()
            ->performedOn($roomtype)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Room Type (Admin Panel)'])
            ->log(' Room Type is deleted');

        return ResponseHelper::success();
    }

    public function trash(RoomType $roomtype)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_room_type')) {
            abort(404);
        }

        $roomtype->trash();
        activity()
            ->performedOn($roomtype)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Room Type (Admin Panel)'])
            ->log(' Room Type is moved to trash');

        return ResponseHelper::success();
    }

    public function restore(RoomType $roomtype)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_room_type')) {
            abort(404);
        }

        $roomtype->restore();
        activity()
            ->performedOn($roomtype)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Room Type (Admin Panel)'])
            ->log(' Room Type is restored from trash');

        return ResponseHelper::success();
    }

}
