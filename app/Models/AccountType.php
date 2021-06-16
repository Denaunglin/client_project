<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
    use Trash;

    // protected $fillable=['name'];
    protected $guarded = [];

    public function discounts()
    {
        return $this->hasOne('App\Models\Discounts', 'user_account_id', 'id');
    }

}
