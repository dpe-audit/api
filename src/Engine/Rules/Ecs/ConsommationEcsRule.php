<?php

namespace App\Engine\Rules\Ecs;

use App\Domain\Ecs\Generateur\EnergieGenerateur;
use App\Engine\Input\Ecs\SystemeInputRuleIterator;

final class ConsommationEcsRule extends SystemeInputRuleIterator
{
    /**
     * Consommation finale d'eau chaude sanitaire exprimée en kWh/an
     */
    public function cef(): float
    {
        return $this->get('cef', function (): float {
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
    public function cep(): float
    {
        return $this->get('cep', function (): float {
            return $this->cef() * $this->item()->generateur()->energie()->to()->facteur_energie_primaire();
        });
    }

    /**
     * Emissions de CO2 d'eau chaude sanitaire exprimées en kg/an
     */
    public function eges(): float
    {
        return $this->get('eges', function (): float {
            if ($contenu_co2_reseau_chaleur = $this->item()->generateur()->contenu_co2_reseau_chaleur()) {
                return $this->cef() * $contenu_co2_reseau_chaleur;
            }
            return $this->cef() * match ($this->item()->generateur()->energie()) {
                EnergieGenerateur::ELECTRICITE => 0.065,
                EnergieGenerateur::GAZ_NATUREL => 0.227,
                EnergieGenerateur::GPL => 0.272,
                EnergieGenerateur::FIOUL => 0.324,
                EnergieGenerateur::BOIS_BUCHE => 0.03,
                EnergieGenerateur::BOIS_PLAQUETTE => 0.024,
                EnergieGenerateur::BOIS_GRANULE => 0.03,
                EnergieGenerateur::CHARBON => 0.385,
                EnergieGenerateur::RESEAU_CHALEUR => 0.385,
            };
        });
    }
}
