<?php

namespace App\Domain\Enveloppe\Lnc\Baie;

enum Mitoyennete: string
{
    case EXTERIEUR = 'exterieur';
    case ENTERRE = 'enterre';
    case VIDE_SANITAIRE = 'vide_sanitaire';
    case TERRE_PLEIN = 'terre_plein';
    case LOCAL_NON_CHAUFFE = 'local_non_chauffe';
    case LOCAL_CHAUFFE = 'local_chauffe';
    case LOCAL_NON_ACCESSIBLE = 'local_non_accessible';
}
