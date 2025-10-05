<?php

namespace App\Domain\Enveloppe\Paroi;

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
}
