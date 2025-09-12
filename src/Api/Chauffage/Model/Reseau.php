<?php

namespace App\Api\Chauffage\Model;

use App\Model\Chauffage\Entity\Systeme as Entity;
use App\Model\Chauffage\Enum\{IsolationReseau, TypeDistribution};
use Symfony\Component\Validator\Constraints as Assert;

final class Reseau
{
    public function __construct(
        public TypeDistribution $type_distribution,

        public bool $presence_circulateur_externe,

        #[Assert\Positive]
        public int $niveaux_desservis,

        public ?IsolationReseau $isolation,
    ) {}

    public static function from(Entity $entity): ?self
    {
        return $entity->reseau() ? new self(
            type_distribution: $entity->reseau()->type_distribution,
            presence_circulateur_externe: $entity->reseau()->presence_circulateur_externe,
            niveaux_desservis: $entity->reseau()->niveaux_desservis,
            isolation: $entity->reseau()->isolation,
        ) : null;
    }
}
