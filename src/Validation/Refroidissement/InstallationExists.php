<?php

namespace App\Validation\Refroidissement;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class InstallationExists extends Constraint
{
    public string $message = 'L\'installation {{ installation_id }} associée au système {{ systeme_id }} n\'existe pas';
    public string $mode = 'strict';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
