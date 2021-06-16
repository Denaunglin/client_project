<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\HelperFunction;
use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookingCreate;
use App\Http\Requests\BookingUpdate;
use App\Http\Traits\AuthorizePerson;
use App\Models\AccountType;
use App\Models\Booking;
use App\Models\BookingCalendar;
use App\Models\CardType;
use App\Models\Discounts;
use App\Models\EarlyLateCheck;
use App\Models\ExtraBedPrice;
use App\Models\ExtraInvoice;
use App\Models\Invoice;
use App\Models\OtherServicesCategory;
use App\Models\OtherServicesItem;
use App\Models\Payslip;
use App\Models\RoomLayout;
use App\Models\Rooms;
use App\Models\RoomSchedule;
use App\Models\Tax;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class BookingController extends Controller
{
    use AuthorizePerson;

    public function index(Request $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_booking')) {
            abort(404);
        }

        $username = User::where('trash', '0')->get();
        if ($request->ajax()) {
            $daterange = $request->daterange ? explode(' , ', $request->daterange) : null;
            $book = Booking::anyTrash($request->trash)->orderBy('id', 'desc')->with('room', 'room.roomtype', 'room.bedtype');

            if ($daterange) {
                $book = $book->whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
            }

            if ($request->status != '') {
                $book = $book->where('status', $request->status);
            }

            if ($request->payment_status != '') {
                $book = $book->where('payment_status', $request->payment_status);
            }

            if ($request->booking_user_name != '') {
                $book = $book->where('name', 'like', "%{$request->booking_user_name}");

            }

            if ($request->username != '') {
                $book = $book->where('name', $request->username);
            }

            return Datatables::of($book)
                ->addIndexColumn()
                ->addColumn('room', function ($book) {
                    $roomtype = '-';
                    $bedtype = '-';

                    if ($book->room) {
                        $roomtype = $book->room->roomtype ? $book->room->roomtype->name : '-';
                        $bedtype = $book->room->bedtype ? $book->room->bedtype->name : '-';
                    }

                    return '<ul class="list-group">
                            <li class="list-group-item">Room Type - ' . $roomtype . '</li>
                            <li class="list-group-item">Bed Type - ' . $bedtype . '</li>
                            <li class="list-group-item">Room Qty - ' . $book->room_qty . '</li>
                        </ul>';
                })
                ->filterColumn('room', function ($query, $keyword) {
                    $query->whereHas('room', function ($q1) use ($keyword) {
                        $q1->whereHas('roomtype', function ($q2) use ($keyword) {
                            $q2->where('name', 'LIKE', "%{$keyword}%");
                        })->orWhereHas('bedtype', function ($q2) use ($keyword) {
                            $q2->where('name', 'LIKE', "%{$keyword}%");
                        });
                    });
                })

                ->addColumn('checkin_checkout', function ($book) {
                    $output = '<span class="text-primary">' . $book->check_in . ' , ' . $book->check_out . '</span>';
                    return $output;
                })
                ->addColumn('cancellation', function ($book) {
                    $output = '-';
                    if ($book->cancellation == 1) {
                        $output = '<span class="text-danger"> Canceled </span>';
                    }
                    return $output;
                })
                ->addColumn('person', function ($book) {
                    return '<ul class="list-group">
                            <li class="list-group-item">Name - ' . $book->name . '</li>
                            <li class="list-group-item">Email - ' . $book->email . '</li>
                            <li class="list-group-item">Phone - ' . $book->phone . '</li>
                        </ul>';
                })
                ->editColumn('status', function ($book) {
                    return '<ul class="list-group">
                            <li class="list-group-item">Status - ' . HelperFunction::statusUI($book->status) . '</li>
                            <li class="list-group-item">Payment Status - ' . HelperFunction::paymentStatusUI($book->payment_status) . '</li>
                        </ul>';
                })
                ->addColumn('action', function ($booking) use ($request) {

                    $detail_btn = '';
                    $restore_btn = '';
                    $status_btn = '';
                    $invoice_btn = '';
                    $restore_btn = '';
                    $trash_or_delete_btn = '';

                    if ($this->getCurrentAuthUser('admin')->can('view_status')) {
                        $status_btn = '<a class="edit text text-primary" href="' . route('admin.booking.status', ['booking' => $booking->id]) . '"><i class="far fa-file fa-lg"></i></a>';
                    }
                    if ($this->getCurrentAuthUser('admin')->can('view_invoice')) {
                        $invoice_btn = '<a class="edit text text-primary" href="' . route('admin.admin_invoice_pdf', $booking->id) . '"><i class="fas fa-file-invoice-dollar fa-lg"></i></a>';
                    }

                    if ($this->getCurrentAuthUser('admin')->can('delete_booking')) {
                        if ($request->trash == 1) {
                            $restore_btn = '<a class="restore text text-dark" href="#" data-id="' . $booking->id . '"><i class="fa fa-trash-restore fa-lg"></i></a>';
                            $trash_or_delete_btn = '<a class="destroy text text-danger" href="#" data-id="' . $booking->id . '"><i class="fa fa-minus-circle fa-lg"></i></a>';
                        } else {
                            $trash_or_delete_btn = '<a class="trash text text-danger" href="#" data-id="' . $booking->id . '"><i class="fas fa-trash fa-lg"></i></a>';
                        }
                    }

                    return "<div class='action'>${status_btn} ${detail_btn} ${invoice_btn}  ${restore_btn} ${trash_or_delete_btn}</div>";
                })
                ->addColumn('plus-icon', function () {
                    return null;
                })
                ->filterColumn('person', function ($query, $keyword) {
                    $query->where('bookings.name', 'like', "%{$keyword}%")
                        ->orWhere('bookings.email', 'like', "%{$keyword}%")
                        ->orWhere('bookings.phone', 'like', "%{$keyword}%");
                })
                ->rawColumns(['room', 'cancellation', 'person', 'checkin_checkout', 'status', 'action'])
                ->make(true);
        }

        return view('backend.admin.booking.index', compact('username'));
    }

    public function create()
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_booking')) {
            abort(404);
        }

        $client_user = User::where('trash', '0')->orderBy('id', 'desc')->get();
        $month = config('app.month');
        $year = config('app.year');
        $cardtype = CardType::where('trash', '0')->get();
        $rooms = Rooms::where('trash', 0)->get();

        return view('backend.admin.booking.create', compact('rooms', 'month', 'year', 'cardtype', 'client_user'));
    }

    public function store(BookingCreate $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('add_booking')) {
            abort(404);
        }

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
        $client_user = User::where('id', $request->client_user)->first();
        $account_type = $client_user->accounttype->id;
        $membertype = $client_user->accounttype->name;
        $discount_type = Discounts::where('trash', '0')->where('user_account_id', $account_type)->where('room_type_id', $room->id)->first();
        $extrabedprice = ExtraBedPrice::where('trash', '0')->where('user_account_id', $account_type)->first();
        $earlylatecheck = EarlyLateCheck::where('trash', '0')->where('user_account_id', $account_type)->first();
        $bookingdiscounts = ResponseHelper::roomschedulediscount($room, $request->nationality, $client_user, $discount_type);
        $bookingdiscount = $bookingdiscounts['0'];
        $addon = $bookingdiscounts['1'];
        $checkin_checkout = explode(' - ', $request->checkin_checkout);

        if ($checkin_checkout[0] == $checkin_checkout[1]) {
            return redirect()->route('admin.booking.create')->with(['error' => 'Please do not select the same chech-in & check-out date!']);
        }

        $avaliable_room_qty = ResponseHelper::avaliable_room_qty($room, $checkin_checkout[0], $checkin_checkout[1]);

        if ($avaliable_room_qty < $request->room_qty) {
            return back()->withErrors(['fail' => 'Your booking room qty is greater than avaliable room qty.'])->withInput();
        }

        if ($request->room_qty == 0) {
            return back()->withErrors(['fail' => 'Your booking room is not Avaliable . '])->withInput();
        }

        $nights = Carbon::parse($checkin_checkout[1])->diffInDays(Carbon::parse($checkin_checkout[0]));

        if ($request->nationality == 1) {
            $room_total = ($request->room_qty * $bookingdiscount) * $nights;
            $price = $addon;
            $discount = $bookingdiscount;
            $extra_bed_total = ($request->extra_bed_qty * $room->extra_bed_mm_price) * $nights;

            if ($extrabedprice) {
                $extra_bed_total = ($request->extra_bed_qty * (($room->extra_bed_mm_price + $extrabedprice->add_extrabed_price_mm) - $extrabedprice->subtract_extrabed_price_mm)) * $nights;
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
                } elseif ($request->early_late['0'] == 2) {
                    $late_check_out = $room->late_checkout_mm;
                    if ($earlylatecheck) {
                        $late_check_out = ($room->late_checkout_mm + $earlylatecheck->add_late_checkout_mm) - $earlylatecheck->subtract_late_checkout_mm;
                    }
                }
            }

        } else {
            $room_total = ($request->room_qty * $bookingdiscount) * $nights;
            $price = $addon;
            $discount = $bookingdiscount;
            $extra_bed_total = ($request->extra_bed_qty * $room->extra_bed_foreign_price) * $nights;
            if ($extrabedprice) {
                $extra_bed_total = ($request->extra_bed_qty * (($room->extra_bed_foreign_price + $extrabedprice->add_extrabed_price_foreign) - $extrabedprice->subtract_extrabed_price_foreign)) * $nights;
            }

            $subtotal = $room_total + $extra_bed_total;
            $early_check_in = 0;
            $late_check_out = 0;
            $both_check = 0;

            if ($request->early_late) {
                if ($request->early_late == array(1, 2)) {
                    $both_check = $room->early_checkin_foreign + $room->late_checkout_foreign;
                    if ($earlylatecheck) {
                        $both_check = ($room->early_checkin_foreign + $room->late_checkout_foreign + $earlylatecheck->add_early_checkin_foreign + $earlylatecheck->add_late_checkout_foreign) - ($earlylatecheck->subtract_early_checkin_foreign);
                    }
                } elseif ($request->early_late['0'] == 1) {
                    $early_check_in = $room->early_checkin_foreign;
                    if ($earlylatecheck) {
                        $early_check_in = ($room->early_checkin_foreign + $earlylatecheck->add_early_checkin_foreign) - $earlylatecheck->subtract_early_checkin_foreign;

                    }
                } elseif ($request->early_late['0'] == 2) {
                    $late_check_out = $room->late_checkout_foreign;
                    if ($earlylatecheck) {
                        $late_check_out = ($room->late_checkout_foreign + $earlylatecheck->late_checkout_foreign) - $earlylatecheck->subtract_late_checkout_foreign;
                    }
                }
            }
        }

        $service_tax = $subtotal * $tax2;
        $total = $subtotal + $service_tax + $early_check_in + $late_check_out + $both_check;
        $commercial_tax = $total * $tax1;
        $grand_total = $total + $commercial_tax;

        $commission = 0;
        $commission_percentage = 0;

        if ($client_user) {
            $account_type = $client_user->accounttype->id;
            $account = AccountType::where('id', $account_type)->first();
            $commission_percentage = $account->commission;
            $commission = (($total + $commercial_tax) - ($early_check_in + $late_check_out + $both_check)) * ($account->commission / 100);
        }

        $booking = new Booking();
        $booking->room_id = $room->id;
        $booking->client_user = $client_user->id;
        $booking->name = $client_user->name;
        $booking->email = $client_user->email;
        $booking->phone = $client_user->phone;
        $booking->nrc_passport = $client_user->nrc_passport;
        $booking->message = $request->message;
        $booking->price = number_format($price, 2, '.', '');
        $booking->discount_price = number_format($discount, 2, '.', '');
        $booking->nationality = $request->nationality;
        $booking->total = number_format($total, 2, '.', '');
        $booking->commercial_tax = number_format($commercial_tax, 2, '.', '');
        $booking->service_tax = number_format($service_tax, 2, '.', '');
        $booking->grand_total = number_format($grand_total, 2, '.', '');
        $booking->room_qty = $request->room_qty;
        $booking->member_type = $membertype;
        $booking->commission = number_format($commission, 2, '.', '');
        $booking->commission_percentage = number_format($commission_percentage, 2, '.', '');
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
        $booking->deposite = 0;
        $booking->update();

        activity()
            ->performedOn($booking)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Booking (Admin Panel)'])
            ->log('New booking (' . $booking->booking_number . ') is added');

        return redirect()->route('admin.booking.index')->with('success', 'Successfully Booked.');

    }

    public function destroy(Booking $booking)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_booking')) {
            abort(404);
        }

        if ($booking->roomschedule) {
            $room_schedule = RoomSchedule::where('booking_id', $booking->id)->delete();
            $booking_calendar = BookingCalendar::where('booking_id', $booking->id)->delete();
            $invoice = Invoice::where('booking_id', $booking->id)->delete();
            $extrainvoice = Extrainvoice::where('booking_id', $booking->id)->delete();
        }

        $booking->delete();
        activity()
            ->performedOn($booking)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Booking (Admin Panel)'])
            ->log('Booking (' . $booking->booking_number . ') is deleted');

        return ResponseHelper::success();
    }

    public function trash(Booking $booking)
    {
        if (!$this->getCurrentAuthUser('admin')->can('delete_booking')) {
            abort(404);
        }
        $roomschedule = RoomSchedule::where('booking_id', $booking->id)->first();
        if ($roomschedule) {
            $roomschedule->trash();
        }
        $booking->trash();
        activity()
            ->performedOn($booking)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Booking (Admin Panel)'])
            ->log('Booking (' . $booking->booking_number . ') is moved to trash');

        return ResponseHelper::success();
    }

    public function restore(Booking $booking)
    {
        $roomschedule = RoomSchedule::where('booking_id', $booking->id)->first();
        $roomschedule->restore();
        $booking->restore();
        activity()
            ->performedOn($booking)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Booking (Admin Panel)'])
            ->log('Booking (' . $booking->booking_number . ') is restored from trash');

        return ResponseHelper::success();
    }

    public function status(Booking $booking)
    {
        if (!$this->getCurrentAuthUser('admin')->can('view_status')) {
            abort(404);
        }

        $takeroom = RoomSchedule::where('booking_id', $booking->id)->get();
        $payslips = Payslip::where('booking_no', $booking->booking_number)->get();
        $payslip = $payslips->last();
        $tax = Tax::all();
        if ($tax) {
            $tax1 = Tax::where('id', 1)->first();
            $tax2 = Tax::where('id', 2)->first();
            $commercial_percentage = $tax1->amount;
            $service_percentage = $tax2->amount;
        }

        if ($booking->other_services) {
            $otherservicesdata = unserialize($booking->other_services);
        } else {
            $otherservicesdata = null;
        }

        $commission = 0;
        $commission_percentage = 0;

        if ($booking->commission) {
            $commission_percentage = $booking->commission_percentage;
            $commission = $booking->commission;
        }

        $condition1 = 0;
        $condition2 = 0;

        if ($booking->roomschedule) {
            if ($booking->roomschedule->status == '5' or $booking->roomschedule->status == '3') {
                $condition1 = '1';
                $condition2 = '1';
                if ($booking->payment_status == '1') {
                    $condition2 = '2';
                }
            }
        }

        $invoice = Invoice::where('trash', '0')->where('booking_id', $booking->id)->orderBy('id', 'desc')->paginate(3);
        $extrainvoice = ExtraInvoice::where('trash', '0')->where('booking_id', $booking->id)->orderBy('id', 'desc')->paginate(3);
        $otherservicescategory = OtherServicesCategory::where('trash', '0')->get();
        $otherservicesitem = OtherServicesItem::where('trash', '0')->get();
        $pay_method = config('app.pay_method');
        $month = config('app.month');
        $year = config('app.year');
        $roomno = RoomLayout::where('room_id', $booking->room_id)->where('maintain', '0')->get();
        $check_room = RoomSchedule::where('trash', 0)->where('room_id', $booking->room_id)->where('check_in', $booking->check_in)->whereIn('status', [1, 2, 3])->orwhere('check_out', '>', $booking->check_in)->where('check_in', '<', $booking->check_in)->where('trash', 0)->whereIn('status', [1, 2, 3])->get();
        $nationality = config('app.nationality');
        $status = config('app.status');
        $payment = config('app.payment');
        $avaliable_room_qty = 0;

        if ($booking->room) {
            $avaliable_room_qty = ResponseHelper::avaliable_room_qty($booking->room, $booking->check_in, $booking->check_out);
        }
        if ($booking->nationality == 1) {
            $sign1 = '';
            $sign2 = 'MMK';
        } else {
            $sign1 = '$';
            $sign2 = '';
        }

        return view('backend.admin.booking.status', compact('condition1', 'condition2', 'payslip', 'commercial_percentage', 'takeroom', 'service_percentage', 'extrainvoice', 'invoice', 'commission_percentage', 'roomno', 'commission', 'nationality', 'sign1', 'sign2', 'booking', 'status', 'payment', 'avaliable_room_qty', 'pay_method', 'check_room', 'month', 'year', 'otherservicescategory', 'otherservicesitem', 'otherservicesdata'));
    }

    public function show(Booking $booking)
    {
        return view('backend.admin.booking.status', compact('booking'));
    }

    public function update(Booking $booking, BookingUpdate $request)
    {
        if (!$this->getCurrentAuthUser('admin')->can('edit_booking')) {
            abort(404);
        }

        $room_no = $request->room_no;
        $room_no_count = count($request->room_no);

        if ($booking->room_qty < $room_no_count) {
            return back()->withErrors(['fail' => 'The assigned room qty is greater than the room qty of booking.'])->withInput();
        }

        if ($booking->status != 1) {

            $check_room = RoomSchedule::where('trash', 0)->where('room_id', $booking->room_id)->where('check_in', $booking->check_in)->whereIn('status', [1, 2, 3])->orwhere('check_out', '>', $booking->check_in)->where('check_in', '<', $booking->check_in)->where('trash', 0)->whereIn('status', [1, 2, 3])->get();
            foreach ($check_room as $data) {
                foreach ($request->room_no as $roomno) {
                    if ($data->room_no == $roomno) {
                        return back()->withErrors(['fail' => 'This Room Number is already taken   .'])->withInput();
                    }
                }
            }
        }

        if ($request->other_services) {
            $other_service_ary = [];
            $other_services = $request->other_services;
            $otherservicesitem = OtherServicesItem::findMany($other_services);

            foreach ($otherservicesitem as $otherservicesitem_each) {
                $other_service_qty_key = 'other_service_qty_' . $otherservicesitem_each->id;
                $other_service_qty = $request->$other_service_qty_key;

                $item = [];
                $item['id'] = $otherservicesitem_each->id;
                $item['name'] = $otherservicesitem_each->name;
                $item['qty'] = $other_service_qty;
                $item['charges'] = $booking->nationality == 1 ? $otherservicesitem_each->charges_mm : $otherservicesitem_each->charges_foreign;
                $item['total'] = $item['charges'] * $item['qty'];
                $other_service_ary[] = $item;
            }
            $other_services_array = $other_service_ary;
            $total = 0;
            foreach ($other_services_array as $data) {
                $total += $data['total'];
            }

            $total_charges = $total;
            $other_charges_total = $total_charges;
            $booking->other_services = serialize($other_services_array);
            $booking->other_charges_total = number_format($other_charges_total, 2, '.', '');
        } else {
            $booking->other_services = null;
            $booking->other_charges_total = null;
        }

        $booking->update();

        $room = Rooms::findOrFail($booking->room_id);
        $client_user = User::where('id', $booking->client_user)->first();
        $earlylatecheck = 0;

        if ($client_user) {
            $accounttype = $client_user->accounttype->id;
            $extrabedprice = Extrabedprice::where('trash', '0')->where('user_account_id', $accounttype)->first();
            $earlylatecheck = EarlyLateCheck::where('trash', '0')->where('user_account_id', $accounttype)->first();
        }

        $nights = Carbon::parse($booking->check_in)->diffInDays(Carbon::parse($booking->check_out));
        $avaliable_room_qty = ResponseHelper::avaliable_room_qty($room, $booking->check_in, $booking->check_out);
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
        if ($booking->nationality == 1) {
            $extra_bed_total = ($booking->extra_bed_qty * $room->extra_bed_mm_price) * $nights;

            if ($client_user) {

                if ($extrabedprice) {
                    $extra_bed_total = ($booking->extra_bed_qty * (($room->extra_bed_mm_price + $extrabedprice->add_extrabed_price_mm) - $extrabedprice->subtract_extrabed_price_mm)) * $nights;
                }
            }

            $early_check_in = 0;
            $late_check_out = 0;
            $both_check = 0;

            if ($request->early_late) {
                if ($request->early_late == array(1, 2)) {
                    $both_check = $room->early_checkin_mm + $room->late_checkout_mm;
                    if ($earlylatecheck) {
                        $both_check = ($room->early_checkin_mm + $room->late_checkout_mm + $earlylatecheck->add_early_checkin_mm + $earlylatecheck->add_late_checkin_mm) - ($earlylatecheck->subtract_early_checkin_mm + $earlylatecheck->subtract_late_checkout_mm);
                    }
                } elseif ($request->early_late['0'] == 1) {
                    $early_check_in = $room->early_checkin_mm;
                    if ($earlylatecheck) {
                        $early_check_in = ($room->early_checkin_mm + $earlylatecheck->add_early_checkin_mm) - $earlylatecheck->subtract_early_checkin_mm;
                    }
                } elseif ($request->early_late['0'] == 2) {
                    $late_check_out = $room->late_checkout_mm;
                    if ($earlylatecheck) {
                        $late_check_out = ($room->late_checkout_mm + $earlylatecheck->add_late_checkout_mm) - $earlylatecheck->subtract_late_checkout_mm;

                    }
                }
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

            if ($request->early_late) {
                if ($request->early_late = array(1, 2)) {
                    $both_check = ($room->early_checkin_foreign + $room->late_checkout_foreign);
                    if ($earlylatecheck) {
                        $both_check = ($room->early_checkin_foreign + $room->late_checkout_foreign + $earlylatecheck->add_early_checkin_foreign + $earlylatecheck->add_late_checkout_foreign) - ($earlylatecheck->subtract_early_checkin_foreign + $earlylatecheck->subtract_late_checkout_foreign);
                    }
                } elseif ($request->early_late == 1) {
                    $early_check_in = $room->early_checkin_foreign;
                    if ($earlylatecheck) {
                        $early_check_in = ($room->early_checkin_foreign + $earlylatecheck->add_early_checkin_foreign) - $earlylatecheck->subtract_early_checkin_foreign;
                    }
                } elseif ($request->early_late == 2) {
                    $late_check_out = $room->late_checkout_foreign;
                    if ($earlylatecheck) {
                        $late_check_out = ($room->late_checkout_foreign + $earlylatecheck->add_late_checkout_foreign) - $earlylatecheck->subtract_late_checkout_foreign;
                    }
                }
            }
        }
        $room_total = ($booking->room_qty * $booking->discount_price) * $nights;
        $subtotal = ($room_total + $extra_bed_total);
        $service_tax = ($subtotal) * $tax2;
        $total = $subtotal + $service_tax + $early_check_in + $late_check_out + $both_check;

        $commercial_tax = $total * $tax1;
        $grand_total = $total + $commercial_tax;
        $deposite = $request->deposite ?? 0;
        if ($request->early_late) {
            $booking->early_late = serialize($request->early_late);
        } else {
            $booking->early_late = '0';
        }

        $commission = 0;
        $commission_percentage = 0;

        if ($client_user) {
            $account_type = $client_user->accounttype->id;
            $account = AccountType::where('id', $account_type)->first();
            $commission_percentage = $booking->commission_percentage;
            $commission = (($total + $commercial_tax) - ($early_check_in + $late_check_out + $both_check)) * ($booking->commission_percentage / 100);
        }

        $booking->total = number_format($total, 2, '.', '');
        $booking->grand_total = number_format($grand_total, 2, '.', '');
        $booking->commercial_tax = number_format($commercial_tax, 2, '.', '');
        $booking->early_check_in = number_format($early_check_in, 2, '.', '');
        $booking->late_check_out = number_format($late_check_out, 2, '.', '');
        $booking->both_check = number_format($both_check, 2, '.', '');
        $booking->commission = number_format($commission, 2, '.', '');
        $booking->deposite = number_format($deposite, 2, '.', '');
        $booking->commission_percentage = number_format($commission_percentage, 2, '.', '');
        $booking->early_checkin_time = $request->early_checkin_time;
        $booking->late_checkout_time = $request->late_checkout_time;
        $booking->update();

        if ($request['status'] == 1) {
            $calendar = BookingCalendar::where('booking_id', $booking->id)->first();
            if ($calendar) {
                $calendar->room_id = $booking->room_id;
                $calendar->room_qty = $booking->room_qty;
                $calendar->check_in = $booking->check_in;
                $calendar->check_out = $booking->check_out;
                $calendar->update();
            } else {

                if ($booking->room_qty > $avaliable_room_qty) {
                    return back()->withErrors(['fail' => 'Booking room quantity is greater than avaliable room quantity.'])->withInput();
                }
                $calendar = new BookingCalendar();
                $calendar->booking_id = $booking->id;
                $calendar->room_id = $booking->room_id;
                $calendar->room_qty = $booking->room_qty;
                $calendar->check_in = $booking->check_in;
                $calendar->check_out = $booking->check_out;
                $calendar->save();
            }

            $roomschedule = RoomSchedule::where('booking_id', $booking->id)->get()->count();
            if ($roomschedule != 0) {
                $check_room = RoomSchedule::where('trash', 0)->where('booking_id', $booking->id)->where('check_in', $booking->check_in)->whereIn('status', [1, 2, 3])->orwhere('check_out', '>', $booking->check_in)->where('check_in', '<', $booking->check_in)->where('trash', 0)->whereIn('status', [1, 2, 3])->get();

                if ($roomschedule == 1) {
                    $data = RoomSchedule::where('booking_id', $booking->id)->first();
                    $data->room_no = $request->room_no['0'];
                    $data->room_id = $booking->room_id;
                    $data->client_user = $booking->client_user;
                    $data->guest = $booking->guest;
                    $data->room_qty = $booking->room_qty;
                    $data->extra_bed_qty = $booking->extra_bed_qty;
                    $data->nationality = $booking->nationality;
                    $data->check_in = $data->check_in;
                    $data->check_out = $data->check_out;
                    $data->status = $data->status;
                    $data->update();

                } else {

                    foreach ($request->room_no as $roomno) {
                        $aa[] = $roomno;
                    }
                    for ($var = 0; $var < $booking->room_qty - 1;) {
                        foreach ($check_room as $data) {
                            $data->room_no = $aa[$var];
                            $data->room_id = $booking->room_id;
                            $data->client_user = $booking->client_user;
                            $data->guest = $booking->guest;
                            $data->room_qty = $booking->room_qty;
                            $data->extra_bed_qty = $booking->extra_bed_qty;
                            $data->nationality = $booking->nationality;
                            $data->check_in = $booking->check_in;
                            $data->check_out = $booking->check_out;
                            $data->status = $data->status;
                            $data->update();
                            $var++;
                        }
                    }
                }

            } else {

                if ($booking->room_qty > $avaliable_room_qty) {
                    return back()->withErrors(['fail' => 'Booking room quantity is greater than avaliable room quantity.'])->withInput();
                }

                foreach ($request->room_no as $data) {
                    $room_schedule = new RoomSchedule();
                    $room_schedule->room_no = $data;
                    $room_schedule->room_id = $booking->room_id;
                    $room_schedule->client_user = $booking->client_user;
                    $room_schedule->guest = $booking->guest;
                    $room_schedule->room_qty = $booking->room_qty;
                    $room_schedule->extra_bed_qty = $booking->extra_bed_qty;
                    $room_schedule->nationality = $booking->nationality;
                    $room_schedule->booking_id = $booking->id;
                    $room_schedule->check_in = $booking->check_in;
                    $room_schedule->check_out = $booking->check_out;
                    $room_schedule->status = '1';
                    $room_schedule->save();
                }
            }
        }

        if ($booking->status) {
            $booking->status = $request['status'];
            $booking->payment_status = $request['payment_status'];
            $booking->update();
        } else {
            $booking->status = $request['status'];
            $booking->payment_status = $request['payment_status'];
            $booking->save();
        }

        activity()
            ->performedOn($booking)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['source' => 'Booking (Admin Panel)'])
            ->log('Booking (' . $booking->booking_number . ') is updated');

        return redirect()->route('admin.booking.index')->with('success', 'Successfully Updated.');
    }
}
