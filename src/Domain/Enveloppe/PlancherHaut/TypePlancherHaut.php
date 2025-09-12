<?php

namespace App\Domain\Enveloppe\PlancherHaut;

enum TypePlancherHaut: string
{
    case PLAFOND_AVEC_OU_SANS_REMPLISSAGE = 'plafond_avec_ou_sans_remplissage';
    case PLAFOND_ENTRE_SOLIVES_METALLIQUES = 'plafond_entre_solives_metalliques';
    case PLAFOND_ENTRE_SOLIVES_BOIS = 'plafond_entre_solives_bois';
    case PLAFOND_BOIS_SUR_SOLIVES_METALLIQUES = 'plafond_bois_sur_solives_metalliques';
    case PLAFOND_BOIS_SOUS_SOLIVES_METALLIQUES = 'plafond_bois_sous_solives_metalliques';
    case BARDEAUX_ET_REMPLISSAGE = 'bardeaux_et_remplissage';
    case PLAFOND_BOIS_SUR_SOLIVES_BOIS = 'plafond_bois_sur_solives_bois';
    case PLAFOND_BOIS_SOUS_SOLIVES_BOIS = 'plafond_bois_sous_solives_bois';
    case DALLE_BETON = 'dalle_beton';
    case PLAFOND_LOURD = 'plafond_lourd';
    case COMBLES_AMENAGES_SOUS_RAMPANT = 'combles_amenages_sous_rampant';
    case TOITURE_CHAUME = 'toiture_chaume';
    case PLAFOND_PATRE = 'plafond_patre';
    case BAC_ACIER = 'bac_acier';

    public function pont_thermique_negligeable(): bool
    {
        return \in_array($this, [
            self::PLAFOND_AVEC_OU_SANS_REMPLISSAGE,
            self::PLAFOND_ENTRE_SOLIVES_METALLIQUES,
            self::PLAFOND_ENTRE_SOLIVES_BOIS,
            self::PLAFOND_BOIS_SUR_SOLIVES_METALLIQUES,
            self::PLAFOND_BOIS_SOUS_SOLIVES_METALLIQUES,
            self::BARDEAUX_ET_REMPLISSAGE,
            self::PLAFOND_BOIS_SUR_SOLIVES_BOIS,
            self::PLAFOND_BOIS_SOUS_SOLIVES_BOIS,
            self::COMBLES_AMENAGES_SOUS_RAMPANT,
            self::TOITURE_CHAUME,
            self::PLAFOND_PATRE,
        ]);
    }
}
