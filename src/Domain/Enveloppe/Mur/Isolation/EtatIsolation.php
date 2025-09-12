<?php

namespace App\Domain\Enveloppe\Mur\Isolation;

enum EtatIsolation: string
{
    case NON_ISOLE = 'non_isole';
    case ISOLE = 'isole';

    public static function from_boolean(bool $isolation): self
    {
        return $isolation ? self::ISOLE : self::NON_ISOLE;
    }

    public function toBoolean(): bool
    {
        return $this === self::ISOLE;
    }
}
