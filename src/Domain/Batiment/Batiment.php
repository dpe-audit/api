<?php

namespace App\Domain\Batiment;

use App\Domain\Adresse\Adresse;

final class Batiment
{
    public function __construct(
        public readonly TypeBatiment $type,
        public readonly int $annee_construction,
        public readonly PeriodeConstruction $periode_construction,
        public readonly float $altitude,
        public readonly ClasseAltitude $classe_altitude,
        public readonly int $logements,
        public readonly float $surface_habitable,
        public readonly float $hauteur_sous_plafond,
        public readonly float $volume_habitable,
        public readonly bool $materiaux_anciens,
        public readonly Adresse $adresse,
        public readonly ?string $rnb_id,
    ) {}

    public static function create(
        TypeBatiment $type,
        int $annee_construction,
        float $altitude,
        int $logements,
        float $surface_habitable,
        float $hauteur_sous_plafond,
        bool $materiaux_anciens,
        Adresse $adresse,
        ?string $rnb_id,
    ): self {
        return new self(
            type: $type,
            annee_construction: $annee_construction,
            periode_construction: PeriodeConstruction::fromAnnee($annee_construction),
            altitude: $altitude,
            classe_altitude: ClasseAltitude::fromAltitude($altitude),
            logements: $logements,
            surface_habitable: $surface_habitable,
            hauteur_sous_plafond: $hauteur_sous_plafond,
            volume_habitable: $surface_habitable * $hauteur_sous_plafond,
            materiaux_anciens: $materiaux_anciens,
            adresse: $adresse,
            rnb_id: $rnb_id,
        );
    }
}
