<?php

namespace App\Database\Observatoire\Model;

use App\Domain\Chauffage\Emetteur\{TemperatureDistribution, TypeEmetteur, TypeEmission};
use App\Domain\Chauffage\Systeme\Reseau\TypeDistribution;
use App\Domain\Common\ValueObject\Id;

final class XMLEmetteurChauffage extends XMLUniqueElement
{
    public function __construct(
        public readonly string $reference,
        public readonly ?string $description,
        public readonly float $surface_chauffee,
        public readonly int $tv_rendement_emission_id,
        public readonly int $tv_rendement_distribution_ch_id,
        public readonly int $tv_rendement_regulation_id,
        public readonly int $enum_type_emission_distribution_id,
        public readonly int $tv_intermittence_id,
        public readonly ?bool $reseau_distribution_isole,
        public readonly int $enum_equipement_intermittence_id,
        public readonly int $enum_type_regulation_id,
        public readonly ?int $enum_periode_installation_emetteur_id,
        public readonly int $enum_type_chauffage_id,
        public readonly int $enum_temp_distribution_ch_id,
        public readonly int $enum_lien_generateur_emetteur_id,

        public readonly float $i0,
        public readonly float $rendement_emission,
        public readonly float $rendement_distribution,
        public readonly float $rendement_regulation
    ) {}

    /**
     * XSD logement/installation_chauffage_collection/installation_chauffage/emetteur_chauffage_collection/emetteur_chauffage
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            reference: (string) $xml->donnee_entree->reference,
            description: (string) $xml->donnee_entree->description ?: null,
            surface_chauffee: (float) $xml->donnee_entree->surface_chauffee,
            tv_rendement_emission_id: (int) $xml->donnee_entree->tv_rendement_emission_id,
            tv_rendement_distribution_ch_id: (int) $xml->donnee_entree->tv_rendement_distribution_ch_id,
            tv_rendement_regulation_id: (int) $xml->donnee_entree->tv_rendement_regulation_id,
            enum_type_emission_distribution_id: (int) $xml->donnee_entree->enum_type_emission_distribution_id,
            tv_intermittence_id: (int) $xml->donnee_entree->tv_intermittence_id,
            reseau_distribution_isole: (bool)(int) $xml->donnee_entree->reseau_distribution_isole ?: null,
            enum_equipement_intermittence_id: (int) $xml->donnee_entree->enum_equipement_intermittence_id,
            enum_type_regulation_id: (int) $xml->donnee_entree->enum_type_regulation_id,
            enum_periode_installation_emetteur_id: (int) $xml->donnee_entree->enum_periode_installation_emetteur_id ?: null,
            enum_type_chauffage_id: (int) $xml->donnee_entree->enum_type_chauffage_id,
            enum_temp_distribution_ch_id: (int) $xml->donnee_entree->enum_temp_distribution_ch_id,
            enum_lien_generateur_emetteur_id: (int) $xml->donnee_entree->enum_lien_generateur_emetteur_id,
            i0: (float) $xml->donnee_intermediaire->i0,
            rendement_emission: (float) $xml->donnee_intermediaire->rendement_emission,
            rendement_distribution: (float) $xml->donnee_intermediaire->rendement_distribution,
            rendement_regulation: (float) $xml->donnee_intermediaire->rendement_regulation
        );
    }

    /**
     * XSD logement/installation_chauffage_collection/installation_chauffage/emetteur_chauffage_collection
     * 
     * @return array<self>
     */
    public static function from_collection(\SimpleXMLElement $xml): array
    {
        $collection = [];

        foreach ($xml->emetteur_chauffage as $item) {
            $collection[] = self::from($item);
        }
        return $collection;
    }

    /**
     * @inheritDoc
     */
    public function identifiers(): array
    {
        return [$this->reference];
    }

    public function description(): string
    {
        return $this->description ?? '-';
    }

    public function appoint_electrique_sdb(): bool
    {
        return $this->enum_lien_generateur_emetteur_id === 3;
    }

    public function surface_appoint_electrique_sdp(): float
    {
        return $this->appoint_electrique_sdb() ? $this->surface_chauffee : 0;
    }

