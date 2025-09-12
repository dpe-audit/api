<?php

namespace App\Engine\Rules\Performance;

use App\Engine\Rule;

final class PerformanceAuxiliairesRule extends Rule
{
    /**
     * Consommation finale des auxiliaires (chauffage, ecs, ventilation) en kWh/an
     */
    public function cef(): float
    {
        return $this->get('cef', function (): float {
            return array_sum([
                $this->data()->chauffage->cef_auxiliaires(),
                $this->data()->ecs->cef_auxiliaires(),
                $this->data()->ventilation->cef_auxiliaires(),
            ]);
        });
    }

    /**
     * Consommation primaire des auxiliaires (chauffage, ecs, ventilation) en kWh/an
     */
    public function cep(): float
    {
        return $this->get('cep', function (): float {
            return array_sum([
                $this->data()->chauffage->cep_auxiliaires(),
                $this->data()->ecs->cep_auxiliaires(),
                $this->data()->ventilation->cep_auxiliaires(),
            ]);
        });
    }

    /**
     * Emission de CO2 des auxiliaires (chauffage, ecs, ventilation) en kg/an
     */
    public function eges(): float
    {
        return $this->get('eges', function (): float {
            return array_sum([
                $this->data()->chauffage->eges_auxiliaires(),
                $this->data()->ecs->eges_auxiliaires(),
                $this->data()->ventilation->eges_auxiliaires(),
            ]);
        });
    }
}
