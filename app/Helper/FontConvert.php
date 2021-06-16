<?php

namespace App\Helper;

use Googlei18n\MyanmarTools\ZawgyiDetector;
use Rabbit;

class FontConvert
{
    public static function zg2uni($str)
    {
        $str = $str ?? "";
        $detector = new ZawgyiDetector();
        return $detector->getZawgyiProbability($str) >= 0.95 ? Rabbit::zg2uni($str) : $str;
    }

    public static function uni2zg($str)
    {
        $str = $str ?? "";
        $detector = new ZawgyiDetector();
        return $detector->getZawgyiProbability($str) < 0.95 ? Rabbit::uni2zg($str) : $str;
    }
}
