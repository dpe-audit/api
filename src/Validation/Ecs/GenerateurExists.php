<?php

namespace App\Validation\Ecs;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class GenerateurExists extends Constraint
{
    public string $message = 'Le générateur {{ generateur_id }} associé au système {{ systeme_id }} n\'existe pas';
    public string $mode = 'strict';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
