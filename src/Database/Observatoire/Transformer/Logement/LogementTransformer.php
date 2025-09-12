<?php

namespace App\Database\Observatoire\Transformer\Logement;

use App\Database\Observatoire\Model\XMLLogementVisite;
use App\Database\Observatoire\Model\XMLRessource;
use App\Domain\Common\ValueObject\Id;
use App\Dto\Logement\LogementDto;

final class LogementTransformer
{
    /**
     * @return array<LogementDto>
     */
    public function __invoke(XMLRessource $ressource): array
    {
        return array_map(
            fn(XMLLogementVisite $logement_visite): LogementDto => new LogementDto(
                id: Id::create(),
                description: $logement_visite->description,
                surface_habitable: $logement_visite->surface_habitable_logement,
                hauteur_sous_plafond: $ressource->logement()->caracteristique_generale->hsp,
                position: $logement_visite->position(),
                typologie: $logement_visite->typologie(),
            ),
            $ressource->logement_visite_collection ?? [],
        );
    }
}
