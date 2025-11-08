<?php

namespace App\Validation\Annee;

use Symfony\Component\Validator\Constraints\Compound;
use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute]
class AnneeValid extends Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new Assert\Type('int'),
            new Assert\LessThanOrEqual((int) date('Y')),
        ];
    }
}
