<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\AvailableRoomQtyResource;
use App\Http\Resources\BookingCalendarResource;
use App\Http\Traits\AuthorizePerson;
use App\Models\BookingCalendar;
use App\Models\Rooms;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Response;

class BookingCalendarController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_booking_calendar')) {
            abort(404);
        }

        $pay_method = config('app.pay_method');
        $rooms = Rooms::where('trash', 0)->get();

        if (request()->ajax()) {

            $start = (!empty($_GET["start"])) ? ($_GET["start"]) : ('');
            $end = (!empty($_GET["end"])) ? ($_GET["end"]) : ('');
            $data = BookingCalendar::with('room', 'booking')->whereDate('check_in', '>=', $start)->whereDate('check_out', '<=', $end)->get();

            if ($request->roomtype) {
                $data = collect($data)->where('room.room_type', $request->roomtype);
            }

            $data = BookingCalendarResource::collection($data);

            return Response::json($data);
        }
        return view('backend.admin.booking_calender.index', compact('rooms', 'pay_method'));
    }

    public function available_room_qty(Request $request)
    {
        if (request()->ajax()) {
            $rooms = Rooms::where('id', $request->roomtype)->where('trash', 0)->get();
            $data = AvailableRoomQtyResource::collection($rooms);
            return ResponseHelper::success($data);
        }
    }
}
