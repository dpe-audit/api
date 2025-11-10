<?php

namespace App\Engine\Rules\Refroidissement;

use App\Domain\Common\Consommation\{Consommation, ConsommationCollection};
use App\Domain\Common\Enum\{Energie, Scenario, Usage};

abstract class PerformanceAuxiliaireRule extends DimensionnementSystemeRule
{
    /**
     * Liste des consommations des auxiliaires de refroidissement
     */
    public function consommations(): ConsommationCollection
    {
        $collection = new ConsommationCollection();
        return $collection->with(...Scenario::each(function (Scenario $scenario): Consommation {
            return Consommation::create(
                scenario: $scenario,
                usage: Usage::AUXILIAIRE,
                energie: Energie::ELECTRICITE,
                cef: $this->cef_aux(),
                cep: $this->cep_aux(),
                eges: $this->eges_aux(),
            );
        }));
    }

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
}
