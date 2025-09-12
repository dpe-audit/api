<?php

namespace App\Engine\Rules\Ecs\Perte;

use App\Domain\Common\Enum\Mois;
use App\Engine\Rule;

final class PerteEcsRule extends Rule
{
    /**
     * Somme des pertes d'eau chaude sanitaire exprimées en Wh
     */
    public function pertes(): float
    {
        return $this->get('pertes', function (): float {
            $value = 0;
            foreach ($this->data()->ecs->generateurs as $item) {
                $value += $item->pertes_generation();
                $value += $item->pertes_stockage();
            }
            foreach ($this->data()->ecs->systemes as $item) {
                $value += $item->pertes_distribution();
                $value += $item->pertes_stockage();
            }
            return $value;
        });
    }

    /**
     * Somme des pertes d'eau chaude sanitaire récupérables exprimées en Wh
     */
    public function pertes_recuperables(): float
    {
        return $this->get('pertes_recuperables', function (): float {
            return Mois::reduce(function (float $carry, Mois $mois): float {
                return $carry += $this->pertes_recuperables_j($mois);
            });
        });
    }

    /**
     * Somme des pertes d'eau chaude sanitaire récupérables pour le mois j exprimées en Wh
     */
    public function pertes_recuperables_j(Mois $mois): float
    {
        $key = "pertes_recuperables::{$mois->value}";
        return $this->get($key, function () use ($mois): float {
            $value = 0;
            foreach ($this->data()->ecs->generateurs as $item) {
                $value += $item->pertes_generation_recuperables($mois);
                $value += $item->pertes_stockage_recuperables($mois);
            }
            foreach ($this->data()->ecs->systemes as $item) {
                $value += $item->pertes_distribution_recuperables($mois);
                $value += $item->pertes_stockage_recuperables($mois);
            }
            return $value;
        });
    }
}
