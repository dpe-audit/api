<?php

namespace App\Domain\Ecs\Generateur\Position;

use App\Domain\Chauffage\Generateur\Generateur;
use App\Domain\Reseau\Reseau;

final class Position
{
    public function __construct(
        public readonly bool $generateur_collectif,
        public readonly bool $generateur_multi_batiment,
        public readonly bool $position_volume_chauffe,
        public readonly ?PositionChauffeEau $position_chauffe_eau,
        public readonly ?Generateur $generateur_mixte,
        public readonly ?Reseau $reseau_chaleur,
    ) {}

    public static function create(
        bool $generateur_collectif,
        bool $generateur_multi_batiment,
        bool $position_volume_chauffe,
        ?PositionChauffeEau $position_chauffe_eau,
        ?Generateur $generateur_mixte,
        ?Reseau $reseau_chaleur
    ): self {
        return new self(
            generateur_collectif: $generateur_collectif,
            generateur_multi_batiment: $generateur_multi_batiment,
            position_volume_chauffe: $position_volume_chauffe,
            position_chauffe_eau: $position_chauffe_eau,
            generateur_mixte: $generateur_mixte,
            reseau_chaleur: $reseau_chaleur
        );
    }
}
