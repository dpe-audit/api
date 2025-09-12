<?php

namespace App\Domain\Enveloppe\Masque;

enum SecteurMasque: string
{
    case LATERAL = 'lateral';
    case LATERAL_SUD = 'lateral_sud';
    case CENTRAL = 'central';
    case CENTRAL_SUD = 'central_sud';
}
