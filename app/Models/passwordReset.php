<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class passwordReset extends Model
{
    use Trash;
    const UPDATED_AT = null;

    protected $guarded = [];

}
