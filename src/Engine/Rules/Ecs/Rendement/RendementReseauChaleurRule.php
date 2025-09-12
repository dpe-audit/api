<?php

namespace App\Engine\Rules\Ecs\Rendement;

use App\Domain\Ecs\Systeme\Reseau\IsolationReseau;
use App\Engine\Input\Ecs\SystemeInput;

final class RendementReseauChaleurRule extends RendementSystemeRule
{
    /**
     * @inheritDoc
     */
    public function rgs(): float
    {
        return match ($this->item()->isolation_reseau()) {
            IsolationReseau::ISOLE => 0.9,
            IsolationReseau::NON_ISOLE => 0.75,
            default => 0.75,
        };
    }

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return array_filter(parent::collection(), function (SystemeInput $item) {
            return $item->generateur()->type()->is_reseau_chaleur()
                || $item->generateur()->generateur_multi_batiment();
        });
    }
}
