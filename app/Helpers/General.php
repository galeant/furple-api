<?php

namespace App\Helpers;

class General
{
    public static function parsePhoneNumber(string $phoneNumber)
    {
        if ($phoneNumber[0] == '+') {
            return explode('+', $phoneNumber)[1];
        }

        if ($phoneNumber[0] == '0') {
            return substr_replace($phoneNumber, '62', 0, 1);
        }

        return $phoneNumber;
    }
}
