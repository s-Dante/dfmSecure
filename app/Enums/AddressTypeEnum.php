<?php

namespace App\Enums;

enum AddressTypeEnum: string
{
    case FISCAL = 'fiscal';
    case HOME = 'home';
    case OFFICE = 'office';

    public function label(): string
    {
        return match ($this) {
            self::FISCAL => 'Domicilio Fiscal',
            self::HOME => 'Domicilio',
            self::OFFICE => 'Oficina'
        };
    }
}
