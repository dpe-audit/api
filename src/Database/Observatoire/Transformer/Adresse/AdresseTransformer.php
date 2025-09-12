<?php

namespace App\Database\Observatoire\Transformer\Adresse;

use App\Database\Observatoire\Model\XMLRessource;
use App\Dto\Adresse\AdresseDto;

final class AdresseTransformer
{
    public function __invoke(XMLRessource $ressource): AdresseDto
    {
        $adresse_bien = $ressource->administratif->geolocalisation->adresses->adresse_bien;

        return new AdresseDto(
            nom: $adresse_bien->nom(),
            code_postal: $adresse_bien->code_postal(),
            code_insee: $adresse_bien->code_insee() ?? $adresse_bien->code_postal(),
            commune: $adresse_bien->commune(),
            ban_id: $adresse_bien->ban_id,
        );
    }
}
