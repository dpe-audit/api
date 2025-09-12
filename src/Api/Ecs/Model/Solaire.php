<?php

namespace App\Api\Ecs\Model;

use App\Model\Ecs\Entity\Installation as Entity;
use App\Model\Ecs\Enum\UsageEcs;
use App\Services\Validator\Constraints as DpeAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class Solaire
{
    public function __construct(
        public UsageEcs $usage,

        #[DpeAssert\Annee]
        public ?int $annee_installation,

        #[Assert\PositiveOrZero]
        #[Assert\LessThanOrEqual(100)]
        public ?float $fecs,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            usage: $entity->solaire_thermique()?->usage,
            fecs: $entity->solaire_thermique()?->fecs?->value(),
            annee_installation: $entity->solaire_thermique()?->annee_installation?->value(),
        );
    }
}
