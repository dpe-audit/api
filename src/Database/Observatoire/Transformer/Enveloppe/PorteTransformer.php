<?php

namespace App\Database\Observatoire\Transformer\Enveloppe;

use App\Database\Observatoire\Model\{XMLPorte, XMLRessource};
use App\Dto\Enveloppe\Porte\MenuiserieDto;
use App\Dto\Enveloppe\Porte\{PorteDto, PositionDto, VitrageDto};

final class PorteTransformer
{
    public function supports(XMLPorte $element): bool
    {
        return $element->nb_porte > 0 && $element->surface() > 0;
    }

    /**
     * @return array<PorteDto>
     */
    public function __invoke(XMLRessource $ressource): array
    {
        /** @var array<PorteDto> */
        $collection = [];

        foreach ($ressource->logement()->enveloppe->porte_collection as $porte) {
            if (false === $this->supports($porte)) {
                continue;
            }
            $collection[] = new PorteDto(
                id: (string) $porte->id(),
                description: $porte->description(),
                isolation: $porte->isolation(),
                materiau: $porte->materiau(),
                annee_installation: null,
                u: $porte->uporte_saisi,
                position: new PositionDto(
                    type_pose: $porte->type_pose(),
                    surface: $porte->surface(),
                    mitoyennete: $porte->mitoyennete(),
                    orientation: null,
                    presence_sas: $porte->presence_sas(),
                    paroi_id: $porte->paroi_id($ressource)?->__toString(),
                    local_non_chauffe_id: $porte->lnc_id($ressource)?->__toString(),
                ),
                menuiserie: new MenuiserieDto(
                    largeur_dormant: $porte->largeur_dormant(),
                    presence_joint: $porte->presence_joint,
                    presence_retour_isolation: $porte->presence_retour_isolation,
                ),
                vitrage: new VitrageDto(
                    surface: $porte->surface_vitrage(),
                    type: $porte->type_vitrage(),
                )
            );
        }
        return $collection;
    }
}
