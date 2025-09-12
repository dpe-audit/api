<?php

namespace App\Engine\Rules\Ecs\Rendement;

use App\Engine\Input\Ecs\SystemeInput;

final class RendementChauffeEauCombustionRule extends RendementSystemeRule
{
    /**
     * @inheritDoc
     */
    public function rg(): float
    {
        return $this->get('rg', function (): float {
            if (false === $this->item()->generateur()->type()->is_chauffe_eau()) {
                return parent::rg();
            }
            if (0 === $this->item()->generateur()->volume_stockage()) {
                return parent::rg();
            }
            $becs = $this->becs();
            $rpn = $this->item()->generateur()->rpn();
            $qp0 = $this->item()->generateur()->qp0();
            $pveilleuse = $this->item()->generateur()->pveilleuse();

            $rg = 1 / $rpn;
            $rg += 1790 * ($qp0 / $becs);
            $rg += 6970 * ($pveilleuse / $becs);
            return 1 / $rg;
        });
    }

    /**
     * @inheritDoc
     */
    public function rgs(): float
    {
        return $this->get("rgs", function (): float {
            $becs = $this->becs();
            $pertes = $this->pertes_stockage();
            $rpn = $this->item()->generateur()->rpn();
            $qp0 = $this->item()->generateur()->qp0();
            $pveilleuse = $this->item()->generateur()->pveilleuse();

            if ($this->item()->generateur()->type()->is_chauffe_eau()) {
                if ($this->item()->generateur()->volume_stockage()) {
                    return parent::rgs();
                }
                $rgs = 1 / $rpn;
                $rgs += (8592 * $qp0 + $pertes) / $becs;
                $rgs += 6970 * ($pveilleuse / $becs);
                return 1 / $rgs;
            }
            $rgs = 1 / $rpn;
            $rgs += (1790 * $qp0 + $pertes) / $becs;
            $rgs += 6970 * (0.5 * $pveilleuse / $becs);
            return 1 / $rgs;
        });
    }

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return array_filter(parent::collection(), function (SystemeInput $item) {
            return $item->generateur()->type()->is_chaudiere() || ($item->generateur()->type()->is_chauffe_eau())
                && $item->generateur()->energie()->is_combustible()
                && false === $item->generateur()->generateur_multi_batiment();
        });
    }
}
