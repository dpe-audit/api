<?php

namespace App\Domain\Chauffage\Generateur;

enum TypeGenerateur: string
{
    case CHAUDIERE = 'chaudiere';
    case POELE_BOUILLEUR = 'poele_bouilleur';
    case CONVECTEUR_BI_JONCTION = 'convecteur_bi_jonction';
    case CONVECTEUR_ELECTRIQUE = 'convecteur_electrique';
    case PANNEAU_RAYONNANT_ELECTRIQUE = 'panneau_rayonnant_electrique';
    case PLAFOND_RAYONNANT_ELECTRIQUE = 'plafond_rayonnant_electrique';
    case PLANCHER_RAYONNANT_ELECTRIQUE = 'plancher_rayonnant_electrique';
    case RADIATEUR_ELECTRIQUE = 'radiateur_electrique';
    case RADIATEUR_ELECTRIQUE_ACCUMULATION = 'radiateur_electrique_accumulation';
    case GENERATEUR_AIR_CHAUD = 'generateur_air_chaud';
    case PAC_AIR_AIR = 'pac_air_air';
    case PAC_AIR_EAU = 'pac_air_eau';
    case PAC_EAU_EAU = 'pac_eau_eau';
    case PAC_EAU_GLYCOLEE_EAU = 'pac_eau_glycolee_eau';
    case PAC_GEOTHERMIQUE = 'pac_geothermique';
    case CUISINIERE = 'cuisiniere';
    case FOYER_FERME = 'foyer_ferme';
    case INSERT = 'insert';
    case POELE = 'poele';
    case RADIATEUR_GAZ = 'radiateur_gaz';
    case RESEAU_CHALEUR = 'reseau_chaleur';

    public function is_chaudiere(): bool
    {
        return $this === self::CHAUDIERE;
    }

    public function is_generateur_air_chaud(): bool
    {
        return $this === self::GENERATEUR_AIR_CHAUD;
    }

    public function is_emetteur_electrique(): bool
    {
        return \in_array($this, [
            self::CONVECTEUR_BI_JONCTION,
            self::CONVECTEUR_ELECTRIQUE,
            self::PANNEAU_RAYONNANT_ELECTRIQUE,
            self::PLAFOND_RAYONNANT_ELECTRIQUE,
            self::PLANCHER_RAYONNANT_ELECTRIQUE,
            self::RADIATEUR_ELECTRIQUE,
            self::RADIATEUR_ELECTRIQUE_ACCUMULATION,
        ]);
    }

    public function is_pac(): bool
    {
        return \in_array($this, [
            self::PAC_AIR_AIR,
            self::PAC_AIR_EAU,
            self::PAC_EAU_EAU,
            self::PAC_EAU_GLYCOLEE_EAU,
            self::PAC_GEOTHERMIQUE,
        ]);
    }

    public function is_poele_insert(): bool
    {
        return \in_array($this, [
            self::CUISINIERE,
            self::FOYER_FERME,
            self::INSERT,
            self::POELE,
        ]);
    }

    public function is_poele_bouilleur(): bool
    {
        return $this === self::POELE_BOUILLEUR;
    }

    public function is_radiateur_gaz(): bool
    {
        return $this === self::RADIATEUR_GAZ;
    }

    public function is_reseau_chaleur(): bool
    {
        return $this === self::RESEAU_CHALEUR;
    }

    public function is_chauffage_central(): bool
    {
        return \in_array($this, [
            self::CHAUDIERE,
            self::GENERATEUR_AIR_CHAUD,
            self::PAC_AIR_AIR,
            self::PAC_AIR_EAU,
            self::PAC_EAU_EAU,
            self::PAC_EAU_GLYCOLEE_EAU,
            self::PAC_GEOTHERMIQUE,
            self::POELE_BOUILLEUR,
            self::RESEAU_CHALEUR,
        ]);
    }

    public function is_chauffage_divise(): bool
    {
        return \in_array($this, [
            self::CONVECTEUR_BI_JONCTION,
            self::CONVECTEUR_ELECTRIQUE,
            self::GENERATEUR_AIR_CHAUD,
            self::PANNEAU_RAYONNANT_ELECTRIQUE,
            self::PLAFOND_RAYONNANT_ELECTRIQUE,
            self::PLANCHER_RAYONNANT_ELECTRIQUE,
            self::RADIATEUR_ELECTRIQUE,
            self::RADIATEUR_ELECTRIQUE_ACCUMULATION,
            self::PAC_AIR_AIR,
            self::CUISINIERE,
            self::FOYER_FERME,
            self::INSERT,
            self::POELE,
            self::RADIATEUR_GAZ,
        ]);
    }
}
