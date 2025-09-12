<?php

namespace App\Database\Observatoire\Transformer\Enveloppe;

use App\Database\Observatoire\Model\XMLRessource;
use App\Dto\Enveloppe\DoubleFenetre\DoubleFenetreDto;
use App\Dto\Enveloppe\DoubleFenetre\MenuiserieDto;
use App\Dto\Enveloppe\DoubleFenetre\PositionDto;
use App\Dto\Enveloppe\DoubleFenetre\SurvitrageDto;
use App\Dto\Enveloppe\DoubleFenetre\VitrageDto;

final class DoubleFenetreTransformer
{
    /**
     * @return array<DoubleFenetreDto>
     */
    public function __invoke(XMLRessource $ressource): array
    {
        /** @var array<DoubleFenetreDto> */
        $collection = [];

        foreach ($ressource->logement()->enveloppe->baie_vitree_collection as $baie_vitree) {
            if (null === $baie_vitree_double_fenetre = $baie_vitree->baie_vitree_double_fenetre) {
                continue;
            }
            $collection[] = new DoubleFenetreDto(
                id: (string) $baie_vitree->id(),
                description: $baie_vitree->description(),
                type: $baie_vitree_double_fenetre->type_baie(),
                ug: $baie_vitree_double_fenetre->ug_saisi,
                uw: $baie_vitree_double_fenetre->uw_saisi,
                sw: $baie_vitree_double_fenetre->sw_saisi,
                position: new PositionDto(
                    inclinaison: $baie_vitree_double_fenetre->inclinaison(),
                    type_pose: $baie_vitree_double_fenetre->type_pose(),
                    presence_soubassement: $baie_vitree_double_fenetre->presence_soubassement()
                ),
                vitrage: new VitrageDto(
                    type: $baie_vitree_double_fenetre->type_vitrage(),
                    nature_lame: $baie_vitree_double_fenetre->nature_lame(),
                    epaisseur_lame: $baie_vitree_double_fenetre->epaisseur_lame,
                ),
                survitrage: $baie_vitree_double_fenetre->type_survitrage() ? new SurvitrageDto(
                    type: $baie_vitree_double_fenetre->type_survitrage(),
                    epaisseur_lame: null,
                ) : null,
                menuiserie: $baie_vitree_double_fenetre->type_baie()->is_paroi_vitree() ? null : new MenuiserieDto(
                    materiau: $baie_vitree_double_fenetre->materiau(),
                    largeur_dormant: null,
                    presence_joint: null,
                    presence_retour_isolation: null,
                    presence_rupteur_pont_thermique: $baie_vitree_double_fenetre->presence_rupteur_pont_thermique(),
                )
            );
        }
        return $collection;
    }
}
