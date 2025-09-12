<?php

namespace App\Api\Ecs\Model;

use App\Model\Ecs\Entity\Systeme as Entity;
use Symfony\Component\Validator\Constraints as Assert;

final class Stockage
{
    public function __construct(
        #[Assert\PositiveOrZero]
        public float $volume,

        #[Assert\Positive]
        public bool $position_volume_chauffe,
    ) {}

    public static function from(Entity $entity): ?self
    {
        return $entity->stockage() ? new self(
            volume: $entity->stockage()->volume,
            position_volume_chauffe: $entity->stockage()->position_volume_chauffe,
        ) : null;
    }
}
