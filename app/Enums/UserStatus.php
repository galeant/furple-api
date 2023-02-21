<?php

namespace App\Enums;

enum UserStatus: int
{
    case InActive = 0;

    case Active = 1;

    case NotVerified = 2;

    public static function getRandomData()
    {
        $rand = rand(0, (count(self::cases()) - 1));

        return self::cases()[$rand];
    }
}
