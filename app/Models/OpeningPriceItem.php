<?php

namespace App;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class OpeningPriceItem extends Model
{
    use Trash;
    protected $guarded = [];
    
}
