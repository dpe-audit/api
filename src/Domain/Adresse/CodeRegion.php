<?php

namespace App\Domain\Adresse;

enum CodeRegion: string
{
    case GUADELOUPE = '01';
    case MARTINIQUE = '02';
    case GUYANE = '03';
    case LA_REUNION = '04';
    case MAYOTTE = '06';
    case ILE_DE_FRANCE = '11';
    case CENTRE_VAL_DE_LOIRE = '24';
    case BOURGOGNE_FRANCHE_COMTE = '27';
    case NORMANDIE = '28';
    case HAUTS_DE_FRANCE = '32';
    case GRAND_EST = '44';
    case PAYS_DE_LA_LOIRE = '52';
    case BRETAGNE = '53';
    case NOUVELLE_AQUITAINE = '75';
    case OCCITANIE = '76';
    case AUVERGNE_RHONE_ALPES = '84';
    case PROVENCE_ALPES_COTE_D_AZUR = '93';
    case CORSE = '94';

    /**
     * @return self[]
     */
    public static function cases_outre_mer(): array
    {
        return [
            self::GUADELOUPE,
            self::MARTINIQUE,
            self::GUYANE,
            self::LA_REUNION,
            self::MAYOTTE,
        ];
    }
}
