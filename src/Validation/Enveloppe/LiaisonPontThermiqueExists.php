<?php

namespace App\Validation\Enveloppe;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class LiaisonPontThermiqueExists extends Constraint
{
    public string $message = 'La paroi {{ paroi_id }} associée au pont thermique {{ pont_thermique_id }} n\'existe pas';
    public string $mode = 'strict';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
