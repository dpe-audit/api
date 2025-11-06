<?php

namespace App\Engine\Rules\Enveloppe\Apport;

use App\Domain\Common\Enum\Mois;
use App\Domain\Enveloppe\Lnc\Baie\{Baie, Materiau, Mitoyennete, TypeVitrage};
use App\Engine\RuleIterator;
use App\Engine\Rules\Batiment\WithBatimentRule;
use App\Engine\Table\{LncTableValeurRepository, SollicitationsClimatiquesTableValeurRepository};

/**
 * @extends RuleIterator<Baie>
 */
final class SurfaceSudEquivalenteEtsBaieRule extends RuleIterator
{
    use WithBatimentRule;

    public function __construct(
        private LncTableValeurRepository $repository,
        private SollicitationsClimatiquesTableValeurRepository $ext_repository,
    ) {}

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        $collection = [];
        foreach ($this->input()->enveloppe->locaux_non_chauffes() as $item) {
            $collection = array_merge($collection, $item->baies()->values());
        }
        return $collection;
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->id();
    }

    // * Données d'entrée

    public function mitoyennete(): Mitoyennete
    {
        return $this->item()->position()->mitoyennete;
    }

    public function surface(): float
    {
        return $this->mitoyennete() === Mitoyennete::EXTERIEUR ? $this->surface() : 0;
    }

    public function orientation(): ?float
    {
        return $this->item()->position()->orientation;
    }

    public function inclinaison(): float
    {
        return $this->item()->position()->inclinaison;
    }

    public function type_vitrage(): TypeVitrage
    {
        return $this->item()->type_vitrage();
    }

    public function materiau(): Materiau
    {
        return $this->item()->materiau() ?? Materiau::PVC;
    }

    public function presence_rupteur_pont_thermique(): bool
    {
        return $this->item()->presence_rupteur_pont_thermique() ?? false;
    }

    // * Données calculées

    /**
     * Surface sud équivalente des apports totaux dans la véranda
     */
    public function sst(): float
    {
        return $this->get('sst', function (): float {
            return Mois::reduce(fn(Mois $mois) => $this->sst_j($mois));
        });
    }

    /**
     * Surface sud équivalente des apports totaux dans la véranda
     */
    public function sst_j(Mois $mois): float
    {
        return $this->get("sst::{$mois->value}", function () use ($mois): float {
            return $this->surface() * (0.8 * $this->t() + 0.024) * $this->fe() * $this->c1_j($mois);
        });
    }

    /**
     * Facteur d'ensoleillement
     */
    public function fe(): float
    {
        return 1;
    }

    /**
     * Coefficient de transparence
     */
    public function t(): float
    {
        return $this->get('t', function (): float {
            return $this->repository->t(
                type_vitrage: $this->type_vitrage(),
                materiau: $this->materiau(),
                presence_rupteur_pont_thermique: $this->presence_rupteur_pont_thermique(),
            ) ?? throw new \DomainException('Valeur forfaitaire t non trouvée');
        });
    }

    /**
     * Coefficient d'orientation et d'inclinaison de la baie pour le mois j
     */
    public function c1_j(Mois $mois): float
    {
        return $this->get("c1::{$mois->value}", function () use ($mois): float {
            return array_find($this->c1(), fn(array $item) => $item['mois'] === $mois)['c1']
                ?? throw new \DomainException("Valeur forfaitaire C1 non trouvée pour le mois {$mois->value}");
        });
    }

    /**
     * Coefficient d'orientation et d'inclinaison de la baie
     * 
     * @return array{mois: Mois, c1: float}[]
     */
    public function c1(): array
    {
        return $this->get('c1', function (): array {
            return $this->ext_repository->c1(
                zone_climatique: $this->zone_climatique(),
                orientation: $this->orientation(),
                inclinaison: $this->inclinaison(),
            );
        });
    }
}
