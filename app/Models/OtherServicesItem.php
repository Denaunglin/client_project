<?php

namespace App\Models;

use App\Traits\Trash;
use Illuminate\Database\Eloquent\Model;

class OtherServicesItem extends Model
{
    protected $guarded = [];
    use Trash;

    public function otherservicescategory()
    {
        return $this->belongsTo('App\Models\OtherServicesCategory', 'other_services_category_id', 'id');
    }
}
