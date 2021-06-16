<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class CardType extends Model
{
    use Trash;
    protected $guarded = [];
}
