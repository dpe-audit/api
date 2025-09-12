<?php

namespace App\Domain\Enveloppe\Porte;

enum Isolation: string
{
    case NON_ISOLE = 'non_isole';
    case ISOLE = 'isole';

    public static function from_enum_type_porte_id(int $type_porte_id): ?self
    {
        return match ($type_porte_id) {
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12 => self::NON_ISOLE,
            13, 15 => self::ISOLE,
            default => null,
        };
    }

    public static function from_boolean(bool $isolation): self
    {
        return $isolation ? self::ISOLE : self::NON_ISOLE;
    }

    public function is_isole(): bool
    {
        return $this === self::ISOLE;
    }
}
