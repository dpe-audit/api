<?php

namespace App\Database\Observatoire\Transformer\Enveloppe;

use App\Database\Observatoire\Model\XMLRessource;
use App\Dto\Enveloppe\Baie\BaieDto;
use App\Dto\Enveloppe\Baie\MenuiserieDto;
use App\Dto\Enveloppe\Baie\PositionDto;
use App\Dto\Enveloppe\Baie\SurvitrageDto;
use App\Dto\Enveloppe\Baie\VitrageDto;

final class BaieTransformer
{
    /**
     * @return array<BaieDto>
     */
    public function __invoke(XMLRessource $ressource): array
    {
        /** @var array<BaieDto> */
        $collection = [];

        foreach ($ressource->logement()->enveloppe->baie_vitree_collection as $baie_vitree) {
            $masques = [];

            if ($baie_vitree->masque_proche_id()) {
                $masques[] = (string) $baie_vitree->masque_proche_id();
            }
            if ($baie_vitree->masque_lointain_homogene_id()) {
                $masques[] = (string) $baie_vitree->masque_lointain_homogene_id();
            }
            foreach ($baie_vitree->masque_lointain_non_homogene_collection as $index => $value) {
                $masques[] = (string) $baie_vitree->masque_lointain_non_homogene_id($index);
            }

            $collection[] = new BaieDto(
                id: (string) $baie_vitree->id(),
                description: $baie_vitree->description(),
                type: $baie_vitree->type_baie(),
                presence_protection_solaire: $baie_vitree->presence_protection_solaire(),
                type_fermeture: $baie_vitree->type_fermeture(),
                annee_installation: null,
                ug: $baie_vitree->ug_saisi,
                uw: $baie_vitree->uw_saisi,
                ujn: $baie_vitree->ujn_saisi,
                sw: $baie_vitree->sw_saisi,
                position: new PositionDto(
                    surface: $baie_vitree->surface(),
                    mitoyennete: $baie_vitree->mitoyennete(),
                    inclinaison: $baie_vitree->inclinaison(),
                    orientation: $baie_vitree->orientation(),
                    type_pose: $baie_vitree->type_pose(),
                    presence_soubassement: $baie_vitree->presence_soubassement(),
                    paroi_id: $baie_vitree->paroi_id($ressource)?->toBinary(),
                    local_non_chauffe_id: $baie_vitree->lnc_id($ressource)?->toBinary(),
                    double_fenetre_id: $baie_vitree->double_fenetre_id()?->toBinary(),
                ),
                vitrage: new VitrageDto(
                    type: $baie_vitree->type_vitrage(),
                    nature_lame: $baie_vitree->nature_lame(),
                    epaisseur_lame: $baie_vitree->epaisseur_lame,
                ),
                survitrage: $baie_vitree->type_survitrage() ? new SurvitrageDto(
                    type: $baie_vitree->type_survitrage(),
                    epaisseur_lame: null,
                ) : null,
                menuiserie: $baie_vitree->type_baie()->is_paroi_vitree() ? null : new MenuiserieDto(
                    materiau: $baie_vitree->materiau(),
                    largeur_dormant: null,
                    presence_joint: $baie_vitree->presence_joint,
                    presence_retour_isolation: $baie_vitree->presence_retour_isolation,
                    presence_rupteur_pont_thermique: $baie_vitree->presence_rupteur_pont_thermique(),
                ),
                masques: $masques,
            );
        }
        return $collection;
    }
}
