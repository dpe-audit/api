<?php

namespace App\Domain\Enveloppe\Deperditions;

use Webmozart\Assert\Assert;

final class Deperditions
{
    public function __construct(
        public readonly ?float $gv,
        public readonly ?float $dp,
        public readonly ?float $dp_murs,
        public readonly ?float $dp_planchers_bas,
        public readonly ?float $dp_planchers_hauts,
        public readonly ?float $dp_baies,
        public readonly ?float $dp_portes,
        public readonly ?float $pt,
        public readonly ?float $dr,
        public readonly ?float $ubat,
        public readonly ?Performance $performance,
    ) {}

    public static function create(
        ?float $gv = null,
        ?float $dp = null,
        ?float $dp_murs = null,
        ?float $dp_planchers_bas = null,
        ?float $dp_planchers_hauts = null,
        ?float $dp_baies = null,
        ?float $dp_portes = null,
        ?float $pt = null,
        ?float $dr = null,
        ?float $ubat = null,
        ?Performance $performance = null,
    ): self {
        Assert::nullOrGreaterThanEq($gv, 0);
        Assert::nullOrGreaterThanEq($dp, 0);
        Assert::nullOrGreaterThanEq($dp_murs, 0);
        Assert::nullOrGreaterThanEq($dp_planchers_bas, 0);
        Assert::nullOrGreaterThanEq($dp_planchers_hauts, 0);
        Assert::nullOrGreaterThanEq($dp_baies, 0);
        Assert::nullOrGreaterThanEq($dp_portes, 0);
        Assert::nullOrGreaterThanEq($pt, 0);
        Assert::nullOrGreaterThanEq($dr, 0);
        Assert::nullOrGreaterThanEq($ubat, 0);

        return new self(
            gv: $gv,
            dp: $dp,
            dp_murs: $dp_murs,
            dp_planchers_bas: $dp_planchers_bas,
            dp_planchers_hauts: $dp_planchers_hauts,
            dp_baies: $dp_baies,
            dp_portes: $dp_portes,
            pt: $pt,
            dr: $dr,
            ubat: $ubat,
            performance: $performance,
        );
    }

    public function with(
        ?float $gv = null,
        ?float $dp = null,
        ?float $pt = null,
        ?float $dr = null,
        ?float $ubat = null,
        ?Performance $performance = null,
    ): self {
        return static::create(
            gv: $gv ?? $this->gv,
            dp: $dp ?? $this->dp,
            pt: $pt ?? $this->pt,
            dr: $dr ?? $this->dr,
            ubat: $ubat ?? $this->ubat,
            performance: $performance ?? $this->performance,
        );
    }
}
