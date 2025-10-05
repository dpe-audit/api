<?php

namespace App\Engine\Rules\Refroidissement;

use App\Engine\Input\Refroidissement\SystemeInputRuleIterator;

/**
 * @see https://github.com/dpe-audit/methode-3cl/discussions/36
 */
final class ConsommationAuxiliaireRule extends SystemeInputRuleIterator
{
    /**
     * Consommation d'énergie finale de l'auxiliaire de refroidissement en kWh/an
     */
    public function cef_aux(): float
    {
        return 0;
    }

    /**
     * Consommation d'énergie primaire de l'auxiliaire de refroidissement en kWh/an
     */
    public function cep_aux(): float
    {
        return 0;
    }

    /**
     * Emissions de CO2 de l'auxiliaire de refroidissement en kgCO2/an
     */
    public function eges_aux(): float
    {
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->item()->entity->calcule($this->item()->entity->data()->with(
            cef_aux: $this->cef_aux(),
            cep_aux: $this->cep_aux(),
            eges_aux: $this->eges_aux(),
        ));
    }
}
