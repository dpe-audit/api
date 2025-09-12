<?php

namespace App\Domain\Chauffage\Systeme;

/**
 * @see https://github.com/dpe-audit/methode-3cl/discussions/25
 */
enum Configuration: string
{
    case BASE = 'base';
    case RELEVE = 'releve';
    case APPOINT = 'appoint';
}
