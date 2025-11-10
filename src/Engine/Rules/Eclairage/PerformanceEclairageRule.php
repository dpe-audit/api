<?php

namespace App\Engine\Rules\Eclairage;

use App\Domain\Common\Consommation\{Consommation, ConsommationCollection};
use App\Domain\Common\Enum\{Energie, Scenario, Usage};
use App\Engine\Rule;
use App\Engine\Rules\Batiment\WithBatimentRule;
use App\Engine\Table\EclairageTableValeurRepository;

final class PerformanceEclairageRule extends Rule
{
    use WithBatimentRule;

    // Constante de puissance d'éclairage en W/m²
    public final const PUISSANCE_ECLAIRAGE = 1.4;
    // Constante de taux d'utilisation de l'éclairage
    public final const TAUX_UTILISATION = 0.9;

    public function __construct(
        private EclairageTableValeurRepository $repository,
    ) {}

    /**
     * Liste des consommations d'éclairage
     */
    public function consommations(): ConsommationCollection
    {
        return $this->get('consommations', function (): ConsommationCollection {
            $collection = Scenario::each(fn(Scenario $scenario) => Consommation::create(
                scenario: $scenario,
                usage: Usage::ECLAIRAGE,
                energie: Energie::ELECTRICITE,
                cef: $this->cef_ecl(),
                cep: $this->cep_ecl(),
                eges: $this->eges_ecl(),
            ));
            return ConsommationCollection::create(...$collection);
        });
    }

    /**
     * Consommation finale d'éclairage en kWh/an
     */
    public function cef_ecl(): float
    {
        return $this->get('cef_ecl', function (): float {
            $cef = $this->nhecl();
            $cef *= self::PUISSANCE_ECLAIRAGE * self::TAUX_UTILISATION;
            $cef *= $this->surface_reference();
            $cef /= 1000;
            return $cef;
        });
    }

    /**
     * Consommation primaire d'éclairage en kWh/an
     */
    public function cep_ecl(): float
    {
        return $this->get('cep_ecl', function (): float {
            return $this->cef_ecl() * Energie::ELECTRICITE->facteur_energie_primaire();
        });
    }

    /**
     * Emissions de CO2 d'éclairage en kg/an
     */
    public function eges_ecl(): float
    {
        return $this->get('eges_ecl', function (): float {
            return $this->cef_ecl() * 0.069;
        });
    }

    /**
     * Nombre d'heures d'éclairage par an
     */
    public function nhecl(): float
    {
        return $this->get("nhecl", function (): float {
            return $this->repository->nhecl($this->zone_climatique())
                ?? throw new \DomainException('Valeur forfaitaires "nhecl" non trouvée');
        });
    }
}
