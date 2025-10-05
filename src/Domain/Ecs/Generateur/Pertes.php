<?php

namespace App\Domain\Ecs\Generateur;

use Webmozart\Assert\Assert;

final class Pertes
{
    public function __construct(
        public readonly ?float $pertes_generation = null,
        public readonly ?float $pertes_generation_recuperables = null,
        public readonly ?float $pertes_stockage_integre = null,
        public readonly ?float $pertes_stockage_integre_recuperables = null,
    ) {}

    public static function create(
        ?float $pertes_generation = null,
        ?float $pertes_generation_recuperables = null,
        ?float $pertes_stockage_integre = null,
        ?float $pertes_stockage_integre_recuperables = null,
    ): self {
        Assert::nullOrGreaterThanEq($pertes_generation, 0);
        Assert::nullOrGreaterThanEq($pertes_generation_recuperables, 0);
        Assert::nullOrGreaterThanEq($pertes_stockage_integre, 0);
        Assert::nullOrGreaterThanEq($pertes_stockage_integre_recuperables, 0);

        return new self(
            pertes_generation: $pertes_generation,
            pertes_generation_recuperables: $pertes_generation_recuperables,
            pertes_stockage_integre: $pertes_stockage_integre,
            pertes_stockage_integre_recuperables: $pertes_stockage_integre_recuperables,
        );
    }

    public function with(
        ?float $pertes_generation = null,
        ?float $pertes_generation_recuperables = null,
        ?float $pertes_stockage_integre = null,
        ?float $pertes_stockage_integre_recuperables = null,
    ): self {
        return self::create(
            pertes_generation: $pertes_generation ?? $this->pertes_generation,
            pertes_generation_recuperables: $pertes_generation_recuperables ?? $this->pertes_generation_recuperables,
            pertes_stockage_integre: $pertes_stockage_integre ?? $this->pertes_stockage_integre,
            pertes_stockage_integre_recuperables: $pertes_stockage_integre_recuperables ?? $this->pertes_stockage_integre_recuperables,
        );
    }
}
