<?php

namespace App\Engine\Input\Chauffage;

use App\Domain\Chauffage\Emetteur\{Emetteur, TemperatureDistribution, TypeEmetteur, TypeEmission};
use App\Engine\{Engine, Input};

final class EmetteurInput extends Input
{
    public function __construct(
        public readonly Engine $context,
        public readonly Emetteur $entity,
    ) {}

    public function type(): TypeEmetteur
    {
        return $this->entity->type();
    }

    public function type_emission(): TypeEmission
    {
        return $this->entity->type_emission();
    }

    public function temperature_distribution(): TemperatureDistribution
    {
        return $this->entity->temperature_distribution();
    }

    public function presence_robinet_thermostatique(): bool
    {
        return $this->entity->presence_robinet_thermostatique();
    }

    public function annee_installation(): int
    {
        return $this->entity->annee_installation()
            ?? $this->context->data()->batiment->annee_construction();
    }
}
