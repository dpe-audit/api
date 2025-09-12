<?php

namespace App\Domain\Chauffage\Installation;

use App\Domain\Common\ValueObject\Id;

interface InstallationRepository
{
    public function find(Id $id): ?Installation;
}
