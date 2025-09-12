<?php

namespace App\Domain\Enveloppe\PlancherBas;

enum TypePlancherBas: string
{
    case PLANCHER_AVEC_OU_SANS_REMPLISSAGE = 'plancher_avec_ou_sans_remplissage';
    case PLANCHER_ENTRE_SOLIVES_METALLIQUES = 'plancher_entre_solives_metalliques';
    case PLANCHER_ENTRE_SOLIVES_BOIS = 'plancher_entre_solives_bois';
    case PLANCHER_BOIS_SUR_SOLIVES_METALLIQUES = 'plancher_bois_sur_solives_metalliques';
    case BARDEAUX_ET_REMPLISSAGE = 'bardeaux_et_remplissage';
    case VOUTAINS_SUR_SOLIVES_METALLIQUES = 'voutains_sur_solives_metalliques';
    case VOUTAINS_BRIQUES_OU_MOELLONS = 'voutains_briques_ou_moellons';
    case DALLE_BETON = 'dalle_beton';
    case PLANCHER_BOIS_SUR_SOLIVES_BOIS = 'plancher_bois_sur_solives_bois';
    case PLANCHER_LOURD_TYPE_ENTREVOUS_TERRE_CUITE_OU_POUTRELLES_BETON = 'plancher_lourd_type_entrevous_terre_cuite_ou_poutrelles_beton';
    case PLANCHER_ENTREVOUS_ISOLANT = 'plancher_entrevous_isolant';

    public function pont_thermique_negligeable(): bool
    {
        return \in_array($this, [
            TypePlancherBas::BARDEAUX_ET_REMPLISSAGE,
            TypePlancherBas::PLANCHER_BOIS_SUR_SOLIVES_BOIS,
            TypePlancherBas::PLANCHER_BOIS_SUR_SOLIVES_METALLIQUES,
        ]);
    }
}
