<?php

namespace App\Engine\Rules\Chauffage\Generateur;

use App\Engine\Rules\Chauffage\PerformanceGenerateurRule;

final class PerformancePoeleInsertRule extends PerformanceGenerateurRule
{
    public function supports(): bool
    {
        return $this->type_generateur()->is_poele_insert() && false === $this->generateur_multi_batiment();
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
