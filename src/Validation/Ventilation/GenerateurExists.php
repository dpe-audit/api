<?php

namespace App\Validation\Ventilation;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class GenerateurExists extends Constraint
{
    public string $message = 'Le générateur {{ generateur_id }} associé à l\'installation {{ installation_id }} n\'existe pas';
    public string $mode = 'strict';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
