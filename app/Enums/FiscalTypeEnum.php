<?php

namespace App\Enums;

use App\Traits\EnumHelper;

enum FiscalTypeEnum: string
{
    use EnumHelper;

    case LEGAL_PERSON = 'legal_person';
    case NATURAL_PERSON = 'natural_person';

    public function label(): string
    {
        return match ($this) {
            self::LEGAL_PERSON => 'Persona Moral',
            self::NATURAL_PERSON => 'Persona Fisica'
        };
    }
}
