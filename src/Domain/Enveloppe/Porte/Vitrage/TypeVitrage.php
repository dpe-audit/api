<?php

namespace App\Domain\Enveloppe\Porte\Vitrage;

enum TypeVitrage: string 
{
    case SIMPLE_VITRAGE = 'simple_vitrage';
    case DOUBLE_VITRAGE = 'double_vitrage';
    case TRIPLE_VITRAGE = 'triple_vitrage';
}
