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

    public function sse(Mois $mois): float
    {
        return $this->get("sse::{$mois->value}", function () use ($mois): float {
            return $this->input()->enveloppe->baies()
                ->map(fn($item) => $this->requireIterator(SurfaceSudEquivalenteBaieRule::class, $item)->sse($mois))
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    public function ssind(Mois $mois): float
    {
        return $this->get("ssind::{$mois->value}", function () use ($mois): float {
            return $this->input()->enveloppe->locaux_non_chauffes()
                ->map(fn($item) => $this->requireIterator(SurfaceSudEquivalenteEtsRule::class, $item)->ssind($mois))
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    // * Données calculées

    /**
     * Fraction des besoins de chauffage couverts par les apports gratuits
     */
    public function f(?Mois $mois = null): float
    {
        $key = $mois ? "f::{$mois->value}" : 'f';
        return $this->get($key, function () use ($mois): float {
            if (null === $mois) {
                return Mois::reduce(fn(Mois $item) => $this->f($item) * ($item->nj() / Mois::NOMBRE_JOURS_OCCUPATION));
            }
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
     * Apports gratuits en période de chauffage en Wh
     */
    public function apport(?Mois $mois = null): float
    {
        $key = $mois ? "apport::{$mois->value}" : 'apport';
        return $this->get($key, function () use ($mois): float {
            return $this->apport_interne($mois) + $this->apport_solaire($mois);
        });
    }

    /**
     * Apports gratuits en période de refroidissement en Wh
     */
    public function apport_fr(?Mois $mois = null): float
    {
        $key = $mois ? "apport_fr::{$mois->value}" : 'apport_fr';
        return $this->get($key, function () use ($mois): float {
            return $this->apport_interne_fr($mois) + $this->apport_solaire_fr($mois);
        });
    }

    /**
     * Apports internes en période de chauffage en Wh
     */
    public function apport_interne(?Mois $mois = null): float
    {
        $key = $mois ? "ai::{$mois->value}" : 'ai';
        return $this->get($key, function () use ($mois): float {
            if (null === $mois) {
                return Mois::reduce(fn(Mois $item) => $this->apport_interne($item));
            }
            $nref = $this->nref($mois);
            $sh = $this->surface_reference();
            $nadeq = $this->nadeq();
            $ai = (self::APPORT_INTERNE_EQUIPEMENT + self::APPORT_INTERNE_ECLAIRAGE) * $sh;
            $ai += self::APPORT_INTERNE_OCCUPANT * (132 / 168) * $nadeq;
            return $ai * $nref;
        });
    }

    /**
     * Apports internes en période de refroidissement en Wh
     */
    public function apport_interne_fr(?Mois $mois = null): float
    {
        $key = $mois ? "ai_fr::{$mois->value}" : 'ai_fr';
        return $this->get($key, function () use ($mois): float {
            if (null === $mois) {
                return Mois::reduce(fn(Mois $item) => $this->apport_interne_fr($item));
            }
            $nref = $this->nref_fr($mois);
            $sh = $this->surface_reference();
            $nadeq = $this->nadeq();
            $ai = (self::APPORT_INTERNE_EQUIPEMENT + self::APPORT_INTERNE_ECLAIRAGE) * $sh;
            $ai += self::APPORT_INTERNE_OCCUPANT * (132 / 168) * $nadeq;
            return $ai * $nref;
        });
    }

    /**
     * Apports solaires en période de chauffage en Wh
     */
    public function apport_solaire(?Mois $mois = null): float
    {
        $key = $mois ? "as::{$mois->value}" : 'as';
        return $this->get($key, function () use ($mois): float {
            if (null === $mois) {
                return Mois::reduce(fn(Mois $item) => $this->apport_solaire($item));
            }
            $sse = $this->sse($mois);
            $ssind = $this->ssind($mois);
            $e = $this->e($mois);
            return 1000 * ($sse + $ssind) * $e;
        });
    }

    /**
     * Apports solaires  en période de refroidissement en Wh
     */
    public function apport_solaire_fr(?Mois $mois = null): float
    {
        $key = $mois ? "as_fr::{$mois->value}" : 'as_fr';
        return $this->get($key, function () use ($mois): float {
            if (null === $mois) {
                return Mois::reduce(fn(Mois $item) => $this->apport_solaire_fr($item));
            }
            $sse = $this->sse($mois);
            $ssind = $this->ssind($mois);
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
                f: $this->f(),
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
