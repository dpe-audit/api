<?php

namespace App\Engine\Rules\Ecs;

use App\Engine\Input\Ecs\GenerateurInputRuleIterator;

final class ConsommationGenerateurRule extends GenerateurInputRuleIterator
{
    /**
     * Consommation d'énergie finale du générateur d'eau chaude sanitaire en kWh/an
     */
    public function cef_ecs(): float
    {
        return $this->get('cef_ecs', function (): float {
            return array_sum(array_map(fn($item) => $item->cef_ecs(), $this->item()->systemes()));
        });
    }

    /**
     * Consommation d'énergie primaire du générateur d'eau chaude sanitaire en kWh/an
     */
    public function cep_ecs(): float
    {
        return $this->get('cep_ecs', function (): float {
            return array_sum(array_map(fn($item) => $item->cep_ecs(), $this->item()->systemes()));
        });
    }

    /**
     * Emissions de CO2 du générateur d'eau chaude sanitaire en kg/an
     */
    public function eges_ecs(): float
    {
        return array_sum(array_map(fn($item) => $item->eges_ecs(), $this->item()->systemes()));
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->item()->entity->calcule($this->item()->entity->data()->with(
            cef_ecs: $this->cef_ecs(),
            cep_ecs: $this->cep_ecs(),
            eges_ecs: $this->eges_ecs(),
        ));
    }
}
