<?php

namespace App\Engine\Rules\Apport\SurfaceSudEquivalente;

use App\Domain\Common\Enum\Mois;
use App\Domain\Enveloppe\Baie\Position\Mitoyennete;
use App\Domain\Enveloppe\Lnc\TypeLnc;
use App\Engine\Input\Enveloppe\BaieInputRuleIterator;
use App\Engine\Table\SollicitationsClimatiquesTableValeurRepository;

final class SurfaceSudEquivalenteBaieRule extends BaieInputRuleIterator
{
    public function __construct(
        private SollicitationsClimatiquesTableValeurRepository $repository,
    ) {}

    /**
     * Surface sud équivalente en m²
     */
    public function sse(): float
    {
        return $this->get("sse", function (): float {
            return Mois::reduce(fn(float $carry, Mois $mois) => $carry += $this->sse_j($mois));
        });
    }

    /**
     * Surface sud équivalente pour le mois j en m²
     */
    public function sse_j(Mois $mois): float
    {
        return $this->get("sse::{$mois->value}", function () use ($mois): float {
            if (
                $this->item()->mitoyennete() !== Mitoyennete::EXTERIEUR
                || $this->item()->local_non_chauffe()?->type() === TypeLnc::ESPACE_TAMPON_SOLARISE
            ) {
                return 0;
            }

            $a = $this->item()->surface();
            $sw = $this->item()->sw();
            $fe = $this->item()->fe();
            $c1 = $this->c1_j($mois);
            $t = $this->t();
            return static::round($a * $sw * $fe * $c1 * $t);
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
            return $this->repository->c1(
                zone_climatique: $this->data()->batiment->zone_climatique(),
                orientation: $this->item()->orientation(),
                inclinaison: $this->item()->inclinaison(),
            );
        });
    }

    /**
     * Coefficient de transparence de la baie
     */
    public function t(): float
    {
        return $this->get('t', function (): float {
            return $this->item()->local_non_chauffe()?->t() ?? 1;
        });
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->item()->entity->calcule($this->item()->entity->data()->with(
            sse: $this->sse()
        ));
    }
}
