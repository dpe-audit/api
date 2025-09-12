<?php

namespace App\Domain\Ecs\Generateur\Signaletique;

enum ModeCombustion: string
{
    case STANDARD = 'standard';
    case BASSE_TEMPERATURE = 'basse_temperature';
    case CONDENSATION = 'condensation';
}
