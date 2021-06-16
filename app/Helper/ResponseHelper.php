<?php

namespace App\Helper;

use App\Models\Booking;
use App\Models\RoomLayout;
use Auth;

class ResponseHelper
{
    public static function success($data = [], $message = 'success')
    {
        return response()->json([
            'result' => 1,
            'message' => $message,
            'data' => $data,
        ]);
    }   

    public static function fail($data = [], $message = 'fail')
    {
        return response()->json([
            'result' => 0,
            'message' => $message,
            'data' => $data,
        ]);
    }

    public static function successMessage($message = "success")
    {
        return response()->json([
            'result' => 1,
            'message' => $message,
        ]);
    }

    public static function failedMessage($message = "fail")
    {
        return response()->json([
            'result' => 0,
            'message' => $message,
        ]);
    }

    public static function sale_price($room, $nationality)
    {

        $room_price = $nationality == 1 ? $room->price : $room->foreign_price;
        $dis1 = 0;
        $dis2 = 0;
        $addon1 = 0;
        $addon2 = 0;

        $price = ($room_price + $addon1 + $addon2) - ($dis1 + $dis2);
        return $price;
    }

    public static function price($room, $nationality, $discount_type)
    {

        $room_price = $nationality == 1 ? $room->price : $room->foreign_price;
        $dis1 = 0;
        $dis2 = 0;
        $addon1 = 0;
        $addon2 = 0;

        if (auth()->check()) {
            $user = auth()->user();
            $account_type = $user->accounttype;
            $dis_percentage = 0;
            $dis_amount = 0;
            $addon_percentage = 0;
            $addon_amount = 0;
            $dis1 = 0;
            $dis2 = 0;
            $addon1 = 0;
            $addon2 = 0;

            if ($discount_type->where('trash', '0')->get()) {
                $dis_percentage = $nationality == 1 ? $discount_type->discount_percentage_mm : $discount_type->discount_percentage_foreign;
                $dis_amount = $nationality == 1 ? $discount_type->discount_amount_mm : $discount_type->discount_amount_foreign;
                $addon_percentage = $nationality == 1 ? $discount_type->addon_percentage_mm : $discount_type->addon_percentage_foreign;
                $addon_amount = $nationality == 1 ? $discount_type->addon_amount_mm : $discount_type->addon_amount_foreign;
                $dis1 = $room_price * ($dis_percentage / 100);
                $dis2 = $dis_amount;
                $addon1 = $room_price * ($addon_percentage / 100);
                $addon2 = $addon_amount;
            }

            $discount = ($room_price + $addon1 + $addon2) - ($dis1 + $dis2);
            $addon = $room_price + $addon1 + $addon2;

        } else {
            $discount = ($room_price + $addon1 + $addon2) - ($dis1 + $dis2);
            $addon = $room_price + $addon1 + $addon2;
        }
        if ($addon == $discount) {
            $price = $discount;
        } else {
            $price = '<s class="text-warning">' . $addon . ' </s> &nbsp;  ~ &nbsp;' . $discount . '&nbsp;';
        }

        return $price;
    }

    public static function roomschedulediscount($room, $nationality, $client_user, $discount_type)
    {
        $room_price = $nationality == 1 ? $room->price : $room->foreign_price;
        $dis1 = 0;
        $dis2 = 0;
        $addon1 = 0;
        $addon2 = 0;

        if ($client_user) {
            $user = $client_user;
            $account_type = $user->accounttype;
            $dis_percentage = 0;
            $dis_amount = 0;
            $addon_percentage = 0;
            $addon_amount = 0;
            $dis1 = 0;
            $dis2 = 0;
            $addon1 = 0;
            $addon2 = 0;

            if ($discount_type) {
                $dis_percentage = $nationality == 1 ? $discount_type->discount_percentage_mm : $discount_type->discount_percentage_foreign;
                $dis_amount = $nationality == 1 ? $discount_type->discount_amount_mm : $discount_type->discount_amount_foreign;
                $addon_percentage = $nationality == 1 ? $discount_type->addon_percentage_mm : $discount_type->addon_percentage_foreign;
                $addon_amount = $nationality == 1 ? $discount_type->addon_amount_mm : $discount_type->addon_amount_foreign;
                $dis1 = $room_price * ($dis_percentage / 100);
                $dis2 = $dis_amount;
                $addon1 = $room_price * ($addon_percentage / 100);
                $addon2 = $addon_amount;
            }
        }
        $price = ($room_price + $addon1 + $addon2) - ($dis1 + $dis2);
        $addon = $room_price + $addon1 + $addon2;

        return [$price, $addon];

    }

    public static function avaliable_room_qty($room, $check_in_date, $check_out_date)
    {
        $booking_room_qty = 0;
        $maintain_count = 0;

        $maintain = RoomLayout::where('room_id', $room->id)->where('maintain', '1')->get();
        if ($maintain) {
            $maintain_count = count($maintain);
        }

        $booking_room_qty1 = Booking::with('roomscheduledata')->where('room_id', $room->id)
            ->where('trash', 0)
            ->where('check_in', '>=', $check_in_date)
            ->where('check_in', '<=', $check_out_date)
        // ->where('status', 1)
            ->get();

        foreach ($booking_room_qty1 as $each) {
            $booking_room_qty += count(collect($each->roomscheduledata)->whereIn('status', [1, 2, 3]));
        }

        $booking_room_qty2 = Booking::with('roomscheduledata')->where('room_id', $room->id)
            ->where('trash', 0)
            ->where('check_in', '<', $check_in_date)
            ->where('check_out', '>', $check_in_date)
        // ->where('status', 1)
            ->get();

        foreach ($booking_room_qty2 as $each) {
            $booking_room_qty += count(collect($each->roomscheduledata)->whereIn('status', [1, 2, 3]));
        }

        // $booking_room_qty1 = Booking::where('room_id', $room->id)
        //     ->where('trash', 0)
        //     ->where('check_in', '>=', $check_in_date)
        //     ->where('check_in', '<', $check_out_date)
        //     ->where('status', 1)
        //     ->get()
        //     ->sum('room_qty');

        // $booking_room_qty2 = Booking::where('room_id', $room->id)
        //     ->where('trash', 0)
        //     ->where('check_in', '<', $check_in_date)
        //     ->where('check_out', '>', $check_in_date)
        //     ->where('status', 1)
        //     ->get()
        //     ->sum('room_qty');

        $avaliable_room_qty = 0;
        if ($booking_room_qty < $room->room_qty) {
            $avaliable_room_qty = $room->room_qty - $booking_room_qty - $maintain_count;
        }

        return $avaliable_room_qty;
    }
}
