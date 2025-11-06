<?php

namespace App\Engine\Rules\Enveloppe\Apport;

use App\Domain\Common\Enum\Mois;
use App\Engine\{Context, Rule};
use App\Engine\Rules\Batiment\WithBatimentRule;
use App\Engine\Rules\Ecs\PerformanceEcsRule;
use App\Engine\Rules\Enveloppe\{WithDeperditionRule, WithInertieRule};

final class ApportEnveloppeRule extends Rule
{
    use WithDeperditionRule, WithInertieRule, WithBatimentRule;

    public final const APPORT_INTERNE_EQUIPEMENT = 3.18;
    public final const APPORT_INTERNE_ECLAIRAGE = 0.34;
    public final const APPORT_INTERNE_OCCUPANT = 90;

    // * Données intermédiaires

    public function nadeq(): float
    {
        return $this->get('nadeq', function (): float {
            return $this->require(PerformanceEcsRule::class)->nadeq();
        });
    }

    public function sse_j(Mois $mois): float
    {
        return $this->get("sse::{$mois->value}", function () use ($mois): float {
            return $this->input()->enveloppe->baies()
                ->map(fn($item) => $this->requireIterator(SurfaceSudEquivalenteBaieRule::class, $item)->sse_j($mois))
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    public function ssind_j(Mois $mois): float
    {
        return $this->get("ssind::{$mois->value}", function () use ($mois): float {
            return $this->input()->enveloppe->locaux_non_chauffes()
                ->map(fn($item) => $this->requireIterator(SurfaceSudEquivalenteEtsRule::class, $item)->ssind_j($mois))
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    // * Données calculées

    /**
     * Fraction des besoins de chauffage couverts par les apports gratuits
     */
    public function f(Mois $mois): float
    {
        return $this->get("f::{$mois->value}", function () use ($mois): float {
            $gv = $this->gv();
            $dh = $this->dh($mois);
            $e = $this->inertie()->exposant();
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
            return Mois::reduce(fn(Mois $mois) => $this->apport_interne_j($mois));
        });
    }

    /**
     * Apports internes annuels en période de refroidissement exprimés en Wh
     */
    public function apport_interne_fr(): float
    {
        return $this->get("ai_fr", function (): float {
            return Mois::reduce(fn(Mois $mois) => $this->apport_interne_fr_j($mois));
        });
    }

    /**
     * Apports internes mensuels en période de chauffage exprimés en Wh
     */
    public function apport_interne_j(Mois $mois): float
    {
        return $this->get("ai::{$mois->value}", function () use ($mois): float {
            $nref = $this->nref($mois);
            $sh = $this->surface_reference();
            $nadeq = $this->nadeq();
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
            $nref = $this->nref_fr($mois);
            $sh = $this->surface_reference();
            $nadeq = $this->nadeq();
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
            return Mois::reduce(fn(Mois $mois) => $this->apport_solaire_j($mois));
        });
    }

    /**
     * Apports solaires annuels en période de refroidissement exprimés en Wh
     */
    public function apport_solaire_fr(): float
    {
        return $this->get("as_fr", function (): float {
            return Mois::reduce(fn(Mois $mois) => $this->apport_solaire_fr_j($mois));
        });
    }

    /**
     * Apports solaires mensuels en période de chauffage exprimés en Wh
     */
    public function apport_solaire_j(Mois $mois): float
    {
        return $this->get("as::{$mois->value}", function () use ($mois): float {
            $sse = $this->sse_j($mois);
            $ssind = $this->ssind_j($mois);
            $e = $this->e($mois);
            return 1000 * ($sse + $ssind) * $e;
        });
    }

    /**
     * Apports solaires mensuels en période de refroidissement exprimés en Wh
     */
    public function apport_solaire_fr_j(Mois $mois): float
    {
        return $this->get("as_fr::{$mois->value}", function () use ($mois): float {
            $sse = $this->sse_j($mois);
            $ssind = $this->ssind_j($mois);
            $e = $this->e_fr($mois);
            return 1000 * ($sse + $ssind) * $e;
        });
    }

    /**
     * @inheritDoc
     */
    public function __invoke(mixed $data, Context $context): void
    {
        parent::__invoke($data, $context);

        $context->input()->enveloppe->calcule($context->input()->enveloppe->data()->with(
            apports: $context->input()->enveloppe->data()->apports->with(
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
