<?php

namespace App\Engine\Rules\Refroidissement;

use App\Domain\Common\Consommation\{Consommation, ConsommationCollection};
use App\Domain\Common\Enum\{Energie, Usage};
use App\Engine\Input\Refroidissement\InstallationInputRuleIterator;

final class ConsommationInstallationRule extends InstallationInputRuleIterator
{
    /**
     * Liste des consommations de l'installation de refroidissement
     */
    public function consommations(): ConsommationCollection
    {
        return $this->get('consommations', function (): ConsommationCollection {
            $collection = ConsommationCollection::create();

            foreach ($this->item()->systemes() as $item) {
                $collection->with(Consommation::create(
                    usage: Usage::REFROIDISSEMENT,
                    energie: $item->generateur()->energie()->to(),
                    cef: $item->cef_fr(),
                    cep: $item->cep_fr(),
                    eges: $item->eges_fr(),
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
