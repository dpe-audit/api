<?php

namespace App\Legacy\Transformer\Adresse;

use App\Dto\Adresse\AdresseDto;
use App\Legacy\Model\Adresse;

final class AdresseTransformer
{
    public function __invoke(Adresse $adresse): AdresseDto
    {
        return new AdresseDto(
            nom: $adresse->nom(),
            code_postal: $adresse->code_postal(),
            code_insee: $adresse->code_insee() ?? $adresse->code_postal(),
            commune: $adresse->commune(),
            ban_id: $adresse->ban_id,
        );
    }
}
