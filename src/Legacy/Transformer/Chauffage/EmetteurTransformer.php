<?php

namespace App\Legacy\Transformer\Chauffage;

use App\Domain\Chauffage\Emetteur\{TemperatureDistribution, TypeEmetteur};
use App\Dto\Chauffage\Emetteur\EmetteurDto;
use App\Legacy\Model\EmetteurChauffage;
use App\Legacy\Transformer\Context;

final class EmetteurTransformer
{
    private Context $context;
    private EmetteurChauffage $emetteur_chauffage;

    /**
     * Les énumérations relatives aux émissions directes sont traités au niveau du réseau de distribution
     */
    public function type(): ?TypeEmetteur
    {
        return match ($this->emetteur_chauffage->enum_type_emission_distribution_id) {
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 19, 20, 21, 22, 23, 40, 42, 46, 47, 48, 49, 50 => null,
            11, 12, 13, 14, 43 => TypeEmetteur::PLANCHER_CHAUFFANT,
            15, 16, 17, 18, 44 => TypeEmetteur::PLAFOND_CHAUFFANT,
            24, 25, 26, 27, 28, 29, 30, 31 => TypeEmetteur::RADIATEUR_MONOTUBE,
            32, 33, 34, 35, 36, 37, 38, 39 => TypeEmetteur::RADIATEUR_BITUBE,
            41, 45 => TypeEmetteur::RADIATEUR,
        };
    }

    public function temperature_distribution(): TemperatureDistribution
    {
        return match ($this->emetteur_chauffage->enum_temp_distribution_ch_id) {
            2 => TemperatureDistribution::BASSE,
            3 => TemperatureDistribution::MOYENNE,
            4 => TemperatureDistribution::HAUTE,
            default => match ($this->emetteur_chauffage->enum_type_emission_distribution_id) {
                12, 14, 16, 18, 25, 27, 29, 31, 33, 35, 37, 39, 47, 79 => TemperatureDistribution::MOYENNE,
                11, 13, 15, 17, 24, 26, 28, 30, 32, 34, 36, 38, 46, 48 => TemperatureDistribution::HAUTE,
                default => TemperatureDistribution::HAUTE,
            }
        };
    }

    public function presence_robinet_thermostatique(): bool
    {
        return match ($this->emetteur_chauffage->enum_type_emission_distribution_id) {
            29, 30, 31, 36, 37, 38, 39 => true,
            24, 25, 26, 27, 28, 32, 33, 34, 35 => false,
            default => false,
        };
    }

    public function annee_installation(): ?int
    {
        return match ($this->emetteur_chauffage->enum_periode_installation_emetteur_id) {
            1 => 1980,
            2 => 2000,
            3 => $this->context->ressource()->administratif()->annee_etablissement(),
            default => null,
        };
    }

    public function __invoke(EmetteurChauffage $emetteur, Context $context): ?EmetteurDto
    {
        $this->context = $context;
        $this->emetteur_chauffage = $emetteur;

        if (null === $type = $this->type()) {
            return null;
        }
        return new EmetteurDto(
            id: $emetteur->id(),
            description: $emetteur->description(),
            type: $type,
            temperature_distribution: $this->temperature_distribution(),
            presence_robinet_thermostatique: $this->presence_robinet_thermostatique(),
            annee_installation: $this->annee_installation(),
        );
    }
}
