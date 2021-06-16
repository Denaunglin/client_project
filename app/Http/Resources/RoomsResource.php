<?php

namespace App\Http\Resources;

use App\Helper\ResponseHelper;
use App\Models\Discounts;
use App\Models\RoomType;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class RoomsResource extends JsonResource
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
        $detailprice = [0, 0];
        $alert = '-';
        $check_in_date = $request->check_in_date ? $request->check_in_date : '';
        $check_out_date = $request->check_out_date ? $request->check_out_date : '';
        $room_qty = $request->room_qty ? intval($request->room_qty) : 1;
        $extra_bed_qty = $request->extra_bed_qty ? intval($request->extra_bed_qty) : 0;
        $nationality = $request->nationality ? intval($request->nationality) : 1;
        $guest = $request->guest ? $request->guest : 1;
        $room_type = $request->room_type ? $request->room_type : '';
        $image = $this->image_path();
        $request_data = ["check_in_date" => $check_in_date, "check_out_date" => $check_out_date, "room_qty" => $room_qty, "extra_bed_qty" => $extra_bed_qty, "nationality" => $nationality, "guest" => $guest];
        $avaliable_room_qty = ResponseHelper::avaliable_room_qty($this, $check_in_date, $check_out_date);

        if (Auth::guard('api')->check()) {
            $client_user = Auth::guard('api')->user();

            if ($client_user->accounttype->limit = 0) {
                $room_limit = 1;
            }

            $account_type = $client_user->accounttype->id;
            $discount_type = Discounts::where('user_account_id', $account_type)->where('room_type_id', $this->id)->first();
            $detailprice = ResponseHelper::roomschedulediscount($this, $nationality, $client_user, $discount_type);
        }
        if ($nationality == 1) {
            $price = $this->price;
        } else {
            $price = $this->foreign_price;
        }

        if ($room_qty > $avaliable_room_qty) {
            $alert = 'Room Qty is not enough !.';
        }
        if ($avaliable_room_qty == 0) {
            $alert = 'Not Available';

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
            'room_type' => $this->roomtype ? $this->roomtype->name : '-',
            'room_price' => intval($price),
            'addon_price' => $addon_price,
            'discount_price' => $discount_price,
            'bed_type' => $this->bedtype ? $this->bedtype->name : '-',
            'guest' => $this->adult_qty ?? 0,
            'avaliable_room_qty' => $avaliable_room_qty ?? 0,
            'alert' => $alert,
            'image' => $image,
            'nationality' => $nationality,
            'request_data' => $request_data,
        ];
    }
}
