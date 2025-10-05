<?php

namespace App\Database\Observatoire\Model;

use App\Domain\Chauffage\Generateur\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Chauffage\Generateur\Position\PositionChaudiere;
use App\Domain\Chauffage\Generateur\Signaletique\{LabelGenerateur, ModeCombustion};
use App\Domain\Chauffage\Systeme\Reseau\IsolationReseau;
use App\Domain\Chauffage\Systeme\Reseau\TypeDistribution;
use App\Domain\Chauffage\TypeChauffage;

final class XMLGenerateurChauffage
{
    use WithDescription, WithReferences;

    public function __construct(
        public readonly string $reference,
        public readonly ?string $reference_generateur_mixte,
        public readonly ?string $description,
        public readonly ?string $ref_produit_generateur_ch,
        public readonly int $enum_type_generateur_ch_id,
        public readonly int $enum_usage_generateur_id,
        public readonly int $enum_type_energie_id,
        public readonly bool $position_volume_chauffe,
        public readonly ?int $tv_rendement_generation_id,
        public readonly ?int $tv_scop_id,
        public readonly ?int $tv_temp_fonc_100_id,
        public readonly ?int $tv_temp_fonc_30_id,
        public readonly ?int $tv_generateur_combustion_id,
        public readonly ?int $tv_reseau_chaleur_id,
        public readonly ?string $identifiant_reseau_chaleur,
        public readonly ?string $date_arrete_reseau_chaleur,
        public readonly ?int $n_radiateurs_gaz,
        public readonly ?int $priorite_generateur_cascade,
        public readonly ?bool $presence_ventouse,
        public readonly ?bool $presence_regulation_combustion,
        public readonly int $enum_methode_saisie_carac_sys_id,
        public readonly int $enum_lien_generateur_emetteur_id,

        public readonly ?float $scop,
        public readonly ?float $pn,
        public readonly ?float $qp0,
        public readonly ?float $pveilleuse,
        public readonly ?float $temp_fonc_30,
        public readonly ?float $temp_fonc_100,
        public readonly ?float $rpn,
        public readonly ?float $rpint,
        public readonly ?float $rendement_generation,
        public readonly float $conso_ch,
        public readonly float $conso_ch_depensier
    ) {}

    /**
     * XSD logement/installation_chauffage_collection/installation_chauffage/generateur_chauffage_collection/generateur_chauffage
     */
    public static function from(\SimpleXMLElement $element): self
    {
        return new self(
            reference: (string) $element->donnee_entree->reference,
            reference_generateur_mixte: (string) $element->donnee_entree->reference_generateur_mixte ?: null,
            description: (string) $element->donnee_entree->description ?: null,
            ref_produit_generateur_ch: (string) $element->donnee_entree->ref_produit_generateur_ch ?: null,
            enum_type_generateur_ch_id: (int) $element->donnee_entree->enum_type_generateur_ch_id,
            enum_usage_generateur_id: (int) $element->donnee_entree->enum_usage_generateur_id,
            enum_type_energie_id: (int) $element->donnee_entree->enum_type_energie_id,
            position_volume_chauffe: (bool)(int) $element->donnee_entree->position_volume_chauffe,
            tv_rendement_generation_id: (int) $element->donnee_entree->tv_rendement_generation_id ?: null,
            tv_scop_id: (int) $element->donnee_entree->tv_scop_id ?: null,
            tv_temp_fonc_100_id: (int) $element->donnee_entree->tv_temp_fonc_100_id ?: null,
            tv_temp_fonc_30_id: (int) $element->donnee_entree->tv_temp_fonc_30_id ?: null,
            tv_generateur_combustion_id: (int) $element->donnee_entree->tv_generateur_combustion_id ?: null,
            tv_reseau_chaleur_id: (int) $element->donnee_entree->tv_reseau_chaleur_id ?: null,
            identifiant_reseau_chaleur: (string) $element->donnee_entree->identifiant_reseau_chaleur ?: null,
            date_arrete_reseau_chaleur: (string) $element->donnee_entree->date_arrete_reseau_chaleur ?: null,
            n_radiateurs_gaz: (int) $element->donnee_entree->n_radiateurs_gaz ?: null,
            priorite_generateur_cascade: (int) $element->donnee_entree->priorite_generateur_cascade ?: null,
            presence_ventouse: (bool)(int) $element->donnee_entree->presence_ventouse ?: null,
            presence_regulation_combustion: (bool)(int) $element->donnee_entree->presence_regulation_combustion ?: null,
            enum_methode_saisie_carac_sys_id: (int) $element->donnee_entree->enum_methode_saisie_carac_sys_id,
            enum_lien_generateur_emetteur_id: (int) $element->donnee_entree->enum_lien_generateur_emetteur_id,
            scop: (float) $element->donnee_intermediaire->scop ?: null,
            pn: (float) $element->donnee_intermediaire->pn ?: null,
            qp0: (float) $element->donnee_intermediaire->qp0 ?: null,
            pveilleuse: (float) $element->donnee_intermediaire->pveilleuse ?: null,
            temp_fonc_30: (float) $element->donnee_intermediaire->temp_fonc_30 ?: null,
            temp_fonc_100: (float) $element->donnee_intermediaire->temp_fonc_100 ?: null,
            rpn: (float) $element->donnee_intermediaire->rpn ?: null,
            rpint: (float) $element->donnee_intermediaire->rpint ?: null,
            rendement_generation: (float) $element->donnee_intermediaire->rendement_generation ?: null,
            conso_ch: (float) $element->donnee_intermediaire->conso_ch,
            conso_ch_depensier: (float) $element->donnee_intermediaire->conso_ch_depensier
        );
    }

