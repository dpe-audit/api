<?php

namespace App\Domain\Enveloppe\Baie;

enum TypeFermeture: string
{
    case SANS_FERMETURE = 'sans_fermeture';
    case JALOUSIE_ACCORDEON = 'jalousie_accordeon';
    case FERMETURE_LAMES_ORIENTABLES = 'fermeture_lames_orientables';
    case VENITIENS_EXTERIEURS_METAL = 'venitiens_exterieurs_metal';
    case VOLET_BATTANT_AVEC_AJOURS_FIXES = 'volet_battant_avec_ajours_fixes';
    case PERSIENNES_AVEC_AJOURS_FIXES = 'persiennes_avec_ajours_fixes';
    case FERMETURE_SANS_AJOURS = 'fermeture_sans_ajours';
    case VOLETS_ROULANTS_ALUMINIUM = 'volets_roulants_aluminium';
    case VOLETS_ROULANTS_PVC_BOIS_EPAISSEUR_LTE_12MM = 'volets_roulants_pvc_bois_epaisseur_lte_12mm';
    case VOLETS_ROULANTS_PVC_BOIS_EPAISSEUR_GT_12MM = 'volets_roulants_pvc_bois_epaisseur_gt_12mm';
    case PERSIENNE_COULISSANTE_EPAISSEUR_LTE_22MM = 'persienne_coulissante_epaisseur_lte_22mm';
    case PERSIENNE_COULISSANTE_EPAISSEUR_GT_22MM = 'persienne_coulissante_epaisseur_gt_22mm';
    case VOLET_BATTANT_PVC_BOIS_EPAISSEUR_LTE_22MM = 'volet_battant_pvc_bois_epaisseur_lte_22mm';
    case VOLET_BATTANT_PVC_BOIS_EPAISSEUR_GT_22MM = 'volet_battant_pvc_bois_epaisseur_gt_22mm';
    case FERMETURE_ISOLEE_SANS_AJOURS = 'fermeture_isolee_sans_ajours';
}
