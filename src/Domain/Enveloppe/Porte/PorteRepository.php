<?php

namespace App\Domain\Enveloppe\Porte;

use App\Domain\Common\ValueObject\Id;

interface PorteRepository
{
    public function find(Id $id): ?Porte;
}
