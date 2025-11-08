<?php

namespace App\Domain\Chauffage\Systeme\Reseau;

enum TypeDistribution: string
{
    case HYDRAULIQUE = 'hydraulique';
    case AERAULIQUE = 'aeraulique';
}
