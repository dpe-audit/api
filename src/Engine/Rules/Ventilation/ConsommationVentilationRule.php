<?php

namespace App\Engine\Rules\Ventilation;

use App\Domain\Common\Consommation\{Consommation, ConsommationCollection};
use App\Domain\Common\Enum\{Energie, Usage};
use App\Engine\Rule;

final class ConsommationVentilationRule extends Rule
{
    /**
     * Liste des consommations d'énergie de ventilation
     */
    public function consommations(): ConsommationCollection
    {
        return $this->get('consommations', function (): ConsommationCollection {
            return ConsommationCollection::create(...array_map(
                fn($item) => Consommation::create(
                    energie: Energie::ELECTRICITE,
                    usage: Usage::AUXILIAIRE,
                    cef: $item->cef_aux(),
                    cep: $item->cep_aux(),
                    eges: $item->eges_aux(),
                ),
                $this->data()->ventilation->generateurs,
            ));
        });
    }

    /**
     * Consommation d'énergie final des auxiliaires de ventilation en kWh/an
     */
    public function cef_aux(): float
    {
        return $this->get('cef_aux', function (): float {
            return array_sum(array_map(
                fn($item) => $item->cef_aux(),
                $this->data()->ventilation->generateurs,
            ));
        });
    }

    /**
     * Consommation d'énergie primaire des auxiliaires de ventilation en kWh/an
     */
    public function cep_aux(): float
    {
        return $this->get('cep_aux', function (): float {
            return array_sum(array_map(
                fn($item) => $item->cep_aux(),
                $this->data()->ventilation->generateurs,
            ));
        });
    }

    /**
     * Consommation d'énergie primaire des auxiliaires de ventilation en kWh/an
     */
    public function eges_aux(): float
    {
        return $this->get('eges_aux', function (): float {
            return array_sum(array_map(
                fn($item) => $item->eges_aux(),
                $this->data()->ventilation->generateurs,
            ));
        });
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->ressource()->ventilation()->calcule($this->ressource()->ventilation()->data()->with(
            consommations: $this->consommations(),
        ));
    }
}
