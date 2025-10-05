<?php

namespace App\Engine\Rules\Ventilation;

use App\Domain\Common\Consommation\{Consommation, ConsommationCollection};
use App\Domain\Common\Enum\{Energie, Usage};
use App\Engine\Input\Ventilation\InstallationInputRuleIterator;

final class ConsommationInstallationRule extends InstallationInputRuleIterator
{
    /**
     * Liste des consommations d'énergie de l'installation
     */
    public function consommations(): ConsommationCollection
    {
        return $this->get('consommations', function (): ConsommationCollection {
            return ConsommationCollection::create(...[
                Consommation::create(
                    energie: Energie::ELECTRICITE,
                    usage: Usage::AUXILIAIRE,
                    cef: $this->item()->generateur()->cef_aux(),
                    cep: $this->item()->generateur()->cep_aux(),
                    eges: $this->item()->generateur()->eges_aux(),
                )
            ]);
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
