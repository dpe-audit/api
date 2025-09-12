<?php

namespace App\Domain\Common\Enum;

enum Usage: string
{
    case CHAUFFAGE = 'chauffage';
    case ECS = 'ecs';
    case REFROIDISSEMENT = 'refroidissement';
    case ECLAIRAGE = 'eclairage';
    case AUXILIAIRE = 'auxiliaire';
}
