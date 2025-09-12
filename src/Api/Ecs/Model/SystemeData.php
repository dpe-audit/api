<?php

namespace App\Api\Ecs\Model;

use App\Api\Common\Model\Perte;
use App\Model\Common\ValueObject\{Consommation, Emission};
use App\Model\Ecs\Entity\Systeme as Entity;
use App\Model\Ecs\ValueObject\Rendement;

/**
 * @property array<Rendement> $rendements
 * @property array<Perte> $pertes
 * @property array<Perte> $pertes_recuperables
 * @property array<Consommation> $consommations
 * @property array<Emission> $emissions
 */
final class SystemeData
{
    public function __construct(
        public ?float $rdim,
        /** @var Rendement[] */
        public array $rendements,
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
            rendements: $entity->data()->rendements?->values() ?? [],
            pertes: $entity->data()->pertes ? Perte::from($entity->data()->pertes) : [],
            pertes_recuperables: $entity->data()->pertes_recuperables ? Perte::from($entity->data()->pertes_recuperables) : [],
            consommations: $entity->data()->consommations?->values() ?? [],
            emissions: $entity->data()->emissions?->values() ?? [],
        );
    }
}
