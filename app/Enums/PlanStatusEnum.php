<?php

namespace App\Enums;

use App\Traits\EnumHelper;

enum PlanStatusEnum: string
{
    use EnumHelper;

    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case DELETED = 'deleted';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Activo',
            self::INACTIVE => 'Inactivo',
            self::DELETED => 'Eliminado',
        };
    }
}
