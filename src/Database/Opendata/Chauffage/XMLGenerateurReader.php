<?php

namespace App\Database\Opendata\Chauffage;

use App\Database\Opendata\XMLReader;
use App\Model\Chauffage\Enum\{EnergieGenerateur, LabelGenerateur, ModeCombustion, TypeChaudiere, TypeGenerateur, UsageChauffage};
use App\Model\Chauffage\ValueObject\Generateur\{Combustion, Signaletique};
use App\Model\Common\ValueObject\Id;

final class XMLGenerateurReader extends XMLReader
{
    /**
     * Les caractéristiques des générateurs de type "PAC Hybride - partie chaudière" sont agrégées aux
     * générateurs associés de type "PAC Hybride - partie pompe à chaleur".
     */
    public function supports(): bool
    {
        return false === $this->is_pac_hybride_partie_chaudiere();
    }

    public function is_pac_hybride(): bool
    {
        return \in_array($this->enum_type_generateur_ch_id(), [143, 144, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 157, 158, 159, 160, 161, 162, 163, 164, 165, 166, 167, 168, 169, 170,]);
    }

    public function is_pac_hybride_partie_pac(): bool
    {
        return $this->is_pac_hybride() && false === $this->is_pac_hybride_partie_chaudiere();
    }

    public function is_pac_hybride_partie_chaudiere(): bool
    {
        return \in_array($this->enum_type_generateur_ch_id(), [148, 149, 150, 151, 152, 153, 154, 155, 156, 157, 158, 159, 160, 161]);
    }

    public function match(array $references): bool
    {
        return count(array_intersect($this->references(), $references)) > 0;
    }

    public function references(): array
    {
        return array_filter([
            $this->reference(),
            $this->reference_generateur_mixte(),
            $this->findOne('.//description')?->reference(),
        ]);
    }

    public function installation(): XMLInstallationReader
    {
        return XMLInstallationReader::from($this->findOneOrError('./ancestor::installation_chauffage'));
    }

    /**
     * @return XMLEmetteurReader[]
     */
    public function emetteurs(): array
    {
        return array_filter(
            $this->chauffage()->emetteurs(),
            fn(XMLEmetteurReader $item): bool => $item->supports() && $item->enum_lien_generateur_emetteur_id() === $this->enum_lien_generateur_emetteur_id(),
        );
    }

    public function signaletique(): Signaletique
    {
        return Signaletique::create(
            type_chaudiere: $this->type_chaudiere(),
            pn: $this->pn_saisi(),
            scop: $this->scop_saisi(),
            label: $this->label(),
            priorite_cascade: $this->priorite_cascade(),
            combustion: $this->combustion(),
        );
    }

    public function combustion(): ?Combustion
    {
        return $this->mode_combustion() ? Combustion::create(
            mode_combustion: $this->mode_combustion(),
            presence_ventouse: $this->presence_ventouse(),
            presence_regulation_combustion: $this->presence_regulation_combustion(),
            pveilleuse: $this->pveilleuse_saisi(),
            qp0: $this->qp0_saisi(),
            rpn: $this->rpn_saisi(),
            rpint: $this->rpint_saisi(),
            tfonc30: $this->tfonc30_saisi(),
            tfonc100: $this->tfonc100_saisi(),
        ) : null;
    }

    /**
     * Générateur de type "PAC Hybride - partie chaudière" associé pour les générateurs de type "PAC Hybride - partie pompe à chaleur"
     */
    public function generateur_hybride_partie_chaudiere(): ?self
    {
        if (false === $this->is_pac_hybride_partie_pac()) {
            return null;
        }
        foreach ($this->chauffage()->generateurs() as $reader) {
            if (false === $reader->is_pac_hybride_partie_chaudiere()) {
                continue;
            }
            if (false === $reader->match($this->references())) {
                continue;
            }
            return $reader;
        }
        throw new \RuntimeException("Générateur hybride associé non trouvé pour {$this->reference()}");
    }

    public function id(): Id
    {
        return Id::from($this->reference());
    }

    public function reference(): string
    {
        return $this->findOneOrError('.//reference')->reference();
    }

    public function generateur_mixte_id(): ?Id
    {
        if (null === $reference = $this->reference_generateur_mixte()) {
            return null;
        }
        foreach ($this->ecs()->generateurs() as $item) {
            if ($item->match($this->references())) {
                return $item->id();
            }
        }
        throw new \RuntimeException("Générateur mixte {$reference} non trouvé");
    }

