<?php

namespace App\Engine\Rules\Performance;

use App\Domain\Common\Consommation\ConsommationCollection;
use App\Domain\Common\Perte\PerteCollection;
use App\Domain\Ressource\{Bilan, EtiquetteClimat, EtiquetteEnergie};
use App\Engine\Rule;
use App\Engine\Table\PerformanceTableValeurRepository;

final class PerformanceRule extends Rule
{
    public function __construct(
        private PerformanceTableValeurRepository $repository
    ) {}

    /**
     * Liste des consommations
     */
    public function consommations(): ConsommationCollection
    {
        return $this->get('consommations', function (): ConsommationCollection {
            return ConsommationCollection::create(...[
                ...$this->data()->chauffage->consommations()->values(),
                ...$this->data()->ecs->consommations()->values(),
                ...$this->data()->refroidissement->consommations()->values(),
                ...$this->data()->ventilation->consommations()->values(),
                ...$this->data()->eclairage->consommations()->values(),
            ]);
        });
    }

    /**
     * Liste des pertes
     */
    public function pertes(): PerteCollection
    {
        return $this->get('pertes', function (): PerteCollection {
            return PerteCollection::create(...[
                ...$this->data()->chauffage->pertes()->values(),
                ...$this->data()->ecs->pertes()->values(),
            ]);
        });
    }

    /**
     * Consommation finale d'énergie en kWh/m²/an
     */
    public function cef(): float
    {
        return $this->get('cef', function (): float {
            return $this->consommations()->cef() / $this->data()->batiment->surface_habitable();
        });
    }

    /**
     * Consommation primaire d'énergie en kWh/m²/an
     */
    public function cep(): float
    {
        return $this->get('cep', function (): float {
            return $this->consommations()->cep() / $this->data()->batiment->surface_habitable();
        });
    }

    /**
     * Emisssions de CO2 exprimées en kg/m²/an
     */
    public function eges(): float
    {
        return $this->get('eges', function (): float {
            return $this->consommations()->eges() / $this->data()->batiment->surface_habitable();
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
                cep: $this->cep(),
                eges: $this->eges(),
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
                eges: $this->eges(),
            ) ?? throw new \DomainException("Etiquette climat non trouvée");
        });
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->ressource()->calcule($this->ressource()->data()->with(
            pertes: $this->pertes(),
            consommations: $this->consommations(),
            bilan: Bilan::create(
                cef: $this->cef(),
                cep: $this->cep(),
                eges: $this->eges(),
                etiquette_energie: $this->etiquette_energie(),
                etiquette_climat: $this->etiquette_climat(),
            )
        ));
    }
}
