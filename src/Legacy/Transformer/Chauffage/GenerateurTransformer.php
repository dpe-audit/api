<?php

namespace App\Legacy\Transformer\Chauffage;

use App\Domain\Chauffage\Generateur\EnergieGenerateur;
use App\Domain\Chauffage\Generateur\Position\PositionChaudiere;
use App\Domain\Chauffage\Generateur\Signaletique\{LabelGenerateur, ModeCombustion};
use App\Domain\Chauffage\Generateur\TypeGenerateur;
use App\Dto\Chauffage\Generateur\{GenerateurDto, PositionDto, SignaletiqueDto};
use App\Legacy\Model\{GenerateurChauffage, InstallationChauffage};
use App\Legacy\Transformer\Context;

final class GenerateurTransformer
{
    private Context $context;
    private InstallationChauffage $installation_chauffage;
    private GenerateurChauffage $generateur_chauffage;
    private ?GenerateurChauffage $generateur_hybride = null;

    public function generateur_mixte_id(): ?string
    {
        foreach ($this->context->logement()->installation_ecs_collection as $installation_ecs) {
            foreach ($installation_ecs->generateur_ecs_collection as $generateur_ecs) {
                if (null === $generateur_ecs->reference_generateur_mixte) {
                    continue;
                }
                if ($this->generateur_chauffage->match($generateur_ecs->reference_generateur_mixte)) {
                    return $generateur_ecs->id();
                }
            }
        }
        if (null === $reference = $this->generateur_chauffage->reference_generateur_mixte) {
            return null;
        }
        return $this->context->logement()->find_generateur_ecs($reference)?->id()
            ?? $this->context->logement()->match_generateur_ecs($this->generateur_chauffage)?->id();
    }

    public function reseau_chaleur_id(): ?string
    {
        return $this->generateur_chauffage->identifiant_reseau_chaleur;
    }

    public function type(): ?TypeGenerateur
    {
        return match ($this->generateur_chauffage->enum_type_generateur_ch_id) {
            55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80,
            81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 106, 109, 110, 111, 119, 120, 121, 122, 123, 124, 125,
            126, 127, 128, 129, 130, 131, 132, 133, 134, 135, 136, 137, 138, 139, 171 => TypeGenerateur::CHAUDIERE,
            105 => TypeGenerateur::CONVECTEUR_BI_JONCTION,
            98, 99, 100, 101 => TypeGenerateur::CONVECTEUR_ELECTRIQUE,
            20, 24, 28, 32, 36, 40 => TypeGenerateur::CUISINIERE,
            21, 25, 29, 33, 37, 41 => TypeGenerateur::FOYER_FERME,
            50, 51, 52 => TypeGenerateur::GENERATEUR_AIR_CHAUD,
            23, 27, 31, 35, 39, 43 => TypeGenerateur::INSERT,
            1, 2, 3 => TypeGenerateur::PAC_AIR_AIR,
            4, 5, 6, 7, 112, 145, 146, 147 => TypeGenerateur::PAC_AIR_EAU,
            8, 9, 10, 11, 162, 163, 164 => TypeGenerateur::PAC_EAU_EAU,
            12, 13, 14, 15, 165, 166, 167 => TypeGenerateur::PAC_EAU_GLYCOLEE_EAU,
            16, 17, 18, 19, 168, 169, 170 => TypeGenerateur::PAC_GEOTHERMIQUE,
            102 => TypeGenerateur::PANNEAU_RAYONNANT_ELECTRIQUE,
            103 => TypeGenerateur::PLANCHER_RAYONNANT_ELECTRIQUE,
            22, 26, 30, 34, 38, 42, 44, 45, 46, 47 => TypeGenerateur::POELE,
            48, 49, 140, 141 => TypeGenerateur::POELE_BOUILLEUR,
            101, 104 => TypeGenerateur::RADIATEUR_ELECTRIQUE,
            53, 54 => TypeGenerateur::RADIATEUR_GAZ,
            107, 108, 142 => TypeGenerateur::RESEAU_CHALEUR,
            default => null,
        };
    }

