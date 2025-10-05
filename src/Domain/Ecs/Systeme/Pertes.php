<?php

namespace App\Domain\Ecs\Systeme;

use Webmozart\Assert\Assert;

final class Pertes
{
    public function __construct(
        public readonly ?float $pertes_generation = null,
        public readonly ?float $pertes_generation_recuperables = null,
        public readonly ?float $pertes_stockage_integre = null,
        public readonly ?float $pertes_stockage_integre_recuperables = null,
        public readonly ?float $pertes_stockage_independant = null,
        public readonly ?float $pertes_stockage_independant_recuperables = null,
        public readonly ?float $pertes_distribution = null,
        public readonly ?float $pertes_distribution_recuperables = null,
    ) {}

    public static function create(
        ?float $pertes_generation = null,
        ?float $pertes_generation_recuperables = null,
        ?float $pertes_stockage_integre = null,
        ?float $pertes_stockage_integre_recuperables = null,
        ?float $pertes_stockage_independant = null,
        ?float $pertes_stockage_independant_recuperables = null,
        ?float $pertes_distribution = null,
        ?float $pertes_distribution_recuperables = null,
    ): self {
        Assert::nullOrGreaterThanEq($pertes_generation, 0);
        Assert::nullOrGreaterThanEq($pertes_generation_recuperables, 0);
        Assert::nullOrGreaterThanEq($pertes_stockage_integre, 0);
        Assert::nullOrGreaterThanEq($pertes_stockage_integre_recuperables, 0);
        Assert::nullOrGreaterThanEq($pertes_stockage_independant, 0);
        Assert::nullOrGreaterThanEq($pertes_stockage_independant_recuperables, 0);
        Assert::nullOrGreaterThanEq($pertes_distribution, 0);
        Assert::nullOrGreaterThanEq($pertes_distribution_recuperables, 0);

        return new self(
            pertes_generation: $pertes_generation,
            pertes_generation_recuperables: $pertes_generation_recuperables,
            pertes_stockage_integre: $pertes_stockage_integre,
            pertes_stockage_integre_recuperables: $pertes_stockage_integre_recuperables,
            pertes_stockage_independant: $pertes_stockage_independant,
            pertes_stockage_independant_recuperables: $pertes_stockage_independant_recuperables,
            pertes_distribution: $pertes_distribution,
            pertes_distribution_recuperables: $pertes_distribution_recuperables,
        );
    }

    public function with(
        ?float $pertes_generation = null,
        ?float $pertes_generation_recuperables = null,
        ?float $pertes_stockage_integre = null,
        ?float $pertes_stockage_integre_recuperables = null,
        ?float $pertes_stockage_independant = null,
        ?float $pertes_stockage_independant_recuperables = null,
        ?float $pertes_distribution = null,
        ?float $pertes_distribution_recuperables = null,
    ): self {
        return self::create(
            pertes_generation: $pertes_generation ?? $this->pertes_generation,
            pertes_generation_recuperables: $pertes_generation_recuperables ?? $this->pertes_generation_recuperables,
            pertes_stockage_integre: $pertes_stockage_integre ?? $this->pertes_stockage_integre,
            pertes_stockage_integre_recuperables: $pertes_stockage_integre_recuperables ?? $this->pertes_stockage_integre_recuperables,
            pertes_stockage_independant: $pertes_stockage_independant ?? $this->pertes_stockage_independant,
            pertes_stockage_independant_recuperables: $pertes_stockage_independant_recuperables ?? $this->pertes_stockage_independant_recuperables,
            pertes_distribution: $pertes_distribution ?? $this->pertes_distribution,
            pertes_distribution_recuperables: $pertes_distribution_recuperables ?? $this->pertes_distribution_recuperables,
        );
    }
}
