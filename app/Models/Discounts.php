<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class Discounts extends Model
{
    use Trash;
    protected $guarded = [];

    public function roomtype()
    {
        return $this->belongsTo('App\Models\RoomType', 'room_type_id', 'id');
    }
    public function accounttype()
    {
        return $this->belongsTo('App\Models\AccountType', 'user_account_id', 'id');
    }

    public function account_type()
    {
        return $this->hasMany('App\Models\AccountType', 'user_account_id', 'id');
    }
    public function room()
    {
        return $this->belongsTo('App\Models\Rooms', 'room_type_id', 'id');

    }
}
