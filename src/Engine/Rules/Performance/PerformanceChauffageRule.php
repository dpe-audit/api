<?php

namespace App\Engine\Rules\Performance;

use App\Engine\Input\Chauffage\SystemeInput;
use App\Engine\Rule;

final class PerformanceChauffageRule extends Rule
{
    /**
     * Consommation finale de chauffage en kWh/an
     */
    public function cef(): float
    {
        return $this->get('cef', function (): float {
            return array_sum(array_map(
                fn(SystemeInput $item) => $item->cef(),
                $this->data()->chauffage->systemes
            ));
        });
    }

    /**
     * Consommation primaire de chauffage en kWh/an
     */
    public function cep(): float
    {
        return $this->get('cep', function (): float {
            return array_sum(array_map(
                fn(SystemeInput $item) => $item->cep(),
                $this->data()->chauffage->systemes
            ));
        });
    }

    /**
     * Emission de CO2 de chauffage en kg/an
     */
    public function eges(): float
    {
        return $this->get('eges', function (): float {
            return array_sum(array_map(
                fn(SystemeInput $item) => $item->eges(),
                $this->data()->chauffage->systemes
            ));
        });
    }
}
