<?php

namespace App\Domain\Enveloppe\Niveau;

use App\Domain\Common\ValueObject\Id;

interface NiveauRepository
{
    public function find(Id $id): ?Niveau;
}
