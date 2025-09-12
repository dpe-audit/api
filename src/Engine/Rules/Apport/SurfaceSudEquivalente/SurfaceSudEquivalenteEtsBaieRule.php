<?php

namespace App\Engine\Rules\Apport\SurfaceSudEquivalente;

use App\Domain\Common\Enum\Mois;
use App\Domain\Enveloppe\Lnc\Baie\Mitoyennete;
use App\Engine\Input\Enveloppe\LncBaieInputRuleIterator;
use App\Engine\Table\SollicitationsClimatiquesTableValeurRepository;

final class SurfaceSudEquivalenteEtsBaieRule extends LncBaieInputRuleIterator
{
    public function __construct(
        private SollicitationsClimatiquesTableValeurRepository $repository,
    ) {}

    /**
     * Surface sud équivalente des apports totaux dans la véranda
     */
    public function sst(): float
    {
        return $this->get('sst', function (): float {
            return Mois::reduce(fn(float $carry, Mois $mois) => $carry += $this->sst_j($mois));
        });
    }

    /**
     * Surface sud équivalente des apports totaux dans la véranda
     */
    public function sst_j(Mois $mois): float
    {
        return $this->get("sst::{$mois->value}", function () use ($mois): float {
            $t = $this->item()->local_non_chauffe()->t();
            return $this->surface() * (0.8 * $t + 0.024) * $this->fe() * $this->c1_j($mois);
        });
    }

    /**
     * Surface de la baie séparant l'espace tampon solarisé de l'extérieur
     */
    public function surface(): float
    {
        return $this->get('surface', function (): float {
            return $this->item()->mitoyennete() === Mitoyennete::EXTERIEUR
                ? $this->item()->surface()
                : 0;
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
            return $this->repository->c1(
                zone_climatique: $this->data()->batiment->zone_climatique(),
                orientation: $this->item()->orientation(),
                inclinaison: $this->item()->inclinaison(),
            );
        });
    }
}
