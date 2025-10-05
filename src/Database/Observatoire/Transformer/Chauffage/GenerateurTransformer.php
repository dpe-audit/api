<?php

namespace App\Database\Observatoire\Transformer\Chauffage;

use App\Database\Observatoire\Model\{XMLGenerateurChauffage, XMLRessource};
use App\Dto\Chauffage\Generateur\GenerateurDto;
use App\Dto\Chauffage\Generateur\PositionDto;
use App\Dto\Chauffage\Generateur\SignaletiqueDto;

final class GenerateurTransformer
{
    private array $registre = [];

    public function supports(XMLGenerateurChauffage $element): bool
    {
        return null !== $element->type() && false === in_array($element->reference, $this->registre);
    }

    public function __invoke(XMLRessource $ressource): array
    {
        $collection = [];

        foreach ($ressource->logement()->installation_chauffage_collection as $installation_chauffage) {
            foreach ($installation_chauffage->generateur_chauffage_collection as $generateur_chauffage) {
                if (false === $this->supports($generateur_chauffage)) {
                    continue;
                }
                $this->registre[] = $generateur_chauffage->reference;
                $partie_chaudiere = $installation_chauffage->find_generateur_hybride_partie_chaudiere($generateur_chauffage);
                $collection[] = new GenerateurDto(
                    id: (string) $generateur_chauffage->id(),
                    description: $generateur_chauffage->description(),
                    type: $generateur_chauffage->type(),
                    energie: $generateur_chauffage->energie(),
                    bienergie: $partie_chaudiere?->energie(),
                    annee_installation: $generateur_chauffage->annee_installation($ressource),
                    position: new PositionDto(
                        generateur_collectif: $generateur_chauffage->generateur_collectif($installation_chauffage),
                        generateur_multi_batiment: $generateur_chauffage->generateur_multi_batiment(),
                        position_volume_chauffe: $generateur_chauffage->position_volume_chauffe,
                        cascade: $generateur_chauffage->priorite_generateur_cascade > 0 ? 1 : null,
                        priorite_cascade: $generateur_chauffage->priorite_generateur_cascade,
                        position_chaudiere: $generateur_chauffage->position_chaudiere(),
                        generateur_mixte_id: $generateur_chauffage->generateur_mixte_id($ressource),
                        reseau_chaleur_id: $generateur_chauffage->reseau_chaleur_id(),
                    ),
                    signaletique: new SignaletiqueDto(
                        pn: $generateur_chauffage->pn_saisi(),
                        label: $generateur_chauffage->label(),
                        scop: $generateur_chauffage->scop_saisi(),
                        mode_combustion: $generateur_chauffage->mode_combustion() ?? $partie_chaudiere?->mode_combustion(),
                        presence_ventouse: $generateur_chauffage->presence_ventouse ?? $partie_chaudiere?->presence_ventouse,
                        presence_regulation_combustion: $generateur_chauffage->presence_regulation_combustion ?? $partie_chaudiere?->presence_regulation_combustion,
                        pveilleuse: $generateur_chauffage->pveilleuse_saisi() ?? $partie_chaudiere?->pveilleuse_saisi(),
                        qp0: $generateur_chauffage->qp0_saisi() ?? $partie_chaudiere?->qp0_saisi(),
                        rpn: $generateur_chauffage->rpn_saisi() ?? $partie_chaudiere?->rpn_saisi(),
                        rpint: $generateur_chauffage->rpint_saisi() ?? $partie_chaudiere?->rpint_saisi(),
                        tfonc30: $generateur_chauffage->tfonc30_saisi() ?? $partie_chaudiere?->tfonc30_saisi(),
                        tfonc100: $generateur_chauffage->tfonc100_saisi() ?? $partie_chaudiere?->tfonc100_saisi(),
                    ),
                );
            }
        }
        return $collection;
    }
}
