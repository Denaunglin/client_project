<?php

namespace App\Helper;

class OTP
{
    public static function generateOTP()
    {
        // if (config('app.env') != 'production') {
        //     return 123123;
        // }

        $otp = mt_rand(100000, 999999);
        return $otp;
    }
}
