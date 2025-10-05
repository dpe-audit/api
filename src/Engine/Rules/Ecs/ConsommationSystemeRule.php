<?php

namespace App\Engine\Rules\Ecs;

use App\Domain\Common\Enum\Usage;
use App\Domain\Ecs\Generateur\EnergieGenerateur;
use App\Engine\Input\Ecs\SystemeInputRuleIterator;

final class ConsommationSystemeRule extends SystemeInputRuleIterator
{
    /**
     * Consommation finale d'eau chaude sanitaire exprimée en kWh/an
     */
    public function cef_ecs(): float
    {
        return $this->get('cef_ecs', function (): float {
            $becs = $this->data()->ecs->becs();
            $iecs = $this->item()->iecs();
            $fecs = $this->item()->installation()->fecs();
            $rdim = $this->item()->rdim();
            return $becs * (1 - $fecs) * $iecs * $rdim;
        });
    }

    /**
     * Consommation primaire d'eau chaude sanitaire exprimée en kWh/an
     */
    public function cep_ecs(): float
    {
        return $this->get('cep_ecs', function (): float {
            return $this->cef_ecs() * $this->item()->generateur()->energie()->to()->facteur_energie_primaire();
        });
    }

    /**
     * Emissions de CO2 d'eau chaude sanitaire exprimées en kg/an
     */
    public function eges_ecs(): float
    {
        return $this->get('eges_ecs', function (): float {
            if ($contenu_co2_reseau_chaleur = $this->item()->generateur()->contenu_co2_reseau_chaleur()) {
                return $this->cef_ecs() * $contenu_co2_reseau_chaleur;
            }
            return $this->cef_ecs() * match ($this->item()->generateur()->energie()) {
                EnergieGenerateur::BOIS_BUCHE => 0.03,
                EnergieGenerateur::BOIS_PLAQUETTE => 0.024,
                EnergieGenerateur::BOIS_GRANULE => 0.03,
                default => $this->item()->generateur()->energie()->to()->facteur_eges(Usage::CHAUFFAGE),
            };
        });
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->item()->entity->calcule($this->item()->entity->data()->with(
            cef_ecs: $this->cef_ecs(),
            cep_ecs: $this->cep_ecs(),
            eges_ecs: $this->eges_ecs(),
        ));
    }
}
