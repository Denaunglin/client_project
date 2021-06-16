<?php

namespace App\Http\Controllers\Frontend;

use App\Helper\FontConvert;
use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBooking;
use App\Models\AccountType;
use App\Models\Booking;
use App\Models\BookingCalendar;
use App\Models\CardType;
use App\Models\Deposit;
use App\Models\Discounts;
use App\Models\EarlyLateCheck;
use App\Models\ExtraBedPrice;
use App\Models\Payslip;
use App\Models\RoomLayout;
use App\Models\Rooms;
use App\Models\RoomSchedule;
use App\Models\showGallery;
use App\Models\Tax;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Storage;

class BookingController extends Controller
{
    public function bookingView($id, Request $request)
    {
        $early_late_data = $request->early_late ? $request->early_late : '-';
        $month = config('app.month');
        $year = config('app.year');
        $cardtype = CardType::where('trash', '0')->get();

        $tax = Tax::all();
        if ($tax) {
            $tax1 = Tax::where('id', 1)->first();
            $tax2 = Tax::where('id', 2)->first();
            $commercial_percentage = $tax1->amount;
            $service_percentage = $tax2->amount;
        }

        $extra_bed_qty = $request['extra_bed_qty'];
        $app = config('app.facilities');
        $facilities = config('app.facilities');
        $room = Rooms::findOrFail($id);
        $facilities = $room->facilities ? unserialize($room->facilities) : [];
        $gallery = showGallery::where('rooms_id', $id)->get();
        $nights = Carbon::parse(request()->check_out_date)->diffInDays(Carbon::parse(request()->check_in_date));
        $avaliable_room_qty = ResponseHelper::avaliable_room_qty($room, $request->check_in_date, $request->check_out_date);

        if ($avaliable_room_qty < $request->room_qty) {
            return back();
        }
        $earlylatecheck = 0;
        if (Auth::user()) {
            $client_user = Auth::user();
            $accounttype = $client_user->accounttype->id;
            $discount_type = Discounts::where('trash', '0')->where('user_account_id', $accounttype)->where('room_type_id', $room->id)->first();
            $extrabedprice = ExtrabedPrice::where('trash', '0')->where('user_account_id', $accounttype)->first();
            $detailprices = ResponseHelper::roomschedulediscount($room, $request->nationality, $client_user, $discount_type);
            $earlylatecheck = EarlyLateCheck::where('trash', '0')->where('user_account_id', $accounttype)->first();
            $detailprice = $detailprices['0'];
            $addon = $detailprices['1'];
        } else {
            $detailprice = ResponseHelper::sale_price($room, $request->nationality);
            $addon = $detailprice;
        }

        $extra_bed_total = 0;

        if ($request->nationality == 1) {
            if ($room->extra_bed_qty) {
                if ($room->extra_bed_mm_price) {
                    $extra_bed_total = ($request->extra_bed_qty * $room->extra_bed_mm_price) * $nights;
                }
            }

            if (Auth::user()) {
                if ($extrabedprice) {
                    $extra_bed_total = ($request->extra_bed_qty * (($room->extra_bed_mm_price + $extrabedprice->add_extrabed_price_mm) - $extrabedprice->subtract_extrabed_price_mm)) * $nights;
                }
                $room_total = ($request->room_qty * $detailprice) * $nights;
                $discount_price = $detailprice;
                $price = $addon;
            } else {
                $room_total = ($request->room_qty * $room->price) * $nights;
                $price = $addon;
            }

            $sign1 = '';
            $sign2 = 'MMK';

            $extra_bed_price = $room->extra_bed_mm_price;
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

            if ($room->extra_bed_qty) {
                if ($room->extra_bed_foreign_price) {
                    $extra_bed_total = ($request->extra_bed_qty * $room->extra_bed_foreign_price) * $nights;
                }
            }

            if (Auth::user()) {
                if ($extrabedprice) {
                    $extra_bed_total = ($request->extra_bed_qty * (($room->extra_bed_foreign_price + $extrabedprice->add_extrabed_price_foreign) - $extrabedprice->subtract_extrabed_price_foreign)) * $nights;
                }
                $room_total = ($request->room_qty * $detailprice) * $nights;
                $discount = $detailprice;
                $price = $addon;
            } else {
                $room_total = ($request->room_qty * $room->foreign_price) * $nights;
                $price = $addon;
            }

            $sign1 = '$';
            $sign2 = '';

            $extra_bed_price = $room->extra_bed_foreign_price;

            $subtotal = $room_total + $extra_bed_total;
            $early_check_in = 0;
            $late_check_out = 0;
            $both_check = 0;

            if ($request->early_late) {
                if ($request->early_late == array(1, 2)) {
                    $both_check = $room->early_checkin_foreign + $room->late_checkout_foreign;
                    if ($earlylatecheck) {
                        $both_check = ($room->early_checkin_foreign + $room->late_checkout_foreign + $earlylatecheck->add_early_checkin_foreign + $earlylatecheck->add_late_checkout_foreign) - ($earlylatecheck->subtract_early_checkin_foreign + $earlylatecheck->subtract_early_checkout_foreign);
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

        $service = $subtotal * ($tax2->amount / 100);
        $service_tax = round($service, 2);
        $total = $subtotal + $service_tax + $early_check_in + $late_check_out + $both_check;
        $commercial = $total * ($tax1->amount / 100);
        $commercial_tax = round($commercial, 2);
        $grand_total = $total + $commercial_tax;

        if ($room->extra_bed_qty == 0) {
            $extra_bed_qty = "No Availiable";
        }

        $deposit = Deposit::where('id', '1')->first();

        return view('frontend.booking', compact('commercial_percentage', 'service_percentage', 'early_late_data', 'deposit', 'sign1', 'sign2', 'price', 'room', 'gallery', 'app', 'facilities', 'nights', 'total', 'commercial_tax', 'service_tax', 'grand_total', 'avaliable_room_qty', 'extra_bed_qty', 'extra_bed_total', 'extra_bed_price', 'month', 'year', 'cardtype', 'detailprice', 'subtotal', 'early_check_in', 'late_check_out', 'both_check'));
    }

    public function bookingStore($id, StoreBooking $request)
    {
        $commercial_tax_obj = Tax::find(1);
        $service_charges_obj = Tax::find(2);
        $tax1 = 0;
        $tax2 = 0;

        if ($commercial_tax_obj) {
            $tax1 = $commercial_tax_obj->amount ? $commercial_tax_obj->amount / 100 : 0;
        }

        if ($service_charges_obj) {
            $tax2 = $service_charges_obj->amount ? $service_charges_obj->amount / 100 : 0;
        }

        $room = Rooms::findOrFail($id);
        $layout_count = count($room->roomlayout);

        if ($layout_count == 0) {
            return back()->withErrors(['fail' => 'This Room cannot book right now ! .'])->withInput();
        }
        $join_room = $room->roomtype->join_roomtype;

        if ($join_room) {
            $join_booking = Booking::where('phone', $request->phone)->where('check_in', $request->check_in)->get();
            if ($join_booking) {
                $count = $join_booking ? count($join_booking) : '';
                if ($count == 0) {
                    return back()->withErrors(['fail' => 'This Room is joined with Suite Room , please take a book the Suite Room first .'])->withInput();
                }
            }
        }

        $membertype = 0;
        $earlylatecheck = 0;
        if (Auth::user()) {
            $client_user = auth()->user();
            $accounttype = $client_user->accounttype->id;
            $membertype = $client_user->accounttype->name;
            $extrabedprice = ExtraBedPrice::where('user_account_id', $accounttype)->first();
            $earlylatecheck = EarlyLateCheck::where('trash', '0')->where('user_account_id', $accounttype)->first();
            $discount_type = Discounts::where('trash', '0')->where('user_account_id', $accounttype)->where('room_type_id', $room->id)->first();
            $detailprices = ResponseHelper::roomschedulediscount($room, $request->nationality, $client_user, $discount_type);
            $detailprice = $detailprices['0'];
            $addon = $detailprices['1'];

        } else {

            $detailprice = ResponseHelper::sale_price($room, $request->nationality);
            $addon = $detailprice;
        }

        $avaliable_room_qty = ResponseHelper::avaliable_room_qty($room, $request->check_in, $request->check_out);

        if ($avaliable_room_qty < $request->room_qty) {
            return back()->withErrors(['fail' => 'Your booking room qty is greater than avaliable room qty.'])->withInput();
        }

        if ($room->extra_bed_qty < $request->extra_bed_qty) {
            return back()->withErrors(['fail' => 'Your booking extra bed qty is greater than avaliable extra bed qty.'])->withInput();
        }

        $nights = Carbon::parse($request->check_out)->diffInDays(Carbon::parse($request->check_in));

        if ($request->nationality == 1) {

            $extra_bed_total = ($request->extra_bed_qty * $room->extra_bed_mm_price) * $nights;
            if (Auth::user()) {
                if ($extrabedprice) {
                    $extra_bed_total = ($request->extra_bed_qty * (($room->extra_bed_mm_price + $extrabedprice->add_extrabed_price_mm) - $extrabedprice->subtract_extrabed_price_mm)) * $nights;
                }
            }
            $room_total = ($request->room_qty * $detailprice) * $nights;
            $discount_price = $detailprice;
            $price = $addon;
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
            $extra_bed_total = ($request->extra_bed_qty * $room->extra_bed_foreign_price) * $nights;
            if (Auth::user()) {
                if ($extrabedprice) {
                    $extra_bed_total = ($request->extra_bed_qty * (($room->extra_bed_foreign_price + $extrabedprice->add_extrabed_price_foreign) - $extrabedprice->subtract_extrabed_price_foreign)) * $nights;
                }
            }

            $room_total = ($request->room_qty * $detailprice) * $nights;
            $discount_price = $detailprice;
            $price = $addon;
            $subtotal = $room_total + $extra_bed_total;
            $early_check_in = 0;
            $late_check_out = 0;
            $both_check = 0;
            if ($request->early_late) {
                if ($request->early_late == array(1, 2)) {
                    $both_check = $room->ealry_checkin_foreign + $room->late_checkout_foreign;
                    if ($earlylatecheck) {
                        $both_check = ($room->ealry_checkin_foreign + $room->late_checkout_foreign + $earlylatecheck->add_early_checkin_foreign + $earlylatecheck->add_late_checkout_foreign) - ($earlylatecheck->subtract_early_checkin_foreign + $earlylatecheck->subtract_late_checkout_foreign);
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
        $total = $subtotal + $service_tax + $early_check_in + $late_check_out + $both_check;
        $commercial_tax = $total * $tax1;
        $grand_total = $total + $commercial_tax;

        $commission = 0;
        $commission_percentage = 0;
        $user_id = null;
        if (Auth::user()) {
            $account_type = $client_user->accounttype->id;
            $account = AccountType::where('id', $account_type)->first();
            $user_id = auth()->user()->id;
            $commission_percentage = $account->commission;
            $commission = (($total + $commercial_tax) - ($early_check_in + $late_check_out + $both_check)) * ($account->commission / 100);
        }

        $booking = new Booking();
        $booking->room_id = $room->id;
        $booking->client_user = $user_id;
        $booking->name = FontConvert::zg2uni($request->name);
        $booking->email = $request->email;
        $booking->phone = $request->phone;
        $booking->nrc_passport = $request->nrc_passport;
        $booking->message = FontConvert::zg2uni($request->message);
        $booking->member_type = $membertype;
        $booking->commission = number_format($commission, 2, '.', '');
        $booking->commission_percentage = number_format($commission_percentage, 2, '.', '');
        $booking->price = number_format($price, 2, '.', '');

        if (Auth::user()) {
            $booking->discount_price = number_format($discount_price, 2, '.', '');
        } else {
            $booking->discount_price = number_format($price, 2, '.', '');
        }

        $booking->nationality = $request->nationality;
        $booking->total = number_format($total, 2, '.', '');
        $booking->commercial_tax = number_format($commercial_tax, 2, '.', '');
        $booking->service_tax = number_format($service_tax, 2, '.', '');
        $booking->grand_total = number_format($grand_total, 2, '.', '');
        $booking->room_qty = $request->room_qty;
        $booking->extra_bed_qty = $request->extra_bed_qty;

        if ($request->early_late) {
            $booking->early_late = serialize($request->early_late);
        } else {
            $booking->early_late = null;
        }
        $booking->early_check_in = number_format($early_check_in, 2, '.', '');
        $booking->late_check_out = number_format($late_check_out, 2, '.', '');
        $booking->both_check = number_format($both_check, 2, '.', '');
        $booking->extra_bed_total = number_format($extra_bed_total, 2, '.', '');
        $booking->guest = $request->guest;
        $booking->pay_method = $request->pay_method;

        if ($request->pay_method == 1) {

            $booking->credit_type = $request->credit_type;
            $booking->credit_no = $request->credit_no;
            $booking->expire_month = $request->expire_month;
            $booking->expire_year = $request->expire_year;
        }

        $booking->check_in = $request->check_in;
        $booking->check_out = $request->check_out;
        $booking->save();
        $booking_number = config('app.booking_prefix') . '_' . $booking->id;
        $booking->booking_number = $booking_number;
        $booking->update();

        $room = Rooms::findOrFail($id);

        if ($request->room_qty <= $room->autoconfirm) {
            $roomlayout = RoomLayout::where('room_id', $room->id)->where('maintain', '0')->get();

            $check_room = RoomSchedule::where('trash', 0)->where('room_id', $room->id)->where('check_in', $request->check_in)->whereIn('status', [1, 2, 3])->orwhere('check_out', '>', $request->check_in)->where('check_in', '<', $request->check_in)->where('trash', 0)->whereIn('status', [1, 2, 3])->get();
            $aa = [];
            foreach ($check_room as $item) {
                $aa[] = [$item->room_no];
            }

            $availiable_room_no = $roomlayout = RoomLayout::where('room_id', $room->id)->where('maintain', '0')->whereNotIn('id', $aa)->get();
            $chunk = $availiable_room_no ? $availiable_room_no->chunk($request->room_qty) : null;
            if (count($chunk) == 0) {
                return back()->withErrors(['fail' => 'This Room is not avaliable to book , please take another room .'])->withInput();
            }

            if ($chunk) {

                $aa = $chunk['0'];
                foreach ($aa as $data) {
                    $room_schedule = new RoomSchedule();
                    $room_schedule->room_no = $data->id;
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
                $calendar = new BookingCalendar();
                $calendar->booking_id = $booking->id;
                $calendar->room_id = $booking->room_id;
                $calendar->room_qty = $booking->room_qty;
                $calendar->check_in = $booking->check_in;
                $calendar->check_out = $booking->check_out;
                $calendar->save();

                $booking->status = '1';
                $booking->update();
            }

        }

        $booking_number = config('app.booking_prefix') . '_' . $booking->id;
        $booking->booking_number = $booking_number;
        $booking->update();

        return redirect('/booking/retrieve/detail?booking_no=' . $booking_number . '&room_type=' . $room->id . '&phone=' . $booking->phone . '&checkin_checkout=' . $booking->check_in . ' - ' . $booking->check_out)->with('booking-success', 'Successfully booked.');
    }

    public function payslipUpload(Request $request)
    {

        $booking = Booking::where('id', $request->booking_id)->first();
        $payslips = Payslip::where('booking_no', $booking->booking_number)->get();
        $payslip = $payslips->last();

        if ($request->hasFile('payslip_image')) {
            $image_file = $request->file('payslip_image');
            $image_name = time() . '_' . uniqid() . $booking->booking_number . '.' . $image_file->getClientOriginalExtension();
            Storage::put(
                'uploads/payslip/' . $image_name,
                file_get_contents($image_file->getRealPath())
            );

            $file_path = public_path('storage/uploads/payslip/' . $image_name);
            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->setTimeout(10)->optimize($file_path);

        } else {
            $image_name = $payslip->payslip_image;
        }

        $payslip_data = Payslip::updateOrCreate(
            [
                'payslip_image' => $image_name,
            ],
            [
                'booking_no' => $booking->booking_number,
                'payslip_image' => $image_name,
                'remark' => FontConvert::zg2uni($request->remark),
            ]
        );

        return redirect()->back()->with(['success' => "Successfully upload your payslip"], ['payslip_uploaded' => 'uploaded']);
    }

    public function Cancellation(Request $request)
    {
        $booking = Booking::where('id', $request->id)->first();
        $booking->cancellation = '1';
        $booking->status = '2';
        $booking->cancellation_remark = FontConvert::zg2uni($request->cancellation_remark);
        $booking->update();

        $roomschedule = Roomschedule::where('booking_id', $request->id)->get();
        if ($roomschedule) {
            foreach ($roomschedule as $data) {
                $data->delete();
            }
        }

        return redirect()->back();
    }
}
