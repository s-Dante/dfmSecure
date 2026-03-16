<?php

namespace App\Enums;

use App\Traits\EnumHelper;

enum RoleEnum: string
{
    use EnumHelper;

    case INSURED = 'insured';
    case ADJUSTER = 'adjuster';
    case SUPERVISOR = 'supervisor';
    case ADMIN = 'admin';

    public function label(): string 
    {
        return match ($this) 
        {
            self::INSURED => 'Asegurado',
            self::ADJUSTER => 'Ajustador',
            self::SUPERVISOR => 'Supervisor',
            self::ADMIN => 'Administrador',
        };
    }
}
