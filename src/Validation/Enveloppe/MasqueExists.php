<?php

namespace App\Validation\Enveloppe;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class MasqueExists extends Constraint
{
    public string $message = 'Le masque {{ masque_id }} associé à la paroi {{ paroi_id }} n\'existe pas';
    public string $mode = 'strict';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
