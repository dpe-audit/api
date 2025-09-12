<?php

namespace App\Domain\Ecs\Generateur\Position;

enum PositionChauffeEau: string
{
    case CHAUFFE_EAU_HORIZONTAL = 'chauffe_eau_horizontal';
    case CHAUFFE_EAU_VERTICAL = 'chauffe_eau_vertical';
}
