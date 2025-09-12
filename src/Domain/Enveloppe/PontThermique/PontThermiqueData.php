<?php

namespace App\Domain\Enveloppe\PontThermique;

use Webmozart\Assert\Assert;

final class PontThermiqueData
{
    public function __construct(
        public readonly ?float $kpt,
        public readonly ?float $pt,
    ) {}

    public static function create(?float $kpt = null, ?float $pt = null): self
    {
        Assert::nullOrGreaterThanEq($kpt, 0);
        Assert::nullOrGreaterThanEq($pt, 0);
        return new self(kpt: $kpt, pt: $pt);
    }

    public function with(?float $kpt = null, ?float $pt = null): self
    {
        return self::create(
            kpt: $kpt ?? $this->kpt,
            pt: $pt ?? $this->pt,
        );
    }
}
