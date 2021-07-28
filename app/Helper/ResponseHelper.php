<?php

namespace App\Helper;

use Auth;
use App\Models\Item;
use App\Models\Booking;
use App\Models\RoomLayout;

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

    // public static function dataitem($id){
    //     $data = Item::findMany($id);
    //     $name = [];
    //     foreach($data as $item){
    //         $name []= '<li class="list-group-item">
    //                         '.$item->name.'
    //                     </li>' ;
    //     }

    //     return $name;
    // }

  
   
}
