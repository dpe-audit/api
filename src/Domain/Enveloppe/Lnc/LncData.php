<?php

namespace App\Domain\Enveloppe\Lnc;

use Webmozart\Assert\Assert;

final class LncData
{
    public function __construct(
        public readonly ?float $aiu,
        public readonly ?float $aue,
        public readonly ?float $uvue,
        public readonly ?float $b,
        public readonly ?float $sse,
    ) {}

    public static function create(
        ?float $aue = null,
        ?float $aiu = null,
        ?float $uvue = null,
        ?float $b = null,
        ?float $sse = null,
    ): self {
        Assert::nullOrGreaterThanEq($aue, 0);
        Assert::nullOrGreaterThanEq($aiu, 0);
        Assert::nullOrGreaterThanEq($uvue, 0);
        Assert::nullOrGreaterThanEq($b, 0);
        Assert::nullOrGreaterThanEq($sse, 0);

        return new self(
            aue: $aue,
            aiu: $aiu,
            uvue: $uvue,
            b: $b,
            sse: $sse,
        );
    }

    public function with(
        ?float $aue = null,
        ?float $aiu = null,
        ?float $uvue = null,
        ?float $b = null,
        ?float $sse = null,
    ): self {
        return self::create(
            aue: $aue ?? $this->aue,
            aiu: $aiu ?? $this->aiu,
            uvue: $uvue ?? $this->uvue,
            b: $b ?? $this->b,
            sse: $sse ?? $this->sse,
        );
    }
}
