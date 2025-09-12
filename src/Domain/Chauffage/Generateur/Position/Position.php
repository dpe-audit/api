<?php

namespace App\Domain\Chauffage\Generateur\Position;

use App\Domain\Ecs\Generateur\Generateur;
use App\Domain\Reseau\Reseau;

final class Position
{
    public function __construct(
        public readonly bool $position_volume_chauffe,
        public readonly bool $generateur_collectif,
        public readonly bool $generateur_multi_batiment,
        public readonly ?int $cascade,
        public readonly ?int $priorite_cascade,
        public readonly ?PositionChaudiere $position_chaudiere,
        public readonly ?Generateur $generateur_mixte,
        public readonly ?Reseau $reseau_chaleur,
    ) {}

    public static function create(
        bool $position_volume_chauffe,
        bool $generateur_collectif,
        bool $generateur_multi_batiment,
        ?int $cascade,
        ?int $priorite_cascade,
        ?PositionChaudiere $position_chaudiere,
        ?Generateur $generateur_mixte,
        ?Reseau $reseau_chaleur,
    ): self {
        return new self(
            position_volume_chauffe: $position_volume_chauffe,
            generateur_collectif: $generateur_collectif,
            generateur_multi_batiment: $generateur_multi_batiment,
            cascade: $cascade,
            priorite_cascade: $priorite_cascade,
            position_chaudiere: $position_chaudiere,
            generateur_mixte: $generateur_mixte,
            reseau_chaleur: $reseau_chaleur,
        );
    }
}
