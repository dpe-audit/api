<?php

namespace App\Api\Chauffage\Model;

use App\Model\Chauffage\Entity\Installation as Entity;
use App\Model\Chauffage\Enum\UsageChauffage;
use App\Services\Validator\Constraints as DpeAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class Solaire
{
    public function __construct(
        public UsageChauffage $usage,

        #[DpeAssert\Annee]
        public ?int $annee_installation,

        #[Assert\PositiveOrZero]
        #[Assert\LessThanOrEqual(100)]
        public ?float $fch,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            usage: $entity->solaire_thermique()?->usage,
            fch: $entity->solaire_thermique()?->fch?->value(),
            annee_installation: $entity->solaire_thermique()?->annee_installation?->value(),
        );
    }
}
