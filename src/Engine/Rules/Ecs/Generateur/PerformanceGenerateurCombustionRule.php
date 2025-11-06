<?php

namespace App\Engine\Rules\Ecs\Generateur;

use App\Domain\Common\Enum\Mois;
use App\Domain\Ecs\Generateur\Generateur;
use App\Domain\Ecs\Generateur\Signaletique\{ModeCombustion};
use App\Engine\Rules\Ecs\PerformanceGenerateurRule;

final class PerformanceGenerateurCombustionRule extends PerformanceGenerateurRule
{
    public static function supports(Generateur $entity): bool
    {
        return (null === $entity->type() || $entity->energie()?->is_combustible())
            && false === $entity->position()->generateur_multi_batiment;
    }

    // * Données d'entrée

    public function mode_combustion(): ModeCombustion
    {
        return $this->item()->signaletique()->mode_combustion ?? ModeCombustion::STANDARD;
    }

    public function presence_ventouse(): bool
    {
        return $this->item()->signaletique()->presence_ventouse ?? false;
    }

    public function generateur_mixte(): bool
    {
        return $this->item()->position()->generateur_mixte_id !== null;
    }

    public function rpn_saisi(): ?float
    {
        return $this->item()->signaletique()->rpn;
    }

    public function qp0_saisi(): ?float
    {
        return $this->item()->signaletique()->qp0;
    }

    public function pveilleuse_saisi(): float
    {
        return $this->item()->signaletique()->pveilleuse ?? 0;
    }

    // * Données de sortie

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

            return $this->repository->qp0(
                type_generateur: $this->type(),
                energie_generateur: $this->energie(),
                mode_combustion: $this->mode_combustion(),
                annee_installation: $this->annee_installation(),
                pn: $this->pn(),
                e: $e,
                f: $f,
            ) ?? throw new \DomainException("Valeurs forfaitaires QP0 non trouvées");
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
            ) ?? throw new \DomainException("Valeurs forfaitaires Pveil non trouvées");
        });
    }

    /**
     * @inheritdoc
     * 
     * TODO: Vérifier l'application de rdim
     */
    public function pertes_generation(?Mois $mois = null): float
    {
        $key = $mois ? "pertes_generation::{$mois->value}" : "pertes_generation";
        return $this->get($key, function () use ($mois): float {
            if (null === $mois) {
                return Mois::reduce(fn(Mois $item): float => $this->pertes_generation($item));
            }
            $nref = $this->nref($mois);
            $cper = $this->presence_ventouse() ? 0.75 : 0.5;
            $dper = $nref * (1790 / 8760);
            $qp0 = $this->qp0();
            return $cper * $qp0 * $dper * $this->rdim();
        });
    }

    /**
     * @inheritdoc
     */
    public function pertes_generation_recuperables(?Mois $mois = null): float
    {
        $key = $mois ? "pertes_generation_recuperables::{$mois->value}" : "pertes_generation_recuperables";
        return $this->get($key, function () use ($mois): float {
            return false === $this->generateur_mixte()
                ? 0.48 * $this->pertes_generation($mois) * $this->rdim()
                : 0;
        });
    }
}
