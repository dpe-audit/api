<?php

namespace App\Domain\Ecs\Generateur\Position;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Reseau\Reseau;

final class Position
{
    public function __construct(
        public readonly bool $generateur_collectif,
        public readonly bool $generateur_multi_batiment,
        public readonly bool $position_volume_chauffe,
        public readonly ?PositionChauffeEau $position_chauffe_eau,
        public readonly ?Id $generateur_mixte_id,
        public readonly ?Reseau $reseau_chaleur,
    ) {}

    public static function create(
        bool $generateur_collectif,
        bool $generateur_multi_batiment,
        bool $position_volume_chauffe,
        ?PositionChauffeEau $position_chauffe_eau,
        ?Id $generateur_mixte_id,
        ?Reseau $reseau_chaleur
    ): self {
        return new self(
            generateur_collectif: $generateur_collectif,
            generateur_multi_batiment: $generateur_multi_batiment,
            position_volume_chauffe: $position_volume_chauffe,
            position_chauffe_eau: $position_chauffe_eau,
            generateur_mixte_id: $generateur_mixte_id,
            reseau_chaleur: $reseau_chaleur
        );
    }
}
