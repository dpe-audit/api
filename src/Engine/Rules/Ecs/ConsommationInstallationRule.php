<?php

namespace App\Engine\Rules\Ecs;

use App\Domain\Common\Consommation\{Consommation, ConsommationCollection};
use App\Domain\Common\Enum\{Energie, Usage};
use App\Engine\Input\Ecs\InstallationInputRuleIterator;
use App\Engine\Rule;

final class ConsommationInstallationRule extends InstallationInputRuleIterator
{
    /**
     * Liste des consommations de l'installation d'eau chaude sanitaire
     */
    public function consommations(): ConsommationCollection
    {
        return $this->get('consommations', function (): ConsommationCollection {
            $collection = ConsommationCollection::create();

            foreach ($this->item()->systemes() as $item) {
                $collection->with(Consommation::create(
                    usage: Usage::ECS,
                    energie: $item->generateur()->energie()->to(),
                    cef: $item->cef_ecs(),
                    cep: $item->cep_ecs(),
                    eges: $item->eges_ecs(),
                ));
                $collection->with(Consommation::create(
                    usage: Usage::AUXILIAIRE,
                    energie: Energie::ELECTRICITE,
                    cef: $item->cef_aux(),
                    cep: $item->cep_aux(),
                    eges: $item->eges_aux(),
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
            consommations: $this->consommations(),
        ));
    }
}
