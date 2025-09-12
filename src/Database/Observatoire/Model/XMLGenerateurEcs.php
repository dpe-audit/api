<?php

namespace App\Database\Observatoire\Model;

use App\Domain\Ecs\Generateur\EnergieGenerateur;
use App\Domain\Ecs\Generateur\Position\PositionChauffeEau;
use App\Domain\Ecs\Generateur\Signaletique\LabelGenerateur;
use App\Domain\Ecs\Generateur\Signaletique\ModeCombustion;
use App\Domain\Ecs\Generateur\TypeChaudiere;
use App\Domain\Ecs\Generateur\TypeGenerateur;

final class XMLGenerateurEcs extends XMLUniqueElement
{
    public function __construct(
        public readonly string $reference,
        public readonly ?string $reference_generateur_mixte,
        public readonly ?string $description,
        public readonly ?string $ref_produit_generateur_ecs,
        public readonly int $enum_type_generateur_ecs_id,
        public readonly int $enum_usage_generateur_id,
        public readonly int $enum_type_energie_id,
        public readonly ?int $tv_generateur_combustion_id,
        public readonly int $enum_methode_saisie_carac_sys_id,
        public readonly ?int $tv_pertes_stockage_id,
        public readonly ?int $tv_scop_id,
        public readonly ?int $enum_periode_installation_ecs_thermo_id,
        public readonly ?string $identifiant_reseau_chaleur,
        public readonly ?string $date_arrete_reseau_chaleur,
        public readonly ?int $tv_reseau_chaleur_id,
        public readonly int $enum_type_stockage_ecs_id,
        public readonly bool $position_volume_chauffe,
        public readonly ?bool $position_volume_chauffe_stockage,
        public readonly float $volume_stockage,
        public readonly ?bool $presence_ventouse,

        public readonly ?float $pn,
        public readonly ?float $qp0,
        public readonly ?float $pveilleuse,
        public readonly ?float $rpn,
        public readonly ?float $cop,
        public readonly float $ratio_besoin_ecs,
        public readonly ?float $rendement_generation,
        public readonly ?float $rendement_generation_stockage,
        public readonly float $conso_ecs,
        public readonly float $conso_ecs_depensier,
        public readonly ?float $rendement_stockage
    ) {}

    /**
     * XSD logement/installation_ecs_collection/installation_ecs/generateur_ecs_collection/generateur_ecs
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            reference: (string) $xml->donnee_entree->reference,
            reference_generateur_mixte: (string) $xml->donnee_entree->description ?: null,
            description: (string) $xml->donnee_entree->description ?: null,
            ref_produit_generateur_ecs: (string) $xml->donnee_entree->ref_produit_generateur_ecs ?: null,
            enum_type_generateur_ecs_id: (int) $xml->donnee_entree->enum_type_generateur_ecs_id,
            enum_usage_generateur_id: (int) $xml->donnee_entree->enum_usage_generateur_id,
            enum_type_energie_id: (int) $xml->donnee_entree->enum_type_energie_id,
            tv_generateur_combustion_id: (int) $xml->donnee_entree->tv_generateur_combustion_id ?: null,
            enum_methode_saisie_carac_sys_id: (int) $xml->donnee_entree->enum_methode_saisie_carac_sys_id,
            tv_pertes_stockage_id: (int) $xml->donnee_entree->tv_pertes_stockage_id ?: null,
            tv_scop_id: (int) $xml->donnee_entree->tv_scop_id ?: null,
            enum_periode_installation_ecs_thermo_id: (int) $xml->donnee_entree->enum_periode_installation_ecs_thermo_id ?: null,
            identifiant_reseau_chaleur: (string) $xml->donnee_entree->identifiant_reseau_chaleur ?: null,
            date_arrete_reseau_chaleur: (string) $xml->donnee_entree->date_arrete_reseau_chaleur ?: null,
            tv_reseau_chaleur_id: (int) $xml->donnee_entree->tv_reseau_chaleur_id ?: null,
            enum_type_stockage_ecs_id: (int) $xml->donnee_entree->enum_type_stockage_ecs_id,
            position_volume_chauffe: (bool)(int) $xml->donnee_entree->position_volume_chauffe,
            position_volume_chauffe_stockage: (bool)(int) $xml->donnee_entree->position_volume_chauffe_stockage ?: null,
            volume_stockage: (float) $xml->donnee_entree->volume_stockage,
            presence_ventouse: (bool)(int) $xml->donnee_entree->presence_ventouse ?: null,
            pn: (float) $xml->donnee_intermediaire->pn ?: null,
            qp0: (float) $xml->donnee_intermediaire->qp0 ?: null,
            pveilleuse: (float) $xml->donnee_intermediaire->pveilleuse ?: null,
            rpn: (float) $xml->donnee_intermediaire->rpn ?: null,
            cop: (float) $xml->donnee_intermediaire->cop ?: null,
            ratio_besoin_ecs: (float) $xml->donnee_intermediaire->ratio_besoin_ecs,
            rendement_generation: (float) $xml->donnee_intermediaire->rendement_generation ?: null,
            rendement_generation_stockage: (float) $xml->donnee_intermediaire->rendement_generation_stockage ?: null,
            conso_ecs: (float) $xml->donnee_intermediaire->conso_ecs,
            conso_ecs_depensier: (float) $xml->donnee_intermediaire->conso_ecs_depensier,
            rendement_stockage: (float) $xml->donnee_intermediaire->rendement_stockage ?: null
        );
    }

    /**
     * XSD logement/installation_ecs_collection/installation_ecs/generateur_ecs_collection
     * 
     * @return array<self>
     */
    public static function from_collection(\SimpleXMLElement $xml): array
    {
        $collection = [];

        foreach ($xml->generateur_ecs as $item) {
            $collection[] = self::from($item);
        }
        return $collection;
    }

