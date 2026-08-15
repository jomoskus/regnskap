<?php

namespace App\Enums;

enum Confidence: string
{
    case Opplagt = 'opplagt';
    case Sannsynlig = 'sannsynlig';
    case Uklart = 'uklart';

    public function isSuggestable(): bool
    {
        return $this === self::Opplagt || $this === self::Sannsynlig;
    }
}
