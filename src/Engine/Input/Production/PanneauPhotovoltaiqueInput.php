<?php

namespace App\Engine\Input\Production;

use App\Domain\Production\PanneauPhotovoltaique\PanneauPhotovoltaique;
use App\Engine\{Engine, Input};
use App\Engine\Rules\Production\ProductionPhotovoltaiqueRule;

final class PanneauPhotovoltaiqueInput extends Input
{
    public function __construct(
        public readonly Engine $context,
        public readonly PanneauPhotovoltaique $entity,
    ) {}

    public function surface_capteurs(): float
    {
        return $this->entity->surface() ?? $this->modules() * 1.6;
    }

    public function modules(): int
    {
        return $this->entity->modules();
    }

    public function inclinaison(): float
    {
        return $this->entity->inclinaison();
    }

    public function orientation(): float
    {
        return $this->entity->orientation();
    }

    // * Données calculées

    public function ppv(): float
    {
        /** @var ProductionPhotovoltaiqueRule $rule */
        $rule = $this->requireIterator(ProductionPhotovoltaiqueRule::class, $this);
        return $rule->ppv();
    }
}
