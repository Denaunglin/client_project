<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class Discounts extends Model
{
    use Trash;
    protected $guarded = [];


    public function accounttype()
    {
        return $this->belongsTo('App\Models\AccountType', 'user_account_id', 'id');
    }

    public function item()
    {
        return $this->belongsTo('App\Models\Item', 'item_id', 'id');
    }
}
