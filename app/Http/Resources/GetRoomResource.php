<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GetRoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'room_id' => $this->id,
            'room_type' => $this->roomtype ? $this->roomtype->name : '',
            'bed_type' => $this->bedtype ? $this->bedtype->name : '',
        ];
    }
}
