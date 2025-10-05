<?php

namespace App\Engine\Rules\Chauffage;

use App\Domain\Common\Consommation\{Consommation, ConsommationCollection};
use App\Domain\Common\Enum\{Energie, Usage};
use App\Engine\Input\Chauffage\InstallationInputRuleIterator;
use App\Engine\Rule;

final class ConsommationInstallationRule extends InstallationInputRuleIterator
{
    /**
     * Liste des consommations de l'installation de chauffage
     */
    public function consommations(): ConsommationCollection
    {
        return $this->get('consommations', function (): ConsommationCollection {
            $collection = ConsommationCollection::create();

            foreach ($this->item()->systemes() as $item) {
                $collection->with(Consommation::create(
                    usage: Usage::CHAUFFAGE,
                    energie: $item->generateur()->energie()->to(),
                    cef: $item->cef_ch(),
                    cep: $item->cep_ch(),
                    eges: $item->eges_ch(),
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
