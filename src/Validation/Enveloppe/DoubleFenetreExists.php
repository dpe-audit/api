<?php

namespace App\Validation\Enveloppe;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class DoubleFenetreExists extends Constraint
{
    public string $message = 'Le double fenêtre {{ double_fenetre_id }} associée à la paroi {{ paroi_id }} n\'existe pas';
    public string $mode = 'strict';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
