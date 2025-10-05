<?php

namespace App\Engine\Rules\Refroidissement;

use App\Domain\Common\Consommation\{Consommation, ConsommationCollection};
use App\Domain\Common\Enum\{Energie, Usage};
use App\Engine\Rule;

final class ConsommationRefroidissementRule extends Rule
{
    /**
     * Liste des consommations de refroidissement
     */
    public function consommations(): ConsommationCollection
    {
        return $this->get('consommations', function (): ConsommationCollection {
            $collection = ConsommationCollection::create();

            foreach ($this->data()->refroidissement->systemes as $item) {
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
     * Consommation d'énergie final de refroidissement en kWh/an
     */
    public function cef_fr(): float
    {
        return $this->get('cef_fr', function (): float {
            return array_sum(array_map(
                fn($item) => $item->cef_fr(),
                $this->data()->refroidissement->systemes,
            ));
        });
    }

    /**
     * Consommation d'énergie primaire de refroidissement en kWh/an
     */
    public function cep_fr(): float
    {
        return $this->get('cep_fr', function (): float {
            return array_sum(array_map(
                fn($item) => $item->cep_fr(),
                $this->data()->refroidissement->systemes,
            ));
        });
    }

    /**
     * Consommation d'énergie primaire de refroidissement en kWh/an
     */
    public function eges_fr(): float
    {
        return $this->get('eges_fr', function (): float {
            return array_sum(array_map(
                fn($item) => $item->eges_fr(),
                $this->data()->refroidissement->systemes,
            ));
        });
    }

    /**
     * Consommation d'énergie final des auxiliaires de refroidissement en kWh/an
     */
    public function cef_aux(): float
    {
        return $this->get('cef_aux', function (): float {
            return array_sum(array_map(
                fn($item) => $item->cef_aux(),
                $this->data()->refroidissement->systemes,
            ));
        });
    }

    /**
     * Consommation d'énergie primaire des auxiliaires de refroidissement en kWh/an
     */
    public function cep_aux(): float
    {
        return $this->get('cep_aux', function (): float {
            return array_sum(array_map(
                fn($item) => $item->cep_aux(),
                $this->data()->refroidissement->systemes,
            ));
        });
    }

    /**
     * Consommation d'énergie primaire des auxiliaires de refroidissement en kWh/an
     */
    public function eges_aux(): float
    {
        return $this->get('eges_aux', function (): float {
            return array_sum(array_map(
                fn($item) => $item->eges_aux(),
                $this->data()->refroidissement->systemes,
            ));
        });
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->ressource()->refroidissement()->calcule($this->ressource()->refroidissement()->data()->with(
            consommations: $this->consommations(),
        ));
    }
}
