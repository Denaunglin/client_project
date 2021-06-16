<?php

namespace App\Http\Controllers\Api\V1;

use App\Helper\FontConvert;
use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApiBookingStoreRequest;
use App\Http\Requests\ApiContactUsRequest;
use App\Http\Requests\ApiRetrieveBookingRequest;
use App\Http\Resources\BookingRoomResource;
use App\Http\Resources\BookingViewResource;
use App\Http\Resources\GetRoomResource;
use App\Http\Resources\PayslipResource;
use App\Http\Resources\RoomDetailResource;
use App\Http\Resources\RoomsResource;
use App\Http\Resources\RoomTypeResource;
use App\Http\Resources\SliderResource;
use App\Models\AccountType;
use App\Models\Booking;
use App\Models\BookingCalendar;
use App\Models\Discounts;
use App\Models\EarlyLateCheck;
use App\Models\ExtraBedPrice;
use App\Models\Messages;
use App\Models\OneSignalSubscriber;
use App\Models\Payslip;
use App\Models\RoomLayout;
use App\Models\Rooms;
use App\Models\RoomSchedule;
use App\Models\RoomType;
use App\Models\SliderUpload;
use App\Models\Tax;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class PageController extends Controller
{
    public function exploreRoom()
    {
        try {

            $rooms = Rooms::with('discount_types')->orderBy('id', 'desc')->where('trash', 0)->paginate(3);
            $data = RoomsResource::collection($rooms);
            return ResponseHelper::success($data);

        } catch (\Exception $e) {
            return ResponseHelper::failedMessage('Something Wrong');
        }
    }

    public function getSlider()
    {
        try {

            $sliders = SliderUpload::where('trash', '0')->get();
            $data = SliderResource::collection($sliders);
            return ResponseHelper::success($data);

        } catch (\Exception $e) {
            return ResponseHelper::failedMessage('Something Wrong');
        }
    }

    public function getRoom(Request $request)
    {
        try {
            $rooms = Rooms::where('trash', 0)->get();
            $data = GetRoomResource::collection($rooms);
            return ResponseHelper::success($data);

        } catch (\Exception $e) {

            return ResponseHelper::failedMessage('Something Wrong');

        }
    }

    public function roomList(Request $request)
    {
        try {

            $rooms = Rooms::orderBy('id', 'desc')->where('trash', 0);

            if ($request->sort) {
                if ($request->sort == 1) {
                    $rooms = Rooms::orderBy('price', 'desc')->where('trash', 0);
                } else if ($request->sort == 2) {
                    $rooms = Rooms::orderBy('price', 'asc')->where('trash', 0);
                } else {
                    $rooms = Rooms::orderBy('room_type', 'desc')->where('trash', 0);
                }
            } else {
                $rooms = Rooms::orderBy('room_type', 'desc')->where('trash', 0);
            }

            if ($request->room_type) {
                $room_type = explode(',', $request->room_type);
                $rooms = Rooms::whereIn('room_type', $room_type)->where('trash', 0);
            }

            $rooms = $rooms->paginate(7);

            return RoomsResource::collection($rooms)->additional(['result' => 1, 'message' => 'success']);

        } catch (\Exception $e) {
            return ResponseHelper::failedMessage('Something Wrong');
        }
    }

    public function roomDetail(Request $request)
    {
        try {
            $room_detail = Rooms::where('id', $request->id)->First();
            if ($room_detail) {
                $data = new RoomDetailResource($room_detail);
                return ResponseHelper::success($data);

            } else {
                return ResponseHelper::fail('Room not found');
            }

        } catch (\Exception $e) {
            return ResponseHelper::failedMessage('Something Wrong' . $e->getMessage());
        }

    }

    public function roomType()
    {
        try {
            $roomtype = RoomType::where('trash', 0)->orderBy('id', 'desc')->get();
            if ($roomtype) {
                $roomType = RoomTypeResource::collection($roomtype);
                return ResponseHelper::success($roomType);

            } else {
                return ResponseHelper::fail('Room type not found');
            }

        } catch (\Exception $e) {
            return ResponseHelper::failedMessage('Something Wrong');
        }
    }

    public function bookingCheckto(Request $request)
    {
        try {

            $room = Rooms::where('id', $request->room_id)->first();
            if ($room) {
                $data = new BookingViewResource($room);
                return ResponseHelper::success($data);
            } else {
                return ResponseHelper::fail('Room not found');
            }
        } catch (\Exception $e) {
            return ResponseHelper::failedMessage('Something Wrong');
        }

    }

    public function bookingStore(ApiBookingStoreRequest $request)
    {
        try {

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

            $room = Rooms::where('id', $request->room_id)->first();

            if ($room) {

                $join_room = $room->roomtype->join_roomtype ? $room->roomtype->join_roomtype : null;

                if ($join_room) {
                    $join_booking = Booking::where('phone', $request->phone)->where('check_in', $request->check_in)->get();
                    if ($join_booking) {
                        $count = $join_booking ? count($join_booking) : '';
                        if ($count == 0) {
                            return ResponseHelper::failedMessage('This Room is joined with Suite Room , please take a book the Suite Room first');
                        }
                    }
                }

                $membertype = 0;
                $earlylatecheck = 0;

                if (Auth::guard('api')->check()) {
                    $client_user = Auth::guard('api')->user();
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
                    return ResponseHelper::failedMessage('Your booking room qty is greater than avaliable room qty.');
                }

                if ($room->extra_bed_qty < $request->extra_bed_qty) {
                    return ResponseHelper::failedMessage('Your booking extra bed qty is greater than avaliable extra bed qty.');
                }

                $nights = Carbon::parse($request->check_out)->diffInDays(Carbon::parse($request->check_in));

                if ($request->nationality == 1) {

                    $extra_bed_total = ($request->extra_bed_qty * $room->extra_bed_mm_price) * $nights;
                    if (Auth::guard('api')->user()) {
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
                    if (Auth::guard('api')->user()) {
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

                if (Auth::guard('api')->check()) {
                    $account_type = $client_user->accounttype->id;
                    $account = AccountType::where('id', $account_type)->first();
                    $commission_percentage = $account->commission;
                    $commission = (($total + $commercial_tax) - ($early_check_in + $late_check_out + $both_check)) * ($account->commission / 100);
                }

                $booking = new Booking();
                $booking->room_id = $room->id;

                if (Auth::guard('api')->check()) {
                    $booking->client_user = $client_user->id;
                } else {
                    $booking->client_user = $request['client_user'];

                }
                $booking->name = $request->name;
                $booking->email = $request->email;
                $booking->phone = $request->phone;
                $booking->nrc_passport = $request->nrc_passport;
                $booking->message = $request->message;
                $booking->member_type = $membertype;
                $booking->commission = number_format($commission, 2, '.', '');
                $booking->commission_percentage = number_format($commission_percentage, 2, '.', '');
                $booking->price = number_format($price, 2, '.', '');

                if (Auth::guard('api')->check()) {
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

                if ($request->room_qty <= $room->autoconfirm) {
                    $roomlayout = RoomLayout::where('room_id', $room->id)->where('maintain', '0')->get();
                    $check_room = RoomSchedule::where('trash', 0)->where('room_id', $room->id)->where('check_in', $request->check_in)->whereIn('status', [1, 2, 3])->orwhere('check_out', '>', $request->check_in)->where('check_in', '<', $request->check_in)->where('trash', 0)->whereIn('status', [1, 2, 3])->get();

                    $aa = [];
                    foreach ($check_room as $item) {
                        $aa[] = [$item->room_no];
                    }

                    $availiable_room_no = RoomLayout::where('room_id', $room->id)->where('maintain', '0')->whereNotIn('id', $aa)->get();

                    $chunk = $availiable_room_no ? $availiable_room_no->chunk($request->room_qty) : null;

                    if (count($chunk) == 0) {
                        return ResponseHelper::failedMessage('This Room is not avaliable to book , please take another room .');
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
                    } else {
                        return ResponseHelper::failedMessage('Room not Availiable');
                    }
                }

                $booking_number = config('app.booking_prefix') . '_' . $booking->id;
                $booking->booking_number = $booking_number;
                $booking->update();

                if ($booking) {
                    $data = ['booking_no' => $booking->booking_number, 'room_type' => $booking->room->id, 'phone' => $booking->phone, 'checkin_checkout' => $booking->check_in . ' - ' . $booking->check_out];
                }

            } else {
                return ResponseHelper::failedMessage('Room not found');
            }
            return ResponseHelper::success($data, "You have been booked successfully");

        } catch (\Exception $e) {
            return ResponseHelper::failedMessage('Something Wrong' . $e->getMessage());
        }

    }

    public function payslipStore(Request $request)
    {
        try {

            $booking = Booking::where('booking_number', $request->booking_number)->first();

            if ($booking) {
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
                    ]);

                if ($payslip_data) {
                    $payslipdata = new PayslipResource($payslip_data);
                } else {
                    $payslipdata = null;
                }

                return ResponseHelper::success($payslipdata);

            } else {
                return ResponseHelper::failedMessage('Booking not found !');
            }

        } catch (\Exception $e) {
            return ResponseHelper::failedMessage('Something Wrong' . $e->getMessage());
        }
    }

    public function bookingRetrieve(ApiRetrieveBookingRequest $request)
    {
        try {
            $checkin_checkout = explode(' - ', $request->checkin_checkout);
            $q1 = $request->room_type;
            $q2 = $checkin_checkout[0];
            $q3 = $checkin_checkout[1];
            $q4 = $request->phone;

            $booking = Booking::with('room')->where('booking_number', $request->booking_no)->where('room_id', $q1)->where('check_in', $q2)->where('check_out', $q3)->where('phone', $q4)->first();

            if ($booking) {
                $data = new BookingRoomResource($booking);
                return ResponseHelper::success($data);
            }
            return ResponseHelper::failedMessage('Your booking not found. Please check your booking information.');
        } catch (\Exception $e) {
            return ResponseHelper::failedMessage('Something Wrong' . $e->getMessage());
        }
    }

    public function contactUs(ApiContactUsRequest $request)
    {
        try {

            $mail = new Messages();
            $mail->name = FontConvert::zg2uni($request->name);
            $mail->email = $request['email'];
            $mail->phone = $request['phone'];
            $mail->message = FontConvert::zg2uni($request->message);
            $mail->save();
            return ResponseHelper::successMessage('Successfully sent your message');

        } catch (\Exception $e) {
            return ResponseHelper::failedMessage();
        }
    }

    public function saveNoti(Request $request)
    {
        try {

            $user = Auth::guard('api')->user();
            $notis = OneSignalSubscriber::where('user_id', $user->id)->where('signal_id', $request->signal_id)->first();

            if ($notis) {
                $notis->user_id = $user->id;
                $notis->signal_id = $request['signal_id'];
                $notis->browser = $request['browser'];
                $notis->update();

                return ResponseHelper::success($notis);
            } else {
                $noti = new OneSignalSubscriber();
                $noti->user_id = $user->id;
                $noti->signal_id = $request['signal_id'];
                $noti->browser = $request['browser'];
                $noti->save();
                return ResponseHelper::successMessage();
            }

        } catch (\Exception $e) {

            return ResponseHelper::failedMessage('Something Wrong' . $e->getMessage());
        }

    }

    public function unsubscribeNoti(Request $request)
    {
        try {

            $user = Auth::guard('api')->user()->id;
            $notis = OneSignalSubscriber::where('user_id', $user)->where('signal_id', $request->signal_id)->first();
            if ($notis) {
                $notis->delete();
                return ResponseHelper::successMessage();
            } else {
                return ResponseHelper::failedMessage('Subscriber not found !');
            }

        } catch (\Exception $e) {
            return ResponseHelper::failedMessage('Something Wrong' . $e->getMessage());
        }

    }
}
