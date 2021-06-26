<?php

namespace App\Helper;

use App\Models\Booking;
use App\Models\RoomLayout;
use Auth;

class ResponseHelper
{
    public static function success($data = [], $message = 'success')
    {
        return response()->json([
            'result' => 1,
            'message' => $message,
            'data' => $data,
        ]);
    }   

    public static function fail($data = [], $message = 'fail')
    {
        return response()->json([
            'result' => 0,
            'message' => $message,
            'data' => $data,
        ]);
    }

    public static function successMessage($message = "success")
    {
        return response()->json([
            'result' => 1,
            'message' => $message,
        ]);
    }

    public static function failedMessage($message = "fail")
    {
        return response()->json([
            'result' => 0,
            'message' => $message,
        ]);
    }

  
   
}
