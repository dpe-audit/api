<?php

namespace App\Engine\Table;

use App\Domain\Batiment\ZoneClimatique;

interface EclairageTableValeurRepository
{
    public function nhecl(ZoneClimatique $zone_climatique): ?float;
}
