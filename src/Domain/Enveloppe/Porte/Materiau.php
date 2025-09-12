<?php

namespace App\Domain\Enveloppe\Porte;

enum Materiau: string
{
    case PVC = 'pvc';
    case BOIS = 'bois';
    case BOIS_METAL = 'bois_metal';
    case METAL = 'metal';
}
