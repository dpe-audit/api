<?php

namespace App\Dto\Batiment;

use App\Domain\Batiment\Batiment;
use App\Domain\Batiment\TypeBatiment;
use App\Dto\Adresse\AdresseDto;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/batiment/batiment.yaml
 */
final class BatimentDto
{
    public function __construct(
        public readonly TypeBatiment $type,
        public readonly int $annee_construction,
        public readonly float $altitude,
        public readonly int $logements,
        public readonly float $surface_habitable,
        public readonly float $hauteur_sous_plafond,
        public readonly bool $materiaux_anciens,
        public readonly AdresseDto $adresse,
        public readonly ?string $rnb_id,
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
            adresse: AdresseDto::from($data->adresse),
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
            adresse: $this->adresse->to(),
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
            'adresse' => $this->adresse->__normalize(),
            'rnb_id' => $this->rnb_id,
        ];
    }
}
