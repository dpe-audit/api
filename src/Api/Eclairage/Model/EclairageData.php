<?php

namespace App\Api\Eclairage\Model;

use App\Model\Common\ValueObject\{Consommation, Emission};
use App\Model\Eclairage\Eclairage as Entity;

/**
 * @property array<Consommation> $consommations
 * @property array<Emission> $emissions
 */
final class EclairageData
{
    public function __construct(
        /** @var array<Consommation> */
        public array $consommations,
        /** @var array<Emission> */
        public array $emissions,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            consommations: $entity->data()->consommations?->values() ?? [],
            emissions: $entity->data()->emissions?->values() ?? [],
        );
    }
}
