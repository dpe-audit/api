<?php

namespace App\Engine\Rules\Ecs\Perte;

use App\Domain\Common\Enum\{Mois, TypePerte};
use App\Domain\Common\Perte\{Perte, PerteCollection};
use App\Engine\Rule;

final class PerteEcsRule extends Rule
{
    /**
     * Liste des pertes d'eau chaude sanitaire
     */
    public function pertes(): PerteCollection
    {
        return $this->get('pertes', function (): PerteCollection {
            $collection = PerteCollection::create();
            foreach ($this->data()->ecs->systemes as $item) {
                $collection->with(Perte::create(
                    type: TypePerte::GENERATION,
                    pertes: $item->pertes_generation(),
                    pertes_recuperables: $item->pertes_generation_recuperables()
                ));
                $collection->with(Perte::create(
                    type: TypePerte::DISTRIBUTION,
                    pertes: $item->pertes_distribution(),
                    pertes_recuperables: $item->pertes_distribution_recuperables()
                ));
                $collection->with(Perte::create(
                    type: TypePerte::STOCKAGE,
                    pertes: $item->pertes_stockage(),
                    pertes_recuperables: $item->pertes_stockage_recuperables()
                ));
            }
            return $collection;
        });
    }

    /**
     * Pertes récupérables en Wh
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
                fn($item) => $item->pertes_generation_recuperables($mois) +
                    $item->pertes_distribution_recuperables($mois) +
                    $item->pertes_stockage_recuperables($mois),
                $this->data()->ecs->systemes,
            ));
        });
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->ressource()->ecs()->calcule($this->ressource()->ecs()->data()->with(
            pertes: $this->pertes(),
        ));
    }
}
