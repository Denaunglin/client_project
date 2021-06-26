<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{
    use Trash;

    protected $guarded = [];

    public function item()
    {
        return $this->belongsTo('App\Models\Item', 'item_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\User', 'customer_id', 'id');
    }
}
