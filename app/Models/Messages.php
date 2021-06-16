<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Trash;
class Messages extends Model
{
    use Trash;

    // protected $fillable=['name'];
    protected $guarded = [];
}
