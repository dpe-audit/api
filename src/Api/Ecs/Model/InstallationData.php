<?php

namespace App\Api\Ecs\Model;

use App\Api\Common\Model\Perte;
use App\Model\Common\ValueObject\{Consommation, Emission};
use App\Model\Ecs\Entity\Installation as Entity;

/**
 * @property array<Perte> $pertes
 * @property array<Perte> $pertes_recuperables
 * @property array<Consommation> $consommations
 * @property array<Emission> $emissions
 */
final class InstallationData
{
    public function __construct(
        public ?float $rdim,
        public ?float $fecs,
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
            rdim: $entity->data()->rdim,
            fecs: $entity->data()->fecs?->value(),
            pertes: $entity->data()->pertes ? Perte::from($entity->data()->pertes) : [],
            pertes_recuperables: $entity->data()->pertes_recuperables ? Perte::from($entity->data()->pertes_recuperables) : [],
            consommations: $entity->data()->consommations?->values() ?? [],
            emissions: $entity->data()->emissions?->values() ?? [],
        );
    }
}
