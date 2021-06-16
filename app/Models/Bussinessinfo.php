<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class Bussinessinfo extends Model
{
    use Trash;
    protected $guarded = [];

    public function image_path()
    {
        if ($this->logo) {
            return asset('storage/uploads/gallery/' . $this->logo);
        }
        return null;
    }
}
