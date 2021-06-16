<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    public function user()
    {
        return $this->hasOne('App\Models\Users', 'user_id', 'id');
    }

    protected $fillable = [
        'email', 'address', 'image', 'user_id',
    ];

    public function image_path()
    {
        if($this->image){
             return asset('storage/uploads/gallery/' . $this->image);
        }
        
        return null;
    }
}
