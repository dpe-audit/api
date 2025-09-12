<?php

namespace App\Engine\Rules\Refroidissement;

use App\Domain\Refroidissement\Generateur\EnergieGenerateur;
use App\Engine\Input\Refroidissement\SystemeInputRuleIterator;

final class ConsommationRefroidissementRule extends SystemeInputRuleIterator
{
    /**
     * Consommation finale du système de refroidissement en kWh/an
     */
    public function cef(): float
    {
        return $this->get('cef', function (): float {
            $bfr = $this->data()->refroidissement->bfr();
            $eer = $this->item()->generateur()->eer();
            $rdim = $this->item()->rdim();
            return 0.9 * ($bfr / $eer) * $rdim;
        });
    }

    /**
     * Consommation primaire du système de refroidissement en kWh/an
     */
    public function cep(): float
    {
        return $this->get('cep', function (): float {
            return $this->cef() * $this->item()->generateur()->energie()->to()->facteur_energie_primaire();
        });
    }

    /**
     * Emissions de CO2 du système de refroidissement en kg/an
     * 
     * @see https://www.legifrance.gouv.fr/loda/article_lc/LEGIARTI000046662777
     */
    public function eges(): float
    {
        if (null !== $contenu_co2 = $this->item()->generateur()->contenu_co2_reseau_froid()) {
            return $this->cef() * $contenu_co2;
        }
        return $this->cef() * match ($this->item()->generateur()->energie()) {
            EnergieGenerateur::ELECTRICITE => 0.064,
            EnergieGenerateur::GAZ_NATUREL => 0.227,
            EnergieGenerateur::GPL => 0.272,
            EnergieGenerateur::RESEAU_FROID => 0.385,
        };
    }
}
