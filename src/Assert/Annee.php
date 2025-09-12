<?php

namespace App\Assert;

use Symfony\Component\Validator\Constraints\Compound;
use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute]
class Annee extends Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new Assert\Type('int'),
            new Assert\LessThanOrEqual((int) date('Y')),
        ];
    }
}
