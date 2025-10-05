<?php

namespace App\Engine\Rules\Chauffage\Perte;

use App\Domain\Common\Enum\{Mois, TypePerte};
use App\Domain\Common\Perte\{Perte, PerteCollection};
use App\Engine\Rule;

final class PerteChauffageRule extends Rule
{
    /**
     * Liste des pertes de chauffage
     */
    public function pertes(): PerteCollection
    {
        return $this->get('pertes', function (): PerteCollection {
            $collection = PerteCollection::create();
            foreach ($this->data()->chauffage->systemes as $item) {
                $collection->with(Perte::create(
                    type: TypePerte::GENERATION,
                    pertes: $item->pertes_generation(),
                    pertes_recuperables: $item->pertes_generation_recuperables()
                ));
            }
            return $collection;
        });
    }

    /**
     * Pertes récupérables de chauffage en Wh
     */
    public function pertes_recuperables(): float
    {
        return $this->get('pertes_recuperables', function (): float {
            return Mois::reduce(fn(Mois $mois): float => $this->pertes_recuperables_j($mois));
        });
    }

    /**
     * Pertes récupérables pour le mois j en Wh
     */
    public function pertes_recuperables_j(Mois $mois): float
    {
        return $this->get('pertes_recuperables_j', function () use ($mois): float {
            return array_sum(array_map(
                fn($item) => $item->pertes_generation_recuperables($mois),
                $this->data()->chauffage->systemes,
            ));
        });
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->ressource()->chauffage()->calcule($this->ressource()->chauffage()->data()->with(
            pertes: $this->pertes(),
        ));
    }
}
