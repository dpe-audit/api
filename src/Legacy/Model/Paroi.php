<?php

namespace App\Legacy\Model;

use App\Legacy\Utils\Normalizer;

abstract class Paroi
{
    use WithId, WithDescription;

    public readonly string $reference;
    public readonly ?string $reference_lnc;
    public readonly ?string $description;
    public readonly ?float $surface_aiu;
    public readonly ?float $surface_aue;
    public readonly int $enum_type_adjacence_id;
    public readonly ?int $enum_cfg_isolation_lnc_id;
    public readonly ?int $tv_coef_reduction_deperdition_id;
    public readonly float $b;

    /**
     * XPATH logement/enveloppe/mur_collection/mur
     * XPATH logement/enveloppe/plancher_bas_collection/plancher_bas
     * XPATH logement/enveloppe/plancher_haut_collection/plancher_haut
     * XPATH logement/enveloppe/baie_vitree_collection/baie_vitree
     * XPATH logement/enveloppe/porte_collection/porte
     * XPATH logement[caracteristique_generale/enum_scenario_id="0"]/enveloppe/mur_collection/mur
     * XPATH logement[caracteristique_generale/enum_scenario_id="0"]/enveloppe/plancher_bas_collection/plancher_bas
     * XPATH logement[caracteristique_generale/enum_scenario_id="0"]/enveloppe/plancher_haut_collection/plancher_haut
     * XPATH logement[caracteristique_generale/enum_scenario_id="0"]/enveloppe/baie_vitree_collection/baie_vitree
     * XPATH logement[caracteristique_generale/enum_scenario_id="0"]/enveloppe/porte_collection/porte
     */
    protected function set(\SimpleXMLElement $xml): self
    {
        $this->reference = Normalizer::referenceval((string) $xml->donnee_entree->reference);
        $this->reference_lnc = Normalizer::referenceval((string) $xml->donnee_entree->reference_lnc);
        $this->description = (string) $xml->donnee_entree->description ?: null;
        $this->surface_aiu = (float) $xml->donnee_entree->surface_aiu ?: null;
        $this->surface_aue = (float) $xml->donnee_entree->surface_aue ?: null;
        $this->enum_type_adjacence_id = (int) $xml->donnee_entree->enum_type_adjacence_id;
        $this->enum_cfg_isolation_lnc_id = (int) $xml->donnee_entree->enum_cfg_isolation_lnc_id ?: null;
        $this->tv_coef_reduction_deperdition_id = (int) $xml->donnee_entree->tv_coef_reduction_deperdition_id ?: null;
        $this->b = (float) $xml->donnee_intermediaire->b;
        return $this;
    }

    abstract public function surface(): float;
}
