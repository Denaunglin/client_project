<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Trash;

class Notificationlist extends Model
{
    use Trash;
    protected $guarded = [];

     public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
