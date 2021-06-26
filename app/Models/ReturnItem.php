<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    use Trash;
    protected $guarded = [];

    public function item()
    {
        return $this->belongsTo('App\Models\Item', 'item_id', 'id');
    }

    public function item_category()
    {
        return $this->belongsTo('App\Models\ItemCategory', 'item_category_id', 'id');
    }

    public function item_sub_category()
    {
        return $this->belongsTo('App\Models\ItemSubCategory', 'item_sub_category_id', 'id');
    }
}
