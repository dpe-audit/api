<?php

namespace App\Engine\Rules\Ecs\Perte;

use App\Domain\Common\Enum\Mois;
use App\Domain\Ecs\Generateur\Position\PositionChauffeEau;
use App\Engine\Input\Ecs\GenerateurInputRuleIterator;
use App\Engine\Table\EcsTableValeurRepository;

abstract class PerteGenerateurRule extends GenerateurInputRuleIterator
{
    public function __construct(
        protected EcsTableValeurRepository $repository,
    ) {}

    /**
     * Pertes de génération exprimées en Wh
     */
    public function pertes_generation(): float
    {
        return 0;
    }

    /**
     * Pertes de génération pour le mois j exprimées en Wh
     */
    public function pertes_generation_j(Mois $mois): float
    {
        return 0;
    }

    /**
     * Pertes de génération récupérables exprimées en Wh
     */
    public function pertes_generation_recuperables(): float
    {
        return 0;
    }

    /**
     * Pertes annuelles de génération récupérables pour le mois j exprimées en Wh
     */
    public function pertes_generation_recuperables_j(Mois $mois): float
    {
        return 0;
    }

    /**
     * Pertes de stockage exprimées en Wh
     */
    public function pertes_stockage(): float
    {
        return $this->get("pertes_stockage", function (): float {
            if (0 === $vs = $this->item()->volume_stockage()) {
                return 0;
            }
            if (false === \in_array($this->item()->type(), [
                PositionChauffeEau::CHAUFFE_EAU_HORIZONTAL,
                PositionChauffeEau::CHAUFFE_EAU_VERTICAL,
            ])) {
                return (67662 * \pow($vs, 0.55)) / 12;
            }
            $cr = $this->repository->cr(
                type_generateur: $this->item()->type(),
                label_generateur: $this->item()->label(),
                volume_stockage: $vs,
            ) ?? throw new \DomainException("Valeur forfaitaire Cr non trouvée");
            return (8592 * (45 / 24) * $vs * $cr) / 12;
        });
    }

    /**
     * Pertes de stockage récupérables exprimées en Wh
     */
    public function pertes_stockage_recuperables(): float
    {
        return $this->get("pertes_stockage_recuperables", function (): float {
            return Mois::reduce(function (float $carry, Mois $mois): float {
                return $carry += $this->pertes_stockage_recuperables_j($mois);
            });
        });
    }

    /**
     * Pertes mensuelles de stockage récupérables exprimées en Wh
     */
    public function pertes_stockage_recuperables_j(Mois $mois): float
    {
        $key = "pertes_stockage_recuperables::{$mois->value}";
        return $this->get($key, function () use ($mois): float {
            return $this->item()->position_volume_chauffe()
                ? 0.48 * $this->data()->batiment->nref($mois) * ($this->pertes_stockage() / 8760)
                : 0;
        });
    }
}
