<?php

namespace App\Engine\Rules\Ecs\Systeme;

use App\Domain\Ecs\Generateur\EnergieGenerateur;
use App\Domain\Ecs\Generateur\Position\PositionChauffeEau;
use App\Domain\Ecs\Generateur\Signaletique\LabelGenerateur;
use App\Engine\Rules\Ecs\PerformanceSystemeRule;

final class PerformanceChauffeEauElectriqueRule extends PerformanceSystemeRule
{
    public function supports(): bool
    {
        return $this->energie_generateur() === EnergieGenerateur::ELECTRICITE
            && false === $this->type_generateur()->is_pac()
            && false === $this->generateur_multi_batiment();
    }

    // * Données d'entrée

    public function position_chauffe_eau(): PositionChauffeEau
    {
        return $this->item()->generateur()->position()->position_chauffe_eau ?? PositionChauffeEau::CHAUFFE_EAU_VERTICAL;
    }

    public function label_generateur(): ?LabelGenerateur
    {
        return $this->item()->generateur()->signaletique()->label;
    }

    public function volume_stockage_integre(): float
    {
        return $this->item()->generateur()->signaletique()->volume_stockage ?? 0;
    }

    // * Données de sortie

    /**
     * @inheritDoc
     */
    public function rs(): float
    {
        return $this->get("rs", function (): float {
            if (0 == $this->volume_stockage()) {
                return 1;
            }
            $becs = $this->becs();
            $pertes = $this->pertes_stockage();
            $rd = $this->rd();

            if ($this->position_chauffe_eau() === PositionChauffeEau::CHAUFFE_EAU_VERTICAL) {
                if ($this->label_generateur() === LabelGenerateur::NE_PERFORMANCE_C) {
                    return 1.08 / (1 + ($pertes * $rd) / ($becs * 1000));
                }
            }
            return 1 / (1 + ($pertes * $rd) / ($becs * 1000));
        });
    }

    /**
     * @inheritDoc
     */
    public function rg(): float
    {
        return $this->get("rg", function (): float {
            return $this->type_generateur()->is_chaudiere() ? 0.97 : 1;
        });
    }
}
