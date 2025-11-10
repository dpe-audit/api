<?php

namespace App\Engine\Rules\Chauffage;

use App\Domain\Common\Consommation\ConsommationCollection;
use App\Domain\Common\Enum\Scenario;
use App\Engine\Context;
use App\Engine\Table\ChauffageTableValeurRepository;

final class PerformanceInstallationRule extends DimensionnementInstallationRule
{
    public function __construct(private ChauffageTableValeurRepository $repository) {}

    /**
     * Liste des consommations de l'installation de chauffage
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
     * Facteur de couverture solaire
     */
    public function fch(): float
    {
        return $this->get("fch", function (): float {
            if (false === $this->solaire_thermique()) {
                return 0;
            }
            return $this->fch_saisi() ?? $this->repository->fch(
                zone_climatique: $this->zone_climatique(),
                type_batiment: $this->type_batiment(),
            ) ?? throw new \DomainException("Valeurs forfaitaires Fch non trouvées");
        });
    }

    /**
     * Inverse du rendement de l'installation
     */
    public function ich(Scenario $scenario): float
    {
        return $this->get(
            self::implode(['ich', $scenario]),
            fn(): float => $this->item()->systemes()
                ->map(fn($entity) => $this->requireIterator(PerformanceSystemeRule::class, $entity))
                ->map(fn(PerformanceSystemeRule $rule) => $rule->ich($scenario) * ($rule->rdim() / $this->rdim()))
                ->reduce(fn($carry, $item) => $carry + $item)
        );
    }

    /**
     * Rendement d'emission de l'installation
     */
    public function re(): float
    {
        return $this->get('re', fn(): float => $this->item()->systemes()
            ->map(fn($entity) => $this->requireIterator(PerformanceSystemeRule::class, $entity))
            ->map(fn(PerformanceSystemeRule $rule) => $rule->re() * ($rule->rdim() / $this->rdim()))
            ->reduce(fn($carry, $item) => $carry + $item));
    }

    /**
     * Rendement de distribution de l'installation
     */
    public function rd(): float
    {
        return $this->get('rd', fn(): float => $this->item()->systemes()
            ->map(fn($entity) => $this->requireIterator(PerformanceSystemeRule::class, $entity))
            ->map(fn(PerformanceSystemeRule $rule) => $rule->rd() * ($rule->rdim() / $this->rdim()))
            ->reduce(fn($carry, $item) => $carry + $item));
    }

    /**
     * Rendement de génération de l'installation
     */
    public function rg(Scenario $scenario): float
    {
        return $this->get(
            self::implode(['rg', $scenario]),
            fn(): float => $this->item()->systemes()
                ->map(fn($entity) => $this->requireIterator(PerformanceSystemeRule::class, $entity))
                ->map(fn(PerformanceSystemeRule $rule) => $rule->rg($scenario) * ($rule->rdim() / $this->rdim()))
                ->reduce(fn($carry, $item) => $carry + $item)
        );
    }

    /**
     * Rendement de régulation de l'installation
     */
    public function rr(): float
    {
        return $this->get('rr', fn(): float => $this->item()->systemes()
            ->map(fn($entity) => $this->requireIterator(PerformanceSystemeRule::class, $entity))
            ->map(fn(PerformanceSystemeRule $rule) => $rule->rr() * ($rule->rdim() / $this->rdim()))
            ->reduce(fn($carry, $item) => $carry + $item));
    }

    /**
     * @inheritDoc
     */
    public function __invoke(mixed $data, Context $context): void
    {
        parent::__invoke($data, $context);

        foreach ($this as $rule) {
            $rule->item()->calcule($rule->item()->data()->with(
                fch: $rule->fch(),
                rdim: $rule->rdim(),
                consommations: $rule->consommations(),
            ));
        }
    }
}
