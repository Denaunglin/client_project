<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use Trash;
    protected $guarded = [];

}
