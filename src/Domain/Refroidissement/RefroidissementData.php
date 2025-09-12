<?php

namespace App\Domain\Refroidissement;

use Webmozart\Assert\Assert;

final class RefroidissementData
{
    public function __construct(
        public readonly ?float $bfr,
        public readonly ?float $cef,
        public readonly ?float $cep,
        public readonly ?float $eges,
    ) {}

    public static function create(
        ?float $bfr = null,
        ?float $cef = null,
        ?float $cep = null,
        ?float $eges = null,
    ): self {
        Assert::nullOrGreaterThanEq($bfr, 0);
        Assert::nullOrGreaterThanEq($cef, 0);
        Assert::nullOrGreaterThanEq($cep, 0);
        Assert::nullOrGreaterThanEq($eges, 0);

        return new self(
            bfr: $bfr,
            cef: $cef,
            cep: $cep,
            eges: $eges,
        );
    }

    public function with(
        ?float $bfr = null,
        ?float $cef = null,
        ?float $cep = null,
        ?float $eges = null
    ): self {
        return self::create(
            bfr: $bfr ?? $this->bfr,
            cef: $cef ?? $this->cef,
            cep: $cep ?? $this->cep,
            eges: $eges ?? $this->eges,
        );
    }
}
