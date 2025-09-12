<?php

namespace App\Domain\Enveloppe\Lnc;

enum TypeLnc: string
{
    case GARAGE = 'garage';
    case CELLIER = 'cellier';
    case ESPACE_TAMPON_SOLARISE = 'espace_tampon_solarise';
    case COMBLE_FORTEMENT_VENTILE = 'comble_fortement_ventile';
    case COMBLE_FAIBLEMENT_VENTILE = 'comble_faiblement_ventile';
    case COMBLE_TRES_FAIBLEMENT_VENTILE = 'comble_tres_faiblement_ventile';
    case CIRCULATION_SANS_OUVERTURE_EXTERIEURE = 'circulation_sans_ouverture_exterieure';
    case CIRCULATION_AVEC_OUVERTURE_EXTERIEURE = 'circulation_avec_ouverture_exterieure';
    case CIRCULATION_AVEC_BOUCHE_OU_GAINE_DESENFUMAGE_OUVERTE = 'circulation_avec_bouche_ou_gaine_desenfumage_ouverte';
    case HALL_ENTREE_AVEC_FERMETURE_AUTOMATIQUE = 'hall_entree_avec_fermeture_automatique';
    case HALL_ENTREE_SANS_FERMETURE_AUTOMATIQUE = 'hall_entree_sans_fermeture_automatique';
    case GARAGE_COLLECTIF = 'garage_collectif';
    case AUTRES = 'autres';

    public function is_ets(): bool
    {
        return $this === self::ESPACE_TAMPON_SOLARISE;
    }

    public function is_combles(): bool
    {
        return \in_array($this, [
            self::COMBLE_FORTEMENT_VENTILE,
            self::COMBLE_FAIBLEMENT_VENTILE,
            self::COMBLE_TRES_FAIBLEMENT_VENTILE
        ]);
    }
}
