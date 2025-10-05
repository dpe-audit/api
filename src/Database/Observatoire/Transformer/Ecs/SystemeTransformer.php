<?php

namespace App\Database\Observatoire\Transformer\Ecs;

use App\Database\Observatoire\Model\{XMLInstallationEcs, XMLRessource};
use App\Dto\Ecs\Systeme\{ReseauDto, StockageDto, SystemeDto};

final class SystemeTransformer
{
    public function supports(XMLInstallationEcs $element): bool
    {
        return $element->surface() > 0;
    }

    /**
     * @return array<SystemeDto>
     */
    public function __invoke(XMLRessource $ressource): array
    {
        $collection = [];
        foreach ($ressource->logement()->installation_ecs_collection as $installation_ecs) {
            if (false === $this->supports($installation_ecs)) {
                continue;
            }
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
}
