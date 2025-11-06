<?php

namespace App\Engine\Rules\Enveloppe\Apport;

use App\Domain\Common\Enum\Mois;
use App\Domain\Enveloppe\Baie\Baie;
use App\Domain\Enveloppe\Lnc\{Lnc, TypeLnc};
use App\Engine\RuleIterator;
use App\Engine\Rules\Enveloppe\Deperdition\DeperditionBaieRule;
use App\Utils\Math;

/**
 * @extends RuleIterator<Lnc>
 */
final class SurfaceSudEquivalenteEtsRule extends RuleIterator
{
    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->input()->enveloppe->locaux_non_chauffes()->values();
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->id();
    }

    // * Données d'entrée

    public function type_lnc(): TypeLnc
    {
        return $this->item()->type();
    }

    // * Données intermédiaires

    public function sst_j(Mois $mois): float
    {
        return $this->get("sst::{$mois->value}", function () use ($mois): float {
            return $this->item()->baies()
                ->map(fn($item) => $this->requireIterator(SurfaceSudEquivalenteEtsBaieRule::class, $item)->sst_j($mois))
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    public function sse_baies_j(Mois $mois): float
    {
        return $this->get("sse_baies::{$mois->value}", function () use ($mois): float {
            return $this->input()->enveloppe->baies()
                ->filter(fn(Baie $item) => $item->local_non_chauffe()?->id()->equals($this->item()->id()))
                ->map(fn(Baie $item) => $this->requireIterator(SurfaceSudEquivalenteBaieRule::class, $item)->sse($mois))
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    /**
     * Coefficient de réduction des déperditions thermique de l'espace tampon solarisé
     */
    public function bver(): float
    {
        return $this->get('bver', function (): float {
            $valeurs = $this->input()->enveloppe->baies()
                ->filter(fn(Baie $item) => $item->local_non_chauffe()?->id()->equals($this->item()->id()))
                ->map(fn(Baie $item) => $this->requireIterator(DeperditionBaieRule::class, $item)->b())
                ->values();
            $coefficients = $this->input()->enveloppe->baies()
                ->filter(fn(Baie $item) => $item->local_non_chauffe()?->id()->equals($this->item()->id()))
                ->map(fn(Baie $item) => $item->surface())
                ->values();
            return Math::moyenne_ponderee($valeurs, $coefficients);
        });
    }

    /**
     * @return array<int, array{surface: float, t: float}>
     */
    public function baies(): array
    {
        return $this->get('baies', function (): array {
            return $this->item()->baies()
                ->map(fn(Baie $item) => [
                    'surface' => $item->position()->surface,
                    't' => $this->requireIterator(SurfaceSudEquivalenteEtsBaieRule::class, $item)->t(),
                ])
                ->values();
        });
    }

    // * Données calculées

    /**
     * Surface sud équivalente représentant les apports solaires indirects dans le logement pour le mois j
     */
    public function ssind_j(Mois $mois): float
    {
        return $this->get("ssind::{$mois->value}", function () use ($mois): float {
            if ($this->type_lnc() !== TypeLnc::ESPACE_TAMPON_SOLARISE) {
                return 0;
            }
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
            if ($this->type_lnc() !== TypeLnc::ESPACE_TAMPON_SOLARISE) {
                return 0;
            }
            return $this->sse_baies_j($mois) * $this->t();
        });
    }

    /**
     * Coefficient de transparence de l'espace tampon solarisé
     */
    public function t(): ?float
    {
        return $this->get("t", function (): ?float {
            if ($this->type_lnc() !== TypeLnc::ESPACE_TAMPON_SOLARISE) {
                return null;
            }
            $collection = $this->baies();
            $valeurs = array_map(fn($item) => $item['t'], $collection);
            $coefficients = array_map(fn($item) => $item['surface'], $collection);
            return Math::moyenne_ponderee($valeurs, $coefficients);
        });
    }
}