    public function energie(): EnergieGenerateur
    {
        $type = $this->type();

        if ($type->is_pac() || $type->is_emetteur_electrique()) {
            return EnergieGenerateur::ELECTRICITE;
        }
        if ($type->is_reseau_chaleur()) {
            return EnergieGenerateur::RESEAU_CHALEUR;
        }
        if ($type->is_poele_insert() || $type->is_poele_bouilleur()) {
            return match ($this->generateur_chauffage->enum_type_energie_id) {
                4 => EnergieGenerateur::BOIS_BUCHE,
                5 => EnergieGenerateur::BOIS_GRANULE,
                6, 7 => EnergieGenerateur::BOIS_PLAQUETTE,
                default => EnergieGenerateur::BOIS_GRANULE,
            };
        }
        if ($type->is_radiateur_gaz()) {
            return match ($this->generateur_chauffage->enum_type_energie_id) {
                2 => EnergieGenerateur::GAZ_NATUREL,
                9, 10, 13 => EnergieGenerateur::GPL,
                default => EnergieGenerateur::GAZ_NATUREL,
            };
        }
        $value = match ($this->generateur_chauffage->enum_type_energie_id) {
            1, 12 => EnergieGenerateur::ELECTRICITE,
            2 => EnergieGenerateur::GAZ_NATUREL,
            3 => EnergieGenerateur::FIOUL,
            4 => EnergieGenerateur::BOIS_BUCHE,
            5 => EnergieGenerateur::BOIS_GRANULE,
            6, 7 => EnergieGenerateur::BOIS_PLAQUETTE,
            8 => EnergieGenerateur::RESEAU_CHALEUR,
            9, 10, 13 => EnergieGenerateur::GPL,
            11 => EnergieGenerateur::CHARBON,
        };

        if ($this->type()?->is_poele_bouilleur()) {
            return $value->is_bois() ? $value : EnergieGenerateur::BOIS_BUCHE;
        }
        return $value;
    }

    /**
     * Cas des PAC hybrides : on retourne l'énergie de la partie chaudière
     */
    public function bienergie(): ?EnergieGenerateur
    {
        if (null === $this->generateur_hybride) {
            return null;
        }
        return match ($this->generateur_hybride->enum_type_generateur_ch_id) {
            148, 149 => EnergieGenerateur::GAZ_NATUREL,
            150, 151 => EnergieGenerateur::FIOUL,
            152, 153 => EnergieGenerateur::BOIS_GRANULE,
            154, 155, 156 => EnergieGenerateur::BOIS_BUCHE,
            157, 158, 159 => EnergieGenerateur::BOIS_PLAQUETTE,
            160, 161 => EnergieGenerateur::GPL,
            default => null,
        };
    }

    public function generateur_multi_batiment(): bool
    {
        return match ($this->generateur_chauffage->enum_type_generateur_ch_id) {
            109, 110, 111, 112, 171 => true,
            default => false,
        };
    }

    public function generateur_collectif(): bool
    {
        if ($this->generateur_multi_batiment()) {
            return true;
        }
        if ($this->type()?->is_chauffage_divise()) {
            return false;
        }
        return $this->generateur_chauffage->enum_lien_generateur_emetteur_id === 1
            && in_array($this->installation_chauffage->enum_type_installation_id, [2, 3, 4]);
    }

    public function position_chaudiere(): ?PositionChaudiere
    {
        return $this->type()->is_chaudiere() ? match (true) {
            ($this->generateur_chauffage->pn < 18) => PositionChaudiere::CHAUDIERE_MURALE,
            ($this->generateur_chauffage->pn >= 18) => PositionChaudiere::CHAUDIERE_SOL,
            default =>  PositionChaudiere::CHAUDIERE_SOL,
        } : null;
    }

    public function annee_installation(): ?int
    {
        return match ($this->generateur_chauffage->enum_type_generateur_ch_id) {
            75 => 1969,
            76 => 1975,
            55, 62, 69, 120 => 1977,
            77, 85, 127 => 1980,
            86, 94, 128, 136 => 1985,
            20, 21, 22, 23 => 1989,
            78, 87, 129 => 1990,
            56, 63, 70, 121 => 1994,
            88, 91, 95, 130, 133, 137 => 2000,
            57, 64, 71, 122 => 2003,
            24, 25, 26, 27 => 2004,
            50, 53 => 2005,
            32, 33, 34, 35 => 2006,
            1, 4, 8, 12, 16 => 2007,
            44, 48, 140 => 2011,
            58, 65, 72, 123 => 2012,
            2, 5, 9, 13, 17, 145, 162, 165, 168 => 2014,
            79, 81, 83, 89, 92, 96, 131, 134, 138, 148, 150, 160 => 2015,
            6, 10, 14, 18, 146, 163, 166, 169 => 2016,
            36, 37, 38, 39, 59, 66, 124, 154, 157 => 2017,
            45, 60, 67, 73, 125, 152, 155, 158 => 2019,
            3, 7, 11, 15, 19, 28, 29, 30, 31, 40, 41, 42, 43, 46, 49, 51, 52, 54, 61, 68, 74, 80, 82,
            84, 90, 93, 97, 126, 132, 135, 139, 141, 147, 149, 151, 153, 156, 159, 161, 164, 167, 170 => $this->context->ressource()->administratif()->annee_etablissement(),
            default => null,
        };
    }

    public function position_volume_chauffe(): bool
    {
        if ($this->generateur_collectif()) {
            return false;
        }
        return $this->type()?->is_chauffage_divise() ? true : $this->generateur_chauffage->position_volume_chauffe;
    }