    public function reference_generateur_mixte(): ?string
    {
        return $this->findOne('.//reference_generateur_mixte')?->reference();
    }

    public function reseau_chaleur_id(): ?Id
    {
        return $this->findOne('.//identifiant_reseau_chaleur')?->id();
    }

    public function description(): string
    {
        return $this->findOne('.//description')?->strval() ?? 'Générateur non décrit';
    }

    public function usage(): UsageChauffage
    {
        return $this->generateur_mixte_id() ? UsageChauffage::CHAUFFAGE_ECS : UsageChauffage::CHAUFFAGE_ECS;
    }

    public function mode_combustion(): ?ModeCombustion
    {
        return $this->generateur_hybride_partie_chaudiere()?->mode_combustion()
            ?? ModeCombustion::from_enum_type_generateur_ch_id($this->enum_type_generateur_ch_id());
    }

    public function generateur_appoint(): bool
    {
        return $this->enum_lien_generateur_emetteur_id() === 2;
    }

    public function is_appoint_electrique_sdb(): bool
    {
        return $this->enum_lien_generateur_emetteur_id() === 3;
    }

    public function generateur_collectif(): bool
    {
        return $this->enum_lien_generateur_emetteur_id() === 1 && $this->installation()->installation_collective();
    }

    public function type_generateur(): TypeGenerateur
    {
        return TypeGenerateur::from_type_generateur_ch_id($this->enum_type_generateur_ch_id());
    }

    public function generateur_multi_batiment(): bool
    {
        return match ($this->enum_type_generateur_ch_id()) {
            109, 110, 111, 112, 171 => true,
            default => false,
        };
    }

    public function type_chaudiere(): ?TypeChaudiere
    {
        return $this->type_generateur()->is_chaudiere() ? match (true) {
            ($this->pn() < 18) => TypeChaudiere::CHAUDIERE_MURALE,
            ($this->pn() >= 18) => TypeChaudiere::CHAUDIERE_SOL,
            default =>  TypeChaudiere::CHAUDIERE_SOL,
        } : null;
    }

    public function energie_generateur(): EnergieGenerateur
    {
        return EnergieGenerateur::from_enum_type_energie_id($this->enum_type_energie_id());
    }

    public function energie_partie_chaudiere(): ?EnergieGenerateur
    {
        return $this->generateur_hybride_partie_chaudiere()?->energie_generateur();
    }

    public function annee_installation(): ?Annee
    {
        return match ($this->enum_type_generateur_ch_id()) {
            75 => Annee::from(1969),
            76 => Annee::from(1975),
            55, 62, 69, 120 => Annee::from(1977),
            77, 85, 127 => Annee::from(1980),
            86, 94, 128, 136 => Annee::from(1985),
            20, 21, 22, 23 => Annee::from(1989),
            78, 87, 129 => Annee::from(1990),
            56, 63, 70, 121 => Annee::from(1994),
            88, 91, 95, 130, 133, 137 => Annee::from(2000),
            57, 64, 71, 122 => Annee::from(2003),
            24, 25, 26, 27 => Annee::from(2004),
            50, 53 => Annee::from(2005),
            32, 33, 34, 35 => Annee::from(2006),
            1, 4, 8, 12, 16 => Annee::from(2007),
            44, 48, 140 => Annee::from(2011),
            58, 65, 72, 123 => Annee::from(2012),
            2, 5, 9, 13, 17, 145, 162, 165, 168 => Annee::from(2014),
            79, 81, 83, 89, 92, 96, 131, 134, 138, 148, 150, 160 => Annee::from(2015),
            6, 10, 14, 18, 146, 163, 166, 169 => Annee::from(2016),
            36, 37, 38, 39, 59, 66, 124, 154, 157 => Annee::from(2017),
            45, 60, 67, 73, 125, 152, 155, 158 => Annee::from(2019),
            3, 7, 11, 15, 19, 28, 29, 30, 31, 40, 41, 42, 43, 46, 49, 51, 52, 54, 61, 68, 74, 80, 82,
            84, 90, 93, 97, 126, 132, 135, 139, 141, 147, 149, 151, 153, 156, 159, 161, 164, 167, 170 => $this->audit()->annee_etablissement(),
            default => null,
        };
    }

