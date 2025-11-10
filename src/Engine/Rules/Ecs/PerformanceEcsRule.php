<?php

namespace App\Engine\Rules\Ecs;

use App\Domain\Batiment\TypeBatiment;
use App\Domain\Common\Consommation\ConsommationCollection;
use App\Domain\Common\Enum\{Mois, Scenario};
use App\Engine\{Context, Rule};
use App\Engine\Rules\Batiment\{WithBatiment, WithBatimentRule};

final class PerformanceEcsRule extends Rule
{
    use WithBatiment, WithBatimentRule;

    // * Données de sortie

    /**
     * Liste des consommations d'eau chaude sanitaire
     */
    public function consommations(): ConsommationCollection
    {
        return $this->get('consommations', function (): ConsommationCollection {
            return $this->input()->ecs->systemes()
                ->map(fn($item) => $this->requireIterator(PerformanceSystemeRule::class, $item)->consommations())
                ->reduce(fn(ConsommationCollection $carry, ConsommationCollection $item) => $carry->merge($item), new ConsommationCollection);
        });
    }

    /**
     * Besoin d'eau chaude sanitaire en kWh
     */
    public function becs(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->get(
            self::implode(['becs', $scenario, $mois]),
            function () use ($scenario, $mois): float {
                if (null === $mois) {
                    return Mois::reduce(fn(Mois $mois): float => $this->becs($scenario, $mois));
                }
                $nj = $mois->nj();
                $nadeq = $this->nadeq();
                $tefs = $this->tefs($mois);

                return match ($scenario) {
                    Scenario::CONVENTIONNEL => 1.163 * $nadeq * 56 * (40 - $tefs) * $nj / 1000,
                    Scenario::DEPENSIER => 1.163 * $nadeq * 79 * (40 - $tefs) * $nj / 1000,
                };
            }
        );
    }

    /**
     * Coefficient d'occupation maximal
     */
    public function nmax(): float
    {
        return $this->get('nmax', function (): float {
            $surface_moyenne = $this->surface_habitable_moyenne();

            return match ($this->type_batiment()) {
                TypeBatiment::MAISON => match (true) {
                    $surface_moyenne < 30 => 1,
                    $surface_moyenne < 70 => 1.75 - 0.01875 * (70 - $surface_moyenne),
                    default => 0.025 * $surface_moyenne,
                },
                TypeBatiment::IMMEUBLE => match (true) {
                    $surface_moyenne < 10 => 1,
                    $surface_moyenne < 50 => 1.75 - 0.01875 * (50 - $surface_moyenne),
                    default => 0.035 * $surface_moyenne,
                },
            };
        });
    }

    /**
     * Nombre d'adulte équivalent
     */
    public function nadeq(): float
    {
        return $this->get('nadeq', function (): float {
            return ($nmax = $this->nmax()) < 1.75
                ? $this->logements() * $nmax
                : $this->logements() * (1.75 + 0.3 * ($nmax - 1.75));
        });
    }

    /**
     * Pertes en Wh
     */
    public function pertes(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->get(
            self::implode(['pertes', $scenario, $mois]),
            fn(): float => array_sum([
                $this->pertes_generation($scenario, $mois),
                $this->pertes_stockage($mois),
                $this->pertes_distribution($scenario, $mois),
            ])
        );
    }

    /**
     * Pertes récupérables en Wh
     */
    public function pertes_recuperables(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->get(
            self::implode(['pertes_recuperables', $scenario, $mois]),
            fn(): float => array_sum([
                $this->pertes_generation_recuperables($scenario, $mois),
                $this->pertes_stockage_recuperables($scenario, $mois),
                $this->pertes_distribution_recuperables($scenario, $mois),
            ])
        );
    }

    /**
     * Pertes de génération en Wh
     */
    public function pertes_generation(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->get(
            self::implode(['pertes_generation', $scenario, $mois]),
            fn(): float => $this->input()->ecs->generateurs()
                ->map(fn($item) => $this->requireIterator(PerformanceGenerateurRule::class, $item)->pertes_generation($scenario, $mois))
                ->reduce(fn(float $carry, float $item): float => $carry + $item)
        );
    }

    /**
     * Pertes de génération récupérables en Wh
     */
    public function pertes_generation_recuperables(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->get(
            self::implode(['pertes_generation_recuperables', $scenario, $mois]),
            fn(): float => $this->input()->ecs->generateurs()
                ->map(fn($item) => $this->requireIterator(PerformanceGenerateurRule::class, $item)->pertes_generation_recuperables($scenario, $mois))
                ->reduce(fn(float $carry, float $item): float => $carry + $item)
        );
    }

    /**
     * Pertes de stockage en Wh
     */
    public function pertes_stockage(?Mois $mois = null): float
    {
        return $this->get(
            self::implode(['pertes_stockage', $mois]),
            fn(): float => $this->input()->ecs->systemes()
                ->map(fn($item) => $this->requireIterator(PerformanceSystemeRule::class, $item)->pertes_stockage($mois))
                ->reduce(fn(float $carry, float $item): float => $carry + $item)
        );
    }

    /**
     * Pertes de stockage récupérables en Wh
     */
    public function pertes_stockage_recuperables(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->get(
            self::implode(['pertes_stockage_recuperables', $scenario, $mois]),
            fn(): float => $this->input()->ecs->systemes()
                ->map(fn($item) => $this->requireIterator(PerformanceSystemeRule::class, $item)->pertes_stockage_recuperables($scenario, $mois))
                ->reduce(fn(float $carry, float $item): float => $carry + $item)
        );
    }

    /**
     * Pertes de distribution en Wh
     */
    public function pertes_distribution(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->get(
            self::implode(['pertes_distribution', $scenario, $mois]),
            fn(): float => $this->input()->ecs->systemes()
                ->map(fn($item) => $this->requireIterator(PerformanceSystemeRule::class, $item)->pertes_distribution($scenario, $mois))
                ->reduce(fn(float $carry, float $item): float => $carry + $item)
        );
    }

    /**
     * Pertes mensuelles de distribution récupérables en Wh
     */
    public function pertes_distribution_recuperables(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->get(
            self::implode(['pertes_distribution_recuperables', $scenario, $mois]),
            fn(): float => $this->input()->ecs->systemes()
                ->map(fn($item) => $this->requireIterator(PerformanceSystemeRule::class, $item)->pertes_distribution_recuperables($scenario, $mois))
                ->reduce(fn(float $carry, float $item): float => $carry + $item)

        );
    }

    /**
     * @inheritDoc
     */
    public function __invoke(mixed $data, Context $context): void
    {
        parent::__invoke($data, $context);

        $context->input()->ecs->calcule($context->input()->ecs->data()->with(
            nmax: $this->nmax(),
            nadeq: $this->nadeq(),
            becs: $this->becs(Scenario::CONVENTIONNEL),
            pertes_stockage: $this->pertes_stockage(),
            pertes_stockage_recuperables: $this->pertes_stockage_recuperables(Scenario::CONVENTIONNEL),
            pertes_generation: $this->pertes_generation(Scenario::CONVENTIONNEL),
            pertes_generation_recuperables: $this->pertes_generation_recuperables(Scenario::CONVENTIONNEL),
            pertes_distribution: $this->pertes_distribution(Scenario::CONVENTIONNEL),
            pertes_distribution_recuperables: $this->pertes_distribution_recuperables(Scenario::CONVENTIONNEL),
            consommations: $this->consommations(),
        ));
    }
}
