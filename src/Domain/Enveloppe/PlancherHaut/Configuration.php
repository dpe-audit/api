<?php

namespace App\Domain\Enveloppe\PlancherHaut;

enum Configuration: string
{
    case PLANCHER = 'plancher';
    case RAMPANTS = 'rampants';
    case TERRASSE = 'terrasse';
}
