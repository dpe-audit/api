<?php

namespace App\Domain\Common\Perte;

use Webmozart\Assert\Assert;

final class Perte
{
    public function __construct(
        public readonly TypePerte $type,
        public readonly float $pertes,
        public readonly float $pertes_recuperables,
    ) {}

    public static function create(
        TypePerte $type,
        float $pertes,
        float $pertes_recuperables,
    ): self {
        Assert::greaterThanEq($pertes, 0);
        Assert::greaterThanEq($pertes_recuperables, 0);

        return new self(
            type: $type,
            pertes: $pertes,
            pertes_recuperables: $pertes_recuperables,
        );
    }
}
