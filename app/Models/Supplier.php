<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use Trash;
    protected $guarded = [];
}
