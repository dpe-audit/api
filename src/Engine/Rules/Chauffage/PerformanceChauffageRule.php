<?php

namespace App\Engine\Rules\Chauffage;

use App\Domain\Common\Enum\Mois;
use App\Engine\{Context, Rule};
use App\Engine\Rules\Batiment\WithBatimentRule;
use App\Engine\Rules\Ecs\PerformanceEcsRule;
use App\Engine\Rules\Enveloppe\{WithApportRule, WithDeperditionRule};

final class PerformanceChauffageRule extends Rule
{
    use WithBatimentRule, WithDeperditionRule, WithApportRule;

    // * Données intermédiaires

    public function pertes_recuperables_ecs(?Mois $mois = null): float
    {
        return $this->require(PerformanceEcsRule::class)->pertes_recuperables($mois);
    }

    public function pertes_generation(?Mois $mois = null): float
    {
        return $this->input()->chauffage->generateurs()
            ->map(fn($entity) => $this->requireIterator(PerformanceGenerateurRule::class, $entity)->pertes_generation($mois))
            ->reduce(fn($carry, $item) => $carry + $item);
    }

    public function pertes_generation_recuperables(?Mois $mois = null): float
    {
        return $this->input()->chauffage->generateurs()
            ->map(fn($entity) => $this->requireIterator(PerformanceGenerateurRule::class, $entity)->pertes_generation_recuperables($mois))
            ->reduce(fn($carry, $item) => $carry + $item);
    }

    // * Données de sortie

    /**
     * Consommation d'énergie final de chauffage en kWh/an
     */
    public function cef_ch(): float
    {
        return $this->get('cef_ch', function (): float {
            return $this->input()->chauffage->systemes()
                ->map(fn($entity) => $this->requireIterator(PerformanceSystemeRule::class, $entity)->cef_ch())
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    /**
     * Consommation d'énergie primaire de chauffage en kWh/an
     */
    public function cep_ch(): float
    {
        return $this->get('cep_ch', function (): float {
            return $this->input()->chauffage->systemes()
                ->map(fn($entity) => $this->requireIterator(PerformanceSystemeRule::class, $entity)->cep_ch())
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    /**
     * Consommation d'énergie primaire de chauffage en kWh/an
     */
    public function eges_ch(): float
    {
        return $this->get('eges_ch', function (): float {
            return $this->input()->chauffage->systemes()
                ->map(fn($entity) => $this->requireIterator(PerformanceSystemeRule::class, $entity)->eges_ch())
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    /**
     * Consommation d'énergie final des auxiliaires de chauffage en kWh/an
     */
    public function cef_aux(): float
    {
        return $this->get('cef_aux', function (): float {
            return $this->input()->chauffage->systemes()
                ->map(fn($entity) => $this->requireIterator(PerformanceSystemeRule::class, $entity)->cef_aux())
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    /**
     * Consommation d'énergie primaire des auxiliaires de chauffage en kWh/an
     */
    public function cep_aux(): float
    {
        return $this->get('cep_aux', function (): float {
            return $this->input()->chauffage->systemes()
                ->map(fn($entity) => $this->requireIterator(PerformanceSystemeRule::class, $entity)->cep_aux())
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    /**
     * Consommation d'énergie primaire des auxiliaires de chauffage en kWh/an
     */
    public function eges_aux(): float
    {
        return $this->get('eges_aux', function (): float {
            return $this->input()->chauffage->systemes()
                ->map(fn($entity) => $this->requireIterator(PerformanceSystemeRule::class, $entity)->eges_aux())
                ->reduce(fn($carry, $item) => $carry + $item);
        });
    }

    /**
     * Besoin de chauffage hors pertes en kWh
     */
    public function bch_hp(?Mois $mois = null): float
    {
        $key = $mois ? "bch_hp::{$mois->value}" : 'bch_hp';
        return $this->get($key, function () use ($mois): float {
            if (null === $mois) {
                return Mois::reduce(fn(Mois $mois): float => $this->bch_hp($mois));
            }
            $bv = $this->gv() * (1 - $this->f($mois));
            return $bv * $this->dh($mois) / 1000;
        });
    }

    /**
     * Besoin de chauffage en kWh
     */
    public function bch(?Mois $mois = null): float
    {
        $key = $mois ? "bch::{$mois->value}" : 'bch';
        return $this->get($key, function () use ($mois): float {
            if (null === $mois) {
                return Mois::reduce(fn(Mois $mois): float => $this->bch($mois));
            }
            $bv = $this->gv() * (1 - $this->f($mois));
            $bch = $bv * $this->dh($mois) / 1000;
            $pertes_recuperables = min($bch, $this->pertes_recuperables($mois) / 1000);
            return $bch - $pertes_recuperables;
        });
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
    public function pertes_recuperables(?Mois $mois = null): float
    {
        $key = $mois ? "pertes_recuperables::{$mois->value}" : 'pertes_recuperables';
        return $this->get($key, function () use ($mois): float {
            $pertes = $this->pertes_recuperables_ecs($mois);
            $pertes += $this->pertes_generation_recuperables($mois);
            return $pertes;
        });
    }

    public function __invoke(mixed $data, Context $context): void
    {
        parent::__invoke($data, $context);

        $context->input()->chauffage->calcule($context->input()->chauffage->data()->with(
            bch: $this->bch(),
            cef_ch: $this->cef_ch(),
            cep_ch: $this->cep_ch(),
            eges_ch: $this->eges_ch(),
            cef_aux: $this->cef_aux(),
            cep_aux: $this->cep_aux(),
            eges_aux: $this->eges_aux(),
            pertes_generation: $this->pertes_generation(),
            pertes_generation_recuperables: $this->pertes_generation_recuperables(),
        ));
    }
}
