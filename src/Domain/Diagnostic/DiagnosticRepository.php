<?php

namespace App\Domain\Diagnostic;

use App\Domain\Common\ValueObject\Id;

interface DiagnosticRepository
{
    public function save(Diagnostic $entity): void;

    public function find(Id $id): ?Diagnostic;
}
