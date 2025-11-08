<?php

namespace App\Legacy\Model;

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

    abstract public function surface(): float;
    abstract public function u(): float;

    public function match_reference(string $reference): bool
    {
        return $this->reference === $reference || str_contains($this->reference, $reference);
    }
}
