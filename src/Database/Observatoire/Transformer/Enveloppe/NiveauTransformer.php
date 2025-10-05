<?php

namespace App\Database\Observatoire\Transformer\Enveloppe;

use App\Database\Observatoire\Model\XMLRessource;
use App\Domain\Common\ValueObject\Id;
use App\Dto\Enveloppe\Niveau\NiveauDto;

final class NiveauTransformer
{
    /**
     * @return array<NiveauDto>
     */
    public function __invoke(XMLRessource $ressource): array
    {
        $collection = [];
        $collection[] = new NiveauDto(
            id: Id::create(),
            description: 'Niveau reconstitué',
            surface: $ressource->logement()->caracteristique_generale->surface_habitable(),
            inertie_paroi_verticale: $ressource->logement()->enveloppe->inertie_paroi_verticale(),
            inertie_plancher_haut: $ressource->logement()->enveloppe->inertie_plancher_haut(),
            inertie_plancher_bas: $ressource->logement()->enveloppe->inertie_plancher_bas(),
        );
        return $collection;
    }
}
