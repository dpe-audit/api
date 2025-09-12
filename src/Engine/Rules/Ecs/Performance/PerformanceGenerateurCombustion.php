<?php

namespace App\Engine\Rules\Ecs\Performance;

use App\Engine\Input\Ecs\{GenerateurInput, GenerateurInputRuleIterator};
use App\Engine\Table\EcsTableValeurRepository;

final class PerformanceGenerateurCombustionRule extends GenerateurInputRuleIterator
{
    public function __construct(
        private EcsTableValeurRepository $repository
    ) {}

    /**
     * @inheritDoc
     */
    public function rpn(): float
    {
        return $this->get("rpn", function (): float {
            return $this->item()->rpn_saisi() ?? $this->repository->rpn(
                type_generateur: $this->item()->type(),
                energie_generateur: $this->item()->energie(),
                mode_combustion: $this->item()->mode_combustion(),
                annee_installation: $this->item()->annee_installation(),
                pn: $this->item()->pn(),
            ) ?? throw new \DomainException("Valeurs forfaitaires Rpn non trouvées");
        });
    }

    /**
     * @inheritDoc
     */
    public function qp0(): float
    {
        return $this->get("qp0", function (): float {
            if ($this->item()->qp0_saisi()) {
                return $this->item()->qp0_saisi();
            }
            $e = $this->item()->presence_ventouse() ? 1.75 : 2.5;
            $f = $this->item()->presence_ventouse() ? -0.55 : -0.8;

            return $this->repository->qp0(
                type_generateur: $this->item()->type(),
                energie_generateur: $this->item()->energie(),
                mode_combustion: $this->item()->mode_combustion(),
                annee_installation: $this->item()->annee_installation(),
                pn: $this->item()->pn(),
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
            return $this->item()->pveilleuse_saisi() ?? $this->repository->pveilleuse(
                type_generateur: $this->item()->type(),
                energie_generateur: $this->item()->energie(),
                mode_combustion: $this->item()->mode_combustion(),
                annee_installation: $this->item()->annee_installation(),
            ) ?? throw new \DomainException("Valeurs forfaitaires Pveil non trouvées");
        });
    }

    public static function match(GenerateurInput $item): bool
    {
        return $item->energie()->is_combustible()
            && false === $item->generateur_multi_batiment();
    }

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return array_filter(parent::collection(), [static::class, 'match']);
    }
}
