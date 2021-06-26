<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class BuyingItem extends Model
{
    use Trash;
    protected $guarded = [];

    public function item()
    {
        return $this->belongsTo('App\Models\Item', 'item_id', 'id');
    }
    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier', 'supplier_id', 'id');
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
