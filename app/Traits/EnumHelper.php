<?php

namespace App\Traits;

trait EnumHelper
{
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function is(self $case): bool
    {
        return $this === $case;
    }
}
