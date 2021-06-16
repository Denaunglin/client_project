<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use Trash;
    protected $guarded = [];

    public function item_category()
    {
        return $this->belongsTo('App\Models\ItemCategory', 'item_category_id', 'id');
    }

    public function item_sub_category()
    {
        return $this->belongsTo('App\Models\ItemSubCategory', 'item_sub_category_id', 'id');
    }


    public function image_path()
    {
        if($this->image){
            return asset('storage/uploads/gallery/' . $this->image);
        }

        return 'null';
    }


    public function shopstorage()
    {
        return $this->belongsTo('App\Models\ShopStorage', 'id', 'item_id');
    }
}
