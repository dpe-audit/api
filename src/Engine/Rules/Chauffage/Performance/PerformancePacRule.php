<?php

namespace App\Engine\Rules\Chauffage\Performance;

use App\Domain\Chauffage\Generateur\{EnergieGenerateur, TypeGenerateur};
use App\Engine\Input\Chauffage\GenerateurInput;

final class PerformancePacRule extends PerformanceGenerateurRule
{
    /** @inheritDoc */
    public function rpn(): ?float
    {
        return $this->item()->bienergie() ? parent::rpn() : null;
    }

    /** @inheritDoc */
    public function rpint(): ?float
    {
        return $this->item()->bienergie() ? parent::rpint() : null;
    }

    /** @inheritDoc */
    public function qp0(): ?float
    {
        return $this->item()->bienergie() ? parent::qp0() : null;
    }

    /** @inheritDoc */
    public function pveilleuse(): ?float
    {
        return $this->item()->bienergie() ? parent::pveilleuse() : null;
    }

    /** @inheritDoc */
    public function tfonc30(): ?float
    {
        return $this->item()->bienergie() ? parent::tfonc30() : null;
    }

    /** @inheritDoc */
    public function tfonc100(): ?float
    {
        return $this->item()->bienergie() ? parent::tfonc100() : null;
    }

    protected function type_generateur(): TypeGenerateur
    {
        return TypeGenerateur::CHAUDIERE;
    }

    protected function energie_generateur(): EnergieGenerateur
    {
        return $this->item()->bienergie();
    }

    public static function supports(GenerateurInput $item): bool
    {
        return $item->type()->is_pac() && false === $item->generateur_multi_batiment();
    }
}
