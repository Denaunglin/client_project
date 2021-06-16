<?php

namespace App\Models;

use App\Traits\Trash;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Model;

class GroupPermission extends Model
{
    use Trash;
    protected $guarded = [];

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'group_id', 'id');
    }
}
