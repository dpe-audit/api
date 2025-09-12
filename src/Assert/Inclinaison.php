<?php

namespace App\Assert;

use Symfony\Component\Validator\Constraints\Compound;
use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute]
class Inclinaison extends Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new Assert\Type('float'),
            new Assert\GreaterThanOrEqual(0),
            new Assert\LessThanOrEqual(90),
        ];
    }
}
