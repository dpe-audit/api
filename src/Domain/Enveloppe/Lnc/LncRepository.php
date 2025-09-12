<?php

namespace App\Domain\Enveloppe\Lnc;

use App\Domain\Common\ValueObject\Id;

interface LncRepository
{
    public function find(Id $id): ?Lnc;
}
