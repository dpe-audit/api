<?php

namespace App\Domain\Chauffage;

/**
 * @see https://github.com/dpe-audit/methode-3cl/discussions/25
 */
enum TypeChauffage: string
{
    case CHAUFFAGE_DIVISE = 'chauffage_divise';
    case CHAUFFAGE_CENTRAL = 'chauffage_central';
}
