<?php

namespace App\Engine\Rules\Ecs\Dimensionnement;

use App\Engine\Input\Ecs\{InstallationInput, InstallationInputRuleIterator};

final class DimensionnementInstallationRule extends InstallationInputRuleIterator
{
    /**
     * Ratio de dimensionnement de l'installation d'eau chaude sanitaire
     */
    public function rdim(): float
    {
        return $this->get('rdim', function (): float {
            return $this->item()->surface() / array_sum(array_map(
                fn(InstallationInput $item): float => $item->surface(),
                $this->data()->refroidissement->installations
            ));
        });
    }
}
