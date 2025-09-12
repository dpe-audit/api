<?php

namespace App\Database\Observatoire\Transformer\Enveloppe;

use App\Database\Observatoire\Model\XMLRessource;
use App\Dto\Enveloppe\PontThermique\LiaisonDto;
use App\Dto\Enveloppe\PontThermique\PontThermiqueDto;

final class PontThermiqueTransformer
{
    /**
     * @return array<PontThermiqueDto>
     */
    public function __invoke(XMLRessource $ressource): array
    {
        $collection = [];

        foreach ($ressource->logement()->enveloppe->pont_thermique_collection as $pont_thermique) {
            if (0 == $pont_thermique->longueur()) {
                continue;
            }
            $collection[] = new PontThermiqueDto(
                id: (string) $pont_thermique->id(),
                description: $pont_thermique->description(),
                longueur: $pont_thermique->longueur(),
                kpt: $pont_thermique->kpt(),
                liaison: new LiaisonDto(
                    type: $pont_thermique->type_liaison(),
                    pont_thermique_partiel: $pont_thermique->pont_thermique_partiel(),
                    mur_id: (string) $pont_thermique->mur_id($ressource),
                    plancher_id: (string) $pont_thermique->plancher_id($ressource),
                    ouverture_id: (string) $pont_thermique->ouverture_id($ressource),
                )
            );
        }
        return $collection;
    }
}
