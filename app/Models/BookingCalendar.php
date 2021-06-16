<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingCalendar extends Model
{
    public function room()
    {
        return $this->belongsTo('App\Models\Rooms', 'room_id', 'id');
    }

    public function booking()
    {
        return $this->belongsTo('App\Models\Booking', 'booking_id', 'id');
    }
}
