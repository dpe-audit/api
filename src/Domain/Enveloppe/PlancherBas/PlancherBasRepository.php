<?php

namespace App\Domain\Enveloppe\PlancherBas;

use App\Domain\Common\ValueObject\Id;

interface PlancherBasRepository
{
    public function find(Id $id): ?PlancherBas;
}
