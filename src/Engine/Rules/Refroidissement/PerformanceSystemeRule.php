<?php

namespace App\Engine\Rules\Refroidissement;

use App\Domain\Common\Consommation\{Consommation, ConsommationCollection};
use App\Domain\Common\Enum\{Scenario, Usage};
use App\Engine\Context;

final class PerformanceSystemeRule extends PerformanceAuxiliaireRule
{
    /**
     * Liste des consommations du système de refroidissement
     */
    public function consommations(): ConsommationCollection
    {
        return $this->get('consommations', function (): ConsommationCollection {
            $collection = parent::consommations();

            return $collection->with(...Scenario::each(fn(Scenario $scenario) => Consommation::create(
                scenario: $scenario,
                usage: Usage::REFROIDISSEMENT,
                energie: $this->energie_generateur()->to(),
                cef: $this->cef_fr($scenario),
                cep: $this->cep_fr($scenario),
                eges: $this->eges_fr($scenario),
            )));
        });
    }

    /**
     * Consommation finale du système de refroidissement en kWh/an
     */
    public function cef_fr(Scenario $scenario): float
    {
        return $this->get(self::implode(['cef_fr', $scenario]), function () use ($scenario): float {
            return 0.9 * ($this->bfr($scenario) / $this->eer()) * $this->rdim();
        });
    }

    /**
     * Consommation primaire du système de refroidissement en kWh/an
     */
    public function cep_fr(Scenario $scenario): float
    {
        return $this->get(self::implode(['cep_fr', $scenario]), function () use ($scenario): float {
            return $this->cef_fr($scenario) * $this->energie_generateur()->to()->facteur_energie_primaire();
        });
    }

    /**
     * Emissions de CO2 du système de refroidissement en kg/an
     */
    public function eges_fr(Scenario $scenario): float
    {
        return $this->get(self::implode(['eges_fr', $scenario]), function () use ($scenario): float {
            return (null !== $contenu_co2 = $this->contenu_co2_reseau_froid())
                ? $this->cef_fr($scenario) * $contenu_co2
                : $this->cef_fr($scenario) * $this->energie_generateur()->to()->facteur_eges(Usage::REFROIDISSEMENT);
        });
    }

    /**
     * @inheritDoc
     */
    public function __invoke(mixed $data, Context $context): void
    {
        parent::__invoke($data, $context);

        foreach ($this as $rule) {
            $rule->item()->calcule($rule->item()->data()->with(
                rdim: $rule->rdim(),
                consommations: $rule->consommations(),
            ));
        }
    }
}
