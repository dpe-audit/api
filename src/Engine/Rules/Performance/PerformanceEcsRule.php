<?php

namespace App\Engine\Rules\Performance;

use App\Engine\Input\Ecs\SystemeInput;
use App\Engine\Rule;

final class PerformanceEcsRule extends Rule
{
    /**
     * Consommation finale d'eau chaude sanitaire en kWh/an
     */
    public function cef(): float
    {
        return $this->get('cef', function (): float {
            return array_sum(array_map(
                fn(SystemeInput $item) => $item->cef(),
                $this->data()->ecs->systemes
            ));
        });
    }

    /**
     * Consommation primaire d'eau chaude sanitaire en kWh/an
     */
    public function cep(): float
    {
        return $this->get('cep', function (): float {
            return array_sum(array_map(
                fn(SystemeInput $item) => $item->cep(),
                $this->data()->ecs->systemes
            ));
        });
    }

    /**
     * Emission de CO2 d'eau chaude sanitaire en kg/an
     */
    public function eges(): float
    {
        return $this->get('eges', function (): float {
            return array_sum(array_map(
                fn(SystemeInput $item) => $item->eges(),
                $this->data()->ecs->systemes
            ));
        });
    }
}
