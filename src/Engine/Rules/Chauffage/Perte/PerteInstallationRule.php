<?php

namespace App\Engine\Rules\Chauffage\Perte;

use App\Domain\Common\Enum\TypePerte;
use App\Domain\Common\Perte\{Perte, PerteCollection};
use App\Engine\Input\Chauffage\InstallationInputRuleIterator;

final class PerteInstallationRule extends InstallationInputRuleIterator
{
    /**
     * Liste des pertes de l'installation
     */
    public function pertes(): PerteCollection
    {
        return $this->get('pertes', function (): PerteCollection {
            $collection = PerteCollection::create();
            foreach ($this->item()->systemes() as $item) {
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
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->item()->entity->calcule($this->item()->entity->data()->with(
            pertes: $this->pertes(),
        ));
    }
}
