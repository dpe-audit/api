<?php

namespace App\Domain\Ecs\Systeme\Reseau;

enum BouclageReseau: string
{
    case RESEAU_NON_BOUCLE = 'non_boucle';
    case RESEAU_BOUCLE = 'boucle';
    case RESEAU_TRACE = 'trace';
}
