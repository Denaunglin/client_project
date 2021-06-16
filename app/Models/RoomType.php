<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use Trash;
    protected $guarded = [];

    public function booking()
    {
        return $this->hasOne('App\Models\Booking', 'room_id', 'id');
    }
}
