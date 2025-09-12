<?php

namespace App\Engine\Rules\Chauffage\Perte;

use App\Domain\Common\Enum\Mois;
use App\Engine\Input\Chauffage\GenerateurInputRuleIterator;

final class PerteGenerationRule extends GenerateurInputRuleIterator
{
    /**
     * Pertes de génération de chauffage exprimées en Wh
     */
    public function pertes_generation(): float
    {
        return $this->get('pertes_generation', function (): float {
            return Mois::reduce(function (float $carry, Mois $mois): float {
                return $carry += $this->pertes_generation_j($mois);
            });
        });
    }

    /**
     * Pertes de génération pour le mois j exprimées en Wh
     */
    public function pertes_generation_j(Mois $mois): float
    {
        $key = "pertes_generation::{$mois->value}";
        return $this->get($key, function () use ($mois): float {
            $nref = $this->data()->batiment->nref($mois);
            $cper = $this->item()->presence_ventouse() ? 0.75 : 0.5;
            $qp0 = $this->item()->qp0();
            $bch_hp = $this->data()->chauffage->bch_hp($mois);
            $pn = $this->item()->pn();
            $dper = min($nref, (1.3 * $bch_hp) / (0.3 / $pn));

            if ($this->item()->generateur_mixte()) {
                $dper = min($nref, (1.3 * $bch_hp) / (0.3 / $pn) + $nref * (1790 / 8760));
            }
            return $cper * $qp0 * $dper;
        });
    }

    /**
     * Pertes de génération de chauffage récupérables en Wh
     */
    public function pertes_generation_recuperables(): float
    {
        return $this->get('pertes_generation_recuperables', function (): float {
            return Mois::reduce(function (float $carry, Mois $mois): float {
                return $carry + $this->pertes_generation_recuperables_j($mois);
            });
        });
    }

    /**
     * Pertes de génération de chauffage récupérables pour le mois j en Wh
     */
    public function pertes_generation_recuperables_j(Mois $mois): float
    {
        $key = "pertes_generation_recuperables::{$mois->value}";
        return $this->get($key, function () use ($mois): float {
            return 0.48 * $this->pertes_generation_j($mois);
        });
    }
}
