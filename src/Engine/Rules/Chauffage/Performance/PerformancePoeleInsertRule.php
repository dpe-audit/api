<?php

namespace App\Engine\Rules\Chauffage\Performance;

use App\Engine\Input\Chauffage\GenerateurInput;

final class PerformancePoeleInsertRule extends PerformanceGenerateurRule
{
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

    public static function supports(GenerateurInput $item): bool
    {
        return $item->type()->is_poele_insert() && false === $item->generateur_multi_batiment();
    }
}
