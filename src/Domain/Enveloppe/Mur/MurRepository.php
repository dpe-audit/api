<?php

namespace App\Domain\Enveloppe\Mur;

use App\Domain\Common\ValueObject\Id;

interface MurRepository
{
    public function find(Id $id): ?Mur;
}
