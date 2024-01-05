<?php

namespace App\Service;

class CodeGenerator
{
    public static function generateCode($length = 6): string
    {
        $chars = str_split('abcdefghijklmnopqrstuvwxyz0123456789');
        shuffle($chars);
        return strtoupper(substr(implode('', $chars), 0, $length));
    }

}