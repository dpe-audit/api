<?php

namespace App\Domain\Enveloppe\Baie;

use Webmozart\Assert\Assert;

final class BaieData
{
    public function __construct(
        public readonly ?float $sdep,
        public readonly ?float $u,
        public readonly ?float $b,
        public readonly ?float $dp,
        public readonly ?float $fe1,
        public readonly ?float $fe2,
        public readonly ?float $fe,
        public readonly ?float $sw,
        public readonly ?float $sse,
        public readonly ?Performance $performance,
    ) {}

    public static function create(
        ?float $sdep = null,
        ?float $u = null,
        ?float $b = null,
        ?float $dp = null,
        ?float $fe1 = null,
        ?float $fe2 = null,
        ?float $fe = null,
        ?float $sw = null,
        ?float $sse = null,
        ?Performance $performance = null,
    ): self {
        Assert::nullOrGreaterThanEq($sdep, 0);
        Assert::nullOrGreaterThanEq($u, 0);
        Assert::nullOrGreaterThanEq($b, 0);
        Assert::nullOrGreaterThanEq($dp, 0);
        Assert::nullOrGreaterThanEq($fe1, 0);
        Assert::nullOrGreaterThanEq($fe2, 0);
        Assert::nullOrGreaterThanEq($fe, 0);
        Assert::nullOrGreaterThanEq($sw, 0);
        Assert::nullOrGreaterThanEq($sse, 0);

        return new self(
            sdep: $sdep,
            u: $u,
            b: $b,
            dp: $dp,
            fe1: $fe1,
            fe2: $fe2,
            fe: $fe,
            sw: $sw,
            sse: $sse,
            performance: $performance,
        );
    }

    public function with(
        ?float $sdep = null,
        ?float $u = null,
        ?float $b = null,
        ?float $dp = null,
        ?float $fe1 = null,
        ?float $fe2 = null,
        ?float $fe = null,
        ?float $sw = null,
        ?float $sse = null,
        ?Performance $performance = null,
    ): self {
        return self::create(
            sdep: $sdep ?? $this->sdep,
            u: $u ?? $this->u,
            b: $b ?? $this->b,
            dp: $dp ?? $this->dp,
            fe1: $fe1 ?? $this->fe1,
            fe2: $fe2 ?? $this->fe2,
            fe: $fe ?? $this->fe,
            sw: $sw ?? $this->sw,
            sse: $sse ?? $this->sse,
            performance: $performance ?? $this->performance,
        );
    }
}
