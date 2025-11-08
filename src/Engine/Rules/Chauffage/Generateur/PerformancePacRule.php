<?php

namespace App\Engine\Rules\Chauffage\Generateur;

use App\Engine\Rules\Chauffage\PerformanceGenerateurRule;

final class PerformancePacRule extends PerformanceGenerateurRule
{
    public function supports(): bool
    {
        return $this->type_generateur()->is_pac() && false === $this->generateur_multi_batiment();
    }

    /** @inheritDoc */
    public function rpn(): ?float
    {
        return $this->bienergie_generateur() ? parent::rpn() : null;
    }

    /** @inheritDoc */
    public function rpint(): ?float
    {
        return $this->bienergie_generateur() ? parent::rpint() : null;
    }

    /** @inheritDoc */
    public function qp0(): ?float
    {
        return $this->bienergie_generateur() ? parent::qp0() : null;
    }

    /** @inheritDoc */
    public function pveilleuse(): ?float
    {
        return $this->bienergie_generateur() ? parent::pveilleuse() : null;
    }

    /** @inheritDoc */
    public function tfonc30(): ?float
    {
        return $this->bienergie_generateur() ? parent::tfonc30() : null;
    }

    /** @inheritDoc */
    public function tfonc100(): ?float
    {
        return $this->bienergie_generateur() ? parent::tfonc100() : null;
    }
}
