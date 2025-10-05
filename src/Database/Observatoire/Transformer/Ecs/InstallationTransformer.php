<?php

namespace App\Database\Observatoire\Transformer\Ecs;

use App\Database\Observatoire\Model\{XMLInstallationEcs, XMLRessource};
use App\Dto\Ecs\Installation\{InstallationDto, SolaireThermiqueDto};

final class InstallationTransformer
{
    public function supports(XMLInstallationEcs $element): bool
    {
        return $element->surface() > 0;
    }

    /**
     * @return array<InstallationDto>
     */
    public function __invoke(XMLRessource $ressource): array
    {
        return array_filter(array_map(function (XMLInstallationEcs $element) use ($ressource) {
            if (false === $this->supports($element)) {
                return null;
            }
            return new InstallationDto(
                id: (string) $element->id(),
                description: $element->description(),
                surface: $element->surface(),
                solaire_thermique: $element->usage_solaire() ? new SolaireThermiqueDto(
                    usage: $element->usage_solaire(),
                    annee_installation: $element->annee_installation_solaire($ressource),
                    fecs: $element->fecs_saisi(),
                ) : null,
            );
        }, $ressource->logement()->installation_ecs_collection));
    }
}
