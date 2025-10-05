<?php

namespace App\Engine\Rules\Refroidissement;

use App\Engine\Input\Refroidissement\GenerateurInputRuleIterator;

final class ConsommationGenerateurRule extends GenerateurInputRuleIterator
{
    /**
     * Consommation d'énergie finale du générateur de refroidissement en kWh/an
     */
    public function cef_fr(): float
    {
        return $this->get('cef_fr', function (): float {
            return array_sum(array_map(fn($item) => $item->cef_fr(), $this->item()->systemes()));
        });
    }

    /**
     * Consommation d'énergie primaire du générateur de refroidissement en kWh/an
     */
    public function cep_fr(): float
    {
        return $this->get('cep_fr', function (): float {
            return array_sum(array_map(fn($item) => $item->cep_fr(), $this->item()->systemes()));
        });
    }

    /**
     * Emissions de CO2 du générateur de refroidissement en kg/an
     */
    public function eges_fr(): float
    {
        return array_sum(array_map(fn($item) => $item->eges_fr(), $this->item()->systemes()));
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->item()->entity->calcule($this->item()->entity->data()->with(
            cef_fr: $this->cef_fr(),
            cep_fr: $this->cep_fr(),
            eges_fr: $this->eges_fr(),
        ));
    }
}
