<?php

namespace App\Api\Ecs\Model;

use App\Model\Ecs\Entity\{Installation as Entity, InstallationCollection as EntityCollection};
use Symfony\Component\Validator\Constraints as Assert;

final class Installation
{
    public function __construct(
        public string $id,

        public string $description,

        #[Assert\Positive]
        public float $surface,

        #[Assert\Valid]
        public ?Solaire $solaire_thermique,

        public ?InstallationData $data,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            surface: $entity->surface(),
            solaire_thermique: $entity->solaire_thermique() ? Solaire::from($entity) : null,
            data: InstallationData::from($entity),
        );
    }

    /**
     * @return self[]
     */
    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->to_array();
    }
}
