<?php

namespace App\Enums;

enum TaskStatus: int
{
    case NEW = 0;
    case IN_PROGRESS = 1;
    case DONE = 2;

    public static function values(): array
    {
        return array_map(fn(self $s) => $s->value, self::cases());
    }
}
