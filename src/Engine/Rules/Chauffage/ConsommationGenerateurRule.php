<?php

namespace App\Engine\Rules\Chauffage;

use App\Engine\Input\Chauffage\GenerateurInputRuleIterator;

final class ConsommationGenerateurRule extends GenerateurInputRuleIterator
{
    /**
     * Consommation d'énergie finale du générateur de chauffage en kWh/an
     */
    public function cef_ch(): float
    {
        return $this->get('cef_ch', function (): float {
            return array_sum(array_map(fn($item) => $item->cef_ch(), $this->item()->systemes()));
        });
    }

    /**
     * Consommation d'énergie primaire du générateur de chauffage en kWh/an
     */
    public function cep_ch(): float
    {
        return $this->get('cep_ch', function (): float {
            return array_sum(array_map(fn($item) => $item->cep_ch(), $this->item()->systemes()));
        });
    }

    /**
     * Emissions de CO2 du générateur de chauffage en kg/an
     */
    public function eges_ch(): float
    {
        return array_sum(array_map(fn($item) => $item->eges_ch(), $this->item()->systemes()));
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->item()->entity->calcule($this->item()->entity->data()->with(
            cef_ch: $this->cef_ch(),
            cep_ch: $this->cep_ch(),
            eges_ch: $this->eges_ch(),
        ));
    }
}
