<?php

namespace App\Enums;

enum FiscalTypeEnum: string
{
    case LEGAL_PERSON = 'legal_person';
    case NATURAL_PERSON = 'natural_person';

    public function label(): string
    {
        return match ($this) {
            self::LEGAL_PERSON => 'Persona Moral',
            self::NATURAL_PERSON => 'Persona Fisica'
        };
    }

    public static function forSelect(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
