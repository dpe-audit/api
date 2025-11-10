<?php

namespace App\Engine\Rules\Chauffage;

use App\Domain\Common\Consommation\ConsommationCollection;
use App\Domain\Common\Enum\{Mois, Scenario};
use App\Engine\{Context, Rule};
use App\Engine\Rules\Batiment\WithBatimentRule;
use App\Engine\Rules\Ecs\PerformanceEcsRule;
use App\Engine\Rules\Enveloppe\{WithApportRule, WithDeperditionRule};

final class PerformanceChauffageRule extends Rule
{
    use WithBatimentRule, WithDeperditionRule, WithApportRule;

    // * Données intermédiaires

    public function pertes_recuperables_ecs(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->require(PerformanceEcsRule::class)->pertes_recuperables($scenario, $mois);
    }

    public function pertes_generation(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->input()->chauffage->generateurs()
            ->map(fn($entity) => $this->requireIterator(PerformanceGenerateurRule::class, $entity)->pertes_generation($scenario, $mois))
            ->reduce(fn($carry, $item) => $carry + $item);
    }

    public function pertes_generation_recuperables(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->input()->chauffage->generateurs()
            ->map(fn($entity) => $this->requireIterator(PerformanceGenerateurRule::class, $entity)->pertes_generation_recuperables($scenario, $mois))
            ->reduce(fn($carry, $item) => $carry + $item);
    }

    // * Données de sortie

    /**
     * Liste des consommations d'eau chaude sanitaire
     */
    public function consommations(): ConsommationCollection
    {
        return $this->get('consommations', function (): ConsommationCollection {
            return $this->input()->chauffage->systemes()
                ->map(fn($item) => $this->requireIterator(PerformanceSystemeRule::class, $item)->consommations())
                ->reduce(fn(ConsommationCollection $carry, ConsommationCollection $item) => $carry->merge($item), new ConsommationCollection);
        });
    }

    /**
     * Besoin de chauffage hors pertes en kWh
     */
    public function bch_hp(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->get(
            self::implode(['bch_hp', $scenario, $mois]),
            function () use ($scenario, $mois): float {
                if (null === $mois) {
                    return Mois::reduce(fn(Mois $mois): float => $this->bch_hp($scenario, $mois));
                }
                $bv = $this->gv() * (1 - $this->f($scenario, $mois));
                return $bv * $this->dh($scenario, $mois) / 1000;
            }
        );
    }

    /**
     * Besoin de chauffage en kWh
     */
    public function bch(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->get(
            self::implode(['bch', $scenario, $mois]),
            function () use ($scenario, $mois): float {
                if (null === $mois) {
                    return Mois::reduce(fn(Mois $mois): float => $this->bch($scenario, $mois));
                }
                $bv = $this->gv() * (1 - $this->f($scenario, $mois));
                $bch = $bv * $this->dh($scenario, $mois) / 1000;
                $pertes_recuperables = min($bch, $this->pertes_recuperables($scenario, $mois) / 1000);
                return $bch - $pertes_recuperables;
            }
        );
    }

    /**
     * Puissance conventionnelle de chauffage en kW
     */
    public function pch(): float
    {
        return $this->get('pch', function (): float {
            $value = 1.2 * $this->gv() * (19 - $this->tbase());
            $value /= 1000 * \pow(0.95, 3);
            return $value;
        });
    }

    /**
     * Pertes récupérables pour le chauffage en Wh
     */
    public function pertes_recuperables(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->get(
            self::implode(['pertes_recuperables', $scenario, $mois]),
            fn(): float => $this->pertes_recuperables_ecs($scenario, $mois) + $this->pertes_generation_recuperables($scenario, $mois)
        );
    }

    public function __invoke(mixed $data, Context $context): void
    {
        parent::__invoke($data, $context);

        $context->input()->chauffage->calcule($context->input()->chauffage->data()->with(
            bch: $this->bch(Scenario::CONVENTIONNEL),
            pertes_generation: $this->pertes_generation(Scenario::CONVENTIONNEL),
            pertes_generation_recuperables: $this->pertes_generation_recuperables(Scenario::CONVENTIONNEL),
            consommations: $this->consommations()
        ));
    }
}
