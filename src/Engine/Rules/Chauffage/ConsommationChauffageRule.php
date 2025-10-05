<?php

namespace App\Engine\Rules\Chauffage;

use App\Domain\Common\Consommation\{Consommation, ConsommationCollection};
use App\Domain\Common\Enum\{Energie, Usage};
use App\Engine\Rule;

final class ConsommationChauffageRule extends Rule
{
    /**
     * Liste des consommations de chauffage
     */
    public function consommations(): ConsommationCollection
    {
        return $this->get('consommations', function (): ConsommationCollection {
            $collection = ConsommationCollection::create();

            foreach ($this->data()->chauffage->systemes as $item) {
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
     * Consommation d'énergie final de chauffage en kWh/an
     */
    public function cef_ch(): float
    {
        return $this->get('cef_ch', function (): float {
            return array_sum(array_map(
                fn($item) => $item->cef_ch(),
                $this->data()->chauffage->systemes,
            ));
        });
    }

    /**
     * Consommation d'énergie primaire de chauffage en kWh/an
     */
    public function cep_ch(): float
    {
        return $this->get('cep_ch', function (): float {
            return array_sum(array_map(
                fn($item) => $item->cep_ch(),
                $this->data()->chauffage->systemes,
            ));
        });
    }

    /**
     * Consommation d'énergie primaire de chauffage en kWh/an
     */
    public function eges_ch(): float
    {
        return $this->get('eges_ch', function (): float {
            return array_sum(array_map(
                fn($item) => $item->eges_ch(),
                $this->data()->chauffage->systemes,
            ));
        });
    }

    /**
     * Consommation d'énergie final des auxiliaires de chauffage en kWh/an
     */
    public function cef_aux(): float
    {
        return $this->get('cef_aux', function (): float {
            return array_sum(array_map(
                fn($item) => $item->cef_aux(),
                $this->data()->chauffage->systemes,
            ));
        });
    }

    /**
     * Consommation d'énergie primaire des auxiliaires de chauffage en kWh/an
     */
    public function cep_aux(): float
    {
        return $this->get('cep_aux', function (): float {
            return array_sum(array_map(
                fn($item) => $item->cep_aux(),
                $this->data()->chauffage->systemes,
            ));
        });
    }

    /**
     * Consommation d'énergie primaire des auxiliaires de chauffage en kWh/an
     */
    public function eges_aux(): float
    {
        return $this->get('eges_aux', function (): float {
            return array_sum(array_map(
                fn($item) => $item->eges_aux(),
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
            consommations: $this->consommations(),
        ));
    }
}
