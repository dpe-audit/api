<?php

namespace App\Api\Ecs\Model;

use App\Model\Ecs\Entity\{Generateur as Entity, GenerateurCollection as EntityCollection};
use App\Model\Ecs\Enum\{EnergieGenerateur, LabelGenerateur, ModeCombustion, TypeGenerateur, TypeChaudiere};
use App\Services\Validator\Constraints as DpeAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class Generateur
{
    public function __construct(
        public string $id,

        public string $description,

        public TypeGenerateur $type,

        public EnergieGenerateur $energie,

        #[Assert\PositiveOrZero]
        public float $volume_stockage,

        public bool $generateur_collectif,

        public bool $generateur_multi_batiment,

        public bool $position_volume_chauffe,

        #[DpeAssert\Annee]
        public ?int $annee_installation,

        #[Assert\Positive]
        public ?float $pn,

        #[Assert\Positive]
        public ?float $cop,

        public ?LabelGenerateur $label,

        public ?TypeChaudiere $type_chaudiere,

        public ?ModeCombustion $mode_combustion,

        public ?bool $presence_ventouse,

        #[Assert\PositiveOrZero]
        public ?float $pveilleuse,

        #[Assert\Positive]
        public ?float $qp0,

        #[Assert\Positive]
        #[Assert\LessThanOrEqual(150)]
        public ?float $rpn,

        public ?string $reseau_chaleur_id,

        public ?string $generateur_mixte_id,

        public ?GenerateurData $data,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            type: $entity->type(),
            energie: $entity->energie(),
            volume_stockage: $entity->signaletique()->volume_stockage,
            generateur_collectif: $entity->position()->generateur_collectif,
            generateur_multi_batiment: $entity->position()->generateur_multi_batiment,
            position_volume_chauffe: $entity->position()->position_volume_chauffe,
            annee_installation: $entity->annee_installation()?->value,
            pn: $entity->signaletique()->pn,
            cop: $entity->signaletique()->cop,
            label: $entity->signaletique()->label,
            type_chaudiere: $entity->signaletique()->type_chaudiere,
            mode_combustion: $entity->combustion()?->mode_combustion,
            presence_ventouse: $entity->combustion()?->presence_ventouse,
            pveilleuse: $entity->combustion()?->pveilleuse,
            qp0: $entity->combustion()?->qp0,
            rpn: $entity->combustion()?->rpn?->value(),
            generateur_mixte_id: $entity->position()->generateur_mixte_id,
            reseau_chaleur_id: $entity->position()->reseau_chaleur?->id(),
            data: GenerateurData::from($entity),
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
