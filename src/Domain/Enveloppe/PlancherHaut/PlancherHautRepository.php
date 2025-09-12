<?php

namespace App\Domain\Enveloppe\PlancherHaut;

use App\Domain\Common\ValueObject\Id;

interface PlancherHautRepository
{
    public function find(Id $id): ?PlancherHaut;
}
