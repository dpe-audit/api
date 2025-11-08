<?php

namespace App\Validation\Enveloppe;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class LncExists extends Constraint
{
    public string $message = 'Le local non chauffé {{ lnc_id }} associé à la paroi {{ paroi_id }} n\'existe pas';
    public string $mode = 'strict';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
