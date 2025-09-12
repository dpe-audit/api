<?php

namespace App\Engine\Rules\Performance;

use App\Engine\Input\Refroidissement\SystemeInput;
use App\Engine\Rule;

final class PerformanceRefroidissementRule extends Rule
{
    /**
     * Consommation finale de refroidissement en kWh/an
     */
    public function cef(): float
    {
        return $this->get('cef', function (): float {
            return array_sum(array_map(
                fn(SystemeInput $item) => $item->cef(),
                $this->data()->refroidissement->systemes
            ));
        });
    }

    /**
     * Consommation primaire de refroidissement en kWh/an
     */
    public function cep(): float
    {
        return $this->get('cep', function (): float {
            return array_sum(array_map(
                fn(SystemeInput $item) => $item->cep(),
                $this->data()->refroidissement->systemes
            ));
        });
    }

    /**
     * Emissions de CO2 de refroidissement en kg/an
     */
    public function eges(): float
    {
        return $this->get('eges', function (): float {
            return array_sum(array_map(
                fn(SystemeInput $item) => $item->eges(),
                $this->data()->refroidissement->systemes
            ));
        });
    }
}
