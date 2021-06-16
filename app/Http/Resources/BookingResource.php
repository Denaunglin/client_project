<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $room = $this->room->roomtype ? $this->room->roomtype->name : null;
        $image = $this->room ? $this->room->image_path() : null;

        if ($this->status == 0) {
            $color_code = "007bff";
        } elseif ($this->status == 1) {
            $color_code = "28a745";
        } elseif ($this->status == 2) {
            $color_code = "dc3545";
        } elseif ($this->status == 3) {
            $color_code = "28a745";
        }

        $status = config('app.status');

        return [
            'booking_id' => $this->id,
            'booking_number' => $this->booking_number,
            'room' => $room,
            'nationality' => $this->nationality,
            'grand_total' => $this->grand_total,
            'check_in_show' => date('d-M-Y', strtotime($this->check_in)),
            'check_in' => $this->check_in,
            'check_out_show' => date('d-M-Y', strtotime($this->check_out)),
            'check_out' => $this->check_out,
            'status_show' => $status[$this->status],
            'status' => $this->status,
            'status_color_code' => $color_code,
            'image' => $image,
        ];
    }
}
