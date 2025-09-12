<?php

namespace App\Domain\Enveloppe\DoubleFenetre\Vitrage;

enum TypeVitrage: string
{
    case BRIQUE_VERRE = 'brique_verre';
    case POLYCARBONATE = 'polycarbonate';
    case SIMPLE_VITRAGE = 'simple_vitrage';
    case DOUBLE_VITRAGE = 'double_vitrage';
    case DOUBLE_VITRAGE_FE = 'double_vitrage_fe';
    case TRIPLE_VITRAGE = 'triple_vitrage';
    case TRIPLE_VITRAGE_FE = 'triple_vitrage_fe';

    public static function try_from_enum_type_vitrage_id(int $id, ?bool $vitrage_vir): ?self
    {
        return match ($id) {
            1, 4 => self::SIMPLE_VITRAGE,
            2 => $vitrage_vir ? self::DOUBLE_VITRAGE_FE : self::DOUBLE_VITRAGE,
            3 => $vitrage_vir ? self::TRIPLE_VITRAGE_FE : self::TRIPLE_VITRAGE,
            5, 6 => null,
        };
    }

    public function isolation(): bool
    {
        return match ($this) {
            self::TRIPLE_VITRAGE => true,
            default => false,
        };
    }

    public function vitrage_complexe(): bool
    {
        return match ($this) {
            self::DOUBLE_VITRAGE, self::DOUBLE_VITRAGE_FE, self::TRIPLE_VITRAGE, self::TRIPLE_VITRAGE_FE => true,
            default => false,
        };
    }
}
