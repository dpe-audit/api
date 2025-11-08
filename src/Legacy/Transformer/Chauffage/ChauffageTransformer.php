<?php

namespace App\Legacy\Transformer\Chauffage;

use App\Dto\Chauffage\ChauffageDto;
use App\Legacy\Transformer\Context;

final class ChauffageTransformer
{
    public function __construct(
        private readonly EmetteurTransformer $emetteur_transformer,
        private readonly GenerateurTransformer $generateur_transformer,
        private readonly InstallationTransformer $installation_transformer,
        private readonly InstallationAppointElectriqueTransformer $installation_appoint_electrique_transformer,
        private readonly SystemeTransformer $systeme_transformer,
    ) {}

    public function __invoke(Context $context): ChauffageDto
    {
        $emetteurs = [];
        $generateurs = [];
        $installations = [];
        $systemes = [];

        foreach ($context->logement()->installation_chauffage_collection as $installation_chauffage) {
            $installations[] = $this->installation_transformer->__invoke($installation_chauffage, $context);
            $installations[] = $this->installation_appoint_electrique_transformer->__invoke($installation_chauffage, $context);

            foreach ($installation_chauffage->emetteur_chauffage_collection as $emetteur_chauffage) {
                $emetteurs[] = $this->emetteur_transformer->__invoke($emetteur_chauffage, $context);
            }
            foreach ($installation_chauffage->generateur_chauffage_collection as $generateur_chauffage) {
                $generateur = $this->generateur_transformer->__invoke($generateur_chauffage, $installation_chauffage, $context);

                if ($generateur) {
                    $generateurs[] = $generateur;
                    $systemes[] = $this->systeme_transformer->__invoke($generateur_chauffage, $installation_chauffage, $context);
                }
            }
        }

        return new ChauffageDto(
            emetteurs: array_filter($emetteurs),
            generateurs: array_filter($generateurs),
            installations: array_filter($installations),
            systemes: array_filter($systemes),
        );
    }
}
