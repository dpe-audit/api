<?php

namespace App\Engine\Rules\Chauffage;

use App\Domain\Common\Enum\Mois;
use App\Engine\Rule;

final class BesoinChauffageRule extends Rule
{
    /**
     * Besoin mensuel de chauffage hors pertes en kWh
     */
    public function bch_hp(): float
    {
        return $this->get('bch_hp', function (): float {
            return Mois::reduce(
                fn(Mois $mois): float => $this->bch_hp_j($mois)
            );
        });
    }

    /**
     * Besoin mensuel de chauffage hors pertes pour le mois j en kWh
     */
    public function bch_hp_j(Mois $mois): float
    {
        $key = "bch_hp::{$mois->value}";
        return $this->get($key, function () use ($mois): float {
            $gv = $this->data()->enveloppe->gv();
            $f = $this->data()->enveloppe->f($mois);
            $bv = $gv * (1 - $f);
            return $bv * $this->data()->batiment->dh($mois) / 1000;
        });
    }

    /**
     * Besoin de chauffage en kWh
     */
    public function bch(): float
    {
        return $this->get('bch', function (): float {
            return Mois::reduce(fn(Mois $mois): float => $this->bch_j($mois));
        });
    }

    /**
     * Besoin de chauffage pour le mois j en kWh
     */
    public function bch_j(Mois $mois): float
    {
        $key = "bch::{$mois->value}";
        return $this->get($key, function () use ($mois): float {
            $gv = $this->data()->enveloppe->gv();
            $f = $this->data()->enveloppe->f($mois);
            $bv = $gv * (1 - $f);
            $bch = $bv * $this->data()->batiment->dh($mois) / 1000;
            $pertes_recuperables = min($bch, $this->pertes_recuperables_j($mois) / 1000);
            return $bch - $pertes_recuperables;
        });
    }

    /**
     * Pertes récupérables pour le chauffage en Wh
     */
    public function pertes_recuperables_j(Mois $mois): float
    {
        $key = "pertes_recuperables::{$mois->value}";
        return $this->get($key, function () use ($mois): float {
            $pertes = $this->data()->chauffage->pertes_recuperables($mois);
            $pertes += $this->data()->ecs->pertes_recuperables($mois);
            return $pertes;
        });
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->ressource()->chauffage()->calcule($this->ressource()->chauffage()->data()->with(
            bch: $this->bch()
        ));
    }
}
