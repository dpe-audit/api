<?php

namespace App\Engine\Rules\Enveloppe\Apport;

use App\Domain\Common\Enum\{Mois, Scenario};
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
        return $this->get(self::implode(['sse', $mois]), function () use ($mois): float {
            return $this->input()->enveloppe->baies()
                ->map(fn($item) => $this->requireIterator(SurfaceSudEquivalenteBaieRule::class, $item)->sse($mois))
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    public function ssind(Mois $mois): float
    {
        return $this->get(self::implode(['ssind', $mois]), function () use ($mois): float {
            return $this->input()->enveloppe->locaux_non_chauffes()
                ->map(fn($item) => $this->requireIterator(SurfaceSudEquivalenteEtsRule::class, $item)->ssind($mois))
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    // * Données calculées

    /**
     * Fraction des besoins de chauffage couverts par les apports gratuits
     */
    public function f(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->get(self::implode(['f', $scenario, $mois]), function () use ($scenario, $mois): float {
            if (null === $mois) {
                return Mois::reduce(fn(Mois $mois) => $this->f($scenario, $mois) * ($mois->nj() / Mois::NOMBRE_JOURS_OCCUPATION));
            }
            $gv = $this->gv();
            $dh = $this->dh($scenario, $mois);
            $e = $this->inertie()->exposant();
            $as = $this->apport_solaire($mois);
            $ai = $this->apport_interne($scenario, $mois);
            $x = ($gv && $dh) ? ($as + $ai) / ($gv * $dh) : 0;
            $f = $x < 1 ? ($x - \pow($x, $e)) / (1 - \pow($x, $e)) : 1;

            return \min(1, $f);
        });
    }

    /**
     * Apports gratuits en période de chauffage en Wh
     */
    public function apport(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->get(self::implode(['apport', $scenario, $mois]), function () use ($scenario, $mois): float {
            return $this->apport_interne($scenario, $mois) + $this->apport_solaire($mois);
        });
    }

    /**
     * Apports gratuits en période de refroidissement en Wh
     */
    public function apport_fr(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->get(self::implode(['apport_fr', $scenario, $mois]), function () use ($scenario, $mois): float {
            return $this->apport_interne_fr($scenario, $mois) + $this->apport_solaire_fr($scenario, $mois);
        });
    }

    /**
     * Apports internes en période de chauffage en Wh
     */
    public function apport_interne(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->get(self::implode(['apport_interne', $scenario, $mois]), function () use ($scenario, $mois): float {
            if (null === $mois) {
                return Mois::reduce(fn(Mois $mois) => $this->apport_interne($scenario, $mois));
            }
            $nref = $this->nref($scenario, $mois);
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
    public function apport_interne_fr(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->get(self::implode(['apport_interne_fr', $scenario, $mois]), function () use ($scenario, $mois): float {
            if (null === $mois) {
                return Mois::reduce(fn(Mois $mois) => $this->apport_interne_fr($scenario, $mois));
            }
            $nref = $this->nref_fr($scenario, $mois);
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
        return $this->get(self::implode(['apport_solaire', $mois]), function () use ($mois): float {
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
    public function apport_solaire_fr(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->get(self::implode(['apport_solaire_fr', $scenario, $mois]), function () use ($scenario, $mois): float {
            if (null === $mois) {
                return Mois::reduce(fn(Mois $mois) => $this->apport_solaire_fr($scenario, $mois));
            }
            $sse = $this->sse($mois);
            $ssind = $this->ssind($mois);
            $e = $this->e_fr($scenario, $mois);
            return 1000 * ($sse + $ssind) * $e;
        });
    }

    /**
     * @inheritDoc
     */
    public function __invoke(mixed $data, Context $context): void
    {
        parent::__invoke($data, $context);

        $scenario = Scenario::CONVENTIONNEL;

        $context->input()->enveloppe->calcule($context->input()->enveloppe->data()->with(
            apports: $context->input()->enveloppe->data()->apports->with(
                f: $this->f($scenario),
                apport: $this->apport($scenario),
                apport_fr: $this->apport_fr($scenario),
                apport_interne: $this->apport_interne($scenario),
                apport_interne_fr: $this->apport_interne_fr($scenario),
                apport_solaire: $this->apport_solaire(),
                apport_solaire_fr: $this->apport_solaire_fr($scenario),
            )
        ));
    }
}
