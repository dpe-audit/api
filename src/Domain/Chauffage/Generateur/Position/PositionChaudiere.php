<?php

namespace App\Domain\Chauffage\Generateur\Position;

enum PositionChaudiere: string
{
    case CHAUDIERE_MURALE = 'chaudiere_murale';
    case CHAUDIERE_SOL = 'chaudiere_sol';
}