    /**
     * Les énumérations relatives aux émissions directes sont traités au niveau du réseau de distribution
     */
    public function type(): ?TypeEmetteur
    {
        return match ($this->enum_type_emission_distribution_id) {
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 19, 20, 21, 22, 23, 40, 42, 46, 47, 48, 49, 50 => null,
            11, 12, 13, 14, 43 => TypeEmetteur::PLANCHER_CHAUFFANT,
            15, 16, 17, 18, 44 => TypeEmetteur::PLAFOND_CHAUFFANT,
            24, 25, 26, 27, 28, 29, 30, 31 => TypeEmetteur::RADIATEUR_MONOTUBE,
            32, 33, 34, 35, 36, 37, 38, 39 => TypeEmetteur::RADIATEUR_BITUBE,
            41, 45 => TypeEmetteur::RADIATEUR,
        };
    }

    public function type_emission(): ?TypeEmission
    {
        return match ($this->type()) {
            TypeEmetteur::PLANCHER_CHAUFFANT => TypeEmission::PLANCHER_CHAUFFANT,
            TypeEmetteur::PLAFOND_CHAUFFANT => TypeEmission::PLAFOND_CHAUFFANT,
            TypeEmetteur::RADIATEUR_MONOTUBE,
            TypeEmetteur::RADIATEUR_BITUBE,
            TypeEmetteur::RADIATEUR => TypeEmission::RADIATEUR,
        };
    }

    public function type_distribution(): ?TypeDistribution
    {
        return match ($this->enum_type_emission_distribution_id) {
            46, 47, 48, 49, 11, 12, 13, 14, 43, 15, 16, 17, 18, 44, 24,
            25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39 => TypeDistribution::HYDRAULIQUE,
            42, 45 => TypeDistribution::FLUIDE_FRIGORIGENE,
            5 => TypeDistribution::AERAULIQUE,
            default => null,
        };
    }

    public function temperature_distribution(): TemperatureDistribution
    {
        return match ($this->enum_temp_distribution_ch_id) {
            2 => TemperatureDistribution::BASSE,
            3 => TemperatureDistribution::MOYENNE,
            4 => TemperatureDistribution::HAUTE,
            default => match ($this->enum_type_emission_distribution_id) {
                12, 14, 16, 18, 25, 27, 29, 31, 33, 35, 37, 39, 47, 79 => TemperatureDistribution::MOYENNE,
                11, 13, 15, 17, 24, 26, 28, 30, 32, 34, 36, 38, 46, 48 => TemperatureDistribution::HAUTE,
                default => TemperatureDistribution::HAUTE,
            }
        };
    }

    public function chauffage_central(): bool
    {
        return $this->enum_type_chauffage_id === 2;
    }

    public function chauffage_divise(): bool
    {
        return $this->enum_type_chauffage_id === 1;
    }

    public function presence_robinet_thermostatique(): bool
    {
        return match ($this->enum_type_emission_distribution_id) {
            29, 30, 31, 36, 37, 38, 39 => true,
            24, 25, 26, 27, 28, 32, 33, 34, 35 => false,
            default => false,
        };
    }

    public function annee_installation(XMLRessource $ressource): ?int
    {
        return match ($this->enum_periode_installation_emetteur_id) {
            1 => 1980,
            2 => 2000,
            3 => $ressource->administratif->annee_etablissement(),
            default => null,
        };
    }

    public function presence_regulation_centrale(): bool
    {
        return match ($this->enum_equipement_intermittence_id) {
            2, 3 => true,
            default => false,
        };
    }

    public function regulation_centrale_minimum_temperature(): bool
    {
        return match ($this->enum_equipement_intermittence_id) {
            3 => true,
            default => false,
        };
    }

    public function regulation_centrale_detection_presence(): bool
    {
        return match ($this->enum_equipement_intermittence_id) {
            7 => true,
            default => false,
        };
    }

    public function presence_regulation_terminale(): bool
    {
        return match ($this->enum_type_regulation_id) {
            1 => false,
            2 => true,
        };
    }

    public function regulation_terminale_minimum_temperature(): bool
    {
        return match ($this->enum_equipement_intermittence_id) {
            4, 5 => true,
            default => false,
        };
    }

    public function regulation_terminale_detection_presence(): bool
    {
        return match ($this->enum_equipement_intermittence_id) {
            5 => true,
            default => false,
        };
    }

    public function comptage_individuel(): ?bool
    {
        return match ($this->tv_intermittence_id) {
            151, 152, 153, 154, 155, 156, 157, 158, 159, 160, 161, 162, 163, 164, 165, 166, 167, 168, 169 => false,
            170, 171, 172, 173, 174, 175, 176, 177, 178, 179, 180, 181, 182, 183, 184, 185, 186, 187, 188 => true,
            default => null,
        };
    }
}
