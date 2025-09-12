<?php

namespace App\Engine\Rules\Ecs\Rendement;

use App\Engine\Input\Ecs\SystemeInput;

final class RendementPacRule extends RendementSystemeRule
{
    /**
     * @inheritDoc
     */
    public function rgs(): float
    {
        return $this->get('rgs', function (): float {
            return $this->item()->generateur()->cop() ?? throw new \DomainException("Valeur COP non calculée");
        });
    }

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return array_filter(parent::collection(), function (SystemeInput $item) {
            return $item->generateur()->type()->is_pac()
                && false === $item->generateur()->generateur_multi_batiment();
        });
    }
}
