<?php

namespace App\Engine\Rules\Ecs;

use App\Domain\Ecs\Installation\Installation;
use App\Engine\RuleIterator;

/**
 * @extends RuleIterator<Installation>
 */
abstract class DimensionnementInstallationRule extends RuleIterator
{
    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->id();
    }

    // * Données d'entrée

    public function surface(): float
    {
        return $this->item()->surface();
    }

    public function surface_totale(): float
    {
        return $this->input()->ecs->installations()->surface();
    }

    // * Données de sortie

    /**
     * Ratio de dimensionnement de l'installation d'eau chaude sanitaire
     */
    public function rdim(): float
    {
        return $this->get('rdim', function (): float {
            return $this->surface() / $this->surface_totale();
        });
    }
}
