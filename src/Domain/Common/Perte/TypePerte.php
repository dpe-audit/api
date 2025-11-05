<?php

namespace App\Domain\Common\Perte;

enum TypePerte: string
{
    case GENERATION = 'generation';
    case STOCKAGE = 'stockage';
    case DISTRIBUTION = 'distribution';
}
