<?php

namespace App\Engine\Rules\Ecs;

use App\Domain\Batiment\TypeBatiment;
use App\Domain\Common\Enum\{Mois, ScenarioUsage};
use App\Engine\Rule;

final class BesoinEcsRule extends Rule
{
    /**
     * Besoin annuel d'eau chaude sanitaire exprimée en kWh
     */
    public function becs(): float
    {
        return $this->get("becs", function (): float {
            return Mois::reduce(function (float $carry, Mois $mois): float {
                return $carry += $this->becs_j($mois);
            });
        });
    }

    /**
     * Besoin mensuel d'eau chaude sanitaire exprimée en kWh
     */
    public function becs_j(Mois $mois): float
    {
        return $this->get("becs::{$mois->value}", function () use ($mois): float {
            $nj = $mois->nj();
            $nadeq = $this->nadeq();
            $tefs = $this->data()->batiment->tefs($mois);

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
            $surface_moyenne = $this->data()->batiment->surface_habitable_moyenne();

            return match ($this->data()->batiment->type_batiment()) {
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
                ? $this->data()->batiment->logements() * $nmax
                : $this->data()->batiment->logements() * (1.75 + 0.3 * ($nmax - 1.75));
        });
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->ressource()->ecs()->calcule($this->ressource()->ecs()->data()->with(
            nmax: $this->nmax(),
            nadeq: $this->nadeq(),
            becs: $this->becs(),
        ));
    }
}
