<?php

namespace App\Database\Observatoire\Transformer\Ecs;

use App\Database\Observatoire\Model\{XMLGenerateurEcs, XMLRessource};
use App\Dto\Ecs\Generateur\{GenerateurDto, PositionDto, SignaletiqueDto};

final class GenerateurTransformer
{
    private array $registre = [];

    public function supports(XMLGenerateurEcs $element): bool
    {
        return false === in_array($element->reference, $this->registre);
    }

    /**
     * @return array<GenerateurDto>
     */
    public function __invoke(XMLRessource $ressource): array
    {
        $this->registre = [];
        $collection = [];

        foreach ($ressource->logement()->installation_ecs_collection as $installation_ecs) {
            foreach ($installation_ecs->generateur_ecs_collection as $generateur_ecs) {
                if (false === $this->supports($generateur_ecs)) {
                    continue;
                }
                $this->registre[] = $generateur_ecs->reference;
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
}
