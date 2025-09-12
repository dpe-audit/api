<?php

namespace App\Api\Chauffage\Model;

use App\Api\Common\Model\{Besoin, Perte};
use App\Model\Common\ValueObject\{Consommation, Emission};
use App\Model\Chauffage\Chauffage as Entity;

/**
 * @property array<Besoin> $besoins
 * @property array<Perte> $pertes
 * @property array<Perte> $pertes_recuperables
 * @property array<Consommation> $consommations
 * @property array<Emission> $emissions
 */
final class ChauffageData
{
    public function __construct(
        /** @var Besoin[] */
        public array $besoins,
        /** @var Perte[] */
        public array $pertes,
        /** @var Perte[] */
        public array $pertes_recuperables,
        /** @var Consommation[] */
        public array $consommations,
        /** @var Emission[] */
        public array $emissions,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            besoins: $entity->data()->besoins ? Besoin::from($entity->data()->besoins) : [],
            pertes: $entity->data()->pertes ? Perte::from($entity->data()->pertes) : [],
            pertes_recuperables: $entity->data()->pertes_recuperables ? Perte::from($entity->data()->pertes_recuperables) : [],
            consommations: $entity->data()->consommations?->values() ?? [],
            emissions: $entity->data()->emissions?->values() ?? [],
        );
    }
}
