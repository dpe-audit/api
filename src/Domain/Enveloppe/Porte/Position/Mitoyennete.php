<?php

namespace App\Domain\Enveloppe\Porte\Position;

enum Mitoyennete: string
{
    case EXTERIEUR = 'exterieur';
    case ENTERRE = 'enterre';
    case VIDE_SANITAIRE = 'vide_sanitaire';
    case TERRE_PLEIN = 'terre_plein';
    case SOUS_SOL_NON_CHAUFFE = 'sous_sol_non_chauffe';
    case LOCAL_NON_CHAUFFE = 'local_non_chauffe';
    case LOCAL_NON_RESIDENTIEL = 'local_non_residentiel';
    case LOCAL_RESIDENTIEL = 'local_residentiel';
    case LOCAL_NON_ACCESSIBLE = 'local_non_accessible';

    public static function from_type_adjacence_id(int $id): self
    {
        return match ($id) {
            1 => self::EXTERIEUR,
            2 => self::ENTERRE,
            3 => self::VIDE_SANITAIRE,
            4 => self::LOCAL_NON_RESIDENTIEL,
            5 => self::TERRE_PLEIN,
            6 => self::SOUS_SOL_NON_CHAUFFE,
            7 => self::LOCAL_NON_ACCESSIBLE,
            8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 21 => self::LOCAL_NON_CHAUFFE,
            20 => self::LOCAL_NON_RESIDENTIEL,
            22 => self::LOCAL_RESIDENTIEL,
        };
    }
}
