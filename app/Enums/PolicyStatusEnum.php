<?php

namespace App\Enums;

use App\Traits\EnumHelper;

enum PolicyStatusEnum: string
{
    use EnumHelper;

    case PENDING = 'pending';
    case ACTIVE = 'active';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendiente',
            self::ACTIVE => 'Activa',
            self::CANCELLED => 'Cancelada',
            self::EXPIRED => 'Expirada',
        };
    }
}
