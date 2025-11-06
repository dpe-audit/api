<?php

namespace App\Engine\Rules\Ecs;

use App\Domain\Batiment\TypeBatiment;
use App\Domain\Common\Enum\{Mois, ScenarioUsage};
use App\Engine\{Context, Rule};
use App\Engine\Rules\Batiment\{WithBatiment, WithBatimentRule};

final class PerformanceEcsRule extends Rule
{
    use WithBatiment, WithBatimentRule;

    // * Données de sortie

    /**
     * Consommation finale d'eau chaude sanitaire en kWh
     */
    public function cef_ecs(): float
    {
        return $this->get('cef_ecs', function (): float {
            return $this->input()->ecs->systemes()
                ->map(fn($item) => $this->requireIterator(PerformanceSystemeRule::class, $item)->cef_ecs())
                ->reduce(fn(float $carry, float $item): float => $carry + $item);
        });
    }

    /**
     * Consommation primaire  d'eau chaude sanitaire en kWh
     */
    public function cep_ecs(): float
    {
        return $this->get('cep_ecs', function (): float {
            return $this->input()->ecs->systemes()
                ->map(fn($item) => $this->requireIterator(PerformanceSystemeRule::class, $item)->cep_ecs())
                ->reduce(fn(float $carry, float $item): float => $carry + $item);
        });
    }

    /**
     * Emissions de CO2 d'eau chaude sanitaire en kg
     */
    public function eges_ecs(): float
    {
        return $this->get('eges_ecs', function (): float {
            return $this->input()->ecs->systemes()
                ->map(fn($item) => $this->requireIterator(PerformanceSystemeRule::class, $item)->eges_ecs())
                ->reduce(fn(float $carry, float $item): float => $carry + $item);
        });
    }

    /**
     * Consommation finale des auxiliaires d'eau chaude sanitaire en kWh
     */
    public function cef_aux(): float
    {
        return $this->get('cef_aux', function (): float {
            return $this->input()->ecs->systemes()
                ->map(fn($item) => $this->requireIterator(PerformanceSystemeRule::class, $item)->cef_aux())
                ->reduce(fn(float $carry, float $item): float => $carry + $item);
        });
    }

    /**
     * Consommation primaire des auxiliaires d'eau chaude sanitaire en kWh
     */
    public function cep_aux(): float
    {
        return $this->get('cep_aux', function (): float {
            return $this->input()->ecs->systemes()
                ->map(fn($item) => $this->requireIterator(PerformanceSystemeRule::class, $item)->cep_aux())
                ->reduce(fn(float $carry, float $item): float => $carry + $item);
        });
    }

    /**
     * Emissions de CO2 des auxiliaires d'eau chaude sanitaire en kg
     */
    public function eges_aux(): float
    {
        return $this->get('eges_aux', function (): float {
            return $this->input()->ecs->systemes()
                ->map(fn($item) => $this->requireIterator(PerformanceSystemeRule::class, $item)->eges_aux())
                ->reduce(fn(float $carry, float $item): float => $carry + $item);
        });
    }

