<?php

namespace App\Domain\Ventilation;

use App\Domain\Common\Consommation\ConsommationCollection;

final class VentilationData
{
    public function __construct(
        public readonly ?ConsommationCollection $consommations,
    ) {}

    public static function create(?ConsommationCollection $consommations = null): self
    {
        return new self(consommations: $consommations);
    }

    public function with(?ConsommationCollection $consommations = null): self
    {
        return self::create(consommations: $consommations ?? $this->consommations);
    }
}
