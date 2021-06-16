<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use Trash;

    protected $guarded = [];

    public function room()
    {
        return $this->belongsTo('App\Models\Rooms', 'room_id', 'id')->with('roomtype', 'bedtype');
    }

    public function cardtype()
    {
        return $this->belongsTo('App\Models\CardType', 'credit_type', 'id');
    }

    public function roomschedule()
    {
        return $this->hasOne('App\Models\RoomSchedule', 'booking_id', 'id');
    }

    public function roomscheduledata()
    {
        return $this->hasMany('App\Models\RoomSchedule', 'booking_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'client_user', 'id');
    }

    public function payslip()
    {
        return $this->hasMany('App\Models\Payslip', 'booking_no', 'booking_number');

    }

    public function image_path()
    {
        if ($this->payslip_image) {
            return asset('storage/uploads/gallery/' . $this->payslip_image);
        }
        return null;
    }

}
