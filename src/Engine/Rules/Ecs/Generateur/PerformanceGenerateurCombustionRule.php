<?php

namespace App\Engine\Rules\Ecs\Generateur;

use App\Domain\Common\Enum\{Mois, Scenario};
use App\Engine\Rules\Ecs\PerformanceGenerateurRule;

final class PerformanceGenerateurCombustionRule extends PerformanceGenerateurRule
{
    public function supports(): bool
    {
        return $this->type()->is_chaudiere()
            || $this->type()->is_chauffe_eau()
            && $this->energie()->is_combustible()
            && false === $this->generateur_multi_batiment();
    }

    /**
     * @inheritDoc
     */
    public function rpn(): float
    {
        return $this->get("rpn", function (): float {
            return $this->rpn_saisi() ?? $this->repository->rpn(
                type_generateur: $this->type(),
                energie_generateur: $this->energie(),
                mode_combustion: $this->mode_combustion(),
                annee_installation: $this->annee_installation(),
                volume_stockage: $this->volume_stockage_integre(),
                pn: $this->pn(),
            ) ?? throw new \DomainException("Valeurs forfaitaires Rpn non trouvées");
        });
    }

    /**
     * @inheritDoc
     */
    public function qp0(): float
    {
        return $this->get("qp0", function (): float {
            if ($this->qp0_saisi()) {
                return $this->qp0_saisi();
            }
            $e = $this->presence_ventouse() ? 1.75 : 2.5;
            $f = $this->presence_ventouse() ? -0.55 : -0.8;

            $value = $this->repository->qp0(
                type_generateur: $this->type(),
                energie_generateur: $this->energie(),
                mode_combustion: $this->mode_combustion(),
                annee_installation: $this->annee_installation(),
                volume_stockage: $this->volume_stockage_integre(),
                pn: $this->pn(),
                e: $e,
                f: $f,
            ) ?? throw new \DomainException("Valeurs forfaitaires QP0 non trouvées");

            return $value * 1000;
        });
    }

    /**
     * @inheritDoc
     */
    public function pveilleuse(): float
    {
        return $this->get("pveilleuse", function (): float {
            return $this->pveilleuse_saisi() ?? $this->repository->pveilleuse(
                type_generateur: $this->type(),
                energie_generateur: $this->energie(),
                mode_combustion: $this->mode_combustion(),
                annee_installation: $this->annee_installation(),
                volume_stockage: $this->volume_stockage_integre(),
            ) ?? throw new \DomainException("Valeurs forfaitaires Pveil non trouvées");
        });
    }

    /**
     * @inheritdoc
     * 
     * TODO: Vérifier l'application de rdim
     */
    public function pertes_generation(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->get(self::implode(['pertes_generation', $scenario, $mois]), function () use ($scenario, $mois): float {
            if (null === $mois) {
                return Mois::reduce(fn(Mois $mois): float => $this->pertes_generation($scenario, $mois));
            }
            $nref = $this->nref($scenario, $mois);
            $cper = $this->presence_ventouse() ? 0.75 : 0.5;
            $dper = $nref * (1790 / 8760);
            $qp0 = $this->qp0();
            return $cper * $qp0 * $dper * $this->rdim();
        });
    }

    /**
     * @inheritdoc
     */
    public function pertes_generation_recuperables(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->get(self::implode(['pertes_generation_recuperables', $scenario, $mois]), function () use ($scenario, $mois): float {
            return false === $this->generateur_mixte()
                ? 0.48 * $this->pertes_generation($scenario, $mois) * $this->rdim()
                : 0;
        });
    }
}
