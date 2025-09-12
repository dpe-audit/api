<?php

namespace App\Engine\Rules\Chauffage\Rendement\RendementGeneration;

use App\Engine\Input\Chauffage\SystemeInput;
use App\Engine\Rules\Chauffage\Rendement\RendementSystemeRule;

final class RendementPoeleInsertRule extends RendementSystemeRule
{
    /**
     * @inheritDoc
     */
    public function rg(): float
    {
        return $this->get('rg', function (): float {
            return $this->repository->rg(
                type_generateur: $this->item()->generateur()->type(),
                energie_generateur: $this->item()->generateur()->energie(),
                label_generateur: $this->item()->generateur()->label(),
                anne_installation_generateur: $this->item()->generateur()->annee_installation(),
            ) ?? throw new \DomainException('Valeur forfaitaire Rg non trouvée');
        });
    }

    public static function supports(SystemeInput $item): bool
    {
        return $item->generateur()->type()->is_poele_insert()
            || $item->generateur()->generateur_multi_batiment();
    }
}
