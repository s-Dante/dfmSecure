<?php

namespace App\Enums;

use App\Traits\EnumHelper;

enum GenderEnum: string
{
    use EnumHelper;

    case MALE = "male";
    case FEMALE = "female";
    case OTHER = "other";

    public function label(): string
    {
        return match ($this) {
            self::MALE => 'Hombre',
            self::FEMALE => 'Mujer',
            self::OTHER => 'Prefiero no decir',
        };
    }
}
