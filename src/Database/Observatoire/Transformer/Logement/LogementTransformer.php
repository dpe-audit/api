<?php

namespace App\Database\Observatoire\Transformer\Logement;

use App\Database\Observatoire\Model\{XMLLogementVisite, XMLRessource};
use App\Dto\Logement\LogementDto;

final class LogementTransformer
{
    /**
     * @return array<LogementDto>
     */
    public function __invoke(XMLRessource $ressource): array
    {
        return array_map(
            fn(XMLLogementVisite $element): LogementDto => new LogementDto(
                id: (string) $element->id(),
                description: $element->description,
                surface_habitable: $element->surface_habitable_logement,
                hauteur_sous_plafond: $ressource->logement()->caracteristique_generale->hsp,
                position: $element->position(),
                typologie: $element->typologie(),
            ),
            $ressource->logement_visite_collection ?? [],
        );
    }
}
