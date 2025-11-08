<?php

namespace App\Validation\Chauffage;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class EmetteurExists extends Constraint
{
    public string $message = 'L\'emetteur {{ emetteur_id }} associé au système {{ systeme_id }} n\'existe pas';
    public string $mode = 'strict';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
