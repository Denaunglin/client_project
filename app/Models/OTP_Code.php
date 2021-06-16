<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OTP_Code extends Model
{
    protected $guarded = [];

    public function isExpired()
    {
        if (time() < $this->expire_at) {
            return true;
        }
        return false;
    }
}
