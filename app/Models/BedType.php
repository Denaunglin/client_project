<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class BedType extends Model
{
    use Trash;

    // protected $fillable=['name'];
    protected $guarded = [];

    public function room()
    {
        return $this->hasOne('App\Models\Rooms', 'bed_type', 'id');
    }
}
