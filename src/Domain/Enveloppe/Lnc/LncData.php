<?php

namespace App\Domain\Enveloppe\Lnc;

use Webmozart\Assert\Assert;

final class LncData
{
    public function __construct(
        public readonly ?float $uvue,
        public readonly ?float $aue,
        public readonly ?float $aiu,
        public readonly ?bool $isolation_aue,
        public readonly ?bool $isolation_aiu,
        public readonly ?float $b,
        public readonly ?float $sse,
    ) {}

    public static function create(
        ?float $uvue = null,
        ?float $b = null,
        ?float $aue = null,
        ?float $aiu = null,
        ?bool $isolation_aue = null,
        ?bool $isolation_aiu = null,
        ?float $sse = null,
    ): self {
        Assert::nullOrGreaterThanEq($aue, 0);
        Assert::nullOrGreaterThanEq($aiu, 0);
        Assert::nullOrGreaterThanEq($uvue, 0);
        Assert::nullOrGreaterThanEq($b, 0);
        Assert::nullOrGreaterThanEq($sse, 0);

        return new self(
            uvue: $uvue,
            b: $b,
            aue: $aue,
            aiu: $aiu,
            isolation_aue: $isolation_aue,
            isolation_aiu: $isolation_aiu,
            sse: $sse,
        );
    }

    public function with(
        ?float $uvue = null,
        ?float $b = null,
        ?float $aue = null,
        ?float $aiu = null,
        ?bool $isolation_aue = null,
        ?bool $isolation_aiu = null,
        ?float $sse = null,
    ): self {
        return self::create(
            uvue: $uvue ?? $this->uvue,
            b: $b ?? $this->b,
            aue: $aue ?? $this->aue,
            aiu: $aiu ?? $this->aiu,
            isolation_aue: $isolation_aue ?? $this->isolation_aue,
            isolation_aiu: $isolation_aiu ?? $this->isolation_aiu,
            sse: $sse ?? $this->sse,
        );
    }
}
