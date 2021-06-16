<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTranscation extends Model
{
    protected $guarded = [];
    protected $table = 'product_transation';

    
    public function product(){
        return $this->belongsTo(Item::class);
    }
}
