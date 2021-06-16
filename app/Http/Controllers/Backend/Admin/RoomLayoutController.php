<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoomLayoutRequest;
use App\Http\Traits\AuthorizePerson;
use App\Models\RoomLayout;
use App\Models\Rooms;
use App\Models\RoomSchedule;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class RoomLayoutController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_room_layout')) {
            abort(404);
        }

        if ($request->ajax()) {
            $roomlayouts = RoomLayout::anyTrash($request->trash)->with('room')->orderBy('rank', 'asc');
            return Datatables::of($roomlayouts)
                ->addColumn('roomtype', function ($roomlayout) {
                    $output = '-';
                    if ($roomlayout->room) {
                        $output = $roomlayout->room->roomtype ? $roomlayout->room->roomtype->name : '-';
                    }
                    return $output;
                })
                ->filterColumn('roomtype', function ($query, $keyword) {
                    $query->whereHas('room', function ($q1) use ($keyword) {
                        $q1->whereHas('roomtype', function ($q2) use ($keyword) {
                            $q2->where('name', 'LIKE', "%{$keyword}%");
                        });
                    });
                })

                ->addColumn('bedtype', function ($roomlayout) {
                    $output = '-';
                    if ($roomlayout->room) {
                        $output = $roomlayout->room->bedtype ? $roomlayout->room->bedtype->name : '-';
                    }

                    return $output;
                })

                ->filterColumn('bedtype', function ($query, $keyword) {
                    $query->whereHas('room', function ($q1) use ($keyword) {
                        $q1->whereHas('bedtype', function ($q2) use ($keyword) {
                            $q2->where('name', 'LIKE', "%{$keyword}%");
                        });
                    });
                })
                ->addColumn('floor', function ($roomlayout) {
                    $floor = config('app.floor');
                    $output = $floor[$roomlayout->floor];

                    return $output;
                })
                ->addColumn('action', function ($roomlayout) use ($request) {
                    $detail_btn = '';
                    $restore_btn = '';
                    $edit_btn = '';
                    $trash_or_delete_btn = '';

                    if ($this->getCurrentAuthUser('admin')->can('edit_room_layout')) {
                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.roomlayouts.edit', ['roomlayout' => $roomlayout->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }

                    if ($this->getCurrentAuthUser('admin')->can('delete_room_layout')) {
                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $roomlayout->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $roomlayout->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $roomlayout->id . '"><i class="fas fa-trash fa-lg"></i></a>';
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
        return view('backend.admin.room_layout.index');
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_room_layout')) {
            abort(404);
        }
        $rank = config('app.rank');
        $floor = config('app.floor');
        $room = Rooms::where('trash', 0)->get();
        return view('backend.admin.room_layout.create', compact('room', 'floor', 'rank'));
    }

    public function store(RoomLayoutRequest $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_room_layout')) {
            abort(404);
        }
        $roomlayout = new RoomLayout();
        $roomlayout->room_id = $request['room_id'];
        $roomlayout->room_no = $request['room_no'];
        $roomlayout->floor = $request['floor'];
        $roomlayout->rank = $request['rank'];
        $roomlayout->save();

        activity()
            ->performedOn($roomlayout)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Room Layout (Admin Panel)'])
            ->log('New Room Layout is added');

        return redirect()->route('admin.roomlayouts.index')->with('success', 'Successfully Created');
    }

    public function show(RoomLayout $roomlayout)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_room_layout')) {
            abort(404);
        }

        return view('backend.admin.roomlayouts.show', compact('roomlayout'));
    }

    public function edit($id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_room_layout')) {
            abort(404);
        }

        $rank = config('app.rank');
        $floor = config('app.floor');
        $room = Rooms::all();
        $roomlayout = RoomLayout::findOrFail($id);
        return view('backend.admin.room_layout.edit', compact('rank', 'floor', 'roomlayout', 'room'));
    }

    public function update(Request $request, $id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_room_layout')) {
            abort(404);
        }
        $roomlayout = RoomLayout::findOrFail($id);
        $roomlayout->room_id = $request['room_id'];
        $roomlayout->room_no = $request['room_no'];
        $roomlayout->floor = $request['floor'];
        $roomlayout->rank = $request['rank'];
        $roomlayout->maintain = $request['maintain'];
        $roomlayout->update();

        activity()
            ->performedOn($roomlayout)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Room Layout (Admin Panel)'])
            ->log(' Room Layout is updated');

        return redirect()->route('admin.roomlayouts.index')->with('success', 'Successfully Updated');
    }

    public function destroy(RoomLayout $roomlayout)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_room_layout')) {
            abort(404);
        }
        $roomlayout->delete();
        activity()
            ->performedOn($roomlayout)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Room Layout (Admin Panel)'])
            ->log(' Room Layout is deleted');

        return ResponseHelper::success();
    }

    public function trash(RoomLayout $roomlayout)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_room_layout')) {
            abort(404);
        }
        $roomlayout->trash();
        activity()
            ->performedOn($roomlayout)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Room Layout (Admin Panel)'])
            ->log(' Room Layout is moved to trash');

        return ResponseHelper::success();
    }

    public function restore(RoomLayout $roomlayout)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_room_layout')) {
            abort(404);
        }
        $roomlayout->restore();
        activity()
            ->performedOn($roomlayout)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Room Layout (Admin Panel)'])
            ->log(' Room Layout is restored from trash');

        return ResponseHelper::success();
    }

    public function roomPlan(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_room_plan')) {
            abort(404);
        }

        if ($request->date) {
            $date = $request->date;
        } else {
            $date = date('Y-m-d');
        }

        $time = Carbon::now()->format('H:i:s A');

        $check_room = RoomSchedule::where('check_in', $date)->whereIn('status', [1, 2, 3])->orwhere('check_out', '>', $date)->where('check_in', '<', $date)->where('trash', 0)->whereIn('status', [1, 2, 3])->orwhere('check_out', '=', $date)->whereIn('status', [1, 2, 3])->get();
        $ground_floor_rooms = RoomLayout::orderBy('rank', 'asc')->where('floor', 1)->where('trash', 0)->get();
        $ground_floor_rooms = collect($ground_floor_rooms)->chunk(14);

        $first_floor_rooms = RoomLayout::orderBy('rank')->where('floor', 2)->where('trash', 0)->get();
        $first_floor_rooms = collect($first_floor_rooms)->chunk(14);

        return view('backend.admin.room_plan.index', compact('time', 'ground_floor_rooms', 'first_floor_rooms', 'check_room'));
    }

    public function planSearch(Request $request)
    {
        $roomtype = RoomType::where('trash', '0')->get();
        $date = $request->date;
        $time = Carbon::now()->format('H:i:s A');
        $check = RoomSchedule::where('check_in', $date)->whereIn('status', [1, 2, 3])->orwhere('check_out', '>', $date)->where('check_in', '<', $date)->where('trash', 0)->whereIn('status', [1, 2, 3])->orwhere('check_out', '=', $date)->whereIn('status', [1, 2, 3])->get();
        if ($check) {
            $check_room = $check;
        }

        $ground_floor_rooms = RoomLayout::orderBy('rank', 'asc')->where('floor', 1)->where('trash', 0)->get();
        $ground_floor_rooms = collect($ground_floor_rooms)->chunk(14);

        $first_floor_rooms = RoomLayout::orderBy('rank')->where('floor', 2)->where('trash', 0)->get();
        $first_floor_rooms = collect($first_floor_rooms)->chunk(14);

        return view('backend.admin.room_plan.index', compact('time', 'roomtype', 'ground_floor_rooms', 'first_floor_rooms', 'check_room'));

    }

}
