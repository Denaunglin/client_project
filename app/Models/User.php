<?php

namespace App\Models;

use App\Models\UserNrcPicture;
use App\Traits\Trash;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, HasRoles, Trash;

    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'phone', 'nrc_passport', 'date_of_birth', 'gender', 'address', 'account_type', 'password', 'trash',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function userprofile()
    {
        return $this->hasOne('App\Models\UserProfile', 'user_id', 'id');
    }

    public function usercreditcard()
    {
        return $this->hasMany('App\Models\UserCreditCard', 'user_id', 'id');
    }

    public function accounttype()
    {
        return $this->hasOne('App\Models\AccountType', 'id', 'account_type');
    }

    public function usernrcimage()
    {
        return $this->hasOne(UserNrcPicture::class, 'user_id', 'id');
    }

    //     public function routeNotificationForOneSignal()
    // {
    //     return 'ONE_SIGNAL_PLAYER_ID';
    // }

    // public function routeNotificationForMail()
    // {
    //     return $this->email_address;
    // }

}
