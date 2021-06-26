<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class Cashbook extends Model
{
    use Trash;
    protected $guarded = [];
   
    public function item()
    {
        return $this->belongsTo('App\Models\Item', 'item_id', 'id');
    }
    public function buying()
    {
        return $this->belongsTo('App\Models\BuyingItem', 'buying_id', 'id');
    }
    public function selling()
    {
        return $this->belongsTo('App\Models\SellItems', 'selling_id', 'id');
    }
    public function return()
    {
        return $this->belongsTo('App\Models\ReturnItem', 'return_id', 'id');
    }
}
