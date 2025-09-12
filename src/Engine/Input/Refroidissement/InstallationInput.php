<?php

namespace App\Engine\Input\Refroidissement;

use App\Domain\Refroidissement\Installation\Installation;
use App\Engine\{Engine, Input};
use App\Engine\Rules\Refroidissement\DimensionnementInstallationRule;

final class InstallationInput extends Input
{
    private ?array $systemes = null;
    private ?array $generateurs = null;

    public function __construct(
        public readonly Engine $context,
        public readonly Installation $entity,
    ) {}

    /**
     * @return SystemeInput[]
     */
    public function systemes(): array
    {
        return $this->systemes ??= array_filter(
            $this->context->data()->refroidissement->systemes,
            fn(SystemeInput $item) => $item->entity->installation() === $this->entity
        );
    }

    /**
     * @return GenerateurInput[]
     */
    public function generateurs(): array
    {
        return $this->generateurs ??= array_map(
            fn(SystemeInput $item) => $item->generateur(),
            $this->systemes()
        );
    }

    public function surface(): float
    {
        return $this->entity->surface();
    }

    public function surface_totale(): float
    {
        return $this->context->ressource()->refroidissement()->installations()->surface();
    }

    // * Données calculées

    public function rdim(): float
    {
        /** @var DimensionnementInstallationRule $rule */
        $rule = $this->requireIterator(DimensionnementInstallationRule::class, $this);
        return $rule->rdim();
    }
}
