<?php

namespace App\Assert;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class Ventilation extends Constraint
{
    final const ANNEE_INSTALLATION_INVALID = "L'année d'installation %x% du générateur %g% est inférieure à l'année de construction du bâtiment";
    final const INSTALLATION_NOT_FOUND = "L'installation %i% associée au système de ventilation %s% non trouvée";
    final const GENERATEUR_NOT_FOUND = "Le générateur %g% associé au système de ventilation %s% non trouvé";

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
