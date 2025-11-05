<?php

namespace App\Dto\Logement;

use App\Domain\Logement\{Logement, Position, Typologie};

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/logement/logement.yaml
 */
final class LogementDto
{
    public function __construct(
        public string $id,
        public string $description,
        public float $surface_habitable,
        public float $hauteur_sous_plafond,
        public Position $position,
        public Typologie $typologie,
    ) {}

    public static function from(Logement $data): self
    {
        return new self(
            id: (string) $data->id(),
            description: $data->description(),
            surface_habitable: $data->surface_habitable(),
            hauteur_sous_plafond: $data->hauteur_sous_plafond(),
            position: $data->position(),
            typologie: $data->typologie(),
        );
    }

    public function __normalize(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'surface_habitable' => $this->surface_habitable,
            'hauteur_sous_plafond' => $this->hauteur_sous_plafond,
            'position' => $this->position->value,
            'typologie' => $this->typologie->value,
        ];
    }
}
