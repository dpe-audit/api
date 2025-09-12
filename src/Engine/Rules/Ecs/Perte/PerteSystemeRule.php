<?php

namespace App\Engine\Rules\Ecs\Perte;

use App\Domain\Common\Enum\Mois;
use App\Engine\Input\Ecs\SystemeInputRuleIterator;

final class PerteSystemeRule extends SystemeInputRuleIterator
{
    /**
     * Pertes de stockage exprimées en Wh
     */
    public function pertes_stockage(): float
    {
        return $this->get('pertes_stockage', function (): float {
            $vs = $this->item()->volume_stockage();
            return $vs ? (67662 * \pow($vs, 0.55)) / 12 : 0;
        });
    }

    /**
     * Pertes de stockage récupérables pour le mois j exprimées en Wh
     */
    public function pertes_stockage_recuperables_j(Mois $mois): float
    {
        $key = "pertes_stockage_recuperables::{$mois->value}";
        return $this->get($key, function () use ($mois): float {
            return $this->item()->position_volume_chauffe()
                ? 0.48 * $this->data()->batiment->nref($mois) * ($this->pertes_stockage() / 8760)
                : 0;
        });
    }

    /**
     * Pertes de distribution exprimées en Wh
     */
    public function pertes_distribution(): float
    {
        return $this->get('pertes_distribution', function (): float {
            return Mois::reduce(function (float $carry, Mois $mois): float {
                return $carry += $this->pertes_distribution_j($mois);
            });
        });
    }

    /**
     * Pertes de distribution pour le mois j exprimées en Wh
     */
    public function pertes_distribution_j(Mois $mois): float
    {
        $key = "pertes_distribution::{$mois->value}";
        return $this->get($key, function () use ($mois): float {
            $pertes = $this->pertes_distribution_ind_vc_j($mois);
            $pertes += $this->pertes_distribution_col_vc_j($mois);
            $pertes += $this->pertes_distribution_col_hvc_j($mois);
            return $pertes;
        });
    }

    /**
     * Pertes de distribution récupérables exprimées en Wh
     */
    public function pertes_distribution_recuperables(): float
    {
        return $this->get("pertes_distribution_recuperables", function (): float {
            return Mois::reduce(function (float $carry, Mois $mois): float {
                return $carry += $this->pertes_distribution_recuperables_j($mois);
            });
        });
    }

    /**
     * Pertes mensuelles de distribution récupérables exprimées en Wh
     */
    public function pertes_distribution_recuperables_j(Mois $mois): float
    {
        $key = "pertes_distribution_recuperables::{$mois->value}";
        return $this->get($key, function () use ($mois): float {
            return 0.48 * $this->pertes_distribution($mois) / 8760;
        });
    }

    /**
     * Pertes mensuelles de distribution individuelle en volume chauffé exprimées en Wh
     */
    public function pertes_distribution_ind_vc_j(Mois $mois): float
    {
        $key = "pertes_distribution_ind_vc::{$mois->value}";
        return $this->get($key, function () use ($mois): float {
            $becs = $this->data()->ecs->becs($mois);
            $surface = $this->item()->installation()->surface();
            $lvc = 0.2 * $surface * $this->item()->rdim();
            return (0.5 * $lvc) / $surface * $becs * 1000;
        });
    }

    /**
     * Pertes mensuelles de distribution collective en volume chauffé exprimées en Wh
     */
    public function pertes_distribution_col_vc_j(Mois $mois): float
    {
        $key = "pertes_distribution_col_vc::{$mois->value}";
        return $this->get($key, function () use ($mois): float {
            return $this->item()->generateur()->generateur_collectif()
                ? 0.112 * $this->data()->ecs->becs($mois) * 1000 * $this->item()->rdim()
                : 0;
        });
    }

    /**
     * Pertes mensuelles de distribution collective hors volume chauffé exprimées en Wh
     */
    public function pertes_distribution_col_hvc_j(Mois $mois): float
    {
        $key = "pertes_distribution_col_hvc::{$mois->value}";
        return $this->get($key, function () use ($mois): float {
            return $this->item()->generateur()->generateur_collectif()
                ? 0.028 * $this->data()->ecs->becs($mois) * 1000 * $this->item()->rdim()
                : 0;
        });
    }
}
