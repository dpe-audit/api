<?php

namespace App\Engine\Table;

use App\Domain\Adresse\ZoneClimatique;

interface EclairageTableValeurRepository
{
    public function nhecl(ZoneClimatique $zone_climatique): ?float;
}