    public function mode_combustion(): ?ModeCombustion
    {
        $enum_type_generateur_ch_id = $this->generateur_hybride?->enum_type_generateur_ch_id
            ?? $this->generateur_chauffage->enum_type_generateur_ch_id;

        $value = match ($enum_type_generateur_ch_id) {
            50, 51, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80,
            85, 86, 87, 88, 89, 90, 109, 110, 111, 112, 113, 114, 115, 116, 119, 120, 121, 122, 123, 124, 125, 126, 127, 128,
            129, 130, 131, 132, 140, 141, 152, 153, 154, 155, 156, 157, 158, 159, 171 => ModeCombustion::STANDARD,
            81, 82, 91, 92, 93, 133, 134, 135 => ModeCombustion::BASSE_TEMPERATURE,
            52, 83, 84, 94, 95, 96, 97, 136, 137, 138, 139, 148, 149, 150, 151, 160, 161 => ModeCombustion::CONDENSATION,
            default => null,
        };

        if ($value ?? $this->type()?->is_generateur_air_chaud()) {
            return $value === ModeCombustion::BASSE_TEMPERATURE ? ModeCombustion::STANDARD : $value;
        }
        return $value;
    }

    public function label(): ?LabelGenerateur
    {
        return match ($this->generateur_chauffage->enum_type_generateur_ch_id) {
            98, 99, 100 => LabelGenerateur::NF_PERFORMANCE,
            32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 45, 46 => LabelGenerateur::FLAMME_VERTE,
            default => null,
        };
    }

    public function presence_ventouse(): ?bool
    {
        return $this->generateur_chauffage->presence_ventouse ?? $this->generateur_hybride?->presence_ventouse;
    }

    public function presence_regulation_combustion(): ?bool
    {
        return $this->generateur_chauffage->presence_regulation_combustion ?? $this->generateur_hybride?->presence_regulation_combustion;
    }

    public function pn(): ?float
    {
        $value = $this->generateur_chauffage->pn_saisi() ?? $this->generateur_hybride?->pn_saisi();
        return $value > 0 ? $value / 1000 : null;
    }

    public function rpint(): ?float
    {
        $value = $this->generateur_chauffage->rpint_saisi() ?? $this->generateur_hybride?->rpint_saisi();
        return $value > 0 ? $value : null;
    }

    public function rpn(): ?float
    {
        $value = $this->generateur_chauffage->rpn_saisi() ?? $this->generateur_hybride?->rpn_saisi();
        return $value > 0 ? $value : null;
    }

    public function qp0(): ?float
    {
        $value = $this->generateur_chauffage->qp0_saisi() ?? $this->generateur_hybride?->qp0_saisi();
        return $value > 0 ? $value : null;
    }

    public function pveilleuse(): ?float
    {
        $value = $this->generateur_chauffage->pveilleuse_saisi() ?? $this->generateur_hybride?->pveilleuse_saisi();
        return $value > 0 ? $value : null;
    }

    public function scop(): ?float
    {
        return ($value = $this->generateur_chauffage->scop_saisi()) > 0 ? $value : null;
    }

    public function tfonc30(): ?float
    {
        $value = $this->generateur_chauffage->tfonc30_saisi() ?? $this->generateur_hybride?->tfonc30_saisi();
        return $value > 0 ? $value : null;
    }

    public function tfonc100(): ?float
    {
        $value = $this->generateur_chauffage->tfonc100_saisi() ?? $this->generateur_hybride?->tfonc100_saisi();
        return $value > 0 ? $value : null;
    }

    public function __invoke(
        GenerateurChauffage $generateur_chauffage,
        InstallationChauffage $installation_chauffage,
        Context $context,
    ): ?GenerateurDto {
        $this->context = $context;
        $this->installation_chauffage = $installation_chauffage;
        $this->generateur_chauffage = $generateur_chauffage;
        $this->generateur_hybride = null;

        if (null === $type = $this->type()) {
            return null;
        }
        if ($type->is_pac()) {
            $this->generateur_hybride = $installation_chauffage->match_generateur_hybride($generateur_chauffage);
        }

        return new GenerateurDto(
            id: (string) $generateur_chauffage->id(),
            description: $generateur_chauffage->description(),
            type: $this->type(),
            energie: $this->energie(),
            bienergie: $this->bienergie(),
            annee_installation: $this->annee_installation(),
            position: new PositionDto(
                generateur_collectif: $this->generateur_collectif(),
                generateur_multi_batiment: $this->generateur_multi_batiment(),
                position_volume_chauffe: $this->position_volume_chauffe(),
                position_chaudiere: $this->position_chaudiere(),
                generateur_mixte_id: $this->generateur_mixte_id(),
                reseau_chaleur_id: $this->reseau_chaleur_id(),
            ),
            signaletique: new SignaletiqueDto(
                pn: $this->pn(),
                label: $this->label(),
                scop: $this->scop(),
                mode_combustion: $this->mode_combustion(),
                presence_ventouse: $this->presence_ventouse(),
                presence_regulation_combustion: $this->presence_regulation_combustion(),
                pveilleuse: $this->pveilleuse(),
                qp0: $this->qp0(),
                rpn: $this->rpn(),
                rpint: $this->rpint(),
                tfonc30: $this->tfonc30(),
                tfonc100: $this->tfonc100(),
            ),
        );
    }
}
