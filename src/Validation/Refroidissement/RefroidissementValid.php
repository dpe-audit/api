<?php

namespace App\Validation\Refroidissement;

use Symfony\Component\Validator\Constraints\Compound;

#[\Attribute]
final class RefroidissementValid extends Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new GenerateurExists,
            new InstallationExists,
        ];
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
