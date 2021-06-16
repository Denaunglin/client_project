<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class OrderList extends Model
{
    use Trash;
    protected $guarded = [];

}