    /**
     * @inheritDoc
     */
    public function identifiers(): array
    {
        $identifiers = [$this->reference];
        if ($this->description) {
            $identifiers[] = $this->description;
        }
        return $identifiers;
    }

    public function generateur_mixte_id(XMLRessource $ressource): ?string
    {
        if (null === $this->reference_generateur_mixte) {
            return null;
        }
        foreach ($ressource->logement()->installation_chauffage_collection as $installation_chauffage) {
            foreach ($installation_chauffage->generateur_chauffage_collection as $generateur_chauffage) {
                if ($generateur_chauffage->match($this->identifiers())) {
                    return (string) $generateur_chauffage->id();
                }
            }
        }
        return $this->reference_generateur_mixte;
    }

    public function reseau_chaleur_id(): ?string
    {
        return $this->identifiant_reseau_chaleur;
    }

    public function description(): string
    {
        return $this->description ?? 'Description non renseignée';
    }

    public function annee_installation(XMLRessource $ressource): ?int
    {
        return match ($this->enum_type_generateur_ecs_id) {
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
            114, 116 => $ressource->administratif->annee_etablissement(),
            default => null,
        };
    }

    public function label(): ?LabelGenerateur
    {
        return match ($this->enum_type_generateur_ecs_id) {
            70 => LabelGenerateur::NE_PERFORMANCE_B,
            71 => LabelGenerateur::NE_PERFORMANCE_C,
            default => null,
        };
    }

    public function mode_combustion(): ?ModeCombustion
    {
        return match ($this->enum_type_generateur_ecs_id) {
            15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40,
            45, 46, 47, 48, 49, 50, 58, 59, 60, 63, 64, 65, 66, 67, 74, 75, 76, 78, 79, 80, 81, 84, 85, 86, 87, 88,
            89, 90, 91, 92, 93, 94, 95, 96, 97, 105, 106, 107, 110, 111, 112, 113, 114, 124, 125, 126, 127, 128, 129,
            130, 131, 134 => ModeCombustion::STANDARD,
            41, 42, 51, 52, 53, 98, 99, 100 => ModeCombustion::BASSE_TEMPERATURE,
            43, 44, 54, 55, 56, 57, 61, 62, 101, 102, 103, 104, 108, 109, 120, 121, 122, 123, 132, 133 => ModeCombustion::CONDENSATION,
            default => null,
        };
    }

    public function generateur_multi_batiment(): bool
    {
        return match ($this->enum_type_generateur_ecs_id) {
            74, 75, 76, 77, 134 => true,
            default => false,
        };
    }

    /**
     * Compatibilité depuis la version DPE 2.0
     */
    public function type(): ?TypeGenerateur
    {
        return match ($this->enum_type_generateur_ecs_id) {
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
    }

    public function energie(): EnergieGenerateur
    {
        return match ($this->enum_type_energie_id) {
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
    }

    public function position_chauffe_eau(): ?PositionChauffeEau
    {
        return match ($this->enum_type_generateur_ecs_id) {
            68 => PositionChauffeEau::CHAUFFE_EAU_HORIZONTAL,
            69, 70, 71 => PositionChauffeEau::CHAUFFE_EAU_VERTICAL,
            default => null,
        };
    }

    public function presence_ventouse(): ?bool
    {
        return $this->presence_ventouse;
    }

    public function stockage_integre(): bool
    {
        return $this->enum_type_stockage_ecs_id === 3;
    }

    public function stockage_independant(): bool
    {
        return $this->enum_type_stockage_ecs_id === 2;
    }

    public function volume_stockage_integre(): float
    {
        return $this->stockage_integre() ? $this->volume_stockage : 0;
    }

    public function volume_stockage_independant(): float
    {
        return $this->stockage_independant() ? $this->volume_stockage : 0;
    }

    public function position_volume_chauffe(): bool
    {
        return $this->position_volume_chauffe;
    }

    public function position_volume_chauffe_stockage(): ?bool
    {
        return $this->position_volume_chauffe_stockage;
    }

    public function pn_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id) {
            2, 3, 4, 5, 6 => $this->pn,
            default => null,
        };
    }

    public function rpn_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id) {
            2, 3, 4, 5, 6 => $this->rpn,
            default => null,
        };
    }

    public function qp0_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id) {
            2, 3, 4, 5, 6 => $this->qp0,
            default => null,
        };
    }

    public function pveilleuse_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id) {
            2, 3, 4, 5, 6 => $this->pveilleuse,
            default => null,
        };
    }

    public function cop_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id) {
            2, 3, 4, 5, 6 => $this->cop,
            default => null,
        };
    }
}
