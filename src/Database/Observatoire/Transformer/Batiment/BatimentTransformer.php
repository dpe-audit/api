<?php

namespace App\Database\Observatoire\Transformer\Batiment;

use App\Database\Observatoire\Model\XMLRessource;
use App\Dto\Batiment\BatimentDto;

final class BatimentTransformer
{
    public function __invoke(XMLRessource $ressource): BatimentDto
    {
        $caracteristique_generale = $ressource->logement()->caracteristique_generale;
        $geolocalisation = $ressource->administratif->geolocalisation;
        $meteo = $ressource->logement()->meteo;

        return new BatimentDto(
            rnb_id: $geolocalisation->id_batiment_rnb,
            type: $caracteristique_generale->type_batiment(),
            annee_construction: $caracteristique_generale->annee_construction(),
            logements: $caracteristique_generale->logements(),
            surface_habitable: $caracteristique_generale->surface_habitable(),
            hauteur_sous_plafond: $caracteristique_generale->hsp,
            altitude: $meteo->altitude(),
            materiaux_anciens: $meteo->batiment_materiaux_anciens,
        );
    }
}
