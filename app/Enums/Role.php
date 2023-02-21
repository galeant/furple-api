<?php

namespace App\Enums;

enum Role: int
{
    case Admin = 1;

    case Customer = 2;

    public static function getRandomData()
    {
        $rand = rand(0, (count(self::cases()) - 1));

        return self::cases()[$rand];
    }
}
