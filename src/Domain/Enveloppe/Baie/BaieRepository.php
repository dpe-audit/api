<?php

namespace App\Domain\Enveloppe\Baie;

use App\Domain\Common\ValueObject\Id;

interface BaieRepository
{
    public function find(Id $id): ?Baie;
}
