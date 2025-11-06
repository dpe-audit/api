<?php

namespace App\Engine\Rules\Ecs;

use App\Domain\Ecs\Generateur\Generateur;
use App\Engine\Rules\Chauffage\DimensionnementGenerateurRule as DimensionnementGenerateurChauffageRule;
use App\Engine\RuleIterator;

/**
 * @extends RuleIterator<Generateur>
 */
abstract class DimensionnementGenerateurRule extends RuleIterator
{
    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->id();
    }

    // * Données d'entrée

    public function volume_stockage(): float
    {
        return $this->item()->signaletique()->volume_stockage
            + $this->input()->ecs->systemes()->with_generateur($this->item()->id())->volume_stockage();
    }

    public function pn_saisi(): ?float
    {
        return $this->item()->signaletique()->pn;
    }

    // * Données intermédiaires

    /**
     * @return float[]
     */
    public function rdim_systemes(): array
    {
        return $this->input()->ecs->systemes()
            ->with_generateur($this->item()->id())
            ->map(fn($item) => $this->requireIterator(PerformanceSystemeRule::class, $item)->rdim())
            ->values();
    }

    public function pch(): float
    {
        if (null === $this->item()->position()->generateur_mixte_id) {
            return 0;
        }
        $entity = $this->input()->chauffage->generateurs()->find($this->item()->position()->generateur_mixte_id);
        return $this->requireIterator(DimensionnementGenerateurChauffageRule::class, $entity)->pch();
    }

    // * Données de sortie

    /**
     * Ratio de dimensionnement du générateur
     */
    public function rdim(): float
    {
        return $this->get('rdim', function (): float {
            return array_sum($this->rdim_systemes());
        });
    }

    /**
     * Puissance nominale conventionnelle en kW
     */
    public function pn(): float
    {
        return $this->get("pn", function () {
            return $this->pn_saisi() ?? $this->pecs();
        });
    }

    /**
     * Puissance de dimensionnement du générateur en kW
     */
    public function pdim(): float
    {
        return $this->get('pdim', function (): float {
            return max($this->pch(), $this->pecs());
        });
    }

    /**
     * Puissance de dimensionnement du besoin d'eau chaude sanitaire en kW
     */
    public function pecs(): float
    {
        return $this->get('pecs', function (): float {
            if ($this->pn_saisi()) {
                return $this->pn_saisi();
            }
            $volume_stockage = $this->volume_stockage();
            return match (true) {
                $volume_stockage === 0 => 21,
                $volume_stockage <= 20 => 21 - 0.8 * $volume_stockage,
                $volume_stockage <= 150 => 5 - 1.751 * (($volume_stockage - 20) / 65),
                $volume_stockage > 150 => (7.14 * $volume_stockage + 428) / 1000,
            };
        });
    }
}
