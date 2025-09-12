<?php

namespace App\Engine\Table;

interface ProductionTableValeurRepository
{
    public function kpv(float $orientation, float $inclinaison): ?float;
}
