<?php

namespace App\Domain\Common\Enum;

enum TypePerte: string
{
    case GENERATION = 'generation';
    case STOCKAGE = 'stockage';
    case DISTRIBUTION = 'distribution';
}
