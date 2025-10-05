<?php

namespace App\Domain\Enveloppe\Baie;

use App\Domain\Enveloppe\Paroi\Performance;
use Webmozart\Assert\Assert;

final class BaieData
{
    public function __construct(
        public readonly ?float $sdep,
        public readonly ?float $ug,
        public readonly ?float $uw,
        public readonly ?float $deltar,
        public readonly ?float $u,
        public readonly ?float $b,
        public readonly ?float $dp,
        public readonly ?float $fe,
        public readonly ?float $sw,
        public readonly ?float $sse,
        public readonly ?Performance $performance,
    ) {}

    public static function create(
        ?float $sdep = null,
        ?float $ug = null,
        ?float $uw = null,
        ?float $deltar = null,
        ?float $u = null,
        ?float $b = null,
        ?float $dp = null,
        ?float $fe = null,
        ?float $sw = null,
        ?float $sse = null,
        ?Performance $performance = null,
    ): self {
        Assert::nullOrGreaterThanEq($sdep, 0);
        Assert::nullOrGreaterThanEq($ug, 0);
        Assert::nullOrGreaterThanEq($uw, 0);
        Assert::nullOrGreaterThanEq($deltar, 0);
        Assert::nullOrGreaterThanEq($u, 0);
        Assert::nullOrGreaterThanEq($b, 0);
        Assert::nullOrGreaterThanEq($dp, 0);
        Assert::nullOrGreaterThanEq($fe, 0);
        Assert::nullOrGreaterThanEq($sw, 0);
        Assert::nullOrGreaterThanEq($sse, 0);

        return new self(
            sdep: $sdep,
            ug: $ug,
            uw: $uw,
            deltar: $deltar,
            u: $u,
            b: $b,
            dp: $dp,
            fe: $fe,
            sw: $sw,
            sse: $sse,
            performance: $performance,
        );
    }

    public function with(
        ?float $sdep = null,
        ?float $ug = null,
        ?float $uw = null,
        ?float $deltar = null,
        ?float $u = null,
        ?float $b = null,
        ?float $dp = null,
        ?float $fe = null,
        ?float $sw = null,
        ?float $sse = null,
        ?Performance $performance = null,
    ): self {
        return self::create(
            sdep: $sdep ?? $this->sdep,
            ug: $ug ?? $this->ug,
            uw: $uw ?? $this->uw,
            deltar: $deltar ?? $this->deltar,
            u: $u ?? $this->u,
            b: $b ?? $this->b,
            dp: $dp ?? $this->dp,
            fe: $fe ?? $this->fe,
            sw: $sw ?? $this->sw,
            sse: $sse ?? $this->sse,
            performance: $performance ?? $this->performance,
        );
    }
}
