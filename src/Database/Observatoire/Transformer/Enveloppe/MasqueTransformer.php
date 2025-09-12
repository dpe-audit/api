<?php

namespace App\Database\Observatoire\Transformer\Enveloppe;

use App\Database\Observatoire\Model\XMLRessource;
use App\Domain\Enveloppe\Masque\TypeMasque;
use App\Dto\Enveloppe\Masque\MasqueDto;

final class MasqueTransformer
{
    /**
     * @return array<MasqueDto>
     */
    public function __invoke(XMLRessource $ressource): array
    {
        /** @var array<MasqueDto> */
        $collection = [];

        foreach ($ressource->logement()->enveloppe->baie_vitree_collection as $baie_vitree) {
            // Masques proches
            if ($baie_vitree->masque_proche_id()) {
                $collection[] = new MasqueDto(
                    id: (string) $baie_vitree->masque_proche_id(),
                    description: 'Masque proche reconstitué',
                    type: TypeMasque::PROCHE,
                    configuration: $baie_vitree->configuration_masque_proche(),
                    orientation: $baie_vitree->orientation_masque_proche(),
                    profondeur: $baie_vitree->profondeur_masque_proche(),
                    hauteur: null,
                    secteur: null,
                );
            }
            // Masques lointains homogènes
            if ($baie_vitree->masque_lointain_homogene_id()) {
                $collection[] = new MasqueDto(
                    id: (string) $baie_vitree->masque_lointain_homogene_id(),
                    description: 'Masque lointain homogène reconstitué',
                    type: TypeMasque::LOINTAIN,
                    configuration: $baie_vitree->configuration_masque_lointain(),
                    orientation: $baie_vitree->orientation_masque_lointain(),
                    profondeur: null,
                    hauteur: $baie_vitree->hauteur_masque_lointain(),
                    secteur: null,
                );
            }
            // Masques lointains non homogènes
            foreach ($baie_vitree->masque_lointain_non_homogene_collection as $index => $masque_lointain_non_homogene) {
                $collection[] = new MasqueDto(
                    id: (string) $baie_vitree->masque_lointain_non_homogene_id($index),
                    description: 'Masque lointain non homogène reconstitué',
                    type: TypeMasque::LOINTAIN,
                    configuration: $masque_lointain_non_homogene->configuration(),
                    orientation: $masque_lointain_non_homogene->orientation(),
                    profondeur: null,
                    hauteur: $masque_lointain_non_homogene->hauteur(),
                    secteur: $masque_lointain_non_homogene->secteur(),
                );
            }
        }
        return $collection;
    }
}
