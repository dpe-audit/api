<?php

namespace App\Legacy\Transformer\Ecs;

use App\Domain\Ecs\Generateur\Position\PositionChauffeEau;
use App\Domain\Ecs\Generateur\{TypeGenerateur, EnergieGenerateur};
use App\Domain\Ecs\Generateur\Signaletique\LabelGenerateur;
use App\Domain\Ecs\Generateur\Signaletique\ModeCombustion;
use App\Dto\Ecs\Generateur\{GenerateurDto, PositionDto, SignaletiqueDto};
use App\Legacy\Model\{GenerateurEcs, InstallationEcs};
use App\Legacy\Transformer\Context;

final class GenerateurTransformer
{
    private Context $context;
    private GenerateurEcs $generateur_ecs;
    private InstallationEcs $installation_ecs;

    public function generateur_mixte_id(): ?string
    {
        foreach ($this->context->logement()->installation_chauffage_collection as $installation_chauffage) {
            foreach ($installation_chauffage->generateur_chauffage_collection as $generateur_chauffage) {
                if (null === $generateur_chauffage->reference_generateur_mixte) {
                    continue;
                }
                if ($this->generateur_ecs->match($generateur_chauffage->reference_generateur_mixte)) {
                    return $generateur_chauffage->id();
                }
            }
        }
        if (null === $reference = $this->generateur_ecs->reference_generateur_mixte) {
            return null;
        }
        return $this->context->logement()->find_generateur_chauffage($reference)?->id()
            ?? $this->context->logement()->match_generateur_chauffage($this->generateur_ecs)?->id();
    }

    public function reseau_chaleur_id(): ?string
    {
        return $this->generateur_ecs->identifiant_reseau_chaleur;
    }

    public function annee_installation(): ?int
    {
        return match ($this->generateur_ecs->enum_type_generateur_ecs_id) {
            35 => 1969,
            36 => 1975,
            15, 22, 29, 85 => 1977,
            63, 110 => 1979,
            37, 45, 92, 46, 54, 93, 101 => 1980,
            58, 64, 105, 111 => 1989,
            38, 47, 94 => 1990,
            16, 23, 30, 86 => 1994,
            48, 51, 55, 59, 61, 65, 95, 98, 102, 106, 108, 112 => 2000,
            17, 24, 31, 87 => 2003,
            1, 4, 7, 10 => 2009,
            13, 115 => 2011,
            18, 25, 32, 88 => 2012,
            2, 5, 8, 11 => 2014,
            39, 41, 43, 49, 52, 56, 66, 96, 99, 103, 113 => 2015,
            19, 26, 89 => 2017,
            20, 27, 33, 90 => 2019,
            3, 6, 9, 12, 14, 21, 28, 34, 40, 42, 44, 50, 53, 57, 60, 62, 67, 91, 97, 100, 104, 107, 109,
            114, 116 => $this->context->ressource()->administratif()->annee_etablissement(),
            default => null,
        };
    }

    public function label(): ?LabelGenerateur
    {
        return match ($this->generateur_ecs->enum_type_generateur_ecs_id) {
            70 => LabelGenerateur::NE_PERFORMANCE_B,
            71 => LabelGenerateur::NE_PERFORMANCE_C,
            default => null,
        };
    }

    public function mode_combustion(): ?ModeCombustion
    {
        if (null === $this->type()) {
            return null;
        }
        if (!$this->energie()?->is_combustible()) {
            return null;
        }
        $value = match ($this->generateur_ecs->enum_type_generateur_ecs_id) {
            15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40,
            45, 46, 47, 48, 49, 50, 58, 59, 60, 63, 64, 65, 66, 67, 74, 75, 76, 78, 79, 80, 81, 84, 85, 86, 87, 88,
            89, 90, 91, 92, 93, 94, 95, 96, 97, 105, 106, 107, 110, 111, 112, 113, 114, 124, 125, 126, 127, 128, 129,
            130, 131, 134 => ModeCombustion::STANDARD,
            41, 42, 51, 52, 53, 98, 99, 100 => ModeCombustion::BASSE_TEMPERATURE,
            43, 44, 54, 55, 56, 57, 61, 62, 101, 102, 103, 104, 108, 109, 120, 121, 122, 123, 132, 133 => ModeCombustion::CONDENSATION,
            default => null,
        };
        if ($value && $this->type()?->is_chauffe_eau()) {
            return $value === ModeCombustion::BASSE_TEMPERATURE ? ModeCombustion::STANDARD : $value;
        }
        return $value;
    }

    public function generateur_multi_batiment(): bool
    {
        return match ($this->generateur_ecs->enum_type_generateur_ecs_id) {
            74, 75, 76, 77, 134 => true,
            default => false,
        };
    }

    public function generateur_collectif(): bool
    {
        if ($this->generateur_multi_batiment()) {
            return true;
        }
        return \in_array($this->installation_ecs->enum_type_installation_id, [2, 3, 4]);
    }

