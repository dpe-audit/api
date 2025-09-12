<?php

namespace App\Domain\Logement;

enum Position: string
{
    case RDC = 'rdc';
    case ETAGE_INTERMEDIAIRE = 'etage_intermediaire';
    case DERNIER_ETAGE = 'dernier_etage';
}
