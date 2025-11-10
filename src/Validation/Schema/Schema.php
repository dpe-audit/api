<?php

namespace App\Validation\Schema;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class Schema extends Constraint
{
    public function __construct(public string $schemaId, $groups = null, $payload = null)
    {
        parent::__construct([], $groups, $payload);
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
