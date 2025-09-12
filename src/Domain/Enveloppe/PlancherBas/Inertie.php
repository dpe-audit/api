<?php

namespace App\Domain\Enveloppe\PlancherBas;

enum Inertie: string
{
    case LOURDE = 'lourde';
    case LEGERE = 'legere';

    public function toBoolean(): bool
    {
        return $this === self::LOURDE;
    }
}
