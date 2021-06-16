<?php

namespace App\Http\Resources;

use App\Helper\ResponseHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class AvailableRoomQtyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $room_type = $this->roomtype ? $this->roomtype->name : '-';
        $bed_type = $this->bedtype ? $this->bedtype->name : '-';
        $price = $this->price ? $this->price : '-';
        $foreign_price = $this->foreign_price ? $this->foreign_price : '-';
        $qty = ResponseHelper::avaliable_room_qty($this, $request->date, $request->date);
        return [
            'room_type' => $room_type,
            'bed_type' => $bed_type,
            'qty' => $qty,
            'price' => $price,
            'foreign_price' => $foreign_price
        ];
    }
}
