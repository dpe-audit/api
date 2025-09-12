<?php

namespace App\Engine\Rules\Performance;

use App\Domain\Ressource\{EtiquetteClimat, EtiquetteEnergie};
use App\Engine\Rule;
use App\Engine\Table\PerformanceTableValeurRepository;

final class PerformanceRule extends Rule
{
    public function __construct(
        private PerformanceTableValeurRepository $repository
    ) {}

    /**
     * Consommation finale d'énergie en kWh/an
     */
    public function cef(): float
    {
        return $this->get('cef', function (): float {
            return array_sum([
                $this->data()->cef_chauffage(),
                $this->data()->cef_ecs(),
                $this->data()->cef_refroidissement(),
                $this->data()->cef_eclairage(),
                $this->data()->cef_auxiliaires(),
            ]);
        });
    }

    /**
     * Consommation primaire d'énergie en kWh/an
     */
    public function cep(): float
    {
        return $this->get('cep', function (): float {
            return array_sum([
                $this->data()->cep_chauffage(),
                $this->data()->cep_ecs(),
                $this->data()->cep_refroidissement(),
                $this->data()->cep_eclairage(),
                $this->data()->cep_auxiliaires(),
            ]);
        });
    }

    /**
     * Emisssions de CO2 exprimées en kg/an
     */
    public function eges(): float
    {
        return $this->get('eges', function (): float {
            return array_sum([
                $this->data()->eges_chauffage(),
                $this->data()->eges_ecs(),
                $this->data()->eges_refroidissement(),
                $this->data()->eges_eclairage(),
                $this->data()->eges_auxiliaires(),
            ]);
        });
    }

    /**
     * Consommation finale d'énergie en kWh/m²/an
     */
    public function cef_m2(): float
    {
        return $this->get('cef_m2', function (): float {
            return $this->cef() / $this->data()->batiment->surface_habitable();
        });
    }

    /**
     * Consommation primaire d'énergie en kWh/m²/an
     */
    public function cep_m2(): float
    {
        return $this->get('cep_m2', function (): float {
            return $this->cep() / $this->data()->batiment->surface_habitable();
        });
    }

    /**
     * Emisssions de CO2 exprimées en kg/m²/an
     */
    public function eges_m2(): float
    {
        return $this->get('eges_m2', function (): float {
            return $this->eges() / $this->data()->batiment->surface_habitable();
        });
    }

    /**
     * Etiquette énergie
     */
    public function etiquette_energie(): EtiquetteEnergie
    {
        return $this->get('etiquette_energie', function (): EtiquetteEnergie {
            return $this->repository->etiquette_energie(
                zone_climatique: $this->data()->batiment->zone_climatique(),
                altitude: $this->data()->batiment->altitude(),
                cep: $this->cep_m2(),
                eges: $this->eges_m2(),
            ) ?? throw new \DomainException("Etiquette énergie non trouvée");
        });
    }

    /**
     * Etiquette climat
     */
    public function etiquette_climat(): EtiquetteClimat
    {
        return $this->get('etiquette_climat', function (): EtiquetteClimat {
            return $this->repository->etiquette_climat(
                zone_climatique: $this->data()->batiment->zone_climatique(),
                altitude: $this->data()->batiment->altitude(),
                eges: $this->eges_m2(),
            ) ?? throw new \DomainException("Etiquette climat non trouvée");
        });
    }
}
