<?php

namespace App\Domain\Chauffage\Generateur;

use Webmozart\Assert\Assert;

final class Pertes
{
    public function __construct(
        public readonly ?float $pertes_generation = null,
        public readonly ?float $pertes_generation_recuperables = null,
    ) {}

    public static function create(
        ?float $pertes_generation = null,
        ?float $pertes_generation_recuperables = null,
    ): self {
        Assert::nullOrGreaterThanEq($pertes_generation, 0);
        Assert::nullOrGreaterThanEq($pertes_generation_recuperables, 0);

        return new self(
            pertes_generation: $pertes_generation,
            pertes_generation_recuperables: $pertes_generation_recuperables,
        );
    }

    public function with(
        ?float $pertes_generation = null,
        ?float $pertes_generation_recuperables = null,
    ): self {
        return self::create(
            pertes_generation: $pertes_generation ?? $this->pertes_generation,
            pertes_generation_recuperables: $pertes_generation_recuperables ?? $this->pertes_generation_recuperables,
        );
    }
}
