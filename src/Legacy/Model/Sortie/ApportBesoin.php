<?php

namespace App\Legacy\Model\Sortie;

final class ApportBesoin
{
    public function __construct(
        public readonly float $surface_sud_equivalente,
        public readonly float $apport_solaire_fr,
        public readonly float $apport_interne_fr,
        public readonly float $apport_solaire_ch,
        public readonly float $apport_interne_ch,
        public readonly float $fraction_apport_gratuit_ch,
        public readonly float $fraction_apport_gratuit_depensier_ch,
        public readonly float $pertes_distribution_ecs_recup,
        public readonly float $pertes_distribution_ecs_recup_depensier,
        public readonly float $pertes_stockage_ecs_recup,
        public readonly float $pertes_generateur_ch_recup,
        public readonly float $pertes_generateur_ch_recup_depensier,
        public readonly float $nadeq,
        public readonly float $v40_ecs_journalier,
        public readonly float $v40_ecs_journalier_depensier,
        public readonly float $besoin_ch,
        public readonly float $besoin_ch_depensier,
        public readonly float $besoin_ecs,
        public readonly float $besoin_ecs_depensier,
        public readonly float $besoin_fr,
        public readonly float $besoin_fr_depensier
    ) {}

    /**
     * @param \SimpleXMLElement $xml - XPATH logement/sortie/apport_et_besoin
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            surface_sud_equivalente: (float) $xml->surface_sud_equivalente,
            apport_solaire_fr: (float) $xml->apport_solaire_fr,
            apport_interne_fr: (float) $xml->apport_interne_fr,
            apport_solaire_ch: (float) $xml->apport_solaire_ch,
            apport_interne_ch: (float) $xml->apport_interne_ch,
            fraction_apport_gratuit_ch: (float) $xml->fraction_apport_gratuit_ch,
            fraction_apport_gratuit_depensier_ch: (float) $xml->fraction_apport_gratuit_depensier_ch,
            pertes_distribution_ecs_recup: (float) $xml->pertes_distribution_ecs_recup,
            pertes_distribution_ecs_recup_depensier: (float) $xml->pertes_distribution_ecs_recup_depensier,
            pertes_stockage_ecs_recup: (float) $xml->pertes_stockage_ecs_recup,
            pertes_generateur_ch_recup: (float) $xml->pertes_generateur_ch_recup,
            pertes_generateur_ch_recup_depensier: (float) $xml->pertes_generateur_ch_recup_depensier,
            nadeq: (float) $xml->nadeq,
            v40_ecs_journalier: (float) $xml->v40_ecs_journalier,
            v40_ecs_journalier_depensier: (float) $xml->v40_ecs_journalier_depensier,
            besoin_ch: (float) $xml->besoin_ch,
            besoin_ch_depensier: (float) $xml->besoin_ch_depensier,
            besoin_ecs: (float) $xml->besoin_ecs,
            besoin_ecs_depensier: (float) $xml->besoin_ecs_depensier,
            besoin_fr: (float) $xml->besoin_fr,
            besoin_fr_depensier: (float) $xml->besoin_fr_depensier
        );
    }
}
