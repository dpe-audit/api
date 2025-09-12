<?php

namespace App\Domain\Enveloppe\Lnc\Paroi;

enum Isolation: string
{
    case NON_ISOLE = 'non_isole';
    case ISOLE = 'isole';

    public function is_isole(): bool
    {
        return $this === self::ISOLE;
    }
}
