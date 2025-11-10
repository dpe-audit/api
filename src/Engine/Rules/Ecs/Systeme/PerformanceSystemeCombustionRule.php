<?php

namespace App\Engine\Rules\Ecs\Systeme;

use App\Domain\Common\Enum\Mois;
use App\Domain\Ecs\Generateur\Position\PositionChauffeEau;
use App\Domain\Ecs\Generateur\Signaletique\LabelGenerateur;
use App\Engine\Rules\Ecs\{PerformanceGenerateurRule, PerformanceSystemeRule};

abstract class PerformanceSystemeCombustionRule extends PerformanceSystemeRule
{
    public function supports(): bool
    {
        return $this->energie_generateur()->is_combustible() && false === $this->generateur_multi_batiment();
    }

    // * Données d'entrée

    public function position_chauffe_eau(): PositionChauffeEau
    {
        return $this->item()->generateur()->position()->position_chauffe_eau;
    }

    public function label_generateur(): ?LabelGenerateur
    {
        return $this->item()->generateur()->signaletique()->label;
    }

    public function volume_stockage_integre(): float
    {
        return $this->item()->generateur()->signaletique()->volume_stockage ?? 0;
    }

    // * Données intermédiaires

    public function becs(?Mois $mois = null): float
    {
        return parent::becs($mois) * 1000;
    }

    public function qp0(): ?float
    {
        return $this->requireIterator(PerformanceGenerateurRule::class, $this->item()->generateur())->qp0();
    }

    public function rpn(): ?float
    {
        return $this->requireIterator(PerformanceGenerateurRule::class, $this->item()->generateur())->rpn();
    }

    public function pveilleuse(): ?float
    {
        return $this->requireIterator(PerformanceGenerateurRule::class, $this->item()->generateur())->pveilleuse();
    }
}
