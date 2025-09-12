<?php

namespace App\Domain\Enveloppe\Masque;

use App\Domain\Common\ValueObject\Id;

interface MasqueRepository
{
    public function find(Id $id): ?Masque;
}
