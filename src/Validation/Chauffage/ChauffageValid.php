<?php

namespace App\Validation\Chauffage;

use Symfony\Component\Validator\Constraints\Compound;

#[\Attribute]
final class ChauffageValid extends Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new Cascade,
            new EmetteurExists,
            new GenerateurExists,
            new InstallationExists,
            new TypeChauffage,
        ];
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
