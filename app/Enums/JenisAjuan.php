<?php

namespace App\Enums;

enum JenisAjuan: string
{
    case RKAP = 'RKAP';
    case NONRKAP = 'NONRKAP';
    case DEFAULT = '';

    public function labels(): string
    {
        return match ($this) {
            self::RKAP         => "RKAP",
            self::NONRKAP       => "Non RKAP",
            self::DEFAULT => '',
        };
    }
}
