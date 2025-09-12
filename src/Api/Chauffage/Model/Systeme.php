<?php

namespace App\Api\Chauffage\Model;

use App\Model\Chauffage\Enum\TypeChauffage;
use App\Model\Chauffage\Entity\{Emetteur as EmetteurEntity, Systeme as Entity, SystemeCollection as EntityCollection};
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @property string[] $emetteurs
 */
final class Systeme
{
    public function __construct(
        public string $id,

        public string $installation_id,

        public string $generateur_id,

        public TypeChauffage $type,

        #[Assert\Valid]
        public ?Reseau $reseau,

        #[Assert\All([
            new Assert\Type('string'),
        ])]
        public array $emetteurs,

        public ?SystemeData $data,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            installation_id: $entity->installation()->id(),
            generateur_id: $entity->generateur()?->id(),
            type: $entity->type_chauffage(),
            reseau: Reseau::from($entity),
            emetteurs: $entity->emetteurs()->map(fn(EmetteurEntity $item) => $item->id()->value)->to_array(),
            data: SystemeData::from($entity),
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
