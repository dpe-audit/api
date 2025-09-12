<?php

namespace App\Api\Chauffage\Model;

use App\Model\Chauffage\Entity\{Installation as Entity, InstallationCollection as EntityCollection};
use Symfony\Component\Validator\Constraints as Assert;

final class Installation
{
    public function __construct(
        public string $id,

        public string $description,

        #[Assert\Positive]
        public float $surface,

        public bool $comptage_individuel,

        #[Assert\Valid]
        public ?Solaire $solaire_thermique,

        #[Assert\Valid]
        public Regulation $regulation_centrale,

        #[Assert\Valid]
        public Regulation $regulation_terminale,

        public ?InstallationData $data,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            surface: $entity->surface(),
            comptage_individuel: $entity->comptage_individuel(),
            solaire_thermique: $entity->solaire_thermique() ? Solaire::from($entity) : null,
            regulation_centrale: Regulation::from($entity->regulation_centrale()),
            regulation_terminale: Regulation::from($entity->regulation_terminale()),
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
