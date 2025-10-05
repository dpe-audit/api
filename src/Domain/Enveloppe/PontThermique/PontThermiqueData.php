<?php

namespace App\Domain\Enveloppe\PontThermique;

use Webmozart\Assert\Assert;

final class PontThermiqueData
{
    public function __construct(
        public readonly ?float $k,
        public readonly ?float $pt,
    ) {}

    public static function create(?float $k = null, ?float $pt = null): self
    {
        Assert::nullOrGreaterThanEq($k, 0);
        Assert::nullOrGreaterThanEq($pt, 0);
        return new self(k: $k, pt: $pt);
    }

    public function with(?float $k = null, ?float $pt = null): self
    {
        return self::create(
            k: $k ?? $this->k,
            pt: $pt ?? $this->pt,
        );
    }
}
