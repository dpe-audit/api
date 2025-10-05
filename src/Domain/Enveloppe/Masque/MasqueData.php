<?php

namespace App\Domain\Enveloppe\Masque;

use Webmozart\Assert\Assert;

final class MasqueData
{
    public function __construct(
        public readonly ?float $fe1,
        public readonly ?float $fe2,
        public readonly ?float $omb,
    ) {}

    public static function create(
        ?float $fe1 = null,
        ?float $fe2 = null,
        ?float $omb = null,
    ): self {
        Assert::nullOrGreaterThanEq($fe1, 0);
        Assert::nullOrLessThanEq($fe1, 1);
        Assert::nullOrGreaterThanEq($fe2, 0);
        Assert::nullOrLessThanEq($fe2, 1);
        Assert::nullOrGreaterThanEq($omb, 0);
        Assert::nullOrLessThanEq($omb, 100);
        return new self(fe1: $fe1, fe2: $fe2, omb: $omb);
    }

    public function with(
        ?float $fe1 = null,
        ?float $fe2 = null,
        ?float $omb = null,
    ): self {
        return self::create(
            fe1: $fe1 ?? $this->fe1,
            fe2: $fe2 ?? $this->fe2,
            omb: $omb ?? $this->omb,
        );
    }
}
