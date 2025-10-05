<?php

namespace App\Dto\Chauffage\Generateur;

use App\Domain\Chauffage\Generateur\Position\{Position, PositionChaudiere};

final class PositionDto
{
    public function __construct(
        public bool $generateur_collectif,
        public bool $generateur_multi_batiment,
        public bool $position_volume_chauffe,
        public ?int $cascade,
        public ?int $priorite_cascade,
        public ?PositionChaudiere $position_chaudiere,
        public ?string $generateur_mixte_id,
        public ?string $reseau_chaleur_id,
    ) {}

    public static function from(Position $data): self
    {
        return new self(
            generateur_collectif: $data->generateur_collectif,
            generateur_multi_batiment: $data->generateur_multi_batiment,
            position_volume_chauffe: $data->position_volume_chauffe,
            cascade: $data->cascade,
            priorite_cascade: $data->priorite_cascade,
            position_chaudiere: $data->position_chaudiere,
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
            'cascade' => $this->cascade,
            'priorite_cascade' => $this->priorite_cascade,
            'position_chaudiere' => $this->position_chaudiere?->value,
            'generateur_mixte_id' => $this->generateur_mixte_id,
            'reseau_chaleur_id' => $this->reseau_chaleur_id,
        ];
    }
}
