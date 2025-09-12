<?php

namespace App\Engine\Rules\Apport\SurfaceSudEquivalente;

use App\Domain\Common\Enum\Mois;
use App\Engine\Input\Enveloppe\{BaieInput, LncBaieInput, LncInputRuleIterator};
use App\Utils\Math;

final class SurfaceSudEquivalenteEtsRule extends LncInputRuleIterator
{
    /**
     * Surface sud équivalente représentant les apports solaires indirects dans le logement pour le mois j
     */
    public function ssind_j(Mois $mois): float
    {
        return $this->get("ssind::{$mois->value}", function () use ($mois): float {
            return $this->sst_j($mois) - $this->ssd_j($mois) * $this->bver();
        });
    }

    /**
     * Surface sud équivalente représentant l’impact des apports solaires associés au
     * rayonnement solaire traversant directement l’espace tampon pour arriver dans la partie
     * habitable du logement
     */
    public function ssd_j(Mois $mois): float
    {
        return $this->get("ssd::{$mois->value}", function () use ($mois): float {
            return array_sum(array_map(
                fn(BaieInput $item) => $item->sse($mois),
                $this->item()->baies_mitoyennes(),
            )) * $this->item()->t();
        });
    }

    /**
     * @use SurfaceSudEquivalenteEtsBaieRule
     */
    public function sst_j(Mois $mois): float
    {
        return $this->get("sst::{$mois->value}", function () use ($mois): float {
            return array_sum(array_map(
                fn(LncBaieInput $item) => $item->sst($mois),
                $this->item()->baies,
            ));
        });
    }

    /**
     * Coefficient de réduction des déperditions thermique de l'espace tampon solarisé
     */
    public function bver(): float
    {
        return $this->get('bver', function (): float {
            $valeurs = [];
            $coefficients = [];
            foreach ($this->item()->baies_mitoyennes() as $item) {
                $valeurs[] = $item->b();
                $coefficients[] = $item->surface();
            }
            return Math::moyenne_ponderee($valeurs, $coefficients);
        });
    }
}
