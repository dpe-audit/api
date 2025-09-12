<?php

namespace App\Api\Ecs\Model;

use App\Model\Ecs\Entity\Systeme as Entity;
use App\Model\Ecs\Enum\{BouclageReseau, IsolationReseau};
use Symfony\Component\Validator\Constraints as Assert;

final class Reseau
{
    public function __construct(
        public bool $alimentation_contigue,

        #[Assert\Positive]
        public int $niveaux_desservis,

        public ?IsolationReseau $isolation,

        public ?BouclageReseau $bouclage,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            alimentation_contigue: $entity->reseau()->alimentation_contigue,
            niveaux_desservis: $entity->reseau()->niveaux_desservis,
            isolation: $entity->reseau()->isolation,
            bouclage: $entity->reseau()->bouclage,
        );
    }
}
