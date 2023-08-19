<?php

namespace App;

class Helpers
{
    public static function camelToUnderscore($string , $us = "_")
    {
        $pattern = '/(?<!^)[A-Z]+|(?<!^|\d)\d+/';
        return strtolower(preg_replace($pattern,$us.'$0',$string));
    }
}