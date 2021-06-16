<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class Rooms extends Model
{
    use Trash;

    protected $guarded = [];

    public function bedtype()
    {
        return $this->belongsTo('App\Models\BedType', 'bed_type', 'id');
    }

    public function roomtype()
    {
        return $this->belongsTo('App\Models\RoomType', 'room_type', 'id');
    }

    public function roomlayout()
    {
        return $this->hasMany('App\Models\RoomLayout', 'room_id', 'id');
    }

    public function booking()
    {
        return $this->belongsTo('App\Models\Booking', 'room_id', 'id');
    }

    public function showgallery()
    {
        return $this->hasMany('App\Models\showGallery', 'rooms_id', 'id');
    }

    public function image_path()
    {
        if($this->image){
            return asset('storage/uploads/gallery/' . $this->image);
        }

        return null;
    }

    public function discount_types()
    {
        return $this->hasMany(Discounts::class, 'room_type_id', 'id');
    }
}
