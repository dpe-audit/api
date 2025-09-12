<?php

namespace App\Domain\Enveloppe\DoubleFenetre;

use App\Domain\Common\ValueObject\Id;

interface DoubleFenetreRepository
{
    public function find(Id $id): ?DoubleFenetre;
}