    public function label(): ?LabelGenerateur
    {
        return LabelGenerateur::from_enum_type_generateur_ch_id($this->enum_type_generateur_ch_id());
    }

    public function pn_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id()) {
            2, 3, 4, 5, 6 => $this->pn(),
            default => null,
        };
    }

    public function rpn_saisi(): ?Pourcentage
    {
        return match ($this->enum_methode_saisie_carac_sys_id()) {
            2, 3, 4, 5, 6 => $this->rpn(),
            default => null,
        };
    }

    public function rpint_saisi(): ?Pourcentage
    {
        return match ($this->enum_methode_saisie_carac_sys_id()) {
            2, 3, 4, 5, 6 => $this->rpint(),
            default => null,
        };
    }

    public function qp0_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id()) {
            2, 3, 4, 5, 6 => $this->qp0(),
            default => null,
        };
    }

    public function tfonc30_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id()) {
            2, 3, 4, 5, 6 => $this->temp_fonc_30(),
            default => null,
        };
    }

    public function tfonc100_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id()) {
            2, 3, 4, 5, 6 => $this->temp_fonc_100(),
            default => null,
        };
    }

    public function pveilleuse_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id()) {
            2, 3, 4, 5, 6 => $this->pveilleuse(),
            default => null,
        };
    }

    public function scop_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id()) {
            2, 3, 4, 5, 6 => $this->scop(),
            default => null,
        };
    }

    public function enum_type_generateur_ch_id(): int
    {
        return $this->findOneOrError('.//enum_type_generateur_ch_id')->intval();
    }

    public function enum_usage_generateur_id(): int
    {
        return $this->findOneOrError('.//enum_usage_generateur_id')->intval();
    }

    public function enum_type_energie_id(): int
    {
        return $this->findOneOrError('.//enum_type_energie_id')->intval();
    }

    public function enum_lien_generateur_emetteur_id(): int
    {
        return $this->findOneOrError('.//enum_lien_generateur_emetteur_id')->intval();
    }

    public function enum_methode_saisie_carac_sys_id(): int
    {
        return $this->findOneOrError('.//enum_methode_saisie_carac_sys_id')->intval();
    }

    public function surface_chauffee(): float
    {
        return $this->findOneOrError('.//surface_chauffee')->floatval();
    }

    public function position_volume_chauffe(): bool
    {
        return $this->findOneOrError('.//position_volume_chauffe')->boolval();
    }

    public function presence_ventouse(): ?bool
    {
        return $this->findOne('.//presence_ventouse')?->boolval();
    }

    public function presence_regulation_combustion(): ?bool
    {
        return $this->findOne('.//presence_regulation_combustion')?->boolval();
    }

    public function priorite_cascade(): ?bool
    {
        return $this->findOne('.//priorite_generateur_cascade')?->intval();
    }

    public function n_radiateurs_gaz(): ?int
    {
        return $this->findOne('.//n_radiateurs_gaz')?->intval();
    }

    // Données intermédiaires

    public function scop(): ?float
    {
        return $this->findOne('.//scop')?->floatval();
    }

    public function pn(): ?float
    {
        return ($value = $this->findOne('.//pn')?->floatval()) ? $value / 1000 : null;
    }

    public function qp0(): ?float
    {
        return $this->findOne('.//qp0')?->floatval();
    }

    public function pveilleuse(): ?float
    {
        return $this->findOne('.//pveilleuse')?->floatval();
    }

    public function temp_fonc_30(): ?float
    {
        return $this->findOne('.//temp_fonc_30')?->floatval();
    }

    public function temp_fonc_100(): ?float
    {
        return $this->findOne('.//temp_fonc_100')?->floatval();
    }

    public function rpn(): ?Pourcentage
    {
        if (null === $value = $this->findOne('.//rpn')?->floatval()) {
            return null;
        }
        return $value <= 2 ? Pourcentage::from($value * 100) : Pourcentage::from($value);
    }

    public function rpint(): ?Pourcentage
    {
        if (null === $value = $this->findOne('.//rpint')?->floatval()) {
            return null;
        }
        return $value <= 2 ? Pourcentage::from($value * 100) : Pourcentage::from($value);
    }

    public function rendement_generation(): ?float
    {
        return $this->findOne('.//rendement_generation')?->floatval();
    }
}
