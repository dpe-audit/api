<?php

namespace App\Engine\Rules\Ecs\Perte;

use App\Domain\Common\Enum\Mois;
use App\Engine\Input\Ecs\GenerateurInput;

/**
 * TODO: Vérifier l'application de rdim
 */
final class PerteGenerateurCombustionRule extends PerteGenerateurRule
{
    /**
     * Pertes de génération exprimées en Wh
     */
    public function pertes_generation(): float
    {
        return $this->get('pertes_generation', function (): float {
            return Mois::reduce(function (float $carry, Mois $mois): float {
                return $carry += $this->pertes_generation_j($mois);
            });
        });
    }

    /**
     * Pertes de génération pour le mois j exprimées en Wh
     */
    public function pertes_generation_j(Mois $mois): float
    {
        $key = "pertes_generation::{$mois->value}";
        return $this->get($key, function () use ($mois): float {
            $nref = $this->data()->batiment->nref($mois);
            $cper = $this->item()->presence_ventouse() ? 0.75 : 0.5;
            $dper = $nref * (1790 / 8760);
            $qp0 = $this->item()->qp0();
            return $cper * $qp0 * $dper * $this->item()->rdim();
        });
    }

    /**
     * Pertes de génération récupérables exprimées en Wh
     */
    public function pertes_generation_recuperables(): float
    {
        return $this->get('pertes_generation_recuperables', function (): float {
            return Mois::reduce(function (float $carry, Mois $mois): float {
                return $carry += $this->pertes_generation_recuperables_j($mois);
            });
        });
    }

    /**
     * Pertes annuelles de génération récupérables pour le mois j exprimées en Wh
     */
    public function pertes_generation_recuperables_j(Mois $mois): float
    {
        $key = "pertes_generation_recuperables::{$mois->value}";
        return $this->get($key, function () use ($mois): float {
            return 0.48 * $this->pertes_generation_j($mois) * $this->item()->rdim();
        });
    }

    public static function supports(GenerateurInput $item): bool
    {
        return $item->energie()->is_combustible()
            && false === $item->generateur_multi_batiment()
            && null === $item->generateur_mixte();
    }

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return array_filter(parent::collection(), [static::class, 'supports']);
    }
}
