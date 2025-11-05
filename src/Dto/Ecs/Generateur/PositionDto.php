<?php

namespace App\Dto\Ecs\Generateur;

use App\Domain\Ecs\Generateur\Position\{Position, PositionChauffeEau};

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/ecs/generateur.yaml
 */
final class PositionDto
{
    public function __construct(
        public readonly bool $generateur_collectif,
        public readonly bool $generateur_multi_batiment,
        public readonly bool $position_volume_chauffe,
        public readonly ?PositionChauffeEau $position_chauffe_eau,
        public readonly ?string $generateur_mixte_id,
        public readonly ?string $reseau_chaleur_id,
    ) {}

    public static function from(Position $data): self
    {
        return new self(
            generateur_collectif: $data->generateur_collectif,
            generateur_multi_batiment: $data->generateur_multi_batiment,
            position_volume_chauffe: $data->position_volume_chauffe,
            position_chauffe_eau: $data->position_chauffe_eau,
            generateur_mixte_id: $data->generateur_mixte_id,
            reseau_chaleur_id: $data->reseau_chaleur?->id(),
        );
    }

    public function __normalize(): array
    {
        return [
            'generateur_collectif' => $this->generateur_collectif,
            'generateur_multi_batiment' => $this->generateur_multi_batiment,
            'position_volume_chauffe' => $this->position_volume_chauffe,
            'position_chauffe_eau' => $this->position_chauffe_eau?->value,
            'generateur_mixte_id' => $this->generateur_mixte_id,
            'reseau_chaleur_id' => $this->reseau_chaleur_id,
        ];
    }
}
