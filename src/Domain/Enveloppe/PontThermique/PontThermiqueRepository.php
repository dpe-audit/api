<?php

namespace App\Domain\Enveloppe\PontThermique;

use App\Domain\Common\ValueObject\Id;

interface PontThermiqueRepository
{
    public function find(Id $id): ?PontThermique;
}
