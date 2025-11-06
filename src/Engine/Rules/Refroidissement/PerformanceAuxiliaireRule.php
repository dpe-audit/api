<?php

namespace App\Engine\Rules\Refroidissement;

abstract class PerformanceAuxiliaireRule extends DimensionnementSystemeRule
{
    // * Données calculées

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
