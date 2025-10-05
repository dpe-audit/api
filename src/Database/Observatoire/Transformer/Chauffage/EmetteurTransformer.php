<?php

namespace App\Database\Observatoire\Transformer\Chauffage;

use App\Database\Observatoire\Model\{XMLEmetteurChauffage, XMLRessource};
use App\Dto\Chauffage\Emetteur\EmetteurDto;

final class EmetteurTransformer
{
    private array $registre = [];

    public function supports(XMLEmetteurChauffage $element): bool
    {
        return null !== $element->type();
    }

    /**
     * @return array<EmetteurDto>
     */
    public function __invoke(XMLRessource $ressource): array
    {
        $this->registre = [];
        $collection = [];

        foreach ($ressource->logement()->installation_chauffage_collection as $installation_chauffage) {
            foreach ($installation_chauffage->emetteur_chauffage_collection as $emetteur_chauffage) {
                if (false === $this->supports($emetteur_chauffage)) {
                    continue;
                }
                $this->registre[] = $emetteur_chauffage->reference;
                $collection[] = new EmetteurDto(
                    id: (string) $emetteur_chauffage->id(),
                    description: $emetteur_chauffage->description(),
                    type: $emetteur_chauffage->type(),
                    temperature_distribution: $emetteur_chauffage->temperature_distribution(),
                    presence_robinet_thermostatique: $emetteur_chauffage->presence_robinet_thermostatique(),
                    annee_installation: $emetteur_chauffage->annee_installation($ressource),
                );
            }
        }
        return $collection;
    }
}
