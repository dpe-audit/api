<?php

namespace App\Validation\Enveloppe;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class ParoiExists extends Constraint
{
    public string $message = 'La paroi {{ paroi_id }} associée à la paroi {{ id }} n\'existe pas';
    public string $mode = 'strict';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
