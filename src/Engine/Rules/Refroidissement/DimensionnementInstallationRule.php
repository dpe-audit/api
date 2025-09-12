<?php

namespace App\Engine\Rules\Refroidissement;

use App\Engine\Input\Refroidissement\{InstallationInput, InstallationInputRuleIterator};

final class DimensionnementInstallationRule extends InstallationInputRuleIterator
{
    /**
     * Ratio de dimensionnement de l'installation de refroidissement
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
