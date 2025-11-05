<?php

namespace App\Legacy\Transformer\Chauffage;

use App\Domain\Chauffage\TypeChauffage;
use App\Domain\Chauffage\Systeme\Reseau\{IsolationReseau, TypeDistribution};
use App\Dto\Chauffage\Systeme\{ReseauDto, SystemeDto};
use App\Legacy\Model\{GenerateurChauffage, InstallationChauffage};
use App\Legacy\Transformer\Context;

final class SystemeTransformer
{
    private InstallationChauffage $installation_chauffage;
    private GenerateurChauffage $generateur_chauffage;

    public function installation_id(): string
    {
        foreach ($this->installation_chauffage->emetteur_chauffage_collection as $emetteur_chauffage) {
            if ($emetteur_chauffage->enum_lien_generateur_emetteur_id === 3) {
                return $this->installation_chauffage->installation_sdb_id();
            }
        }
        return $this->installation_chauffage->id();
    }

    public function type_chauffage(): TypeChauffage
    {
        foreach ($this->installation_chauffage->emetteur_chauffage_collection as $emetteur_chauffage) {
            if ($emetteur_chauffage->enum_lien_generateur_emetteur_id === $this->generateur_chauffage->enum_lien_generateur_emetteur_id) {
                return TypeChauffage::CHAUFFAGE_CENTRAL;
            }
        }
        return TypeChauffage::CHAUFFAGE_DIVISE;
    }

    public function type_distribution(): ?TypeDistribution
    {
        foreach ($this->installation_chauffage->emetteur_chauffage_collection as $emetteur_chauffage) {
            if ($emetteur_chauffage->enum_lien_generateur_emetteur_id === $this->generateur_chauffage->enum_lien_generateur_emetteur_id) {
                return match ($emetteur_chauffage->enum_type_emission_distribution_id) {
                    46, 47, 48, 49, 11, 12, 13, 14, 43, 15, 16, 17, 18, 44, 24,
                    25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39 => TypeDistribution::HYDRAULIQUE,
                    42, 45 => TypeDistribution::FLUIDE_FRIGORIGENE,
                    5 => TypeDistribution::AERAULIQUE,
                    default => null,
                };
            }
        }
        return null;
    }

    public function isolation_reseau(): ?IsolationReseau
    {
        foreach ($this->installation_chauffage->emetteur_chauffage_collection as $emetteur_chauffage) {
            if ($emetteur_chauffage->reseau_distribution_isole !== null) {
                return $emetteur_chauffage->reseau_distribution_isole ? IsolationReseau::ISOLE : IsolationReseau::NON_ISOLE;
            }
        }
        return null;
    }

    public function presence_circulateur_externe(): bool
    {
        return match ($this->installation_chauffage->enum_type_installation_id) {
            1 => false,
            2, 3, 4 => true,
        };
    }

    public function niveaux_desservis(): int
    {
        return $this->installation_chauffage->nombre_niveau_installation_ch;
    }

    /**
     * @return array<string>
     */
    public function emetteurs(): array
    {
        $collection = [];

        foreach ($this->installation_chauffage->emetteur_chauffage_collection as $emetteur_chauffage) {
            if ($emetteur_chauffage->enum_lien_generateur_emetteur_id === $this->generateur_chauffage->enum_lien_generateur_emetteur_id) {
                $collection[] = $emetteur_chauffage->id();
            }
        }
        return $collection;
    }

    public function __invoke(
        GenerateurChauffage $generateur_chauffage,
        InstallationChauffage $installation_chauffage,
        Context $context,
    ): SystemeDto {
        $this->installation_chauffage = $installation_chauffage;
        $this->generateur_chauffage = $generateur_chauffage;

        return new SystemeDto(
            id: $generateur_chauffage->id(),
            description: $generateur_chauffage->description(),
            generateur_id: $generateur_chauffage->id(),
            installation_id: $this->installation_id(),
            type: $this->type_chauffage(),
            reseau: new ReseauDto(
                type_distribution: $this->type_distribution(),
                presence_circulateur_externe: $this->presence_circulateur_externe(),
                niveaux_desservis: $this->niveaux_desservis(),
                isolation: $this->isolation_reseau(),
            ),
            emetteurs: $this->emetteurs()
        );
    }
}
