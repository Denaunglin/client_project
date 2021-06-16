<?php

namespace App\Http\Resources;

use App\Helper\ResponseHelper;
use App\Http\Resources\RoomGalleryResource;
use App\Models\Booking;
use App\Models\Discounts;
use App\Models\showGallery;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class RoomDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $room_limit = 0;
        $booking_limit = 0;
        $detailprice = [0, 0];
        $avaliable_room_qty = 0;
        $discount_percentage = 0;

        if (Auth::guard('api')->check()) {
            $user = Auth::guard('api')->user();
            if ($user->accounttype->booking_limit == 1) {
                $room_limit = 1;
                $avaliable_room_qty = 1;
                $today = Carbon::now()->format('Y-m-d');
                $booking_limit = Booking::where('client_user', $user->id)->where('check_out', '>', $today)->where('payment_status', '0')->get()->count();
            }
        }

        $image = $this->image_path();
        $gallery = showGallery::where('rooms_id', $this->id)->get();
        if ($gallery) {
            $room_gallery = RoomGalleryResource::collection($gallery);
        }

        $facilitiesdata = config('app.facilities');
        $facilities = \unserialize($this->facilities);
        $check_in_date = $request->check_in_date ? $request->check_in_date : '';
        $check_out_date = $request->check_out_date ? $request->check_out_date : '';
        $room_qty = $request->room_qty ? $request->room_qty : 1;
        $guest = $request->guest ? $request->guest : 1;
        $extra_bed_qty = $request->extra_bed_qty ? $request->extra_bed_qty : 0;
        $nationality = $request->nationality ? $request->nationality : 1;
        $roomtype = $this->roomtype ? $this->roomtype->name : '-';
        $bedtype = $this->bedtype ? $this->bedtype->name : '-';
        $avaliable_room_qty = ResponseHelper::avaliable_room_qty($this, $check_in_date, $check_out_date);

        if ($nationality == 1) {
            $price = $this->price;
            $extrabedprice = $this->extra_bed_mm_price;

        } else {
            $price = $this->foreign_price;
            $extrabedprice = $this->extra_bed_foreign_price;

        }

        if (Auth::guard('api')->check()) {

            $client_user = Auth::guard('api')->user();
            $account_type = $client_user->accounttype->id;
            $discount_type = Discounts::where('user_account_id', $account_type)->where('room_type_id', $this->id)->first();
            $detailprice = ResponseHelper::roomschedulediscount($this, $nationality, $client_user, $discount_type);

            if ($nationality == 1) {
                $price = $this->price;
                $discount_percentage = $discount_type ? $discount_type->discount_percentage_mm : 0;
            } else {
                $price = $this->foreign_price;
                $discount_percentage = $discount_type ? $discount_type->discount_percentage_foreign : 0;

            }

        }

        $available_facilities = [];
        $notavailable_facilities = [];
        $fact_id = [];

        foreach ($facilities as $fac_data) {
            $available_facilities[] = ['id' => $fac_data, 'name' => $facilitiesdata[$fac_data]];
            $fact_id[] = $fac_data;
        }

        $facilitiesdata_key = [];
        foreach ($facilitiesdata as $key => $fac_data) {
            $facilitiesdata_key[] = "$key";
        }
        $collection1 = collect($facilitiesdata_key);
        $collection2 = collect($fact_id);
        $diff = $collection1->diff($collection2);

        foreach ($diff as $data) {
            $notavailable_facilities[] = ['id' => $data, 'name' => $facilitiesdata[$data]];
        }

        if ($booking_limit == 0) {
            $booking_limit_msg = "";
        } else {
            $booking_limit_msg = "Cann't Book Right Now ! (Member rate can use only once !)";
        }

        if ($price > $detailprice[0]) {
            $discount_price = $detailprice[0];
            $addon_price = $detailprice[1];
        } elseif ($price = $detailprice[0]) {
            $discount_price = 0;
            $addon_price = $detailprice[1];
        }

        return [
            'room_id' => $this->id,
            'room_limit' => $room_limit,
            'booking_limit' => $booking_limit,
            'booking_limit_msg' => $booking_limit_msg,
            'image' => $image,
            'room_qty' => $this->room_qty,
            'room_price' => intval($price),
            'addon_price' => $addon_price,
            'discount_price' => $discount_price,
            'discount_percentage' => intval($discount_percentage),
            'room_gallery' => $room_gallery ?? null,
            'room_type' => $roomtype,
            'bed_type' => $bedtype,
            'guest_qty' => $this->adult_qty,
            'extra_bed_qty' => $this->extra_bed_qty,
            'extra_bed_price' => intval($extrabedprice),
            'price' => intval($price),
            'description' => $this->description,
            'available_facilities' => $available_facilities,
            'notavailable_facilities' => $notavailable_facilities,
            'nationality' => intval($nationality),
            'avaliable_room_qty' => $avaliable_room_qty,

        ];
    }
}
