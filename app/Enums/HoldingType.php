<?php

namespace App\Enums;

enum HoldingType: string
{
    case Portefolje = 'portefolje';
    case Unotert = 'unotert';
    case Metall = 'metall';
    case Folkelan = 'folkelan';

    public function label(): string
    {
        return match ($this) {
            self::Portefolje => 'Portefølje',
            self::Unotert => 'Unotert',
            self::Metall => 'Metall',
            self::Folkelan => 'Folkelån',
        };
    }
}
