<?php

namespace App\Validation\Ecs;

use Symfony\Component\Validator\Constraints\Compound;

#[\Attribute]
final class EcsValid extends Compound
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
