<?php

namespace App\Engine\Rules\Ecs;

use App\Domain\Common\Consommation\ConsommationCollection;
use App\Domain\Common\Enum\{Mois, Scenario};
use App\Engine\Context;
use App\Engine\Rules\Batiment\WithBatimentRule;
use App\Engine\Table\EcsTableValeurRepository;

abstract class PerformanceGenerateurRule extends DimensionnementGenerateurRule
{
    use WithBatimentRule;

    public function __construct(
        protected EcsTableValeurRepository $repository,
    ) {}

    /**
     * Liste des consommations du générateur d'eau chaude sanitaire
     */
    public function consommations(): ConsommationCollection
    {
        return $this->get(
            'consommations',
            fn(): ConsommationCollection => $this->item()->systemes()
                ->map(fn($item) => $this->requireIterator(PerformanceSystemeRule::class, $item)->consommations())
                ->reduce(fn(ConsommationCollection $carry, ConsommationCollection $item) => $carry->merge($item), new ConsommationCollection)
        );
    }

    /**
     * Coefficient de performance énergétique
     */
    public function cop(): ?float
    {
        return null;
    }

    /**
     * Rendement à pleine charge
     */
    public function rpn(): ?float
    {
        return null;
    }

    /**
     * Pertes à l'arrêt du générateur en W
     */
    public function qp0(): ?float
    {
        return null;
    }

    /**
     * Puissance de la veilleuse en W
     */
    public function pveilleuse(): ?float
    {
        return null;
    }

    /**
     * Pertes de génération en Wh
     */
    public function pertes_generation(Scenario $scenario, ?Mois $mois = null): float
    {
        return 0;
    }

    /**
     * Pertes de génération récupérables en Wh
     */
    public function pertes_generation_recuperables(Scenario $scenario, ?Mois $mois = null): float
    {
        return 0;
    }

    /**
     * Pertes de stockage intégré en Wh
     */
    public function pertes_stockage(?Mois $mois = null): float
    {
        return $this->get(self::implode(['pertes_stockage', $mois]), function () use ($mois): float {
            if (0 == $vs = $this->volume_stockage()) {
                return 0;
            }
            if (null === $this->position_chauff_eau()) {
                return (67662 * \pow($vs, 0.55)) / 12;
            }
            $cr = $this->repository->cr(
                position_chauffe_eau: $this->position_chauff_eau(),
                label_generateur: $this->label(),
                volume_stockage: $vs,
            ) ?? throw new \DomainException("Valeur forfaitaire Cr non trouvée");

            $value = (8592 * (45 / 24) * $vs * $cr);
            return $mois ? $value / 12 : $value;
        });
    }

    /**
     * Pertes de stockage intégré récupérables en Wh
     */
    public function pertes_stockage_recuperables(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->get(self::implode(['pertes_stockage_recuperables', $scenario, $mois]), function () use ($scenario, $mois): float {
            if (null === $mois) {
                return Mois::reduce(fn(Mois $imois): float => $this->pertes_stockage_recuperables($scenario, $imois));
            }
            return $this->position_volume_chauffe()
                ? 0.48 * $this->nref($scenario, $mois) * ($this->pertes_stockage() / 8760)
                : 0;
        });
    }

    /**
     * @inheritDoc
     */
    public function __invoke(mixed $data, Context $context): void
    {
        parent::__invoke($data, $context);

        foreach ($this as $rule) {
            $rule->item()->calcule($rule->item()->data()->with(
                rdim: $rule->rdim(),
                pn: $rule->pn(),
                pdim: $rule->pdim(),
                pecs: $rule->pecs(),
                cop: $rule->cop(),
                rpn: $rule->rpn(),
                qp0: $rule->qp0(),
                pveilleuse: $rule->pveilleuse(),
                pertes_generation: $rule->pertes_generation(Scenario::CONVENTIONNEL),
                pertes_generation_recuperables: $rule->pertes_generation_recuperables(Scenario::CONVENTIONNEL),
                pertes_stockage: $rule->pertes_stockage(),
                pertes_stockage_recuperables: $rule->pertes_stockage_recuperables(Scenario::CONVENTIONNEL),
                consommations: $rule->consommations(),
            ));
        }
    }
}
