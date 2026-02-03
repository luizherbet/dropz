<?php

namespace App\Enums;

enum DemandPriority: string
{
    case alta = 'Alta';
    case normal = 'Normal';
    case baixa = 'Baixa';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
