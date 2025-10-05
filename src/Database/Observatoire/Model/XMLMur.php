<?php

namespace App\Database\Observatoire\Model;

use App\Domain\Enveloppe\Paroi\Inertie;
use App\Domain\Enveloppe\Mur\TypeDoublage;
use App\Domain\Enveloppe\Mur\TypeMur;

final class XMLMur extends XMLParoiOpaque
{
    public function __construct(
        public readonly int $enum_orientation_id,
        public readonly ?float $surface_paroi_totale,
        public readonly bool $enduit_isolant_paroi_ancienne,

        public readonly ?float $umur0_saisi,
        public readonly ?float $umur_saisi,
        public readonly ?int $tv_umur0_id,
        public readonly ?float $epaisseur_structure,
        public readonly int $enum_materiaux_structure_mur_id,
        public readonly int $enum_type_doublage_id,
        public readonly ?int $tv_umur_id,
        public readonly int $enum_methode_saisie_u_id,

        public readonly float $umur,
        public readonly ?float $umur0
    ) {}

    /**
     * XSD logement/enveloppe/mur_collection/mur
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return (new self(
            enum_orientation_id: (int) $xml->donnee_entree->enum_orientation_id,
            surface_paroi_totale: (float) $xml->donnee_entree->surface_paroi_totale ?: null,
            enduit_isolant_paroi_ancienne: (bool) $xml->donnee_entree->enduit_isolant_paroi_ancienne,
            umur0_saisi: (float) $xml->donnee_entree->umur0_saisi ?: null,
            umur_saisi: (float) $xml->donnee_entree->umur_saisi ?: null,
            tv_umur0_id: (int) $xml->donnee_entree->tv_umur0_id ?: null,
            epaisseur_structure: (float) $xml->donnee_entree->epaisseur_structure ?: null,
            enum_materiaux_structure_mur_id: (int) $xml->donnee_entree->enum_materiaux_structure_mur_id,
            enum_type_doublage_id: (int) $xml->donnee_entree->enum_type_doublage_id,
            tv_umur_id: (int) $xml->donnee_entree->tv_umur_id ?: null,
            enum_methode_saisie_u_id: (int) $xml->donnee_entree->enum_methode_saisie_u_id,

            umur: (float) $xml->donnee_intermediaire->umur,
            umur0: (float) $xml->donnee_intermediaire->umur0 ?: null
        ))->set($xml);
    }

    /**
     * XSD logement/enveloppe/mur_collection
     * 
     * @return array<self>
     */
    public static function from_collection(\SimpleXMLElement $xml): array
    {
        $collection = [];

        foreach ($xml->mur as $item) {
            $collection[] = self::from($item);
        }
        return $collection;
    }

    public function type_structure(): ?TypeMur
    {
        return match ($this->enum_materiaux_structure_mur_id) {
            2 => TypeMur::PIERRE_MOELLONS,
            3 => TypeMur::PIERRE_MOELLONS_AVEC_REMPLISSAGE,
            4 => TypeMur::PISE_OU_BETON_TERRE,
            5 => TypeMur::PAN_BOIS_SANS_REMPLISSAGE,
            6 => TypeMur::PAN_BOIS_AVEC_REMPLISSAGE,
            7 => TypeMur::BOIS_RONDIN,
            8 => TypeMur::BRIQUE_PLEINE_SIMPLE,
            9 => TypeMur::BRIQUE_PLEINE_DOUBLE_AVEC_LAME_AIR,
            10 => TypeMur::BRIQUE_CREUSE,
            11 => TypeMur::BLOC_BETON_PLEIN,
            12 => TypeMur::BLOC_BETON_CREUX,
            13 => TypeMur::BETON_BANCHE,
            14 => TypeMur::BETON_MACHEFER,
            15 => TypeMur::BRIQUE_TERRE_CUITE_ALVEOLAIRE,
            16 => TypeMur::BETON_CELLULAIRE,
            17 => TypeMur::BETON_CELLULAIRE,
            18 => TypeMur::OSSATURE_BOIS_AVEC_REMPLISSAGE_ISOLANT,
            19 => TypeMur::SANDWICH_BETON_ISOLANT_BETON_SANS_ISOLATION_RAPPORTEE,
            20 => TypeMur::CLOISON_PLATRE,
            24 => TypeMur::OSSATURE_BOIS_AVEC_REMPLISSAGE_ISOLANT,
            25 => TypeMur::OSSATURE_BOIS_SANS_REMPLISSAGE,
            26 => TypeMur::OSSATURE_BOIS_AVEC_REMPLISSAGE_ISOLANT,
            27 => TypeMur::OSSATURE_BOIS_AVEC_REMPLISSAGE_TOUT_VENANT,
            default => null,
        };
    }

    public function type_doublage(): ?TypeDoublage
    {
        return match ($this->enum_type_doublage_id) {
            2 => TypeDoublage::SANS_DOUBLAGE,
            3 => TypeDoublage::LAME_AIR_INFERIEUR_15MM,
            4 => TypeDoublage::LAME_AIR_SUPERIEUR_15MM,
            5 => TypeDoublage::MATERIAUX_CONNU,
            default => null,
        };
    }

    public function inertie(XMLRessource $ressource): Inertie
    {
        return $ressource->logement()->enveloppe->inertie_plancher_bas_lourd
            ? Inertie::LOURDE
            : Inertie::LEGERE;
    }
}
