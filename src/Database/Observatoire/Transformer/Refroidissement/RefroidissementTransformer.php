<?php

namespace App\Database\Observatoire\Transformer\Refroidissement;

use App\Database\Observatoire\Model\XMLRessource;
use App\Dto\Refroidissement\GenerateurDto;
use App\Dto\Refroidissement\InstallationDto;
use App\Dto\Refroidissement\RefroidissementDto;
use App\Dto\Refroidissement\SystemeDto;

final class RefroidissementTransformer
{
    public function __invoke(XMLRessource $ressource): RefroidissementDto
    {
        /** @var GenerateurDto[] */
        $generateurs = [];
        /** @var InstallationDto[] */
        $installations = [];
        /** @var SystemeDto[] */
        $systemes = [];

        foreach ($ressource->logement()->climatisation_collection as $climatiseur) {
            $generateurs[] = new GenerateurDto(
                id: $climatiseur->id(),
                reseau_froid_id: null,
                description: $climatiseur->description(),
                type: $climatiseur->type_generateur(),
                energie: $climatiseur->energie_generateur(),
                annee_installation: $climatiseur->annee_installation($ressource),
                seer: $climatiseur->seer(),
            );
            $installations[] = new InstallationDto(
                id: $climatiseur->id(),
                description: $climatiseur->description(),
                surface: $climatiseur->surface_clim,
            );
            $systemes[] = new SystemeDto(
                id: $climatiseur->id(),
                description: $climatiseur->description(),
                installation_id: $climatiseur->id(),
                generateur_id: $climatiseur->id(),
            );
        }

        return new RefroidissementDto(
            generateurs: $generateurs,
            installations: $installations,
            systemes: $systemes,
        );
    }
}
