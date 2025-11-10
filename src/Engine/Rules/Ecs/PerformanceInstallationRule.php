<?php

namespace App\Engine\Rules\Ecs;

use App\Domain\Common\Consommation\ConsommationCollection;
use App\Domain\Common\Enum\Scenario;
use App\Engine\Context;
use App\Engine\Rules\Batiment\WithBatimentRule;
use App\Engine\Table\EcsTableValeurRepository;

final class PerformanceInstallationRule extends CommonInstallationRule
{
    use WithBatimentRule;

    public function __construct(
        private EcsTableValeurRepository $repository
    ) {}

    /**
     * Liste des consommations de l'installation d'eau chaude sanitaire
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
     * Ratio de dimensionnement de l'installation d'eau chaude sanitaire
     */
    public function rdim(): float
    {
        return $this->get('rdim', function (): float {
            return $this->surface() / $this->surface_totale();
        });
    }

    /**
     * Facteur de couverture solaire
     */
    public function fecs(): float
    {
        return $this->get("fecs", function () {
            if (false === $this->solaire_thermique()) {
                return 0;
            }
            return $this->fecs_saisi() ?? $this->repository->fecs(
                zone_climatique: $this->zone_climatique(),
                type_batiment: $this->type_batiment(),
                usage_solaire: $this->usage_solaire_thermique(),
                annee_installation: $this->annee_installation_solaire_thermique(),
            ) ?? throw new \DomainException("Valeurs forfaitaires Fecs non trouvées");
        });
    }

    /**
     * Inverse du rendement de l'installation
     */
    public function iecs(Scenario $scenario): float
    {
        return $this->get(self::implode(['iecs', $scenario]), function () use ($scenario): float {
            return $this->item()->systemes()
                ->map(fn($entity) => $this->requireIterator(PerformanceSystemeRule::class, $entity))
                ->map(fn(PerformanceSystemeRule $rule) => $rule->iecs($scenario) * ($rule->rdim() / $this->rdim()))
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    /**
     * Rendement de distribution de l'installation
     */
    final public function rd(): float
    {
        return $this->get('rd', function (): float {
            return $this->item()->systemes()
                ->map(fn($entity) => $this->requireIterator(PerformanceSystemeRule::class, $entity))
                ->map(fn(PerformanceSystemeRule $rule) => $rule->rd() * ($rule->rdim() / $this->rdim()))
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    /**
     * Rendement de stockage de l'installation
     */
    public function rs(Scenario $scenario): float
    {
        return $this->get(self::implode(['rs', $scenario]), function () use ($scenario): float {
            return $this->item()->systemes()
                ->map(fn($entity) => $this->requireIterator(PerformanceSystemeRule::class, $entity))
                ->map(fn(PerformanceSystemeRule $rule) => $rule->rs($scenario) * ($rule->rdim() / $this->rdim()))
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    /**
     * Rendement de génération/stockage de l'installation
     */
    public function rgs(Scenario $scenario): float
    {
        return $this->get(self::implode(['rgs', $scenario]), function () use ($scenario): float {
            return $this->item()->systemes()
                ->map(fn($entity) => $this->requireIterator(PerformanceSystemeRule::class, $entity))
                ->map(fn(PerformanceSystemeRule $rule) => $rule->rgs($scenario) * ($rule->rdim() / $this->rdim()))
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    /**
     * Rendement de génération de l'installation
     */
    public function rg(Scenario $scenario): float
    {
        return $this->get(self::implode(['rg', $scenario]), function () use ($scenario): float {
            return $this->item()->systemes()
                ->map(fn($entity) => $this->requireIterator(PerformanceSystemeRule::class, $entity))
                ->map(fn(PerformanceSystemeRule $rule) => $rule->rg($scenario) * ($rule->rdim() / $this->rdim()))
                ->reduce(fn($carry, $item) => $carry + $item);
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
                fecs: $rule->fecs(),
                iecs: $rule->iecs(Scenario::CONVENTIONNEL),
                rd: $rule->rd(),
                rs: $rule->rs(Scenario::CONVENTIONNEL),
                rgs: $rule->rgs(Scenario::CONVENTIONNEL),
                rg: $rule->rg(Scenario::CONVENTIONNEL),
                consommations: $rule->consommations(),
            ));
        }
    }
}
