<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class showGallery extends Model
{
    public function rooms()
    {
        return $this->hasOne('App\Models\Rooms', 'rooms_id', 'id');
    }

    public function image_path()
    {
        return asset('storage/uploads/galleryimage/' . $this->file);
    }
}
