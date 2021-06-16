<?php

namespace App\Http\Resources;

use App\Helper\ResponseHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingCalendarResource extends JsonResource
{
    public function toArray($request)
    {

        $nationality = config('app.nationality');
        $room_type = '-';

        if ($this->room) {
            $room_type = $this->room->roomtype ? $this->room->roomtype->name : '-';
            $checkin_date=$this->booking ? $this->booking->check_in : '-';
            $checkout_date=$this->booking ? $this->booking->check_out : '-';
            $avaliable_room_qty = ResponseHelper::avaliable_room_qty($this->room, $checkin_date, $checkout_date);
            
        }
        $price=0;
        $other_charges_total = 0;

        if($this->booking){
             if ($this->booking->other_services) {
            $other_charges_total = $this->booking ? $this->booking->other_charges_total : '-';
        } else {
            $other_charges_total = 0;
        }

        if ($this->booking->discount_price) {
            $price = $this->booking->discount_price ? $this->booking->discount_price : '-';
        } else {
            $price = $this->booking ? $this->booking->price : '-';
        }
        }
       
        $booking_number = $this->booking ? $this->booking->booking_number : '-';
        $check_in = $this->check_in;
        $check_out = $this->check_out;
        $room_qty = $this->booking ? $this->booking->room_qty : '-';
        $guest = $this->booking ? $this->booking->guest : '-';

        $total = $this->booking ? $this->booking->total : '-';
        $commercial_tax = $this->booking ? $this->booking->commercial_tax : '-';
        $service_tax = $this->booking ? $this->booking->service_tax : '-';
        $grand_total = $this->booking ? $this->booking->grand_total : '-';

        $name = $this->booking ? $this->booking->name : '-';
        $email = $this->booking ? $this->booking->email : '-';
        $phone = $this->booking ? $this->booking->phone : '-';
        $national = $this->booking ? $this->booking->nationality : '-';

        $extra_bed_qty = $this->booking ? $this->booking->extra_bed_qty : '-';
        $early_check_in = $this->booking ? $this->booking->early_check_in : '-';
        $late_check_out = $this->booking ? $this->booking->late_check_out : '-';
        $both_check = $this->booking ? $this->booking->both_check : '-';
        $early_checkin_time = $this->booking ? $this->booking->early_checkin_time : '-';
        $late_checkout_time = $this->booking ? $this->booking->late_checkout_time : '-';
        $extra_bed_total = $this->booking ? $this->booking->extra_bed_total : '-';

         $sign1 = '';
         $sign2 = '';
         
        if ($this->booking) {
            if ($national == 1) {
                $sign1 = '';
                $sign2 = 'MMK';
            } else {
                $sign1 = '$';
                $sign2 = '';
            }
        }

        return [
            'availiable_room_qty' => $avaliable_room_qty,
            'title' => 'Booking No : ' . $booking_number . ', Room Type : ' . $room_type,
            'booking_number' => $booking_number,
            'start' => $check_in,
            'end' => $check_out,
            'room_type' => $room_type,
            'room_qty' => $room_qty,
            'guest' => $guest,

            'price' => $price,
            'total' => $total,
            'commercial_tax' => $commercial_tax,
            'service_tax' => $service_tax,
            'grand_total' => $grand_total,

            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'nationality' => $nationality,
            'national' => $national,
            'sign1' => $sign1,
            'sign2' => $sign2,
            'extra_bed_qty' => $extra_bed_qty,
            'early_check_in' => $early_check_in,
            'late_check_out' => $late_check_out,
            'other_charges_total' => $other_charges_total,
            'both_check' => $both_check,
            'early_checkin_time' => $early_checkin_time,
            'late_checkout_time' => $late_checkout_time,
            'extra_bed_total' => $extra_bed_total,

        ];
    }
}
