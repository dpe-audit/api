<?php

namespace App\Domain\Chauffage\Systeme\Reseau;

enum IsolationReseau: string
{
    case NON_ISOLE = 'non_isole';
    case ISOLE = 'isole';
}
