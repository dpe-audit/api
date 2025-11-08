<?php

namespace App\Engine\Rules\Ecs;

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
    public function iecs(): float
    {
        return $this->get('iecs', function (): float {
            return $this->item()->systemes()
                ->map(fn($entity) => $this->requireIterator(PerformanceSystemeRule::class, $entity))
                ->map(fn(PerformanceSystemeRule $rule) => $rule->iecs() * $rule->rdim())
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
                ->map(fn(PerformanceSystemeRule $rule) => $rule->rd() * $rule->rdim())
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    /**
     * Rendement de stockage de l'installation
     */
    public function rs(): float
    {
        return $this->get('rs', function (): float {
            return $this->item()->systemes()
                ->map(fn($entity) => $this->requireIterator(PerformanceSystemeRule::class, $entity))
                ->map(fn(PerformanceSystemeRule $rule) => $rule->rs() * $rule->rdim())
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    /**
     * Rendement de génération/stockage de l'installation
     */
    public function rgs(): float
    {
        return $this->get('rgs', function (): float {
            return $this->item()->systemes()
                ->map(fn($entity) => $this->requireIterator(PerformanceSystemeRule::class, $entity))
                ->map(fn(PerformanceSystemeRule $rule) => $rule->rgs() * $rule->rdim())
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    /**
     * Rendement de génération de l'installation
     */
    public function rg(): float
    {
        return $this->get('rg', function (): float {
            return $this->item()->systemes()
                ->map(fn($entity) => $this->requireIterator(PerformanceSystemeRule::class, $entity))
                ->map(fn(PerformanceSystemeRule $rule) => $rule->rg() * $rule->rdim())
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
                iecs: $rule->iecs(),
                rd: $rule->rd(),
                rs: $rule->rs(),
                rgs: $rule->rgs(),
                rg: $rule->rg(),
            ));
        }
    }
}
