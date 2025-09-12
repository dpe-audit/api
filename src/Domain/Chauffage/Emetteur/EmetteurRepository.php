<?php

namespace App\Domain\Chauffage\Emetteur;

use App\Domain\Common\ValueObject\Id;

interface EmetteurRepository
{
    public function find(Id $id): ?Emetteur;
}
