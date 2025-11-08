<?php

namespace App\Validation\Adresse;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class AdresseExists extends Constraint
{
    public string $message = 'Commune introuvable pour le code postal {{ code_postal }} et le code INSEE {{ code_insee }}';
    public string $mode = 'strict';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
