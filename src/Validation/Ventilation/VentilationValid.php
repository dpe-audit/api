<?php

namespace App\Validation\Ventilation;

use Symfony\Component\Validator\Constraints\Compound;

#[\Attribute]
final class VentilationValid extends Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new GenerateurExists,
        ];
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