    /**
     * Compatibilité depuis la version DPE 2.0
     */
    public function type(): ?TypeGenerateur
    {
        $value = match ($this->generateur_ecs->enum_type_generateur_ecs_id) {
            63, 64, 65, 66, 67, 68, 69, 70, 71, 78, 79, 80, 81, 105, 106, 107, 108, 109,
            110, 111, 112, 113, 114, 117 => TypeGenerateur::CHAUFFE_EAU,
            1, 2, 3, 82 => TypeGenerateur::CET_AIR_AMBIANT,
            4, 5, 6 => TypeGenerateur::CET_AIR_EXTERIEUR,
            7, 8, 9 => TypeGenerateur::CET_AIR_EXTRAIT,
            15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40,
            41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 74, 75, 76, 85, 86, 87, 88, 89,
            90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100, 101, 102, 103, 104, 118, 134 => TypeGenerateur::CHAUDIERE,
            10, 11, 12, 77, 120, 121, 122, 123, 124, 125, 126, 127, 128, 129, 130, 131, 132, 133 => TypeGenerateur::PAC_DOUBLE_SERVICE,
            13, 14, 115, 116 => TypeGenerateur::POELE_BOUILLEUR,
            72, 73, 119 => TypeGenerateur::RESEAU_CHALEUR,
            84 => null,
        };

        if (null === $value) {
            return null;
        }
        if ($value->is_pac() && null !== $this->generateur_mixte_id()) {
            return TypeGenerateur::PAC_DOUBLE_SERVICE;
        }
        return $value;
    }

    public function energie(): EnergieGenerateur
    {
        $type = $this->type();
        if ($type->is_pac()) {
            return EnergieGenerateur::ELECTRICITE;
        }
        if ($type->is_reseau_chaleur()) {
            return EnergieGenerateur::RESEAU_CHALEUR;
        }
        $value = match ($this->generateur_ecs->enum_type_energie_id) {
            1 => EnergieGenerateur::ELECTRICITE,
            2 => EnergieGenerateur::GAZ_NATUREL,
            3 => EnergieGenerateur::FIOUL,
            4 => EnergieGenerateur::BOIS_BUCHE,
            5 => EnergieGenerateur::BOIS_GRANULE,
            6 => EnergieGenerateur::BOIS_PLAQUETTE,
            7 => EnergieGenerateur::BOIS_PLAQUETTE,
            8 => EnergieGenerateur::RESEAU_CHALEUR,
            9 => EnergieGenerateur::GPL,
            10 => EnergieGenerateur::GPL,
            11 => EnergieGenerateur::CHARBON,
            12 => EnergieGenerateur::ELECTRICITE,
            13 => EnergieGenerateur::GPL,
        };

        if ($this->type()?->is_poele_bouilleur()) {
            return $value->is_bois() ? $value : EnergieGenerateur::BOIS_BUCHE;
        }
        return $value;
    }

    public function position_chauffe_eau(): ?PositionChauffeEau
    {
        return match ($this->generateur_ecs->enum_type_generateur_ecs_id) {
            68 => PositionChauffeEau::CHAUFFE_EAU_HORIZONTAL,
            69, 70, 71 => PositionChauffeEau::CHAUFFE_EAU_VERTICAL,
            default => null,
        };
    }

    public function volume_stockage_integre(): float
    {
        return $this->generateur_ecs->enum_type_stockage_ecs_id === 3 ? $this->generateur_ecs->volume_stockage : 0;
    }

    public function presence_ventouse(): ?bool
    {
        return $this->generateur_ecs->presence_ventouse;
    }

    public function position_volume_chauffe(): bool
    {
        if ($this->generateur_collectif()) {
            return false;
        }
        return $this->generateur_ecs->position_volume_chauffe;
    }

    public function pn(): ?float
    {
        return ($value = $this->generateur_ecs->pn_saisi()) > 0 ? $value / 1000 : null;
    }

    public function rpn(): ?float
    {
        return ($value = $this->generateur_ecs->rpn_saisi()) > 0 ? $value : null;
    }

    public function qp0(): ?float
    {
        return ($value = $this->generateur_ecs->qp0_saisi()) > 0 ? $value : null;
    }

    public function pveilleuse(): ?float
    {
        return ($value = $this->generateur_ecs->pveilleuse_saisi()) > 0 ? $value : null;
    }

    public function cop(): ?float
    {
        return ($value = $this->generateur_ecs->cop_saisi()) > 0 ? $value : null;
    }

    public function __invoke(
        GenerateurEcs $generateur_ecs,
        InstallationEcs $installation_ecs,
        Context $context,
    ): GenerateurDto {
        $this->context = $context;
        $this->generateur_ecs = $generateur_ecs;
        $this->installation_ecs = $installation_ecs;

        return new GenerateurDto(
            id: $generateur_ecs->id(),
            description: $generateur_ecs->description(),
            type: $this->type(),
            energie: $this->energie(),
            annee_installation: $this->annee_installation(),
            position: new PositionDto(
                generateur_collectif: $this->generateur_collectif(),
                generateur_multi_batiment: $this->generateur_multi_batiment(),
                position_volume_chauffe: $this->position_volume_chauffe(),
                position_chauffe_eau: $this->position_chauffe_eau(),
                generateur_mixte_id: $this->generateur_mixte_id(),
                reseau_chaleur_id: $this->reseau_chaleur_id(),
            ),
            signaletique: new SignaletiqueDto(
                volume_stockage: $this->volume_stockage_integre(),
                label: $this->label(),
                mode_combustion: $this->mode_combustion(),
                pn: $this->pn(),
                cop: $this->cop(),
                presence_ventouse: $this->presence_ventouse(),
                pveilleuse: $this->pveilleuse(),
                qp0: $this->qp0(),
                rpn: $this->rpn(),
            )
        );
    }
}
