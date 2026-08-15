<?php

namespace App\Enums;

enum FigureSection: string
{
    case Resultat = 'resultat';
    case Formue = 'formue';
    case Gjeld = 'gjeld';
    case Inntekt = 'inntekt';
    case Investering = 'investering';
    case Likviditet = 'likviditet';
    case Bolig = 'bolig';

    public function label(): string
    {
        return match ($this) {
            self::Resultat => 'Resultat',
            self::Formue => 'Formue',
            self::Gjeld => 'Gjeld',
            self::Inntekt => 'Inntekt',
            self::Investering => 'Investering',
            self::Likviditet => 'Likviditet',
            self::Bolig => 'Bolig',
        };
    }
}
