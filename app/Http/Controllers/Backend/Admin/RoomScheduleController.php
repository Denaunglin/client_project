<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoomScheduleRequest;
use App\Http\Requests\RoomScheduleUpdateRequest;
use App\Http\Traits\AuthorizePerson;
use App\Models\AccountType;
use App\Models\Booking;
use App\Models\BookingCalendar;
use App\Models\CardType;
use App\Models\Discounts;
use App\Models\EarlyLateCheck;
use App\Models\ExtraBedPrice;
use App\Models\OtherServicesItem;
use App\Models\RoomLayout;
use App\Models\Rooms;
use App\Models\RoomSchedule;
use App\Models\Tax;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class RoomScheduleController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_room_schedule')) {
            abort(404);
        }

        if ($request->ajax()) {
            $daterange = $request->daterange ? explode(',', $request->daterange) : null;
            $roomschedules = RoomSchedule::anyTrash($request->trash)->orderBy('id', 'desc')->with('room', 'booking', 'roomlayout');

            if ($daterange) {
                $roomschedules = $roomschedules->whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
            }
            return Datatables::of($roomschedules)
                ->addColumn('room', function ($roomschedule) {
                    $output = '';
                    if ($roomschedule->room) {
                        $roomtype = $roomschedule->room->roomtype ? $roomschedule->room->roomtype->name : '-';
                        $output .= '<p>Room Type : ' . $roomtype . '</p>';
                    }
                    if ($roomschedule->room) {
                        $bedtype = $roomschedule->room->bedtype ? $roomschedule->room->bedtype->name : '-';
                        $output .= '<p>Bed Type : ' . $bedtype . '</p>';
                    }
                    return $output;
                })
                ->filterColumn('room', function ($query, $keyword) {
                    $query->whereHas('room', function ($q1) use ($keyword) {
                        $q1->whereHas('roomtype', function ($q2) use ($keyword) {
                            $q2->where('name', 'LIKE', "%{$keyword}%");
                        });
                    });
                })
                ->filterColumn('roomlayout', function ($query, $keyword) {
                    $query->whereHas('roomlayout', function ($q1) use ($keyword) {
                        $q1->where('room_no', 'LIKE', "%{$keyword}%");

                    });
                })
                ->filterColumn('booking_number', function ($query, $keyword) {
                    $query->whereHas('booking', function ($q1) use ($keyword) {
                        $q1->where('booking_number', 'LIKE', "%{$keyword}%");

                    });
                })
                ->addColumn('action', function ($roomschedule) use ($request) {

                    $restore_btn = '';
                    $detail_btn = '';
                    $edit_btn = '';
                    $trash_or_delete_btn = '';

                    if ($this->getCurrentAuthUser('admin')->can('view_room_schedule_detail')) {
                        $detail_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.roomschedules.detail', ['roomschedule' => $roomschedule->id]) . '"><i class="fas fa-info-circle fa-lg"></i></a>';
                    }

                    if ($this->getCurrentAuthUser('admin')->can('edit_room_schedule')) {
                        $edit_btn = '<a class="edit text text-primary mr-2" href="' . route('admin.roomschedules.edit', ['roomschedule' => $roomschedule->id]) . '"><i class="far fa-edit fa-lg"></i></a>';
                    }

                    if ($this->getCurrentAuthUser('admin')->can('delete_room_schedule')) {
                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-warning mr-2" href="#" data-id="' . $roomschedule->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger mr-2" href="#" data-id="' . $roomschedule->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger mr-2" href="#" data-id="' . $roomschedule->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                        }
                    }

                    return " ${detail_btn} ${edit_btn} ${restore_btn} ${trash_or_delete_btn}";
                })
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->rawColumns(['room', 'action'])
                ->make(true);
        }

        return view('backend.admin.room_schedule.index');
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_room_schedule')) {
            abort(404);
        }
        $cardtype = CardType::where('trash', '0')->get();
        $roomlayout = RoomLayout::where('trash', '0');
        $room = Rooms::where('trash', '0');
        return view('backend.admin.room_schedule.create', compact('room', 'roomlayout', 'cardtype'));
    }

    public function store(RoomScheduleRequest $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_room_schedule')) {
            abort(404);
        }

        $checkin_checkout = explode(' - ', $request->checkin_checkout);
        $check_in_date = $checkin_checkout[0];
        $check_out_date = $checkin_checkout[1];

        $availiable_room1 = RoomSchedule::where('room_no', $request->room_no)
            ->where('check_in', '>=', $check_in_date)
            ->where('check_in', '<', $check_out_date)
            ->where('status', 1)
            ->where('trash', 0)
            ->first();

        $availiable_room2 = RoomSchedule::where('room_no', $request->room_no)
            ->where('check_in', '<', $check_in_date)
            ->where('check_out', '>', $check_in_date)
            ->where('status', 1)
            ->where('trash', 0)
            ->first();

        if ($availiable_room1 == null && $availiable_room2 == null) {
            $commercial_tax_obj = Tax::where('name', 'commercial')->first();
            $service_charges_obj = Tax::where('name', 'service')->first();
            $tax1 = 0;
            $tax2 = 0;

            if ($commercial_tax_obj) {
                $tax1 = $commercial_tax_obj->amount ? $commercial_tax_obj->amount / 100 : 0;
            }
            if ($service_charges_obj) {
                $tax2 = $service_charges_obj->amount ? $service_charges_obj->amount / 100 : 0;
            }

            $room = Rooms::where('id', $request->room_id)->first();
            $earlylatecheck = 0;
            $membertype = 0;
            if ($request->client_user) {
                $client_user = User::where('id', $request->client_user)->first();
                $accounttype = $client_user->accounttype->id;
                $membertype = $client_user->accounttype->name;
                $extrabedprice = Extrabedprice::where('trash', '0')->where('user_account_id', $accounttype)->first();
                $discount_type = Discounts::where('trash', '0')->where('user_account_id', $accounttype)->where('room_type_id', $room->id)->first();
                $earlylatecheck = EarlyLateCheck::where('trash', '0')->where('user_account_id', $accounttype)->first();
                $roomschedulediscounts = ResponseHelper::roomschedulediscount($room, $request->nationality, $client_user, $discount_type);
                $roomschedulediscount = $roomschedulediscounts['0'];
                $addon = $roomschedulediscounts['1'];
            } else {
                $roomschedulediscount = $room->price;
                $addon = $room->price;
                $client_user = null;
            }
            $checkin_checkout = explode(' - ', $request->checkin_checkout);
            $nights = Carbon::parse($checkin_checkout[1])->diffInDays(Carbon::parse($checkin_checkout[0]));

            if ($request->nationality == 1) {
                $room_total = ($request->room_qty * $roomschedulediscount) * $nights;
                $discount = $roomschedulediscount;
                $price = $addon;
                $extra_bed_total = ($request->extra_bed_qty * $room->extra_bed_mm_price) * $nights;

                if ($client_user) {
                    if ($extrabedprice) {
                        $extra_bed_total = ($request->extra_bed_qty * (($room->extra_bed_mm_price + $extrabedprice->add_extrabed_price_mm) - $extrabedprice->subtract_extrabed_price_mm)) * $nights;
                    }
                }

                $subtotal = $room_total + $extra_bed_total;
                $early_check_in = 0;
                $late_check_out = 0;
                $both_check = 0;

                if ($request->early_late) {

                    if ($request->early_late == array(1, 2)) {
                        $both_check = $room->early_checkin_mm + $room->late_checkout_mm;
                        if ($earlylatecheck) {
                            $both_check = ($room->early_checkin_mm + $room->late_checkout_mm + $earlylatecheck->add_early_checkin_mm + $earlylatecheck->add_late_checkout_mm) - ($earlylatecheck->subtract_early_checkin_mm + $earlylatecheck->subtract_late_checkout_mm);
                        }

                    } elseif ($request->early_late['0'] == 1) {
                        $early_check_in = $room->early_checkin_mm;
                        if ($earlylatecheck) {
                            $early_check_in = ($room->early_checkin_mm + $earlylatecheck->add_early_checkin_mm) - $earlylatecheck->subtract_early_checkin_mm;
                        }

                    } elseif ($request->early_late['1'] == 2) {
                        $late_check_out = $room->late_checkout_mm;
                        if ($earlylatecheck) {
                            $late_check_out = ($room->late_checkout_mm + $earlylatecheck->add_late_checkout_mm) - $earlylatecheck->subtract_late_checkout_mm;
                        }
                    }
                }

            } else {

                $room_total = ($request->room_qty * $roomschedulediscount) * $nights;
                $discount = $roomschedulediscount;
                $price = $addon;
                $extra_bed_total = ($request->extra_bed_qty * $room->extra_bed_foreign_price) * $nights;

                if ($client_user) {
                    if ($extrabedprice) {
                        $extra_bed_total = ($request->extra_bed_qty * (($room->extra_bed_foreign_price + $extrabedprice->add_extrabed_price_foreign) - $extrabedprice->subtract_extrabed_price_foreign)) * $nights;
                    }
                }

                $subtotal = $room_total + $extra_bed_total;
                $early_check_in = 0;
                $late_check_out = 0;
                $both_check = 0;

                if ($request->early_late) {
                    if ($request->early_late == array(1, 2)) {
                        $both_check = $room->early_checkin_foreign + $room->late_checkout_foreign;
                        if ($earlylatecheck) {
                            $both_check = ($room->early_checkin_foreign + $room->late_checkout_foreign + $earlylatecheck->add_early_checkin_foreign + $earlylatecheck->add_late_checkout_foreign) - ($earlylatecheck->subtract_early_checkin_foreign + $earlylatecheck->subtract_late_checkout_foreign);
                        }

                    } elseif ($request->early_late['0'] == 1) {
                        $early_check_in = $room->early_checkin_foreign;
                        if ($earlylatecheck) {
                            $early_check_in = ($room->early_checkin_foreign + $earlylatecheck->add_early_checkin_foreign) - $earlylatecheck->subtract_early_checkin_foreign;
                        }

                    } elseif ($request->early_late['0'] == 2) {
                        $late_check_out = $room->late_checkout_foreign;
                        if ($earlylatecheck) {
                            $late_check_out = ($room->late_checkout_foreign + $earlylatecheck->add_late_checkout_foreign) - $earlylatecheck->subtract_late_checkout_foreign;
                        }
                    }
                }
            }

            $service_tax = $subtotal * $tax2;
            $total = $subtotal + $service_tax + $early_check_in + $late_check_out;
            $commercial_tax = $total * $tax1;
            $grand_total = $total + $commercial_tax;
            $commission = 0;
            $commission_percentage = 0;
            $deposite = $request->deposite ?? 0;

            if ($client_user) {
                $account_type = $client_user->accounttype->id;
                $account = AccountType::where('id', $account_type)->first();
                $commission_percentage = $account->commission;
                $commission = (($total + $commercial_tax) - ($early_check_in + $late_check_out + $both_check)) * ($account->commission / 100);
            }

            $booking = new Booking();
            $booking->room_id = $room->id;

            if ($request->client_user) {
                $booking->client_user = $client_user->id;
                $booking->name = $client_user->name;
                $booking->email = $client_user->email;
                $booking->phone = $client_user->phone;
                $booking->nrc_passport = $client_user->nrc_passport;
            } else {
                $booking->name = $request->name;
                $booking->email = $request->email;
                $booking->phone = $request->phone;
                $booking->nrc_passport = $request->nrc_passport;
            }
            $booking->message = $request->message;
            $booking->price = number_format($price, 2, '.', '');
            $booking->discount_price = number_format($discount, 2, '.', '');
            $booking->nationality = $request->nationality;
            $booking->total = number_format($total, 2, '.', '');
            $booking->status = '1';
            $booking->payment_status = '0';
            $booking->commercial_tax = number_format($commercial_tax, 2, '.', '');
            $booking->service_tax = number_format($service_tax, 2, '.', '');
            $booking->grand_total = number_format($grand_total, 2, '.', '');
            $booking->room_qty = $request->room_qty;
            $booking->member_type = $membertype;
            $booking->commission_percentage = number_format($commission_percentage, 2, '.', '');
            $booking->commission = number_format($commission, 2, '.', '');
            $booking->deposite = number_format($deposite, 2, '.', '');
            $booking->extra_bed_qty = $request->extra_bed_qty;

            if ($request->early_late) {
                $booking->early_late = serialize($request->early_late);
            } else {
                $booking->early_late = null;
            }

            $booking->early_check_in = number_format($early_check_in, 2, '.', '');
            $booking->late_check_out = number_format($late_check_out, 2, '.', '');
            $booking->both_check = number_format($both_check, 2, '.', '');

            if ($request->early_checkin_time) {
                $booking->early_checkin_time = $request->early_checkin_time;
            }

            if ($request->late_checkout_time) {
                $booking->late_checkout_time = $request->late_checkout_time;
            }

            $booking->extra_bed_total = number_format($extra_bed_total, 2, '.', '');
            $booking->guest = $request->guest;
            $booking->pay_method = $request->pay_method;

            if ($booking->pay_method == 1) {
                $booking->credit_type = $request->credit_type;
                $booking->credit_no = $request->credit_no;
                $booking->expire_month = $request->expire_month;
                $booking->expire_year = $request->expire_year;
            }

            $booking->check_in = $checkin_checkout[0];
            $booking->check_out = $checkin_checkout[1];
            $booking->save();

            $booking_number = config('app.booking_prefix') . '_' . $booking->id;
            $booking->booking_number = $booking_number;
            $booking->update();

            $roomschedule = new RoomSchedule();
            $roomschedule->room_no = $request['room_no'];
            $roomschedule->booking_id = $booking->id;
            $roomschedule->room_id = $request['room_id'];
            $roomschedule->guest = $request['guest'];
            $roomschedule->room_qty = $request['room_qty'];
            $roomschedule->extra_bed_qty = $request['extra_bed_qty'];
            $roomschedule->nationality = $request['nationality'];
            $roomschedule->client_user = $request['client_user'];
            $roomschedule->check_in = $checkin_checkout[0];
            $roomschedule->check_out = $checkin_checkout[1];
            $roomschedule->status = 1;
            $roomschedule->save();

            $room = Rooms::findOrFail($booking->room_id);

            $calendar = BookingCalendar::where('booking_id', $booking->id)->first();
            if ($calendar) {
                $calendar->booking_id = $booking->id;
                $calendar->room_id = $booking->room_id;
                $calendar->room_qty = $booking->room_qty;
                $calendar->check_in = $booking->check_in;
                $calendar->check_out = $booking->check_out;
                $calendar->update();
            } else {
                $calendar = new BookingCalendar();
                $calendar->booking_id = $booking->id;
                $calendar->room_id = $booking->room_id;
                $calendar->room_qty = $booking->room_qty;
                $calendar->check_in = $booking->check_in;
                $calendar->check_out = $booking->check_out;
                $calendar->save();
            }

            activity()
                ->performedOn($roomschedule)
                ->causedBy(auth()->guard('admin')->user())
                ->withProperties(['source' => 'Room Schedule (Admin Panel)'])
                ->log('New RoomSchedule is added');

            return redirect('/admin/roomplan?date=' . $roomschedule->check_in)->with('success', 'Successfully Created');

        } else {
            return back()->withErrors(['fail' => 'This Room is not availiable now ! Please Check the date.'])->withInput();
        }
    }

    public function addPlan(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_room_schedule')) {
            abort(404);
        }

        $client_user = User::where('trash', '0')->get();
        $cardtype = CardType::where('trash', '0')->get();
        $month = config('app.month');
        $year = config('app.year');
        $roomlayout = RoomLayout::where('room_no', $request['room_no'])->first();
        return view('backend.admin.room_schedule.create_room_plan', compact('roomlayout', 'month', 'year', 'cardtype', 'client_user'));
    }

    public function addBooking(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_room_schedule')) {
            abort(404);
        }

        $client_user = User::where('trash', '0')->get();
        $cardtype = cardType::where('trash', '0')->get();
        $roomlayout = RoomLayout::where('room_no', $request['room_no'])->first();
        $bookings = Booking::with('roomscheduledata')->where('room_id', $roomlayout->room_id)->whereIn('status', [0, 1, 3])->where('trash', '0')->get();
        $status = config('app.status');
        return view('backend.admin.room_schedule.create_room_booking', compact('bookings', 'client_user', 'roomlayout', 'status'));
    }

    public function addBookingPost(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_room_schedule')) {
            abort(404);
        }

        $booking = Booking::where('id', $request->booking_id)->first();
        $roomschedule = new RoomSchedule();
        $roomschedule->room_no = $request->room_no;
        if ($booking->client_user) {
            $roomschedule->client_user = $booking->client_user;
        }

        $roomschedule->guest = $booking->guest;
        $roomschedule->room_qty = $booking->room_qty;
        $roomschedule->extra_bed_qty = $booking->extra_bed_qty;
        $roomschedule->nationality = $booking->nationality;
        $roomschedule->room_id = $booking->room_id;
        $roomschedule->booking_id = $booking->id;
        $roomschedule->check_in = $booking->check_in;
        $roomschedule->check_out = $booking->check_out;
        $roomschedule->status = 1;
        $roomschedule->save();

        $calendar = new BookingCalendar();
        $calendar->booking_id = $booking->id;
        $calendar->room_id = $booking->room_id;
        $calendar->room_qty = $booking->room_qty;
        $calendar->check_in = $booking->check_in;
        $calendar->check_out = $booking->check_out;
        $calendar->save();

        if ($booking->room->room_qty > 1) {
            $booking->status = '1';
            $booking->payment_status = '0';
        } else {
            $booking->status = '1';
            $booking->payment_status = '1';
        }

        $booking->update();

        activity()
            ->performedOn($booking)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Room Schedule (Admin Panel)'])
            ->log('New Booking is added');

        return redirect('/admin/roomplan?date=' . $roomschedule->check_in)->with('success', 'Successfully Created');

    }

    public function detail(RoomSchedule $roomschedule)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_room_schedule')) {
            abort(404);
        }

        $tax = Tax::all();
        if ($tax) {
            $tax1 = Tax::where('id', 1)->first();
            $tax2 = Tax::where('id', 2)->first();
            $commercial_percentage = $tax1->amount;
            $service_percentage = $tax2->amount;
        }

        if ($roomschedule->booking->other_services) {
            $data = unserialize($roomschedule->booking->other_services);
            $otherservicesdata = OtherServicesItem::findMany($data);
        } else {
            $otherservicesdata = null;
        }

        $commission = 0;
        $commission_percentage = 0;
        if ($roomschedule->booking->client_user != null) {
            $user = User::where('id', $roomschedule->booking->client_user)->first();
            $account = AccountType::where('id', $user->account_type)->first();
            $commission_percentage = $roomschedule->booking->commission_percentage;
            $commission = $roomschedule->booking->commission;
        }

        if ($roomschedule->booking->nationality == 1) {
            $sign1 = " ";
            $sign2 = "MMK";
        } else {
            $sign1 = "$";
            $sign2 = "";

        }
        $month = config('app.month');
        $year = config('app.year');
        $client_user = User::where('trash', '0')->get();
        $pay_method = config('app.pay_method');
        $nationality = config('app.nationality');
        $room_schedule = RoomSchedule::where('id', $roomschedule['id'])->first();

        $night = Carbon::parse($room_schedule->check_in)->diffInDays(Carbon::parse($roomschedule->check_out));

        return view('backend.admin.room_schedule.detail', compact('commercial_percentage', 'service_percentage', 'commission_percentage', 'commission', 'sign1', 'sign2', 'room_schedule', 'night', 'pay_method', 'nationality', 'client_user', 'month', 'year', 'otherservicesdata'));
    }

    public function show(RoomSchedule $roomschedule)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_room_schedule')) {
            abort(404);
        }

        return view('backend.admin.room_schedule.show', compact('roomlayout'));
    }

    public function edit($id)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_room_schedule')) {
            abort(404);
        }

        $client_user = User::where('trash', '0')->get();
        $cardtype = CardType::where('trash', '0')->get();
        $month = config('app.month');
        $year = config('app.year');
        $roomlayout = RoomLayout::where('trash', '0');
        $room = Rooms::where('trash', '0');
        $roomschedule = RoomSchedule::findOrFail($id);
        $tax = Tax::all();
        $commercial_percentage = 0;
        if ($tax) {
            $tax1 = Tax::where('id', 1)->first();
            $commercial_percentage = $tax1->amount;
        }
        $commission = 0;
        $commission_percentage = 0;
        if ($roomschedule->booking->client_user) {
            $user = User::where('id', $roomschedule->booking->client_user)->first();
            $account = AccountType::where('id', $user->account_type)->first();
            $commission_percentage = $roomschedule->booking->commission_percentage;
            $commission = $roomschedule->booking->commission;
        }

        return view('backend.admin.room_schedule.edit', compact('commission', 'commercial_percentage', 'roomschedule', 'roomlayout', 'room', 'month', 'year', 'cardtype', 'client_user'));
    }

    public function update(RoomScheduleUpdateRequest $request, $id)
    {

        if (!$this->getCurrentAuthUser('admin')->can('edit_room_schedule')) {
            abort(404);
        }
        $roomschedule = RoomSchedule::findOrFail($id);
        $booking = Booking::where('id', $roomschedule->booking_id)->first();

        $default_early_time1 = "07:00:00";
        $default_early_time2 = "11:00:00";
        $default_late_time1 = "14:00:00";
        $default_late_time2 = "17:00:00";
        $time = Carbon::now();
        $today = $time->format('Y-m-d');

        $currenttime = $time->format("H:i:s");
        $request_early_late = null;

        if ($request->early_late) {
            $request_early_late = $request->early_late;
        } else {
            if ($request->status == 2) {
                if ($default_early_time1 <= $currenttime & $currenttime <= $default_early_time2) {
                    $request_early_late = ['1'];
                } else {
                    $request_early_late = null;
                }
            }
            if ($request->status == 3) {
                if ($default_late_time1 <= $currenttime & $currenttime <= $default_late_time2) {
                    if ($booking->check_out < $today) {
                        $request_early_late = ['2'];
                    } else {
                        $request_early_late = null;
                    }
                } else {
                    $request_early_late = null;
                }
            }
        }

        $checkin_checkout = explode(' - ', $request->checkin_checkout);
        $check_in_date = $checkin_checkout[0];
        $check_out_date = $checkin_checkout[1];
        $commercial_tax_obj = Tax::where('name', 'commercial')->first();
        $service_charges_obj = Tax::where('name', 'service')->first();
        $tax1 = 0;
        $tax2 = 0;

        if ($commercial_tax_obj) {
            $tax1 = $commercial_tax_obj->amount ? $commercial_tax_obj->amount / 100 : 0;
        }
        if ($service_charges_obj) {
            $tax2 = $service_charges_obj->amount ? $service_charges_obj->amount / 100 : 0;
        }

        $room = Rooms::where('id', $request->room_id)->first();
        $roomschedule = RoomSchedule::findOrFail($id);
        $booking = Booking::where('id', $roomschedule->booking_id)->first();
        $client_user = User::where('id', $booking->client_user)->first();
        $earlylatecheck = 0;
        if ($client_user) {
            $account_type = $client_user->accounttype->id;
            $extrabedprice = ExtraBedPrice::where('trash', '0')->where('user_account_id', $account_type)->first();
            $earlylatecheck = EarlyLateCheck::where('trash', '0')->where('user_account_id', $account_type)->first();
        }

        $checkin_checkout = explode(' - ', $request->checkin_checkout);
        $nights = Carbon::parse($checkin_checkout[1])->diffInDays(Carbon::parse($checkin_checkout[0]));

        if ($request->nationality == 1) {
            $extra_bed_total = ($booking->extra_bed_qty * $room->extra_bed_mm_price) * $nights;
            if ($client_user) {
                if ($extrabedprice) {
                    $extra_bed_total = ($booking->extra_bed_qty * (($room->extra_bed_mm_price + $extrabedprice->add_extrabed_price_mm) - $extrabedprice->subtract_extrabed_price_mm)) * $nights;
                }
            }
            $early_check_in = 0;
            $late_check_out = 0;
            $both_check = 0;

            if ($request_early_late) {
                if ($request_early_late == array(1, 2)) {
                    $both_check = $room->early_checkin_mm + $room->late_checkout_mm;
                    if ($earlylatecheck) {
                        $both_check = ($room->early_checkin_mm + $room->late_checkout_mm + $earlylatecheck->add_early_checkin_mm + $earlylatecheck->add_late_checkout_mm) - ($earlylatecheck->subtract_early_checkin_mm + $earlylatecheck->subtract_late_checkout_mm);
                    }
                } elseif ($request_early_late['0'] == 1) {
                    $early_check_in = $room->early_checkin_mm;
                    if ($earlylatecheck) {
                        $early_check_in = ($room->early_checkin_mm + $earlylatecheck->add_early_checkin_mm) - $earlylatecheck->subtract_early_checkin_mm;
                    }
                } elseif ($request_early_late['0'] == 2) {
                    $late_check_out = $room->late_checkout_mm;
                    if ($earlylatecheck) {
                        $late_check_out = ($room->late_checkout_mm + $earlylatecheck->add_late_checkout_mm) - $earlylatecheck->subtract_late_checkout_mm;
                    }
                }
            } else {
                $early_check_in = 0;
                $late_check_out = 0;
            }

        } else {

            $extra_bed_total = ($booking->extra_bed_qty * $room->extra_bed_foreign_price) * $nights;
            if ($client_user) {
                if ($extrabedprice) {
                    $extra_bed_total = ($booking->extra_bed_qty * (($room->extra_bed_foreign_price + $extrabedprice->add_extrabed_price_foreign) - $extrabedprice->subtract_extrabed_price_foreign)) * $nights;
                }
            }
            $early_check_in = 0;
            $late_check_out = 0;
            $both_check = 0;

            if ($request_early_late) {
                if ($request_early_late == array(1, 2)) {
                    $both_check = $room->early_checkin_foreign + $room->late_checkout_foreign;
                    if ($earlylatecheck) {
                        $both_check = ($room->early_checkin_foreign + $room->late_checkout_foreign + $earlylatecheck->add_early_checkin_foreign + $earlylatecheck->add_late_checkout_foreign) - ($earlylatecheck->subtract_early_checkin_foreign + $ealrylatecheck->subtract_late_checkout_foreign);
                    }
                } elseif ($request_early_late['0'] == 1) {
                    $early_check_in = $room->early_checkin_foreign;
                    if ($earlylatecheck) {
                        $early_check_in = ($room->early_checkin_foreign + $earlylatecheck->add_early_checkin_foreign) - $earlylatecheck->subtract_early_checkin_foreign;
                    }
                } elseif ($request_early_late['0'] == 2) {
                    $late_check_out = $room->late_checkout_foreign;
                    if ($earlylatecheck) {
                        $late_check_out = ($room->late_checkout_foreign + $earlylatecheck->add_late_checkout_foreign) - $earlylatecheck->subtract_late_checkout_foreign;
                    }
                }
            } else {
                $early_check_in = 0;
                $late_check_out = 0;
            }
        }

        $room_total = ($booking->room_qty * $booking->discount_price) * $nights;
        $subtotal = ($room_total + $extra_bed_total);
        $service_tax = $subtotal * $tax2;
        $total = $subtotal + $service_tax + $early_check_in + $late_check_out + $both_check;
        $commercial_tax = $total * $tax1;
        $grand_total = $total + $commercial_tax;

        $checkin_checkout = explode(' - ', $request->checkin_checkout);

        $commission = 0;
        $commission_percentage = 0;
        $deposite = $request->deposite ?? 0;
        if ($client_user) {
            $account_type = $client_user->accounttype->id;
            $account = AccountType::where('id', $account_type)->first();
            $commission_percentage = $booking->commission_percentage;
            $commission = (($total + $commercial_tax) - ($early_check_in + $late_check_out + $both_check)) * ($booking->commission_percentage / 100);
        }

        if ($request->status == '4') {
            $roomschedule->delete();

            return redirect()->route('admin.roomplan')->with('success', 'Successfully Created');
        }

        $roomschedule->room_no = $request['room_no'];
        $roomschedule->room_id = $request['room_id'];
        if ($request->client_user) {
            $roomschedule->client_user = $request->client_user;
        }
        $roomschedule->guest = $request['guest'];
        $roomschedule->room_qty = $request['room_qty'];
        $roomschedule->extra_bed_qty = $request['extra_bed_qty'];
        $roomschedule->nationality = $request['nationality'];
        $roomschedule->check_in = $checkin_checkout[0];
        $roomschedule->check_out = $checkin_checkout[1];
        $roomschedule->status = $request->status;
        $roomschedule->booking_id = $roomschedule->booking_id;
        $roomschedule->update();

        if ($request->client_user) {
            if ($request->registered == 1) {
                $booking->name = $booking->name;
                $booking->email = $booking->email;
                $booking->phone = $booking->phone;
                $booking->price = $booking->price;
            }

        } else {
            if ($request->registered == 2) {
                $booking->name = $request->name;
                $booking->email = $request->email;
                $booking->phone = $request->phone;
                $booking->price = number_format($price, 2, '.', '');
            }
        }

        $booking->discount_price = $booking->discount_price;
        $booking->nationality = $request->nationality;
        $booking->total = number_format($total, 2, '.', '');
        $booking->status = $booking->status;
        $booking->payment_status = $booking->payment_status;
        $booking->commercial_tax = number_format($commercial_tax, 2, '.', '');
        $booking->service_tax = number_format($service_tax, 2, '.', '');
        $booking->grand_total = number_format($grand_total, 2, '.', '');
        $booking->extra_bed_qty = $request->extra_bed_qty;
        $booking->commission = number_format($commission, 2, '.', '');
        $booking->deposite = number_format($deposite, 2, '.', '');
        $booking->commission_percentage = number_format($commission_percentage, 2, '.', '');

        if ($request_early_late) {
            $booking->early_late = serialize($request_early_late);
        } else {
            $booking->early_late = null;
        }

        $booking->early_check_in = number_format($early_check_in, 2, '.', '');
        $booking->late_check_out = number_format($late_check_out, 2, '.', '');
        $booking->both_check = number_format($both_check, 2, '.', '');
        $booking->early_checkin_time = $request->early_checkin_time;
        $booking->late_checkout_time = $request->late_checkout_time;

        if ($request->status == 2) {
            //  if( $default_early_time1 <= $currenttime  & $currenttime <= $default_early_time2 ){
            $booking->early_checkin_time = $currenttime;
            // }
        }
        if ($request->status == 3) {
            //  if( $default_late_time1 <= $currenttime  & $currenttime <= $default_late_time2){
            $booking->late_checkout_time = $currenttime;
            // }
        }

        $booking->extra_bed_total = number_format($extra_bed_total, 2, '.', '');
        $booking->guest = $request->guest;
        $booking->pay_method = $request->pay_method;

        if ($booking->pay_method == 1) {
            $booking->credit_type = $request->credit_type;
            $booking->credit_no = $request->credit_no;
            $booking->expire_month = $request->expire_month;
            $booking->expire_year = $request->expire_year;
        }

        $booking->check_in = $checkin_checkout[0];
        $booking->check_out = $checkin_checkout[1];
        $booking->update();

        $calendar = BookingCalendar::where('booking_id', $booking->id)->first();
        $calendar->room_id = $booking->room_id;
        $calendar->room_qty = $booking->room_qty;
        $calendar->check_in = $booking->check_in;
        $calendar->check_out = $booking->check_out;
        $calendar->update();

        activity()
            ->performedOn($roomschedule)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Room Schedule (Admin Panel)'])
            ->log('New RoomSchedule is added');

        return redirect()->route('admin.roomplan')->with('success', 'Successfully Created');
    }

    public function destroy(RoomSchedule $roomschedule)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_room_schedule')) {
            abort(404);
        }
        if ($roomschedule->booking) {
            $booking = Booking::where('id', $roomschedule->booking_id)->delete();
            $booking_calendar = BookingCalendar::where('booking_id', $roomschedule->booking_id)->delete();
        }
        $roomschedule->delete();
        activity()
            ->performedOn($roomschedule)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Room Schedule (Admin Panel)'])
            ->log('RoomSchedule is deleted');

        return ResponseHelper::success();
    }

    public function trash(RoomSchedule $roomschedule)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_room_schedule')) {
            abort(404);
        }
        $roomschedule->trash();
        activity()
            ->performedOn($roomschedule)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Room Schedule (Admin Panel)'])
            ->log('RoomSchedule is moved to trash');

        return ResponseHelper::success();
    }

    public function restore(RoomSchedule $roomschedule)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_room_schedule')) {
            abort(404);
        }
        $roomschedule->restore();
        activity()
            ->performedOn($roomschedule)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Room Schedule (Admin Panel)'])
            ->log('RoomSchedule is restored from trash');

        return ResponseHelper::success();
    }

}
