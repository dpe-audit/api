<?php

namespace App\Api\Chauffage\Model;

use App\Model\Common\ValueObject\{Consommation, Emission};
use App\Model\Chauffage\Entity\Installation as Entity;

/**
 * @property array<Consommation> $consommations
 * @property array<Emission> $emissions
 */
final class InstallationData
{
    public function __construct(
        public ?float $rdim,
        public ?float $fch,
        /** @var Consommation[] */
        public array $consommations,
        /** @var Emission[] */
        public array $emissions,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            rdim: $entity->data()->rdim,
            fch: $entity->data()->fch?->value(),
            consommations: $entity->data()->consommations?->values() ?? [],
            emissions: $entity->data()->emissions?->values() ?? [],
        );
    }
}
