<?php

namespace App\Domain\Eclairage;

use Webmozart\Assert\Assert;

final class EclairageData
{
    public function __construct(
        public readonly ?float $cef_ecl,
        public readonly ?float $cep_ecl,
        public readonly ?float $eges_ecl,
    ) {}

    public static function create(
        ?float $cef_ecl = null,
        ?float $cep_ecl = null,
        ?float $eges_ecl = null,
    ): self {
        Assert::nullOrGreaterThanEq($cef_ecl, 0);
        Assert::nullOrGreaterThanEq($cep_ecl, 0);
        Assert::nullOrGreaterThanEq($eges_ecl, 0);
        return new self(
            cef_ecl: $cef_ecl,
            cep_ecl: $cep_ecl,
            eges_ecl: $eges_ecl,
        );
    }

    public function with(
        ?float $cef_ecl = null,
        ?float $cep_ecl = null,
        ?float $eges_ecl = null,
    ): self {
        return self::create(
            cef_ecl: $cef_ecl ?? $this->cef_ecl,
            cep_ecl: $cep_ecl ?? $this->cep_ecl,
            eges_ecl: $eges_ecl ?? $this->eges_ecl,
        );
    }
}
