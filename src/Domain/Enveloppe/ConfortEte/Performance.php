<?php

namespace App\Domain\Enveloppe\ConfortEte;

enum Performance: string
{
    case BON = 'bon';
    case MOYEN = 'moyen';
    case INSUFFISANT = 'insuffisant';
}
