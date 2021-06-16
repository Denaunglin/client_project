<?php
namespace App\Helper;

use Rabbit;

class translateHelper
{
    public static function translate($str, $str_mm, $lang)
    {
        if ($lang == 'uni') {
            return $str_mm;
        } else if ($lang == 'zg') {
            return Rabbit::uni2zg($str_mm);
        } else {
            return $str;
        }
    }
}
