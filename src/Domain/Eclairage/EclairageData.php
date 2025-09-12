<?php

namespace App\Domain\Eclairage;

use Webmozart\Assert\Assert;

final class EclairageData
{
    public function __construct(
        public readonly ?float $cef,
        public readonly ?float $cep,
        public readonly ?float $eges,
    ) {}

    public static function create(
        ?float $cef = null,
        ?float $cep = null,
        ?float $eges = null,
    ): self {
        Assert::nullOrGreaterThanEq($cef, 0);
        Assert::nullOrGreaterThanEq($cep, 0);
        Assert::nullOrGreaterThanEq($eges, 0);
        return new self(cef: $cef, cep: $cep, eges: $eges);
    }

    public function with(
        ?float $cef = null,
        ?float $cep = null,
        ?float $eges = null,
    ): self {
        return self::create(
            cef: $cef ?? $this->cef,
            cep: $cep ?? $this->cep,
            eges: $eges ?? $this->eges
        );
    }
}
