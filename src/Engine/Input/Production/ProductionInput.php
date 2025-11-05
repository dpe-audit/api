<?php

namespace App\Engine\Input\Production;

use App\Domain\Production\PanneauPhotovoltaique\PanneauPhotovoltaique;
use App\Domain\Production\Production;
use App\Engine\{Engine, Input};
use App\Engine\Rules\Production\ProductionRenouvelableRule;

final class ProductionInput extends Input
{
    /** @var PanneauPhotovoltaiqueInput[] */
    public readonly array $panneaux_photovoltaiques;

    public function __construct(public readonly Production $entity, public readonly Engine $context)
    {
        $this->panneaux_photovoltaiques = $entity->panneaux_photovoltaiques()
            ->map(fn(PanneauPhotovoltaique $item) => new PanneauPhotovoltaiqueInput($context, $item))
            ->values();
    }

    // * Données calculées

    public function ppv(): float
    {
        /** @var ProductionRenouvelableRule $rule */
        $rule = $this->require(ProductionRenouvelableRule::class);
        return $rule->ppv();
    }
}
