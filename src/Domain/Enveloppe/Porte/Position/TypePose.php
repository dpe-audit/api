<?php

namespace App\Domain\Enveloppe\Porte\Position;

enum TypePose: string
{
    case NU_EXTERIEUR = 'nu_exterieur';
    case NU_INTERIEUR = 'nu_interieur';
    case TUNNEL = 'tunnel';
}