    /**
     * XSD logement/installation_chauffage_collection/installation_chauffage/generateur_chauffage_collection
     * 
     * @return array<self>
     */
    public static function from_collection(\SimpleXMLElement $element): array
    {
        $collection = [];

        foreach ($element->generateur_chauffage as $item) {
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

    public function description(): string
    {
        return $this->description ?? '-';
    }

    public function generateur_mixte_id(XMLRessource $ressource): ?string
    {
        if (null === $this->reference_generateur_mixte) {
            return null;
        }
        foreach ($ressource->logement()->installation_ecs_collection as $installation_ecs) {
            foreach ($installation_ecs->generateur_ecs_collection as $generateur_ecs) {
                if ($generateur_ecs->match($this->identifiers())) {
                    return (string) $generateur_ecs->id();
                }
            }
        }
        return $this->reference_generateur_mixte;
    }

    public function generateur_chauffage_hybride_partie_chaudiere(XMLRessource $ressource): null|self|false
    {
        if (false === $this->pac_hybride()) {
            return null;
        }
        if (false === $this->pac_hybride_partie_pac()) {
            return null;
        }
        foreach ($ressource->logement()->installation_chauffage_collection as $installation_chauffage) {
            foreach ($installation_chauffage->generateur_chauffage_collection as $generateur_chauffage) {
                if ($generateur_chauffage->match($this->identifiers())) {
                    continue;
                }
                if (false === $generateur_chauffage->pac_hybride_partie_chaudiere()) {
                    continue;
                }
                return $generateur_chauffage;
            }
        }
        return false;
    }

    public function type_chauffage(XMLInstallationChauffage $installation_chauffage): TypeChauffage
    {
        foreach ($installation_chauffage->emetteur_chauffage_collection as $emetteur_chauffage) {
            if ($emetteur_chauffage->enum_lien_generateur_emetteur_id === $this->enum_lien_generateur_emetteur_id) {
                return TypeChauffage::CHAUFFAGE_CENTRAL;
            }
        }
        return TypeChauffage::CHAUFFAGE_DIVISE;
    }

    public function type_distribution(XMLInstallationChauffage $installation_chauffage): ?TypeDistribution
    {
        foreach ($installation_chauffage->emetteur_chauffage_collection as $emetteur_chauffage) {
            if ($emetteur_chauffage->enum_lien_generateur_emetteur_id === $this->enum_lien_generateur_emetteur_id) {
                return $emetteur_chauffage->type_distribution();
            }
        }
        return null;
    }

    public function isolation_reseau(XMLInstallationChauffage $installation_chauffage): ?IsolationReseau
    {
        foreach ($installation_chauffage->emetteur_chauffage_collection as $emetteur_chauffage) {
            if ($emetteur_chauffage->reseau_distribution_isole !== null) {
                return $emetteur_chauffage->reseau_distribution_isole ? IsolationReseau::ISOLE : IsolationReseau::NON_ISOLE;
            }
        }
        return null;
    }

    public function reseau_chaleur_id(): ?string
    {
        return $this->identifiant_reseau_chaleur;
    }

    public function base(): bool
    {
        return $this->enum_lien_generateur_emetteur_id === 1;
    }

    public function appoint(): bool
    {
        return $this->enum_lien_generateur_emetteur_id === 2;
    }

    public function appoint_electrique_sdb(): bool
    {
        return $this->enum_lien_generateur_emetteur_id === 3;
    }

    public function pac_hybride(): bool
    {
        return $this->enum_type_generateur_ch_id >= 145 && $this->enum_type_generateur_ch_id <= 170;
    }

    public function pac_hybride_partie_pac(): bool
    {
        return $this->pac_hybride() && false === $this->pac_hybride_partie_chaudiere();
    }

    public function pac_hybride_partie_chaudiere(): bool
    {
        return $this->pac_hybride()
            && $this->enum_type_generateur_ch_id >= 148
            && $this->enum_type_generateur_ch_id <= 161;
    }

    public function type(): ?TypeGenerateur
    {
        return match ($this->enum_type_generateur_ch_id) {
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
            4, 5, 6, 7, 112 => TypeGenerateur::PAC_AIR_EAU,
            8, 9, 10, 11 => TypeGenerateur::PAC_EAU_EAU,
            12, 13, 14, 15 => TypeGenerateur::PAC_EAU_GLYCOLEE_EAU,
            16, 17, 18, 19 => TypeGenerateur::PAC_GEOTHERMIQUE,
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
        return match ($this->enum_type_energie_id) {
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
    }

    public function generateur_collectif(XMLInstallationChauffage $installation): bool
    {
        return $this->enum_lien_generateur_emetteur_id === 1 && $installation->installation_collective();
    }

    public function generateur_multi_batiment(): bool
    {
        return match ($this->enum_type_generateur_ch_id) {
            109, 110, 111, 112, 171 => true,
            default => false,
        };
    }

    public function position_chaudiere(): ?PositionChaudiere
    {
        return $this->type()->is_chaudiere() ? match (true) {
            ($this->pn < 18) => PositionChaudiere::CHAUDIERE_MURALE,
            ($this->pn >= 18) => PositionChaudiere::CHAUDIERE_SOL,
            default =>  PositionChaudiere::CHAUDIERE_SOL,
        } : null;
    }

    public function annee_installation(XMLRessource $ressource): ?int
    {
        return match ($this->enum_type_generateur_ch_id) {
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
            84, 90, 93, 97, 126, 132, 135, 139, 141, 147, 149, 151, 153, 156, 159, 161, 164, 167, 170 => $ressource->administratif->annee_etablissement(),
            default => null,
        };
    }

    public function mode_combustion(): ?ModeCombustion
    {
        return match ($this->enum_type_generateur_ch_id) {
            50, 51, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80,
            85, 86, 87, 88, 89, 90, 109, 110, 111, 112, 113, 114, 115, 116, 119, 120, 121, 122, 123, 124, 125, 126, 127, 128,
            129, 130, 131, 132, 140, 141, 152, 153, 154, 155, 156, 157, 158, 159, 171 => ModeCombustion::STANDARD,
            81, 82, 91, 92, 93, 133, 134, 135 => ModeCombustion::BASSE_TEMPERATURE,
            52, 83, 84, 94, 95, 96, 97, 136, 137, 138, 139, 148, 149, 150, 151, 160, 161 => ModeCombustion::CONDENSATION,
            default => null,
        };
    }

    public function label(): ?LabelGenerateur
    {
        return match ($this->enum_type_generateur_ch_id) {
            98, 99, 100 => LabelGenerateur::NF_PERFORMANCE,
            32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 45, 46 => LabelGenerateur::FLAMME_VERTE,
            default => null,
        };
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

    public function rpint_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id) {
            2, 3, 4, 5, 6 => $this->rpint,
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

    public function tfonc30_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id) {
            2, 3, 4, 5, 6 => $this->temp_fonc_30,
            default => null,
        };
    }

    public function tfonc100_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id) {
            2, 3, 4, 5, 6 => $this->temp_fonc_100,
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

    public function scop_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id) {
            2, 3, 4, 5, 6 => $this->scop,
            default => null,
        };
    }
}
