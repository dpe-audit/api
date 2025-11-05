<?php

namespace App\Legacy\Model;

abstract class ParoiOpaque extends Paroi
{
    public readonly float $surface_paroi_opaque;
    public readonly ?float $resistance_isolation;
    public readonly ?float $epaisseur_isolation;
    public readonly ?bool $paroi_lourde;
    public readonly int $enum_methode_saisie_u0_id;
    public readonly int $enum_methode_saisie_u_id;
    public readonly int $enum_type_isolation_id;
    public readonly ?int $enum_periode_isolation_id;

    /**
     * XPATH logement/enveloppe/mur_collection/mur
     * XPATH logement/enveloppe/plancher_bas_collection/plancher_bas
     * XPATH logement/enveloppe/plancher_haut_collection/plancher_haut
     * XPATH logement[caracteristique_generale/enum_scenario_id="0"]/enveloppe/mur_collection/mur
     * XPATH logement[caracteristique_generale/enum_scenario_id="0"]/enveloppe/plancher_bas_collection/plancher_bas
     * XPATH logement[caracteristique_generale/enum_scenario_id="0"]/enveloppe/plancher_haut_collection/plancher_haut
     */
    protected function set(\SimpleXMLElement $xml): self
    {
        $this->surface_paroi_opaque = (float) $xml->donnee_entree->surface_paroi_opaque;
        $this->paroi_lourde = (bool)(int) $xml->donnee_entree->paroi_lourde ?: null;
        $this->enum_periode_isolation_id = (int) $xml->donnee_entree->enum_periode_isolation_id ?: null;
        $this->resistance_isolation = (float) $xml->donnee_entree->resistance_isolation ?: null;
        $this->epaisseur_isolation = (float) $xml->donnee_entree->epaisseur_isolation ?: null;
        $this->enum_methode_saisie_u0_id = (int) $xml->donnee_entree->enum_methode_saisie_u0_id;
        $this->enum_methode_saisie_u_id = (int) $xml->donnee_entree->enum_methode_saisie_u_id;
        $this->enum_type_isolation_id = (int) $xml->donnee_entree->enum_type_isolation_id;

        return parent::set($xml);
    }

    public function surface(): float
    {
        return $this->surface_paroi_opaque;
    }
}
