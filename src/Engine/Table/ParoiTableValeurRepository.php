<?php

namespace App\Engine\Table;

use App\Domain\Enveloppe\Paroi\Mitoyennete;

interface ParoiTableValeurRepository
{
    public function b(Mitoyennete $mitoyennete): ?float;
}
