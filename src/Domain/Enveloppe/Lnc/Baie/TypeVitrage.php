<?php

namespace App\Domain\Enveloppe\Lnc\Baie;

enum TypeVitrage: string
{
    case POLYCARBONATE = 'polycarbonate';
    case SIMPLE_VITRAGE = 'simple_vitrage';
    case DOUBLE_VITRAGE = 'double_vitrage';
    case DOUBLE_VITRAGE_FE = 'double_vitrage_fe';
    case TRIPLE_VITRAGE = 'triple_vitrage';
    case TRIPLE_VITRAGE_FE = 'triple_vitrage_fe';

    public function isolation(): bool
    {
        return match ($this) {
            self::TRIPLE_VITRAGE, self::TRIPLE_VITRAGE_FE => true,
            default => false,
        };
    }
}
