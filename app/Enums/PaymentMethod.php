<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Kredittkort = 'Kredittkort';
    case Brukskonto = 'Brukskonto';
    case LonnskontoSparebank1 = 'Lønnskonto Sparebank 1';
    case FakturakontoNordea = 'Fakturakonto Nordea';
    case HovedkontoNordea = 'Hovedkonto Nordea';
    case Wise = 'Wise';
    case DebetkortNordea = 'Debetkort Nordea';
    case SparekontoHandelsbanken = 'Sparekonto Handelsbanken';
    case RegningskontoHandelsbanken = 'Regningskonto Handelsbanken';
    case FysiskeKontanter = 'Fysiske kontanter';

    public static function tryFromLabel(?string $label): ?self
    {
        if ($label === null) {
            return null;
        }

        $label = trim($label);

        if ($label === '') {
            return null;
        }

        $direct = self::tryFrom($label);

        if ($direct instanceof self) {
            return $direct;
        }

        foreach (self::cases() as $case) {
            if (mb_strtolower($case->value) === mb_strtolower($label)) {
                return $case;
            }
        }

        return null;
    }
}
