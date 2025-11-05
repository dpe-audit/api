<?php

namespace App\Domain\Scenario;

enum TypeScenario: string
{
    case RENOVATION_GLOBALE = 'renovation_globale';
    case RENOVATION_PAR_ETAPES = 'renovation_par_etapes';
}