    /**
     * Besoin annuel d'eau chaude sanitaire en kWh
     */
    public function becs(?Mois $mois = null): float
    {
        $key = $mois ? "becs::{$mois->value}" : 'becs';

        return $this->get($key, function () use ($mois): float {
            if (null === $mois) {
                return Mois::reduce(fn(Mois $item): float => $this->becs($item));
            }
            $nj = $mois->nj();
            $nadeq = $this->nadeq();
            $tefs = $this->tefs($mois);

            return match ($this->scenario()) {
                ScenarioUsage::CONVENTIONNEL => 1.163 * $nadeq * 56 * (40 - $tefs) * $nj / 1000,
                ScenarioUsage::DEPENSIER => 1.163 * $nadeq * 79 * (40 - $tefs) * $nj / 1000,
            };
        });
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
    public function pertes(?Mois $mois = null): float
    {
        $key = $mois ? "pertes::{$mois->value}" : 'pertes';
        return $this->get($key, function () use ($mois): float {
            return $this->pertes_generation($mois)
                + $this->pertes_stockage($mois)
                + $this->pertes_distribution($mois);
        });
    }

    /**
     * Pertes récupérables en Wh
     */
    public function pertes_recuperables(?Mois $mois = null): float
    {
        $key = $mois ? "pertes_recuperables::{$mois->value}" : 'pertes_recuperables';
        return $this->get($key, function () use ($mois): float {
            return $this->pertes_generation_recuperables($mois)
                + $this->pertes_stockage_recuperables($mois)
                + $this->pertes_distribution_recuperables($mois);
        });
    }

    /**
     * Pertes de génération en Wh
     */
    public function pertes_generation(?Mois $mois = null): float
    {
        $key = $mois ? "pertes_generation::{$mois->value}" : 'pertes_generation';
        return $this->get($key, function () use ($mois): float {
            return $this->input()->ecs->generateurs()
                ->map(fn($item) => $this->requireIterator(PerformanceGenerateurRule::class, $item)->pertes_generation($mois))
                ->reduce(fn(float $carry, float $item): float => $carry + $item);
        });
    }

    /**
     * Pertes de génération récupérables en Wh
     */
    public function pertes_generation_recuperables(?Mois $mois = null): float
    {
        $key = $mois ? "pertes_generation_recuperables::{$mois->value}" : 'pertes_generation_recuperables';
        return $this->get($key, function () use ($mois): float {
            return $this->input()->ecs->generateurs()
                ->map(fn($item) => $this->requireIterator(PerformanceGenerateurRule::class, $item)->pertes_generation_recuperables($mois))
                ->reduce(fn(float $carry, float $item): float => $carry + $item);
        });
    }

    /**
     * Pertes de stockage en Wh
     */
    public function pertes_stockage(?Mois $mois = null): float
    {
        $key = $mois ? "pertes_stockage::{$mois->value}" : "pertes_stockage";
        return $this->get($key, function () use ($mois): float {
            return $this->input()->ecs->systemes()
                ->map(fn($item) => $this->requireIterator(PerformanceSystemeRule::class, $item)->pertes_stockage($mois))
                ->reduce(fn(float $carry, float $item): float => $carry + $item);
        });
    }

    /**
     * Pertes de stockage récupérables en Wh
     */
    public function pertes_stockage_recuperables(?Mois $mois = null): float
    {
        $key = $mois ? "pertes_stockage_recuperables::{$mois->value}" : "pertes_stockage_recuperables";
        return $this->get($key, function () use ($mois): float {
            return $this->input()->ecs->systemes()
                ->map(fn($item) => $this->requireIterator(PerformanceSystemeRule::class, $item)->pertes_stockage_recuperables($mois))
                ->reduce(fn(float $carry, float $item): float => $carry + $item);
        });
    }

    /**
     * Pertes de distribution en Wh
     */
    public function pertes_distribution(?Mois $mois = null): float
    {
        $key = $mois ? "pertes_distribution::{$mois->value}" : "pertes_distribution";
        return $this->get($key, function () use ($mois): float {
            return $this->input()->ecs->systemes()
                ->map(fn($item) => $this->requireIterator(PerformanceSystemeRule::class, $item)->pertes_distribution($mois))
                ->reduce(fn(float $carry, float $item): float => $carry + $item);
        });
    }

    /**
     * Pertes mensuelles de distribution récupérables en Wh
     */
    public function pertes_distribution_recuperables(?Mois $mois = null): float
    {
        $key = $mois ? "pertes_distribution_recuperables::{$mois->value}" : "pertes_distribution_recuperables";
        return $this->get($key, function () use ($mois): float {
            return $this->input()->ecs->systemes()
                ->map(fn($item) => $this->requireIterator(PerformanceSystemeRule::class, $item)->pertes_distribution_recuperables($mois))
                ->reduce(fn(float $carry, float $item): float => $carry + $item);
        });
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
            becs: $this->becs(),
            cef_ecs: $this->cef_ecs(),
            cep_ecs: $this->cep_ecs(),
            eges_ecs: $this->eges_ecs(),
            cef_aux: $this->cef_aux(),
            cep_aux: $this->cep_aux(),
            eges_aux: $this->eges_aux(),
            pertes_stockage: $this->pertes_stockage(),
            pertes_stockage_recuperables: $this->pertes_stockage_recuperables(),
            pertes_generation: $this->pertes_generation(),
            pertes_generation_recuperables: $this->pertes_generation_recuperables(),
            pertes_distribution: $this->pertes_distribution(),
            pertes_distribution_recuperables: $this->pertes_distribution_recuperables(),
        ));
    }
}
