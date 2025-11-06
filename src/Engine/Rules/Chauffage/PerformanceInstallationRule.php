<?php

namespace App\Engine\Rules\Chauffage;

use App\Engine\Context;
use App\Engine\Rules\Batiment\{WithBatiment, WithBatimentRule};
use App\Engine\Tables\ChauffageTableValeurRepository;

abstract class PerformanceInstallationRule extends DimensionnementInstallationRule
{
    use WithBatiment, WithBatimentRule;

    public function __construct(
        private ChauffageTableValeurRepository $repository
    ) {}

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->input()->chauffage->installations()->values();
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->id();
    }

    // * Données d'entrée

    public function solaire_thermique(): bool
    {
        return $this->item()->solaire_thermique() !== null;
    }

    public function fch_saisi(): ?float
    {
        return $this->item()->solaire_thermique()?->fch;
    }

    // * Données de sortie

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
    public function ich(): float
    {
        return $this->get('ich', function (): float {
            return $this->item()->systemes()
                ->map(fn($entity) => $this->requireIterator(PerformanceSystemeRule::class, $entity))
                ->map(fn(PerformanceSystemeRule $rule) => $rule->ich() * $rule->rdim())
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    /**
     * Rendement d'emission de l'installation
     */
    public function re(): float
    {
        return $this->get('re', function (): float {
            return $this->item()->systemes()
                ->map(fn($entity) => $this->requireIterator(PerformanceSystemeRule::class, $entity))
                ->map(fn(PerformanceSystemeRule $rule) => $rule->re() * $rule->rdim())
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    /**
     * Rendement de distribution de l'installation
     */
    public function rd(): float
    {
        return $this->get('rd', function (): float {
            return $this->item()->systemes()
                ->map(fn($entity) => $this->requireIterator(PerformanceSystemeRule::class, $entity))
                ->map(fn(PerformanceSystemeRule $rule) => $rule->rd() * $rule->rdim())
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
     * Rendement de régulation de l'installation
     */
    public function rr(): float
    {
        return $this->get('rr', function (): float {
            return $this->item()->systemes()
                ->map(fn($entity) => $this->requireIterator(PerformanceSystemeRule::class, $entity))
                ->map(fn(PerformanceSystemeRule $rule) => $rule->rr() * $rule->rdim())
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
                fch: $rule->fch(),
                rdim: $rule->rdim(),

            ));
        }
    }
}
