<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class ShopStorage extends Model
{
    use Trash;

    protected $guarded = [];

    public function item()
    {
        return $this->belongsTo('App\Models\Item', 'item_id', 'id');
    }
}
