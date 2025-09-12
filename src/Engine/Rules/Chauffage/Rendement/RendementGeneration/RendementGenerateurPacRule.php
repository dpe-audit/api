<?php

namespace App\Engine\Rules\Chauffage\Rendement\RendementGeneration;

use App\Engine\Input\Chauffage\SystemeInput;
use App\Engine\Rules\Chauffage\Rendement\RendementSystemeRule;

final class RendementGenerateurPacRule extends RendementSystemeRule
{
    /**
     * @inheritDoc
     */
    public function rg(): float
    {
        return $this->get('rg', function (): float {
            return $this->item()->generateur()->scop() ?? throw new \DomainException('SCOP non calculée');
        });
    }

    public static function supports(SystemeInput $item): bool
    {
        return $item->generateur()->type()->is_pac()
            && null === $item->generateur()->bienergie()
            && false === $item->generateur()->generateur_multi_batiment();
    }
}
