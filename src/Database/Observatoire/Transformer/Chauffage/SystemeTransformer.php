<?php

namespace App\Database\Observatoire\Transformer\Chauffage;

use App\Database\Observatoire\Model\{XMLEmetteurChauffage, XMLRessource};
use App\Dto\Chauffage\Systeme\{ReseauDto, SystemeDto};

final class SystemeTransformer
{
    public function __invoke(XMLRessource $ressource): array
    {
        $collection = [];

        foreach ($ressource->logement()->installation_chauffage_collection as $installation_chauffage) {
            foreach ($installation_chauffage->generateur_chauffage_collection as $generateur_chauffage) {
                $collection[] = new SystemeDto(
                    id: (string) $generateur_chauffage->id(),
                    description: $generateur_chauffage->description(),
                    generateur_id: (string) $generateur_chauffage->id(),
                    installation_id: (string) $installation_chauffage->id(),
                    type: $generateur_chauffage->type_chauffage($installation_chauffage),
                    reseau: new ReseauDto(
                        type_distribution: $generateur_chauffage->type_distribution($installation_chauffage),
                        presence_circulateur_externe: $installation_chauffage->presence_circulateur_externe(),
                        niveaux_desservis: $installation_chauffage->niveaux_desservis(),
                        isolation: $generateur_chauffage->isolation_reseau($installation_chauffage),
                    ),
                    emetteurs: array_unique(array_map(
                        fn(XMLEmetteurChauffage $element) => (string) $element->id(),
                        array_filter(
                            $installation_chauffage->emetteur_chauffage_collection,
                            fn(XMLEmetteurChauffage $element) => $element->type() &&  $element->enum_lien_generateur_emetteur_id === $generateur_chauffage->enum_lien_generateur_emetteur_id
                        )
                    ))
                );
            }
        }
        return $collection;
    }
}
