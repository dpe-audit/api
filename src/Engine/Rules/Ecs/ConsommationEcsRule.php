<?php

namespace App\Engine\Rules\Ecs;

use App\Domain\Common\Consommation\{Consommation, ConsommationCollection};
use App\Domain\Common\Enum\{Energie, Usage};
use App\Engine\Rule;

final class ConsommationEcsRule extends Rule
{
    /**
     * Liste des consommations d'eau chaude sanitaire
     */
    public function consommations(): ConsommationCollection
    {
        return $this->get('consommations', function (): ConsommationCollection {
            $collection = ConsommationCollection::create();

            foreach ($this->data()->ecs->systemes as $item) {
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
     * Consommation d'énergie final d'eau chaude sanitaire en kWh/an
     */
    public function cef_ecs(): float
    {
        return $this->get('cef_ecs', function (): float {
            return array_sum(array_map(
                fn($item) => $item->cef_ecs(),
                $this->data()->ecs->systemes,
            ));
        });
    }

    /**
     * Consommation d'énergie primaire d'eau chaude sanitaire en kWh/an
     */
    public function cep_ecs(): float
    {
        return $this->get('cep_ecs', function (): float {
            return array_sum(array_map(
                fn($item) => $item->cep_ecs(),
                $this->data()->ecs->systemes,
            ));
        });
    }

    /**
     * Consommation d'énergie primaire d'eau chaude sanitaire en kWh/an
     */
    public function eges_ecs(): float
    {
        return $this->get('eges_ecs', function (): float {
            return array_sum(array_map(
                fn($item) => $item->eges_ecs(),
                $this->data()->ecs->systemes,
            ));
        });
    }

    /**
     * Consommation d'énergie final des auxiliaires d'eau chaude sanitaire en kWh/an
     */
    public function cef_aux(): float
    {
        return $this->get('cef_aux', function (): float {
            return array_sum(array_map(
                fn($item) => $item->cef_aux(),
                $this->data()->ecs->systemes,
            ));
        });
    }

    /**
     * Consommation d'énergie primaire des auxiliaires d'eau chaude sanitaire en kWh/an
     */
    public function cep_aux(): float
    {
        return $this->get('cep_aux', function (): float {
            return array_sum(array_map(
                fn($item) => $item->cep_aux(),
                $this->data()->ecs->systemes,
            ));
        });
    }

    /**
     * Consommation d'énergie primaire des auxiliaires d'eau chaude sanitaire en kWh/an
     */
    public function eges_aux(): float
    {
        return $this->get('eges_aux', function (): float {
            return array_sum(array_map(
                fn($item) => $item->eges_aux(),
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
            consommations: $this->consommations(),
        ));
    }
}
