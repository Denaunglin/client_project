<?php

namespace App\Http\Resources;

use App\Helper\ResponseHelper;
use App\Http\Resources\RoomDetailResource;
use App\Models\Deposit;
use App\Models\Discounts;
use App\Models\EarlyLateCheck;
use App\Models\ExtraBedPrice;
use App\Models\Rooms;
use App\Models\Tax;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingViewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $room = Rooms::where('id', $this->id)->First();
        $room_info = new RoomDetailResource($room);
        $nights = 0;
        $user_info = null;
        $early_late = [];
        if ($request->early_late) {
            $early_late = explode(',', $request->early_late);
        }
        $check_in_date = $request->check_in_date ? $request->check_in_date : '-';
        $check_out_date = $request->check_out_date ? $request->check_out_date : '-';
        $guest = $request->guest ? intval($request->guest) : 1;
        $room_qty = $request->room_qty ? intval($request->room_qty) : 1;
        $extra_bed_qty = $request->extra_bed_qty ? intval($request->extra_bed_qty) : 0;
        $nationality = $request->nationality ? $request->nationality : 1;

        if ($request->check_in_date) {
            $nights = Carbon::parse($check_out_date)->diffInDays(Carbon::parse($check_in_date));
        }

        if (Auth::guard('api')->user()) {
            $user = Auth::guard('api')->user();
            $user_info = new ProfileResource($user);
        }

        $tax = Tax::all();
        if ($tax) {
            $tax1 = Tax::where('id', 1)->first();
            $tax2 = Tax::where('id', 2)->first();
            $commercial_percentage = $tax1->amount;
            $service_percentage = $tax2->amount;
        }

        $earlylatecheck = 0;
        if (Auth::guard('api')->user()) {
            $client_user = Auth::guard('api')->user();
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

            if (Auth::guard('api')->user()) {
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

            $extra_bed_price = $room->extra_bed_mm_price;
            $subtotal = $room_total + $extra_bed_total;
            $early_check_in = 0;
            $late_check_out = 0;
            $both_check = 0;

            if ($early_late) {
                if ($early_late == array("1", "2")) {
                    $both_check = $room->early_checkin_mm + $room->late_checkout_mm;
                    if ($earlylatecheck) {
                        $both_check = ($room->early_checkin_mm + $room->late_checkout_mm + $earlylatecheck->add_early_checkin_mm + $earlylatecheck->add_late_checkout_mm) - ($earlylatecheck->subtract_early_checkin_mm + $earlylatecheck->subtract_late_checkout_mm);
                    }
                } elseif ($early_late == array("2", "1")) {
                    $both_check = $room->early_checkin_mm + $room->late_checkout_mm;
                    if ($earlylatecheck) {
                        $both_check = ($room->early_checkin_mm + $room->late_checkout_mm + $earlylatecheck->add_early_checkin_mm + $earlylatecheck->add_late_checkout_mm) - ($earlylatecheck->subtract_early_checkin_mm + $earlylatecheck->subtract_late_checkout_mm);
                    }

                } elseif ($early_late == array("1")) {
                    $early_check_in = $room->early_checkin_mm;
                    if ($earlylatecheck) {
                        $early_check_in = ($room->early_checkin_mm + $earlylatecheck->add_early_checkin_mm) - $earlylatecheck->subtract_early_checkin_mm;
                    }
                } elseif ($early_late == array("2")) {
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

            if (Auth::guard('api')->user()) {
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

            $extra_bed_price = $room->extra_bed_foreign_price;

            $subtotal = $room_total + $extra_bed_total;
            $early_check_in = 0;
            $late_check_out = 0;
            $both_check = 0;

            if ($request->early_late) {
                if ($request->early_late == array("1", "2")) {
                    $both_check = $room->early_checkin_foreign + $room->late_checkout_foreign;
                    if ($earlylatecheck) {
                        $both_check = ($room->early_checkin_foreign + $room->late_checkout_foreign + $earlylatecheck->add_early_checkin_foreign + $earlylatecheck->add_late_checkout_foreign) - ($earlylatecheck->subtract_early_checkin_foreign + $earlylatecheck->subtract_early_checkout_foreign);
                    }

                } elseif ($request->early_late == array("2", "1")) {
                    $both_check = $room->early_checkin_foreign + $room->late_checkout_foreign;
                    if ($earlylatecheck) {
                        $both_check = ($room->early_checkin_foreign + $room->late_checkout_foreign + $earlylatecheck->add_early_checkin_foreign + $earlylatecheck->add_late_checkout_foreign) - ($earlylatecheck->subtract_early_checkin_foreign + $earlylatecheck->subtract_early_checkout_foreign);
                    }

                } elseif ($early_late == array("1")) {
                    $early_check_in = $room->early_checkin_foreign;
                    if ($earlylatecheck) {
                        $early_check_in = ($room->early_checkin_foreign + $earlylatecheck->add_early_checkin_foreign) - $earlylatecheck->subtract_early_checkin_foreign;
                    }
                } elseif ($early_late == array("2")) {
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

        $deposit = Deposit::where('id', '1')->first();
        $deposits = ($detailprice * ($deposit->deposit / 100)) * $nights;

        // if ($room->extra_bed_qty == 0) {
        //     $extra_bed_qty = "No Availiable";
        // }

        return [
            'booking_room_info' => $room_info,
            'user_info' => $user_info,
            'check_in_date' => $check_in_date,
            'check_out_date' => $check_out_date,
            'guest' => $guest,
            'night' => $nights,
            'price_per_night' => $detailprice,
            'request_extra_bed_qty' => $extra_bed_qty,
            'extra_bed_total' => $extra_bed_total,
            'early_late' => $early_late,
            'early_check_in' => intval($early_check_in),
            'late_check_out' => intval($late_check_out),
            'both_check' => $both_check,
            'service_percentage' => intval($service_percentage),
            'service_tax' => $service_tax,
            'room_qty' => $room_qty,
            'total' => $total,
            'commercial_percentage' => intval($commercial_percentage),
            'commercial_tax' => $commercial_tax,
            'grand_total' => $grand_total,
            'deposit' => $deposits,
        ];
    }
}
