<?php

namespace App\Api\Chauffage\Model;

use App\Model\Chauffage\Entity\{Emetteur as Entity, EmetteurCollection as EntityCollection};
use App\Model\Chauffage\Enum\{TemperatureDistribution, TypeEmetteur};
use App\Services\Validator\Constraints as DpeAssert;

final class Emetteur
{
    public function __construct(
        public string $id,

        public string $description,

        public TypeEmetteur $type,

        public TemperatureDistribution $temperature_distribution,

        public bool $presence_robinet_thermostatique,

        #[DpeAssert\Annee]
        public ?int $annee_installation,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            type: $entity->type(),
            temperature_distribution: $entity->temperature_distribution(),
            presence_robinet_thermostatique: $entity->presence_robinet_thermostatique(),
            annee_installation: $entity->annee_installation()?->value,
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
