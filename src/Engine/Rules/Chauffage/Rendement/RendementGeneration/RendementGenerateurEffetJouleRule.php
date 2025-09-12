<?php

namespace App\Engine\Rules\Chauffage\Rendement\RendementGeneration;

use App\Engine\Input\Chauffage\SystemeInput;
use App\Engine\Rules\Chauffage\Rendement\RendementSystemeRule;

final class RendementGenerateurEffetJouleRule extends RendementSystemeRule
{
    /**
     * @inheritDoc
     */
    public function rg(): float
    {
        return $this->get('rg', function (): float {
            return $this->item()->generateur()->type()->is_chaudiere() ? 0.97 : 1;
        });
    }

    public static function supports(SystemeInput $item): bool
    {
        return $item->generateur()->energie()->is_electricite()
            && false === $item->generateur()->type()->is_pac()
            && false === $item->generateur()->generateur_multi_batiment();
    }
}
