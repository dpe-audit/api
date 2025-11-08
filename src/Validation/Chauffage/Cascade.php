<?php

namespace App\Validation\Chauffage;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class Cascade extends Constraint
{
    public string $message = 'Cascade invalide pour le générateur de chauffage {{ id }}';
    public string $mode = 'strict';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
