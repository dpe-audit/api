<?php

namespace App\Engine\Rules\Chauffage\Perte;

use App\Domain\Common\Enum\Mois;
use App\Engine\Input\Chauffage\GenerateurInput;
use App\Engine\Rule;

final class PerteChauffageRule extends Rule
{
    /**
     * Somme des pertes de chauffage en Wh
     */
    public function pertes(): float
    {
        return $this->get('pertes', function (): float {
            return array_sum(array_map(
                fn(GenerateurInput $item) => $item->pertes_generation(),
                $this->data()->chauffage->generateurs,
            ));
        });
    }

    /**
     * Somme des pertes de chauffage pour le mois j en Wh
     */
    public function pertes_j(Mois $mois): float
    {
        $key = "pertes::{$mois->value}";
        return $this->get($key, function () use ($mois): float {
            return array_sum(array_map(
                fn(GenerateurInput $item) => $item->pertes_generation($mois),
                $this->data()->chauffage->generateurs,
            ));
        });
    }

    /**
     * Somme des pertes récupérables de chauffage en Wh
     */
    public function pertes_recuperables(): float
    {
        return $this->get('pertes_recuperables', function (): float {
            return array_sum(array_map(
                fn(GenerateurInput $item) => $item->pertes_generation_recuperables(),
                $this->data()->chauffage->generateurs,
            ));
        });
    }

    /**
     * Somme des pertes récupérables de chauffage pour le mois j en Wh
     */
    public function pertes_recuperables_j(Mois $mois): float
    {
        $key = "pertes_recuperables::{$mois->value}";
        return $this->get($key, function () use ($mois): float {
            return array_sum(array_map(
                fn(GenerateurInput $item) => $item->pertes_generation_recuperables($mois),
                $this->data()->chauffage->generateurs,
            ));
        });
    }
}
