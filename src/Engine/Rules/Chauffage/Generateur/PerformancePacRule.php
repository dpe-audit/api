<?php

namespace App\Engine\Rules\Chauffage\Generateur;

use App\Domain\Chauffage\Generateur\{Generateur, EnergieGenerateur};
use App\Engine\Rules\Chauffage\PerformanceGenerateurRule;

final class PerformancePacRule extends PerformanceGenerateurRule
{
    public static function supports(Generateur $entity): bool
    {
        return $entity->type()?->is_pac() && false === $entity->position()->generateur_multi_batiment;
    }

    public function bienergie(): ?EnergieGenerateur
    {
        return $this->item()->bienergie();
    }

    /** @inheritDoc */
    public function rpn(): ?float
    {
        return $this->bienergie() ? parent::rpn() : null;
    }

    /** @inheritDoc */
    public function rpint(): ?float
    {
        return $this->bienergie() ? parent::rpint() : null;
    }

    /** @inheritDoc */
    public function qp0(): ?float
    {
        return $this->bienergie() ? parent::qp0() : null;
    }

    /** @inheritDoc */
    public function pveilleuse(): ?float
    {
        return $this->bienergie() ? parent::pveilleuse() : null;
    }

    /** @inheritDoc */
    public function tfonc30(): ?float
    {
        return $this->bienergie() ? parent::tfonc30() : null;
    }

    /** @inheritDoc */
    public function tfonc100(): ?float
    {
        return $this->bienergie() ? parent::tfonc100() : null;
    }
}
