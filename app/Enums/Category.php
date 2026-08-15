<?php

namespace App\Enums;

enum Category: string
{
    case O = 'O';
    case Dagligvarer = 'Dagligvarer';
    case BollerOgBrus = 'Boller og brus';
    case TingTilHjemmet = 'Ting til hjemmet';
    case Bokostnader = 'Bokostnader';
    case Trening = 'Trening';
    case Sykkel = 'Sykkel';
    case Bil = 'Bil';
    case OffentligTransport = 'Offentlig transport';
    case OpplevelserReiser = 'Opplevelser, reiser';
    case Abonnement = 'Abonnement';
    case Klaer = 'Klær';
    case Elektronikk = 'Elektronikk';
    case Utdanning = 'Utdanning';
    case Dannelse = 'Dannelse';
    case Tjenester = 'Tjenester';
    case Annet = 'Annet';
    case Tiende = 'Tiende';
    case ReligioseGjenstander = 'Religiøse gjenstander';
    case GaverOgDonasjoner = 'Gaver og donasjoner';
}
