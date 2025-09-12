<?php

namespace App\Engine\Rules\Apport\SurfaceSudEquivalente;

use App\Domain\Enveloppe\Lnc\TypeLnc;
use App\Engine\Input\Enveloppe\LncInputRuleIterator;
use App\Utils\Math;

final class CoefficientTransparenceEtsRule extends LncInputRuleIterator
{
    /**
     * Coefficient de transparence de l'espace tampon solarisé
     */
    public function t(): ?float
    {
        return $this->get("t", function (): ?float {
            if ($this->item()->type() !== TypeLnc::ESPACE_TAMPON_SOLARISE) {
                return null;
            }
            $valeurs = [];
            $coefficients = [];
            foreach ($this->item()->baies as $item) {
                $valeurs[] = $item->t();
                $coefficients[] = $item->surface();
            }
            return Math::moyenne_ponderee($valeurs, $coefficients);
        });
    }
}
