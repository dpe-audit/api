<?php

namespace App\Domain\Enveloppe\Mur;

enum TypeMur: string
{
    case PIERRE_MOELLONS = 'pierre_moellons';
    case PIERRE_MOELLONS_AVEC_REMPLISSAGE = 'pierre_moellons_avec_remplissage';
    case PISE_OU_BETON_TERRE = 'pise_ou_beton_terre';
    case PAN_BOIS_SANS_REMPLISSAGE = 'pan_bois_sans_remplissage';
    case PAN_BOIS_AVEC_REMPLISSAGE = 'pan_bois_avec_remplissage';
    case BOIS_RONDIN = 'bois_rondin';
    case BRIQUE_PLEINE_SIMPLE = 'brique_pleine_simple';
    case BRIQUE_PLEINE_DOUBLE_AVEC_LAME_AIR = 'brique_pleine_double_avec_lame_air';
    case BRIQUE_CREUSE = 'brique_creuse';
    case BLOC_BETON_PLEIN = 'bloc_beton_plein';
    case BLOC_BETON_CREUX = 'bloc_beton_creux';
    case BETON_BANCHE = 'beton_banche';
    case BETON_MACHEFER = 'beton_machefer';
    case BRIQUE_TERRE_CUITE_ALVEOLAIRE = 'brique_terre_cuite_alveolaire';
    case SANDWICH_BETON_ISOLANT_BETON_SANS_ISOLATION_RAPPORTEE = 'sandwich_beton_isolant_beton_sans_isolation_rapportee';
    case CLOISON_PLATRE = 'cloison_platre';
    case OSSATURE_BOIS_SANS_REMPLISSAGE = 'ossature_bois_sans_remplissage';
    case OSSATURE_BOIS_AVEC_REMPLISSAGE_TOUT_VENANT = 'ossature_bois_avec_remplissage_tout_venant';
    case OSSATURE_BOIS_AVEC_REMPLISSAGE_ISOLANT = 'ossature_bois_avec_remplissage_isolant';
    case BETON_CELLULAIRE = 'beton_cellulaire';

    public function epaisseur_structure(): ?int
    {
        return match ($this) {
            self::PIERRE_MOELLONS => 20,
            self::PIERRE_MOELLONS_AVEC_REMPLISSAGE => 50,
            self::PISE_OU_BETON_TERRE => 40,
            self::PAN_BOIS_SANS_REMPLISSAGE => 8,
            self::PAN_BOIS_AVEC_REMPLISSAGE => 8,
            self::BOIS_RONDIN => 10,
            self::BRIQUE_PLEINE_SIMPLE => 9,
            self::BRIQUE_PLEINE_DOUBLE_AVEC_LAME_AIR => 20,
            self::BRIQUE_CREUSE => 15,
            self::BLOC_BETON_PLEIN => 20,
            self::BLOC_BETON_CREUX => 20,
            self::BETON_BANCHE => 20,
            self::BETON_MACHEFER => 20,
            self::BRIQUE_TERRE_CUITE_ALVEOLAIRE => 30,
            self::BETON_CELLULAIRE => 15,
            self::SANDWICH_BETON_ISOLANT_BETON_SANS_ISOLATION_RAPPORTEE => 15,
            self::CLOISON_PLATRE => null,
            self::OSSATURE_BOIS_SANS_REMPLISSAGE => 8,
            self::OSSATURE_BOIS_AVEC_REMPLISSAGE_TOUT_VENANT => 8,
            self::OSSATURE_BOIS_AVEC_REMPLISSAGE_ISOLANT => 10,
        };
    }

    public function pont_thermique_negligeable(): bool
    {
        return \in_array($this, [
            self::BOIS_RONDIN,
            self::PAN_BOIS_SANS_REMPLISSAGE,
            self::PAN_BOIS_AVEC_REMPLISSAGE,
            self::OSSATURE_BOIS_AVEC_REMPLISSAGE_ISOLANT,
            self::OSSATURE_BOIS_AVEC_REMPLISSAGE_TOUT_VENANT,
            self::OSSATURE_BOIS_SANS_REMPLISSAGE,
        ]);
    }
}
