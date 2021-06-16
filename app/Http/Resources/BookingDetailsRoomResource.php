<?php

namespace App\Http\Resources;

use App\Http\Resources\RoomGalleryResource;
use App\Models\showGallery;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingDetailsRoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $image = $this->image_path();
        $facilitiesdata = config('app.facilities');
        $facilities = \unserialize($this->facilities);
        $gallery = showGallery::where('rooms_id', $this->id)->get();
        if ($gallery) {
            $room_gallery = RoomGalleryResource::collection($gallery);
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

        return [
            "room_type" => $this->roomtype ? $this->roomtype->name : '',
            'bed_type' => $this->bedtype ? $this->bedtype->name : '-',
            'image' => $image,
            'room_gallery' => $room_gallery ?? null,
            'description' => $this->description,
            'available_facilities' => $available_facilities,
            'notavailable_facilities' => $notavailable_facilities,
        ];

    }
}
