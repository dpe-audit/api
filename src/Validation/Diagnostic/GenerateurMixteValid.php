<?php

namespace App\Validation\Diagnostic;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class GenerateurMixteValid extends Constraint
{
    public const MESSAGE_GENERATEUR_MIXTE_MISSING = 'Le générateur mixte {{ generateur_mixte_id }} associé au générateur {{ generateur_id }} n\'est pas mixte'; 
    public const MESSAGE_GENERATEUR_MIXTE_NOT_SAME = 'Le générateur mixte {{ generateur_mixte_id }} associé au générateur {{ generateur_id }} référence un générateur différent'; 

    public string $mode = 'strict';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
