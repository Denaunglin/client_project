<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class SliderUpload extends Model
{
    use Trash;
    protected $guarded = [];

    public function image_path(){
       
        return asset('storage/uploads/slider/' . $this->slider_image);

    }
}
