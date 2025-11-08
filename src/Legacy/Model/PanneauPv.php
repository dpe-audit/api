<?php

namespace App\Legacy\Model;

final class PanneauPv
{
    use WithId;

    public function __construct(
        public readonly ?float $surface_totale_capteurs,
        public readonly ?float $ratio_virtualisation,
        public readonly ?int $nombre_module,
        public readonly ?int $enum_orientation_pv_id,
        public readonly ?int $enum_inclinaison_pv_id,
        public readonly ?int $tv_coef_orientation_pv_id,
    ) {}

    /**
     * XPATH //production_elec_enr/panneaux_pv_collection/panneaux_pv
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            surface_totale_capteurs: (float) $xml->surface_totale_capteurs ?: null,
            ratio_virtualisation: (float) $xml->ratio_virtualisation ?: null,
            nombre_module: (int) $xml->nombre_module ?: null,
            enum_orientation_pv_id: (int) $xml->enum_orientation_pv_id ?: null,
            enum_inclinaison_pv_id: (int) $xml->enum_inclinaison_pv_id ?: null,
            tv_coef_orientation_pv_id: (int) $xml->tv_coef_orientation_pv_id ?: null,
        );
    }
}
