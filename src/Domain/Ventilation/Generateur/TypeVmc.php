<?php

namespace App\Domain\Ventilation\Generateur;

enum TypeVmc: string
{
    case AUTOREGLABLE = 'autoreglable';
    case HYGROREGLABLE_TYPE_A = 'hygroreglable_type_a';
    case HYGROREGLABLE_TYPE_B = 'hygroreglable_type_b';
}
