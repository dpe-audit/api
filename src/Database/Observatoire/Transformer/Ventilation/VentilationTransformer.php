<?php

namespace App\Database\Observatoire\Transformer\Ventilation;

use App\Database\Observatoire\Model\XMLRessource;
use App\Dto\Ventilation\GenerateurDto;
use App\Dto\Ventilation\InstallationDto;
use App\Dto\Ventilation\VentilationDto;

final class VentilationTransformer
{
    public function __invoke(XMLRessource $ressource): VentilationDto
    {
        $generateurs = [];
        $installations = [];

        foreach ($ressource->logement()->ventilation_collection as $ventilation) {
            if ($ventilation->type_generateur()) {
                $generateurs[] = new GenerateurDto(
                    id: $ventilation->id(),
                    description: $ventilation->description(),
                    type: $ventilation->type_generateur(),
                    presence_echangeur_thermique: $ventilation->presence_echangeur_thermique(),
                    generateur_collectif: $ventilation->generateur_collectif(),
                    annee_installation: $ventilation->annee_installation($ressource),
                    type_vmc: $ventilation->type_vmc(),
                );
            }

            $installations[] = new InstallationDto(
                id: $ventilation->id(),
                description: $ventilation->description(),
                surface: $ventilation->surface_ventile,
                type: $ventilation->type_ventilation(),
                generateur_id: $ventilation->id(),
            );
        }

        return new VentilationDto(
            generateurs: $generateurs,
            installations: $installations,
        );
    }
}
