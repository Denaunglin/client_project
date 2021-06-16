<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class EarlyLate extends Model
{
    use Trash;
    protected $guarded = [];

     public function accounttype()
    {
        return $this->belongsTo('App\Models\AccountType', 'user_account_id', 'id');
    }
}
