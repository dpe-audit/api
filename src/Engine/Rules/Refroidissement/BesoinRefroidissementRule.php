<?php

namespace App\Engine\Rules\Refroidissement;

use App\Domain\Common\Enum\{Mois, ScenarioUsage};
use App\Engine\Rule;

final class BesoinRefroidissementRule extends Rule
{
    /**
     * Besoin annuel de refroidissement exprimé en kWh
     */
    public function bfr(): float
    {
        return $this->get("bfr", function () {
            return Mois::reduce(fn(float $carry, Mois $mois) => $carry += $this->bfr_j($mois));
        });
    }

    /**
     * Besoin mensuel de refroidissement exprimé en kWh
     */
    public function bfr_j(Mois $mois): float
    {
        return $this->get("bfr::{$mois->value}", function () use ($mois) {
            $text_fr = $this->data()->batiment->text_fr($mois);
            $nref_fr = $this->data()->batiment->nref_fr($mois);

            if (!$text_fr || !$nref_fr) {
                return 0;
            }
            if (0.5 > ($rbth = $this->rbth_j($mois))) {
                return 0;
            }
            $fut = $this->fut_j($mois);
            $tint = $this->tint();

            if (0.5 > $rbth) {
                return 0;
            }
            $gv = $this->data()->enveloppe->gv();
            $apports = $this->data()->enveloppe->apport($mois);
            $bfr = $apports / 1000;
            $bfr -= $fut * ($gv / 1000) * ($tint - $text_fr) * $nref_fr;
            return max($bfr, 0);
        });
    }

    /**
     * Ratio mensuel de bilan thermique
     */
    public function rbth_j(Mois $mois): float
    {
        return $this->get("rbth::{$mois->value}", function () use ($mois) {
            $gv = $this->data()->enveloppe->gv();
            $apports = $this->data()->enveloppe->apport($mois);
            $text_fr = $this->data()->batiment->text_fr($mois);
            $nref_fr = $this->data()->batiment->nref_fr($mois);
            $rbth = $gv * ($text_fr - $this->tint()) * $nref_fr;
            return $rbth ? $apports / $rbth : 0;
        });
    }

    /**
     * Facteur mensuel d'utilisation des apports
     */
    public function fut_j(Mois $mois): float
    {
        return $this->get("fut::{$mois->value}", function () use ($mois) {
            $t = $this->t();
            $rbth = $this->rbth_j($mois);
            $a = 1 + ($t / 15);

            return $rbth > 0 && $rbth !== 1
                ?  (1 - \pow($rbth, -$a)) / (1 - \pow($rbth, -$a - 1))
                : $a / ($a + 1);
        });
    }

    /**
     * Température de consigne en froid exprimée en °C
     */
    public function tint(): float
    {
        return $this->get('tint', fn() => match ($this->scenario()) {
            ScenarioUsage::CONVENTIONNEL => 26,
            ScenarioUsage::DEPENSIER => 28,
        });
    }

    /**
     * Constante de temps de la zone pour le refroidissement exprimée en J/K
     */
    public function t(): float
    {
        return $this->get('t', function (): float {
            return $this->cin() / (3600 * $this->data()->enveloppe->gv());
        });
    }

    /**
     * Capacité thermique intérieure efficace de la zone exprimée en J/K
     */
    public function cin(): float
    {
        return $this->get('cin', function (): float {
            return $this->data()->enveloppe->inertie()->cin() * $this->data()->batiment->surface_habitable();
        });
    }
}
