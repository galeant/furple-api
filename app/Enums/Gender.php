<?php

namespace App\Enums;

enum Gender: int
{
    case Male = 1;

    case Female = 2;

    public static function getRandomData()
    {
        $rand = rand(0, (count(self::cases()) - 1));

        return self::cases()[$rand];
    }

    public static function getAllKey()
    {
        return array_column(self::cases(), 'name');
    }

    public static function getAllValue()
    {
        return array_column(self::cases(), 'value');
    }
}
