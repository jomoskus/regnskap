<?php

namespace App\Enums;

enum RecurringInterval: string
{
    case Monthly = 'monthly';
    case Quarterly = 'quarterly';
    case Biannually = 'biannually';
    case Annually = 'annually';
    case Weekly = 'weekly';

    public function label(): string
    {
        return match ($this) {
            self::Monthly => 'Månedlig',
            self::Quarterly => 'Kvartalsvis',
            self::Biannually => 'Halvårlig',
            self::Annually => 'Årlig',
            self::Weekly => 'Ukentlig',
        };
    }

    public function toMonthlyFactor(): float
    {
        return match ($this) {
            self::Monthly => 1.0,
            self::Quarterly => 1 / 3,
            self::Biannually => 1 / 6,
            self::Annually => 1 / 12,
            self::Weekly => 4.3,
        };
    }
}
