<?php

namespace App\Domain\Chauffage\Generateur\Position;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Reseau\Reseau;

final class Position
{
    public function __construct(
        public readonly bool $position_volume_chauffe,
        public readonly bool $generateur_collectif,
        public readonly bool $generateur_multi_batiment,
        public readonly ?PositionChaudiere $position_chaudiere,
        public readonly ?Id $generateur_mixte_id,
        public readonly ?Reseau $reseau_chaleur,
    ) {}

    public static function create(
        bool $position_volume_chauffe,
        bool $generateur_collectif,
        bool $generateur_multi_batiment,
        ?PositionChaudiere $position_chaudiere,
        ?Id $generateur_mixte_id,
        ?Reseau $reseau_chaleur,
    ): self {
        return new self(
            position_volume_chauffe: $position_volume_chauffe,
            generateur_collectif: $generateur_collectif,
            generateur_multi_batiment: $generateur_multi_batiment,
            position_chaudiere: $position_chaudiere,
            generateur_mixte_id: $generateur_mixte_id,
            reseau_chaleur: $reseau_chaleur,
        );
    }
}
