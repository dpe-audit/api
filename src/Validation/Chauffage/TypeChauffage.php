<?php

namespace App\Validation\Chauffage;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class TypeChauffage extends Constraint
{
    public string $message = 'Type de chauffage incohérent pour le système "{{ systeme_id }}"';
    public string $mode = 'strict';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
