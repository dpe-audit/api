<?php

namespace App\Engine\Rules\Ventilation;

use App\Engine\Input\Ventilation\{GenerateurInputRuleIterator, InstallationInput};

final class DimensionnementGenerateurRule extends GenerateurInputRuleIterator
{
    /**
     * Ratio de dimensionnement du générateur
     */
    public function rdim(): float
    {
        return $this->get('rdim', function (): float {
            return array_sum(array_map(
                fn(InstallationInput $item) => $item->rdim(),
                $this->item()->installations(),
            ));
        });
    }
}
