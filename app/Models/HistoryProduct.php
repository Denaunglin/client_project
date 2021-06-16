<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class HistoryProduct extends Model
{   use Trash;
     protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class);
}
}
