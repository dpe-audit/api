<?php

namespace App\Domain\Enveloppe\Lnc\Baie;

enum Materiau: string
{
    case POLYCARBONATE = 'polycarbonate';
    case BOIS = 'bois';
    case BOIS_METAL = 'bois_metal';
    case PVC = 'pvc';
    case METAL = 'metal';
}
