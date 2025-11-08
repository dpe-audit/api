<?php

namespace App\Engine\Rules\Refroidissement;

use App\Domain\Common\Enum\Usage;
use App\Engine\Context;

final class PerformanceSystemeRule extends PerformanceAuxiliaireRule
{
    /**
     * Consommation finale du système de refroidissement en kWh/an
     */
    public function cef_fr(): float
    {
        return $this->get('cef_fr', function (): float {
            return 0.9 * ($this->bfr() / $this->eer()) * $this->rdim();
        });
    }

    /**
     * Consommation primaire du système de refroidissement en kWh/an
     */
    public function cep_fr(): float
    {
        return $this->get('cep_fr', function (): float {
            return $this->cef_fr() * $this->energie_generateur()->to()->facteur_energie_primaire();
        });
    }

    /**
     * Emissions de CO2 du système de refroidissement en kg/an
     */
    public function eges_fr(): float
    {
        return $this->get('eges_fr', function (): float {
            return (null !== $contenu_co2 = $this->contenu_co2_reseau_froid())
                ? $this->cef_fr() * $contenu_co2
                : $this->cef_fr() * $this->energie_generateur()->to()->facteur_eges(Usage::REFROIDISSEMENT);
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
                cef_fr: $rule->cef_fr(),
                cep_fr: $rule->cep_fr(),
                eges_fr: $rule->eges_fr(),
                cef_aux: $rule->cef_aux(),
                cep_aux: $rule->cep_aux(),
                eges_aux: $rule->eges_aux(),
            ));
        }
    }
}
