<?php

namespace App\Engine\Rules\Ventilation;

use App\Engine\Input\Ventilation\{InstallationInput, InstallationInputRuleIterator};

final class DimensionnementInstallationRule extends InstallationInputRuleIterator
{
    /**
     * Ratio de dimensionnement de l'installation
     */
    public function rdim(): float
    {
        return $this->get('rdim', function (): float {
            return $this->item()->surface() / array_sum(array_map(
                fn(InstallationInput $item): float => $item->surface(),
                $this->data()->ventilation->installations
            ));
        });
    }
}
