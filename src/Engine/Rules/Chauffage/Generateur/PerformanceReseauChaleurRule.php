<?php

namespace App\Engine\Rules\Chauffage\Generateur;

use App\Domain\Chauffage\Generateur\Generateur;
use App\Engine\Rules\Chauffage\PerformanceGenerateurRule;

final class PerformanceReseauChaleurRule extends PerformanceGenerateurRule
{
    public static function supports(Generateur $entity): bool
    {
        return $entity->type()?->is_reseau_chaleur() || $entity->position()->generateur_multi_batiment;
    }

    /** @inheritDoc */
    public function scop(): ?float
    {
        return null;
    }

    /** @inheritDoc */
    public function rpn(): ?float
    {
        return null;
    }

    /** @inheritDoc */
    public function rpint(): ?float
    {
        return null;
    }

    /** @inheritDoc */
    public function qp0(): ?float
    {
        return null;
    }

    /** @inheritDoc */
    public function pveilleuse(): ?float
    {
        return null;
    }

    /** @inheritDoc */
    public function tfonc30(): ?float
    {
        return null;
    }

    /** @inheritDoc */
    public function tfonc100(): ?float
    {
        return null;
    }
}
