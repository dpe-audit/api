<?php

namespace App\Dto\Batiment;

use App\Domain\Batiment\Batiment;
use App\Domain\Batiment\TypeBatiment;
use App\Domain\Common\ValueObject\Annee;

final class BatimentDto
{
    public function __construct(
        public TypeBatiment $type,
        public int $annee_construction,
        public float $altitude,
        public int $logements,
        public float $surface_habitable,
        public float $hauteur_sous_plafond,
        public bool $materiaux_anciens,
        public ?string $rnb_id,
    ) {}

    public static function from(Batiment $data): self
    {
        return new self(
            type: $data->type,
            annee_construction: $data->annee_construction,
            altitude: $data->altitude,
            logements: $data->logements,
            surface_habitable: $data->surface_habitable,
            hauteur_sous_plafond: $data->hauteur_sous_plafond,
            materiaux_anciens: $data->materiaux_anciens,
            rnb_id: $data->rnb_id,
        );
    }

    public function to(): Batiment
    {
        return Batiment::create(
            type: $this->type,
            annee_construction: $this->annee_construction,
            altitude: $this->altitude,
            logements: $this->logements,
            surface_habitable: $this->surface_habitable,
            hauteur_sous_plafond: $this->hauteur_sous_plafond,
            materiaux_anciens: $this->materiaux_anciens,
            rnb_id: $this->rnb_id,
        );
    }

    public function __normalize(): array
    {
        return [
            'type' => $this->type->value,
            'annee_construction' => $this->annee_construction,
            'altitude' => $this->altitude,
            'logements' => $this->logements,
            'surface_habitable' => $this->surface_habitable,
            'hauteur_sous_plafond' => $this->hauteur_sous_plafond,
            'materiaux_anciens' => $this->materiaux_anciens,
            'rnb_id' => $this->rnb_id,
        ];
    }
}
