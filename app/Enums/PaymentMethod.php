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
}
