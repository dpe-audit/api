<?php

namespace App\Engine\Rules\Chauffage\Rendement\RendementGeneration;

use App\Domain\Chauffage\Generateur\TypeGenerateur;
use App\Engine\Input\Chauffage\SystemeInput;
use App\Engine\Rules\Chauffage\Rendement\RendementSystemeRule;

final class RendementReseauChaleurRule extends RendementSystemeRule
{
    /**
     * @inheritDoc
     */
    public function rg(): float
    {
        return $this->get('rg', fn(): float => 0.97);
    }

    public static function supports(SystemeInput $item): bool
    {
        return $item->generateur()->type()->is_reseau_chaleur()
            || $item->generateur()->generateur_multi_batiment();
    }
}
