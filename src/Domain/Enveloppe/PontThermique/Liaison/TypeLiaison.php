<?php

namespace App\Domain\Enveloppe\PontThermique\Liaison;

enum TypeLiaison: string
{
    case PLANCHER_BAS_MUR = 'plancher_bas_mur';
    case PLANCHER_INTERMEDIAIRE_MUR = 'plancher_intermediaire_mur';
    case PLANCHER_HAUT_MUR = 'plancher_haut_mur';
    case REFEND_MUR = 'refend_mur';
    case MENUISERIE_MUR = 'menuiserie_mur';
}
