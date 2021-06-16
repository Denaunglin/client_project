<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class ItemCategory extends Model
{
    use Trash;
    protected $guarded = [];

}
