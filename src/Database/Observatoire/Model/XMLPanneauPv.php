<?php

namespace App\Database\Observatoire\Model;

final class XMLPanneauPv
{
    use WithId;

    public function __construct(
        public readonly ?float $surface_totale_capteurs,
        public readonly ?float $ratio_virtualisation,
        public readonly ?int $nombre_module,
        public readonly ?int $tv_coef_orientation_pv_id,
        public readonly ?int $enum_orientation_pv_id,
        public readonly ?int $enum_inclinaison_pv_id
    ) {}

    /**
     * XSD logement/production_elec_enr/panneaux_pv_collection/panneaux_pv
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            surface_totale_capteurs: (float) $xml->surface_totale_capteurs ?: null,
            ratio_virtualisation: (float) $xml->ratio_virtualisation ?: null,
            nombre_module: (int) $xml->nombre_module ?: null,
            tv_coef_orientation_pv_id: (int) $xml->tv_coef_orientation_pv_id ?: null,
            enum_orientation_pv_id: (int) $xml->enum_orientation_pv_id ?: null,
            enum_inclinaison_pv_id: (int) $xml->enum_inclinaison_pv_id ?: null
        );
    }

    /**
     * XSD logement/production_elec_enr/panneaux_pv_collection
     */
    public static function from_collection(\SimpleXMLElement $xml): array
    {
        $collection = [];
        foreach ($xml->panneaux_pv as $item) {
            $collection[] = self::from($item);
        }
        return $collection;
    }

    public function description(): string
    {
        return $this->description ?? 'Panneau photovoltaïque non décrit';
    }

    public function orientation(): ?float
    {
        return match ($this->enum_orientation_pv_id) {
            1 => 180,
            2 => 0,
            3 => 90,
            4 => 270,
            default => match ($this->tv_coef_orientation_pv_id) {
                1, 6, 11, 16 => 90,
                2, 7, 12, 17 => 135,
                3, 8, 13, 18 => 180,
                4, 9, 14, 19 => 225,
                5, 10, 15, 20 => 270,
                default => null,
            },
        };
    }

    public function inclinaison(): float
    {
        return match ($this->enum_inclinaison_pv_id) {
            1 => 10,
            2 => 30,
            3 => 60,
            4 => 80,
            default => match ($this->tv_coef_orientation_pv_id) {
                1, 2, 3, 4, 5 => 10,
                6, 7, 8, 9, 10 => 30,
                11, 12, 13, 14, 15 => 60,
                16, 17, 18, 19, 20 => 80,
                default => null,
            },
        };
    }

    public function installation_collective(): bool
    {
        return $this->ratio_virtualisation > 0;
    }

    public function modules(): int
    {
        return $this->nombre_module ?? $this->surface_totale_capteurs > 0 ? 1 : 0;
    }
}
