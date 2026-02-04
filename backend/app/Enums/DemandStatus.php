<?php

namespace App\Enums;

enum DemandStatus: string
{
    case Backlog = 'backlog';
    case Autorizacao = 'autorizacao';
    case Fila = 'fila';
    case Desenvolvimento = 'desenvolvimento';
    case Teste = 'teste';
    case Deploy = 'deploy';
    case Concluido = 'concluido';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
