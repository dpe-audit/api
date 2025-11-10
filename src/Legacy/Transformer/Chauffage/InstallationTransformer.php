<?php

namespace App\Legacy\Transformer\Chauffage;

use App\Domain\Chauffage\Installation\Solaire\Usage;
use App\Dto\Chauffage\Installation\{InstallationDto, RegulationDto, SolaireThermiqueDto};
use App\Legacy\Model\InstallationChauffage;
use App\Legacy\Transformer\Context;

final class InstallationTransformer
{
    private InstallationChauffage $installation_chauffage;

    /**
     * En présence d'une installation avec appoint électrique dans la salle de bain, la surface couverte
     * par l'installation est déduite des surfaces couvertes par ces émetteurs.
     */
    public function surface(): float
    {
        $value = 0;
        foreach ($this->installation_chauffage->emetteur_chauffage_collection as $emetteur_chauffage) {
            if ($emetteur_chauffage->enum_lien_generateur_emetteur_id === 3) {
                return $value += $emetteur_chauffage->surface_chauffee;
            }
        }
        $value = max($value, 0.1 * $this->installation_chauffage->surface_chauffee);
        return $this->installation_chauffage->surface_chauffee - $value;
    }

    public function usage_solaire(): ?Usage
    {
        return \in_array($this->installation_chauffage->enum_cfg_installation_ch_id, [2, 7])
            ? Usage::CHAUFFAGE
            : null;
    }

    /**
     * En l'absence d'émetteurs, on considère la présence d'un comptage individuel (émission directe)
     */
    public function comptage_individuel(): ?bool
    {
        foreach ($this->installation_chauffage->emetteur_chauffage_collection as $emetteur_chauffage) {
            if ($emetteur_chauffage->enum_lien_generateur_emetteur_id === 3) {
                continue;
            }
            $value = match ($emetteur_chauffage->tv_intermittence_id) {
                151, 152, 153, 154, 155, 156, 157, 158, 159, 160, 161, 162, 163, 164, 165, 166, 167, 168, 169 => false,
                170, 171, 172, 173, 174, 175, 176, 177, 178, 179, 180, 181, 182, 183, 184, 185, 186, 187, 188 => true,
                default => null,
            };
            if ($value !== null) {
                return $value;
            }
        }
        return true;
    }

    public function presence_regulation_centrale(): bool
    {
        foreach ($this->installation_chauffage->emetteur_chauffage_collection as $emetteur_chauffage) {
            if ($emetteur_chauffage->enum_lien_generateur_emetteur_id === 3) {
                continue;
            }
            if (in_array($emetteur_chauffage->enum_equipement_intermittence_id, [2, 3])) {
                return true;
            }
        }
        return false;
    }

    public function regulation_centrale_minimum_temperature(): bool
    {
        foreach ($this->installation_chauffage->emetteur_chauffage_collection as $emetteur_chauffage) {
            if ($emetteur_chauffage->enum_lien_generateur_emetteur_id === 3) {
                continue;
            }
            if ($emetteur_chauffage->enum_equipement_intermittence_id === 3) {
                return true;
            }
        }
        return false;
    }

    public function regulation_centrale_detection_presence(): bool
    {
        foreach ($this->installation_chauffage->emetteur_chauffage_collection as $emetteur_chauffage) {
            if ($emetteur_chauffage->enum_lien_generateur_emetteur_id === 3) {
                continue;
            }
            if ($emetteur_chauffage->enum_equipement_intermittence_id === 7) {
                return true;
            }
        }
        return false;
    }

    public function presence_regulation_terminale(): bool
    {
        foreach ($this->installation_chauffage->emetteur_chauffage_collection as $emetteur_chauffage) {
            if ($emetteur_chauffage->enum_lien_generateur_emetteur_id === 3) {
                continue;
            }
            if ($emetteur_chauffage->enum_type_regulation_id === 2) {
                return true;
            }
        }
        return false;
    }

    public function regulation_terminale_minimum_temperature(): bool
    {
        foreach ($this->installation_chauffage->emetteur_chauffage_collection as $emetteur_chauffage) {
            if ($emetteur_chauffage->enum_lien_generateur_emetteur_id === 3) {
                continue;
            }
            if (in_array($emetteur_chauffage->enum_equipement_intermittence_id, [4, 5])) {
                return true;
            }
        }
        return false;
    }

    public function regulation_terminale_detection_presence(): bool
    {
        foreach ($this->installation_chauffage->emetteur_chauffage_collection as $emetteur_chauffage) {
            if ($emetteur_chauffage->enum_lien_generateur_emetteur_id === 3) {
                continue;
            }
            if ($emetteur_chauffage->enum_equipement_intermittence_id === 5) {
                return true;
            }
        }
        return false;
    }

    public function fch(): ?float
    {
        return $this->installation_chauffage->fch_saisi > 0 ? $this->installation_chauffage->fch_saisi : null;
    }

    public function __invoke(InstallationChauffage $installation_chauffage, Context $context): InstallationDto
    {
        $this->installation_chauffage = $installation_chauffage;

        return new InstallationDto(
            id: $installation_chauffage->id(),
            description: $installation_chauffage->description(),
            surface: $this->surface(),
            comptage_individuel: $this->comptage_individuel(),
            solaire_thermique: $this->usage_solaire() ? new SolaireThermiqueDto(
                usage: $this->usage_solaire(),
                annee_installation: null,
                fch: $this->fch(),
            ) : null,
            regulation_centrale: new RegulationDto(
                presence_regulation: $this->presence_regulation_centrale(),
                minimum_temperature: $this->regulation_centrale_minimum_temperature(),
                detection_presence: $this->regulation_centrale_detection_presence(),
            ),
            regulation_terminale: new RegulationDto(
                presence_regulation: $this->presence_regulation_terminale(),
                minimum_temperature: $this->regulation_terminale_minimum_temperature(),
                detection_presence: $this->regulation_terminale_detection_presence(),
            ),
        );
    }
}
