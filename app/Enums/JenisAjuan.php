<?php

namespace App\Enums;

enum JenisAjuan: string
{
    case RKAP = 'rkap';
    case NONRKAP = 'nonrkap';
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
