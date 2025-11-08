<?php

namespace App\Validation\Diagnostic;

use Symfony\Component\Validator\Constraints\Compound;

#[\Attribute]
final class DiagnosticValid extends Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new GenerateurMixteExists,
            new GenerateurMixteValid,
        ];
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
