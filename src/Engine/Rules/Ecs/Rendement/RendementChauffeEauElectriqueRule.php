<?php

namespace App\Engine\Rules\Ecs\Rendement;

use App\Domain\Ecs\Generateur\EnergieGenerateur;
use App\Domain\Ecs\Generateur\Position\PositionChauffeEau;
use App\Domain\Ecs\Generateur\Signaletique\LabelGenerateur;
use App\Engine\Input\Ecs\SystemeInput;

final class RendementChauffeEauElectriqueRule extends RendementSystemeRule
{
    /**
     * @inheritDoc
     */
    public function rs(): float
    {
        return $this->get("rs", function (): float {
            if (0 === $this->item()->generateur()->volume_stockage()) {
                return 1;
            }
            $becs = $this->becs();
            $pertes = $this->pertes_stockage();
            $rd = $this->rd();

            if ($this->item()->generateur()->position_chauff_eau() === PositionChauffeEau::CHAUFFE_EAU_VERTICAL) {
                if ($this->item()->generateur()->label() === LabelGenerateur::NE_PERFORMANCE_C) {
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
            if ($this->item()->generateur()->type()->is_chaudiere()) {
                return 0.97;
            }
            if (0 === $this->item()->generateur()->volume_stockage()) {
                $becs = $this->becs();
                $rpn = $this->item()->generateur()->rpn();
                $qp0 = $this->item()->generateur()->qp0();
                $pveilleuse = $this->item()->generateur()->pveilleuse();
                return 1 / ((1 / $rpn) + (1790 * ($qp0 / $becs)) + (6970 * ($pveilleuse / $becs)));
            }
            return $this->repository->rg(
                type_generateur: $this->item()->generateur()->type(),
                energie_generateur: $this->item()->generateur()->energie(),
            ) ?? throw new \RuntimeException('Valeur forfaitaire Rg non trouvée');
        });
    }

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return array_filter(parent::collection(), function (SystemeInput $item) {
            return $item->generateur()->type()->is_chaudiere() || ($item->generateur()->type()->is_chauffe_eau())
                && $item->generateur()->energie() === EnergieGenerateur::ELECTRICITE
                && false === $item->generateur()->generateur_multi_batiment();
        });
    }
}
