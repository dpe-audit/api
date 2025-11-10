<?php

namespace App\Domain\Chauffage;

use App\Domain\Common\Consommation\ConsommationCollection;
use Webmozart\Assert\Assert;

final class ChauffageData
{
    public function __construct(
        public readonly ?float $bch,
        public readonly ?float $pertes_generation,
        public readonly ?float $pertes_generation_recuperables,
        public readonly ?ConsommationCollection $consommations,
    ) {}

    public static function create(
        ?float $bch = null,
        ?float $pertes_generation = null,
        ?float $pertes_generation_recuperables = null,
        ?ConsommationCollection $consommations = null,
    ): self {
        Assert::nullOrGreaterThanEq($bch, 0);
        Assert::nullOrGreaterThanEq($pertes_generation, 0);
        Assert::nullOrGreaterThanEq($pertes_generation_recuperables, 0);

        return new self(
            bch: $bch,
            pertes_generation: $pertes_generation,
            pertes_generation_recuperables: $pertes_generation_recuperables,
            consommations: $consommations,
        );
    }

    public function with(
        ?float $bch = null,
        ?float $pertes_generation = null,
        ?float $pertes_generation_recuperables = null,
        ?ConsommationCollection $consommations = null,
    ): self {
        return self::create(
            bch: $bch ?? $this->bch,
            pertes_generation: $pertes_generation ?? $this->pertes_generation,
            pertes_generation_recuperables: $pertes_generation_recuperables ?? $this->pertes_generation_recuperables,
            consommations: $consommations ?? $this->consommations,
        );
    }
}
