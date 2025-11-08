<?php

namespace App\Validation\Diagnostic;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class GenerateurMixteExists extends Constraint
{
    public string $message = 'Le générateur mixte {{ generateur_mixte_id }} associé au générateur {{ generateur_id }} n\'existe pas';
    public string $mode = 'strict';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
