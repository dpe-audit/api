<?php

namespace App\Validation\Enveloppe;

use Symfony\Component\Validator\Constraints\Compound;

#[\Attribute]
final class EnveloppeValid extends Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new DoubleFenetreExists,
            new LiaisonPontThermiqueExists,
            new LncExists,
            new MasqueExists,
            new ParoiExists,
        ];
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
