<?php

namespace App\Validation\Audit;

use App\Validation\Schema\Schema;
use Symfony\Component\Validator\Constraints\Compound;

#[\Attribute]
final class AuditValid extends Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new Schema('https://schemas.dpe-audit.fr/audit'),
        ];
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
