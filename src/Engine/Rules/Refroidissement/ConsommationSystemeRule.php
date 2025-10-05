<?php

namespace App\Engine\Rules\Refroidissement;

use App\Domain\Common\Enum\Usage;
use App\Engine\Input\Refroidissement\SystemeInputRuleIterator;

final class ConsommationSystemeRule extends SystemeInputRuleIterator
{
    /**
     * Consommation finale du système de refroidissement en kWh/an
     */
    public function cef_fr(): float
    {
        return $this->get('cef_fr', function (): float {
            $bfr = $this->data()->refroidissement->bfr();
            $eer = $this->item()->generateur()->eer();
            $rdim = $this->item()->rdim();
            return 0.9 * ($bfr / $eer) * $rdim;
        });
    }

    /**
     * Consommation primaire du système de refroidissement en kWh/an
     */
    public function cep_fr(): float
    {
        return $this->get('cep_fr', function (): float {
            return $this->cef_fr() * $this->item()->generateur()->energie()->to()->facteur_energie_primaire();
        });
    }

    /**
     * Emissions de CO2 du système de refroidissement en kg/an
     * 
     * @see https://www.legifrance.gouv.fr/loda/article_lc/LEGIARTI000046662777
     */
    public function eges_fr(): float
    {
        return (null !== $contenu_co2 = $this->item()->generateur()->contenu_co2_reseau_froid())
            ? $this->cef_fr() * $contenu_co2
            : $this->cef_fr() * $this->item()->generateur()->energie()->to()->facteur_eges(Usage::REFROIDISSEMENT);
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->item()->entity->calcule($this->item()->entity->data()->with(
            cef_fr: $this->cef_fr(),
            cep_fr: $this->cep_fr(),
            eges_fr: $this->eges_fr(),
        ));
    }
}
