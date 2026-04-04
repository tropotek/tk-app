<?php

namespace App\Enum;

enum Roles: string
{
    case Admin = 'admin';
    case Staff = 'staff';
    case Member = 'member';


    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }

    public static function toValueNameArray(): array
    {
        $items = [];
        foreach (self::cases() as $case) {
            $items[$case->value] = $case->name;
        }
        return $items;
    }
}
