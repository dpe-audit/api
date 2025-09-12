<?php

namespace App\Domain\Chauffage\Systeme\Reseau;

/**
 * TODO: Vérifier le cas des fluides frigorigènes (cas des pompes à chaleur)
 */
enum TypeDistribution: string
{
    case HYDRAULIQUE = 'hydraulique';
    case AERAULIQUE = 'aeraulique';
    #[\Deprecated]
    case FLUIDE_FRIGORIGENE = 'fluide_frigorigene';
}
