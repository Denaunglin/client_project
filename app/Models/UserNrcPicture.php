<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class UserNrcPicture extends Model
{
    use Trash;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function image_path_front()
    {
        if ($this->front_pic) {
            return asset('storage/uploads/gallery/' . $this->front_pic);
        }
        return null;
    }

    public function image_path_back()
    {
        if ($this->back_pic) {
            return asset('storage/uploads/gallery/' . $this->back_pic);
        }
        return null;
    }
}
