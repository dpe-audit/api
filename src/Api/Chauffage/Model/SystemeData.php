<?php

namespace App\Api\Chauffage\Model;

use App\Model\Common\ValueObject\{Consommation, Emission};
use App\Model\Chauffage\Entity\Systeme as Entity;
use App\Model\Chauffage\Enum\ConfigurationSysteme;
use App\Model\Chauffage\ValueObject\{Intermittence, Rendement};

/**
 * @property array<Rendement> $rg
 * @property array<Rendement> $ich
 * @property array<Intermittence> $intermittences
 * @property array<Consommation> $consommations
 * @property array<Emission> $emissions
 */
final class SystemeData
{
    public function __construct(
        public ?ConfigurationSysteme $configuration,
        public ?float $rdim,
        public ?float $rd,
        public ?float $re,
        public ?float $rr,
        /** @var Rendement[] */
        public array $rg,
        /** @var Rendement[] */
        public array $ich,
        /** @var Intermittence[] */
        public array $intermittences,
        /** @var Consommation[] */
        public array $consommations,
        /** @var Emission[] */
        public array $emissions,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            configuration: $entity->data()->configuration,
            rdim: $entity->data()->rdim,
            rd: $entity->data()->rd,
            re: $entity->data()->re,
            rr: $entity->data()->rr,
            rg: $entity->data()->rg?->values() ?? [],
            ich: $entity->data()->ich?->values() ?? [],
            intermittences: $entity->data()->intermittences?->values() ?? [],
            consommations: $entity->data()->consommations?->values() ?? [],
            emissions: $entity->data()->emissions?->values() ?? [],
        );
    }
}
