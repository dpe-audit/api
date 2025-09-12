<?php

namespace App\Engine\Rules\Apport;

use App\Domain\Common\Enum\Mois;
use App\Engine\Input\Enveloppe\{BaieInput, LncInput};
use App\Engine\Rule;

final class ApportEnveloppeRule extends Rule
{
    public final const APPORT_INTERNE_EQUIPEMENT = 3.18;
    public final const APPORT_INTERNE_ECLAIRAGE = 0.34;
    public final const APPORT_INTERNE_OCCUPANT = 90;

    /**
     * Fraction des besoins de chauffage couverts par les apports gratuits
     */
    public function f(Mois $mois): float
    {
        return $this->get("f::{$mois->value}", function () use ($mois): float {
            $gv = $this->data()->enveloppe->gv();
            $dh = $this->data()->batiment->dh($mois);
            $e = $this->data()->enveloppe->inertie()->exposant();
            $as = $this->apport_solaire($mois);
            $ai = $this->apport_interne($mois);
            $x = ($gv && $dh) ? ($as + $ai) / ($gv * $dh) : 0;
            $f = $x < 1 ? ($x - \pow($x, $e)) / (1 - \pow($x, $e)) : 1;
            return \min(1, $f);
        });
    }

    /**
     * Somme des apports gratuits en période de chauffage en Wh
     */
    public function apport(): float
    {
        return $this->get('apport', function (): float {
            return $this->apport_interne() + $this->apport_solaire();
        });
    }

    /**
     * Somme des apports gratuits en période de chauffage pour le mois j en Wh
     */
    public function apport_j(Mois $mois): float
    {
        return $this->get("apport::{$mois->value}", function () use ($mois): float {
            return $this->apport_interne_j($mois) + $this->apport_solaire_j($mois);
        });
    }

    /**
     * Somme des apports gratuits en période de chauffage en Wh
     */
    public function apport_fr(): float
    {
        return $this->get('apport_fr', function (): float {
            return $this->apport_interne_fr() + $this->apport_solaire_fr();
        });
    }

    /**
     * Somme des apports gratuits en période de refroidissement pour le mois j en Wh
     */
    public function apport_fr_j(Mois $mois): float
    {
        return $this->get("apport_fr::{$mois->value}", function () use ($mois): float {
            return $this->apport_interne_fr($mois) + $this->apport_solaire_fr($mois);
        });
    }

    /**
     * Apports internes annuels en période de chauffage exprimés en Wh
     */
    public function apport_interne(): float
    {
        return $this->get("ai", function (): float {
            return Mois::reduce(fn(float $carry, Mois $mois) => $carry += $this->apport_interne_j($mois));
        });
    }

    /**
     * Apports internes annuels en période de refroidissement exprimés en Wh
     */
    public function apport_interne_fr(): float
    {
        return $this->get("ai_fr", function (): float {
            return Mois::reduce(fn(float $carry, Mois $mois) => $carry += $this->apport_interne_fr_j($mois));
        });
    }

    /**
     * Apports internes mensuels en période de chauffage exprimés en Wh
     */
    public function apport_interne_j(Mois $mois): float
    {
        return $this->get("ai::{$mois->value}", function () use ($mois): float {
            $nref = $this->data()->batiment->nref($mois);
            $sh = $this->data()->batiment->surface_habitable();
            $nadeq = $this->data()->ecs->nadeq();
            $ai = (self::APPORT_INTERNE_EQUIPEMENT + self::APPORT_INTERNE_ECLAIRAGE) * $sh;
            $ai += self::APPORT_INTERNE_OCCUPANT * (132 / 168) * $nadeq;
            return $ai * $nref;
        });
    }

    /**
     * Apports internes mensuels en période de refroidissement exprimés en Wh
     */
    public function apport_interne_fr_j(Mois $mois): float
    {
        return $this->get("ai_fr::{$mois->value}", function () use ($mois): float {
            $nref = $this->data()->batiment->nref_fr($mois);
            $sh = $this->data()->batiment->surface_habitable();
            $nadeq = $this->data()->ecs->nadeq();
            $ai = (self::APPORT_INTERNE_EQUIPEMENT + self::APPORT_INTERNE_ECLAIRAGE) * $sh;
            $ai += self::APPORT_INTERNE_OCCUPANT * (132 / 168) * $nadeq;
            return $ai * $nref;
        });
    }
    /**
     * Apports solaires annuels en période de chauffage exprimés en Wh
     */
    public function apport_solaire(): float
    {
        return $this->get("as", function (): float {
            return Mois::reduce(fn(float $carry, Mois $mois) => $carry += $this->apport_solaire_j($mois));
        });
    }

    /**
     * Apports solaires annuels en période de refroidissement exprimés en Wh
     */
    public function apport_solaire_fr(): float
    {
        return $this->get("as_fr", function (): float {
            return Mois::reduce(fn(float $carry, Mois $mois) => $carry += $this->apport_solaire_fr_j($mois));
        });
    }

    /**
     * Apports solaires mensuels en période de chauffage exprimés en Wh
     */
    public function apport_solaire_j(Mois $mois): float
    {
        return $this->get("as::{$mois->value}", function () use ($mois): float {
            $sse = $this->sse($mois);
            $ssind = $this->ssind($mois);
            $e = $this->data()->batiment->e($mois);
            return 1000 * ($sse + $ssind) * $e;
        });
    }

    /**
     * Apports solaires mensuels en période de refroidissement exprimés en Wh
     */
    public function apport_solaire_fr_j(Mois $mois): float
    {
        return $this->get("as_fr::{$mois->value}", function () use ($mois): float {
            $sse = $this->sse($mois);
            $ssind = $this->ssind($mois);
            $e = $this->data()->batiment->e_fr($mois);
            return 1000 * ($sse + $ssind) * $e;
        });
    }

    /**
     * Surface sud équivalente des baies pour le mois j en m² 
     */
    public function sse(Mois $mois): float
    {
        return $this->get("sse::{$mois->value}", function () use ($mois): float {
            return array_sum(array_map(
                fn(BaieInput $item) => $item->sse($mois),
                $this->data()->enveloppe->baies
            ));
        });
    }

    /**
     * Surface sud équivalente représentant les apports solaires indirects pour le mois j en m² 
     */
    public function ssind(Mois $mois): float
    {
        return $this->get("ssind::{$mois->value}", function () use ($mois): float {
            return array_sum(array_map(
                fn(LncInput $item) => $item->ssind($mois),
                $this->data()->enveloppe->locaux_non_chauffes
            ));
        });
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->ressource()->enveloppe()->calcule($this->ressource()->enveloppe()->data()->with(
            apports: $this->ressource()->enveloppe()->data()->apports->with(
                apport: $this->apport(),
                apport_fr: $this->apport_fr(),
                apport_interne: $this->apport_interne(),
                apport_interne_fr: $this->apport_interne_fr(),
                apport_solaire: $this->apport_solaire(),
                apport_solaire_fr: $this->apport_solaire_fr(),
            )
        ));
    }
}
