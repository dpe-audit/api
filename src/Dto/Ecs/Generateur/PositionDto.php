<?php

namespace App\Dto\Ecs\Generateur;

use App\Domain\Ecs\Generateur\Position\{Position, PositionChauffeEau};

final class PositionDto
{
    public function __construct(
        public bool $generateur_collectif,
        public bool $generateur_multi_batiment,
        public bool $position_volume_chauffe,
        public ?PositionChauffeEau $position_chauffe_eau,
        public ?string $generateur_mixte_id,
        public ?string $reseau_chaleur_id,
    ) {}

    public static function from(Position $data): self
    {
        return new self(
            generateur_collectif: $data->generateur_collectif,
            generateur_multi_batiment: $data->generateur_multi_batiment,
            position_volume_chauffe: $data->position_volume_chauffe,
            position_chauffe_eau: $data->position_chauffe_eau,
            generateur_mixte_id: $data->generateur_mixte?->id()->toBinary(),
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
