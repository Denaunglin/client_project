<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class UserCreditCard extends Model
{
    use Trash;

    // protected $fillable=['name'];
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
    public function cardtype()
    {
        return $this->belongsTo('App\Models\CardType', 'credit_type', 'id');
    }
}
