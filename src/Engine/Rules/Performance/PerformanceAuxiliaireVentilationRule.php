<?php

namespace App\Engine\Rules\Performance;

use App\Engine\Input\Ventilation\GenerateurInput;
use App\Engine\Rule;

final class PerformanceAuxiliairesVentilationRule extends Rule
{
    /**
     * Consommation finale des auxiliaires de ventilation en kWh/an
     */
    public function cef(): float
    {
        return $this->get('cef', function (): float {
            return array_sum(array_map(
                fn(GenerateurInput $item) => $item->cef_auxiliaire(),
                $this->data()->ventilation->generateurs
            ));
        });
    }

    /**
     * Consommation primaire des auxiliaires de ventilation en kWh/an
     */
    public function cep(): float
    {
        return $this->get('cep', function (): float {
            return array_sum(array_map(
                fn(GenerateurInput $item) => $item->cep_auxiliaire(),
                $this->data()->ventilation->generateurs
            ));
        });
    }

    /**
     * Emission de CO2 des auxiliaires de ventilation en kg/an
     */
    public function eges(): float
    {
        return $this->get('eges', function (): float {
            return array_sum(array_map(
                fn(GenerateurInput $item) => $item->eges_auxiliaire(),
                $this->data()->ventilation->generateurs
            ));
        });
    }
}
