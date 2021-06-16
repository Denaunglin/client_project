<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class RoomLayout extends Model
{
    use Trash;

    protected $guarded = [];

    public function room()
    {
        return $this->belongsTo('App\Models\Rooms', 'room_id', 'id');
    }

    public function roomschedule()
    {
        return $this->belongsTo('App\Models\RoomSchedule', 'room_no', 'id');
    }
}
