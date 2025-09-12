<?php

namespace App\Database\Observatoire\Transformer\Ecs;

use App\Database\Observatoire\Model\XMLRessource;
use App\Dto\Ecs\EcsDto;
use App\Dto\Ecs\Generateur\{GenerateurDto, PositionDto, SignaletiqueDto};
use App\Dto\Ecs\Installation\{InstallationDto, SolaireThermiqueDto};
use App\Dto\Ecs\Systeme\{ReseauDto, StockageDto, SystemeDto};

final class EcsTransformer
{
    private function fetch_generateurs(XMLRessource $ressource): array
    {
        $collection = [];
        $references_generateurs_ecs = [];
        foreach ($ressource->logement()->installation_ecs_collection as $installation_ecs) {
            foreach ($installation_ecs->generateur_ecs_collection as $generateur_ecs) {
                if (in_array($generateur_ecs->reference, $references_generateurs_ecs)) {
                    continue;
                }
                $references_generateurs_ecs[] = $generateur_ecs->reference;
                $collection[] = new GenerateurDto(
                    id: (string) $generateur_ecs->id(),
                    description: $generateur_ecs->description(),
                    type: $generateur_ecs->type(),
                    energie: $generateur_ecs->energie(),
                    annee_installation: $generateur_ecs->annee_installation($ressource),
                    position: new PositionDto(
                        generateur_collectif: $installation_ecs->installation_collective(),
                        generateur_multi_batiment: $generateur_ecs->generateur_multi_batiment(),
                        position_volume_chauffe: $generateur_ecs->position_volume_chauffe(),
                        position_chauffe_eau: $generateur_ecs->position_chauffe_eau(),
                        generateur_mixte_id: $generateur_ecs->generateur_mixte_id($ressource),
                        reseau_chaleur_id: $generateur_ecs->reseau_chaleur_id(),
                    ),
                    signaletique: new SignaletiqueDto(
                        volume_stockage: $generateur_ecs->volume_stockage_integre(),
                        pn: $generateur_ecs->pn_saisi(),
                        label: $generateur_ecs->label(),
                        cop: $generateur_ecs->cop_saisi(),
                        mode_combustion: $generateur_ecs->mode_combustion(),
                        presence_ventouse: $generateur_ecs->presence_ventouse(),
                        pveilleuse: $generateur_ecs->pveilleuse_saisi(),
                        qp0: $generateur_ecs->qp0_saisi(),
                        rpn: $generateur_ecs->rpn_saisi(),
                    )
                );
            }
        }
        return $collection;
    }

    private function fetch_installations(XMLRessource $ressource): array
    {
        $collection = [];
        foreach ($ressource->logement()->installation_ecs_collection as $installation_ecs) {
            $collection[] = new InstallationDto(
                id: (string) $installation_ecs->id(),
                description: $installation_ecs->description(),
                surface: $installation_ecs->surface(),
                solaire_thermique: $installation_ecs->usage_solaire() ? new SolaireThermiqueDto(
                    usage: $installation_ecs->usage_solaire(),
                    annee_installation: $installation_ecs->annee_installation_solaire($ressource),
                    fecs: $installation_ecs->fecs_saisi(),
                ) : null,
            );
        }
        return $collection;
    }

    private function fetch_systemes(XMLRessource $ressource): array
    {
        $collection = [];
        foreach ($ressource->logement()->installation_ecs_collection as $installation_ecs) {
            foreach ($installation_ecs->generateur_ecs_collection as $generateur_ecs) {
                $collection[] = new SystemeDto(
                    id: (string) $generateur_ecs->id(),
                    description: $generateur_ecs->description(),
                    generateur_id: (string) $generateur_ecs->id(),
                    installation_id: (string) $installation_ecs->id(),
                    reseau: new ReseauDto(
                        alimentation_contigue: $installation_ecs->alimentation_contigues(),
                        niveaux_desservis: $installation_ecs->niveaux_desservis(),
                        isolation: $installation_ecs->isolation_reseau(),
                        bouclage: $installation_ecs->bouclage_reseau(),
                    ),
                    stockage: new StockageDto(
                        volume: $generateur_ecs->volume_stockage_independant(),
                        position_volume_chauffe: $generateur_ecs->position_volume_chauffe_stockage(),
                    )
                );
            }
        }
        return $collection;
    }

    public function __invoke(XMLRessource $ressource): EcsDto
    {
        return new EcsDto(
            generateurs: $this->fetch_generateurs($ressource),
            installations: $this->fetch_installations($ressource),
            systemes: $this->fetch_systemes($ressource),
        );
    }
}
