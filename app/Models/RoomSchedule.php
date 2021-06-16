<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class RoomSchedule extends Model
{
    use Trash;

    protected $guarded = [];

    public function room()
    {
        return $this->belongsTo('App\Models\Rooms', 'room_id', 'id');
    }
    public function booking()
    {
        return $this->belongsTo('App\Models\Booking', 'booking_id', 'id');
    }

    public function roomlayout()
    {
        return $this->belongsTo('App\Models\RoomLayout', 'room_no', 'id');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'client_user', 'id');
    }

}
